<?php

/**
 * Report packinglists
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require_once "library/bootstrap.php";

/**
 * Page settings
 */
$pi["access"] = array("linen_service", "packinglist_generate");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["packinglist_generate"];
$pi["filename_this"] = "packinglist_generate.php";
$pi["filename_list"] = "packinglist_generate.php";
$pi["filename_details"] = "packinglist_generate.php";
$pi["template"] = "layout/pages/packinglist_generate.tpl";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$print = null;

//fetch selected circulationgroup_id or set default
if (!empty($_POST["cid"])) {
    $urlinfo["cid"] = $_POST["cid"];
} else {
    //we use the circulationgroup_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_circulationgroup_conditions["order_by"] = "name";
    $selected_circulationgroup_conditions["limit_start"] = 0;
    $selected_circulationgroup_conditions["limit_num"] = 1;
    $urlinfo["cid"] = db_fetch_row(db_read("circulationgroups", "id", $selected_circulationgroup_conditions));
    $urlinfo["cid"] = $urlinfo["cid"][0];
}

//required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

//set depositbatches final 1 if depositbatches_garments deleted
$depositbatches_clear_sql = "
	UPDATE depositbatches db
    INNER JOIN depositlocations dl ON dl.id = db.depositlocation_id
           SET db.final = 1
         WHERE db.final = 0
           AND dl.circulationgroup_id = " . $urlinfo["cid"] . "
           AND db.id NOT IN (SELECT dbg.depositbatch_id FROM depositbatches_garments dbg)
";
db_query($depositbatches_clear_sql);

//required for selectbox: unfinalized depositbatches
$depositbatches_all_sql = "
	SELECT `depositbatches`.`id`, DATE_FORMAT(`date`, '" . $lang["dB_DATETIME_FORMAT"]. "')
	  FROM `depositbatches`
    INNER JOIN `depositlocations` ON `depositbatches`.`depositlocation_id` = `depositlocations`.`id`
    INNER JOIN `circulationgroups` ON `depositlocations`.`circulationgroup_id` = `circulationgroups`.`id`
         WHERE `circulationgroups`.`id` = " . $urlinfo["cid"] . "
           AND `final` = 0
           ORDER BY `date` DESC
";

$depositbatches_all_data = db_query($depositbatches_all_sql);

if (!empty($depositbatches_all_data)) {
    $depositbatches_all = null;
    while ($row = db_fetch_num($depositbatches_all_data)) {
        $depositbatches_all[$row[0]] = $row[1];
    }
} else {
    $depositbatches_all = null;
}

//catch the selected depositbatches, if they were previously selected
if (isset($_POST["depositbatches_selected"])) {
    foreach ($_POST["depositbatches_selected"] as $num => $id) {
        $depositbatches_selected[$id] = $id;
    }
} else {
    #$depositbatches_selected = $depositbatches_all;
    $depositbatches_selected = null;
}

if (!empty($depositbatches_selected)) {
    $packinglist_sql = "SELECT
        `articles`.`articlenumber` AS 'articlecode',
        `articles`.`description` AS 'description',
        IF(ISNULL(`arsimos`.`modification_id`), `sizes`.`name`, CONCAT(`sizes`.`name`, ' ', `modifications`.`name`)) AS 'size',
        IF(ISNULL(`garments`.`garmentuser_id`), '" . $lang["size"] . "', '" . $lang["garmentuser"] . "') AS 'userbound',
        COUNT(`garments`.`id`) AS 'count'
        FROM
        `circulationgroups`
        INNER JOIN `depositlocations` ON `depositlocations`.`circulationgroup_id` = `circulationgroups`.`id`
        INNER JOIN `depositbatches` ON `depositbatches`.`depositlocation_id` = `depositlocations`.`id`
        INNER JOIN `depositbatches_garments` ON `depositbatches_garments`.`depositbatch_id` = `depositbatches`.`id`
        INNER JOIN `garments` ON `depositbatches_garments`.`garment_id` = `garments`.`id`
        INNER JOIN `arsimos` ON `garments`.`arsimo_id` = `arsimos`.`id`
        INNER JOIN `articles` ON `arsimos`.`article_id` = `articles`.`id`
        INNER JOIN `sizes` ON `arsimos`.`size_id` = `sizes`.`id`
        INNER JOIN `sizegroups` ON `sizes`.`sizegroup_id` = `sizegroups`.`id`
        LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`
        WHERE `circulationgroups`.`id` = " . $urlinfo["cid"];

    if (!empty($depositbatches_selected)) {
        $packinglist_sql .= " AND `depositbatches`.`id` IN (" . implode(",", array_keys($depositbatches_selected)) . ")";
    }

    $packinglist_sql .= "
        GROUP BY
        `arsimos`.`id`,
        ISNULL(`garments`.`garmentuser_id`)
        ORDER BY
        `articles`.`description`,
        `sizes`.`position`,
        `modifications`.`name`
    ";
    $listdata = db_query($packinglist_sql);
} else {
    $listdata = null;
}

// Required for header: total
if ($listdata) {
    $header["total"] = 0;
    while ($row = db_fetch_assoc($listdata)) {
        $header["total"] += $row["count"];
    }
    @db_data_seek($listdata, 0);
} else {
    $header["total"] = null;
}


if ((isset($_POST["finalize"]) || isset($_POST["print"]) || isset($_POST["email"])) && $depositbatches_selected) {
    //with these queries we finalize the depositbatch
    $sql = "INSERT INTO packinglists (circulationgroup_id, date) VALUES (" . $urlinfo["cid"] . ", NOW())";
    $exec = db_query($sql);

    $packinglist_id = db_fetch_row(db_read_last_insert_id("packinglists"));
    $packinglist_id = $packinglist_id[0];

    $sql = "INSERT INTO packinglists_depositbatches
            (
                SELECT DISTINCT $packinglist_id, depositbatches.id
                FROM depositlocations
                INNER JOIN depositbatches ON depositbatches.depositlocation_id = depositlocations.id  AND depositbatches.final = 0
                INNER JOIN depositbatches_garments ON depositbatches_garments .depositbatch_id = depositbatches.id
                INNER JOIN garments ON depositbatches_garments.garment_id = garments.id AND garments.deleted_on IS NULL AND garments.scanlocation_id = depositlocations.scanlocation_id_transport
                WHERE depositlocations.circulationgroup_id = " . $urlinfo["cid"] . " AND `depositbatches`.`id` IN (" . implode(",", array_keys($depositbatches_selected)) . ")
            )";
    $exec = db_query($sql);

    $sql = "UPDATE depositlocations
            INNER JOIN depositbatches ON depositbatches.depositlocation_id = depositlocations.id  AND depositbatches.final = 0
            INNER JOIN depositbatches_garments ON depositbatches.id = depositbatches_garments.depositbatch_id
            INNER JOIN garments ON depositbatches_garments.garment_id = garments.id AND garments.deleted_on IS NULL AND garments.scanlocation_id = depositlocations.scanlocation_id_transport
            SET depositbatches.final = 1, garments.scanlocation_id = depositlocations.scanlocation_id_transport_to_laundry, garments.lastscan = NOW() 
            WHERE depositlocations.circulationgroup_id = " . $urlinfo["cid"]. " AND `depositbatches`.`id` IN (" . implode(",", array_keys($depositbatches_selected)) . ")";
    $exec = db_query($sql);
    
    $sql = "UPDATE depositlocations
            INNER JOIN depositbatches ON depositbatches.depositlocation_id = depositlocations.id  AND depositbatches.final = 0
            INNER JOIN depositbatches_garments ON depositbatches.id = depositbatches_garments.depositbatch_id
            INNER JOIN garments ON depositbatches_garments.garment_id = garments.id
            SET depositbatches.final = 1
            WHERE depositlocations.circulationgroup_id = " . $urlinfo["cid"]. " AND `depositbatches`.`id` IN (" . implode(",", array_keys($depositbatches_selected)) . ")";
    $exec = db_query($sql);

    if (isset($_POST["print"])) {
        $print = "<script type='text/javascript'>window.open('report_packinglists_history_print.php?p=" . $packinglist_id . "&print','new_window1','');</script>";
    } elseif(isset($_POST["email"])) {
        $circulationgroup_sql = "SELECT `name` FROM `circulationgroups` WHERE `id` = " . $urlinfo["cid"];
        $circulationgroup_sql = db_query($circulationgroup_sql);
        $circulationgroup = db_fetch_assoc($circulationgroup_sql);
        $location = $circulationgroup["name"];
             
        $plist_sql = "SELECT
            `articles`.`articlenumber` AS 'articlenumber',
            `articles`.`description` AS 'description',
            IF(ISNULL(`arsimos`.`modification_id`), `sizes`.`name`, CONCAT(`sizes`.`name`, ' ', `modifications`.`name`)) AS 'size',
            IF(ISNULL(`garments`.`garmentuser_id`), '" . $lang["size"] . "', '" . $lang["garmentuser"] . "') AS 'sb_ub',
            COUNT(`garments`.`id`) AS 'count'
        FROM
            `packinglists`
            INNER JOIN `packinglists_depositbatches` ON `packinglists`.`id` = `packinglists_depositbatches`.`packinglist_id`
            INNER JOIN `depositbatches_garments` ON `packinglists_depositbatches`.`depositbatch_id` = `depositbatches_garments`.`depositbatch_id`
            INNER JOIN `garments` ON `depositbatches_garments`.`garment_id` = `garments`.`id`
            INNER JOIN `arsimos` ON `garments`.`arsimo_id` = `arsimos`.`id`
            INNER JOIN `articles` ON `arsimos`.`article_id` = `articles`.`id`
            INNER JOIN `sizes` ON `arsimos`.`size_id` = `sizes`.`id`
            LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`
        WHERE `packinglists`.`id` = " . $packinglist_id . "
        GROUP BY
            `arsimos`.`id`,
            ISNULL(`garments`.`garmentuser_id`)
            ORDER BY
            `articles`.`description`,
            `sizes`.`position`,
            `modifications`.`name`";

        $plist = db_query($plist_sql);

        // Required for header: total
        $total = 0;
        if ($plist) {
            while ($row = db_fetch_assoc($plist)) {
                $total += $row["count"];
            }
            db_data_seek($plist, 0);
        }
        
        $plist_table_rows = "";
        while ($row = db_fetch_assoc($plist)){
            $plist_table_rows .= '<tr style="background-color: #F4F4F5;"><td style="text-align:left;">'. trim($row['sb_ub']) .'</td><td style="text-align:left;">'. $row['articlenumber'] .'</td><td style="text-align:left;">'. $row['description'] .'</td><td style="text-align:center;">'. $row['size'] .'</td><td style="text-align:center;">'. $row['count'] .'</td></tr>';
        }
        
        $plist_table_data .='<table style="text-align:center;width:100%;border: 2px solid #98AAB1;">';
        $plist_table_data .='<tr style="color: #FFFFFF;background-color: #25814E;"><td style="text-align:left;"><strong>'. $lang["sb_ub"] .'</strong></td><td style="text-align:left;"><strong>'. $lang["articlenumber"] .'</strong></td><td style="text-align:left;"><strong>'. $lang["description"] .'</strong></td><td style="text-align:center;"><strong>'. $lang["size"] .'</strong></td><td style="text-align:center;"><strong>'. $lang["count"] .'</strong></td></tr>';
        $plist_table_data .= $plist_table_rows;
        $plist_table_data .='</table>';

        $final_data = '<p><strong>Techni<span style="color:#1C5A39;">X</span> GS</strong> - '. $lang["packinglist"] .'
                        '. $lang["location"] .': '.$location.'
                        '. $lang["date"] .': '.date("Y-m-d").'
                        '. $lang["total"] . ' ' . $lang["garments"] . ': ' . $total .'
                        '. $lang["packinglist_number"] .': '. $packinglist_id . '<br /></p>' . $plist_table_data;

        $email_addresses_sql = "SELECT `email_address`, `name`
                                  FROM `emailaddresses`
                                 WHERE `group` = 'PACKINGLIST'";
        $email_addresses = db_query($email_addresses_sql);

        while ($row = db_fetch_assoc($email_addresses)) {
            $current_emailaddress = $row["email_address"];
            $current_name = $row["name"];

            try {
                $m = new Email();
                $m->setRecepients(array($current_emailaddress => $current_name));
                $m->setSubject($lang["packinglist"] ." ". $packinglist_id." - ". $location);
                $m->addBody($final_data, "text/html");
                $m->send();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        redirect($pi["filename_list"]);
    }
    else {
        redirect($pi["filename_list"]);
    }
}

/**
* Export
*/
if (isset($_POST["export"])) {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = "";
    $header.=$lang["sb_ub"]."\t";
    $header.=$lang["articlenumber"]."\t";
    $header.=$lang["description"]."\t";
    $header.=$lang["size"]."\t";
    $header.=$lang["count"]."\t";

    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";

        $in = array();
        array_push($in,$row["userbound"]);
        array_push($in,$row["articlecode"]);
        array_push($in,$row["description"]);
        array_push($in,$row["size"]);
        array_push($in,$row["count"]);

        foreach($in as $value) {
            if ((!isset($value)) OR ($value == "")) {
                $value = "\t";
            } else {
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"' . "\t";
            }
            $line .= $value;
        }
        $data .= trim($line)."\n";
    }
    $data_r = str_replace("\r","",$data);

    print "$header\n$data_r";
    die();
}
     
/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "depositbatches_all" => $depositbatches_all,
    "depositbatches_selected" => $depositbatches_selected,
    "header" => $header,
    "listdata" => $listdata,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "print" => $print
);

template_parse($pi, $urlinfo, $cv);

?>
