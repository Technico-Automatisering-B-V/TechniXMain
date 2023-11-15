<?php

/**
 * Report garmentusers per arsimo
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi["access"] = array("reports", "garmentusers_per_arsimo");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["garmentusers_per_arsimo"];
$pi["filename_list"] = "report_garmentusers_per_arsimo.php";
$pi["filename_details"] = "report_garmentusers_per_arsimo_details.php";
$pi["template"] = "layout/pages/report_garmentusers_per_arsimo.tpl";
$pi["page"] = "list";
$pi["toolbar"]["no_new"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
} else {
    $hassubmit = "";
}

/**
 * Get ids
 */
if (!empty($_GET["cid"])) {$urlinfo["cid"] = $_GET["cid"];} else {$urlinfo["cid"] = "";}
if (!empty($_GET["aid"])) {$urlinfo["aid"] = $_GET["aid"];} else {$urlinfo["aid"] = "";}
if (!empty($_GET["sid"])) {$urlinfo["sid"] = $_GET["sid"];} else {$urlinfo["sid"] = "";}
if (!empty($_GET["mid"])) {$urlinfo["mid"] = $_GET["mid"];} else {$urlinfo["mid"] = "";}

if (!empty($_GET["clientdepartment_id"])){$urlinfo["clientdepartment_id"] = trim($_GET["clientdepartment_id"]);} else {$urlinfo["clientdepartment_id"] = null;}
if (!empty($_GET["costplace_id"])) {$urlinfo["costplace_id"] = trim($_GET["costplace_id"]);} else {$urlinfo["costplace_id"] = null;}
if (!empty($_GET["function_id"])) {$urlinfo["function_id"] = trim($_GET["function_id"]);} else {$urlinfo["function_id"] = null;}

/** required for selectbox: circulationgroups **/
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

// Required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles = db_read("articles", "id description", $articles_conditions);

// Required for selectbox: clientdepartment_id
$clientdepartments_conditions["order_by"] = "name";
$clientdepartments = db_read("clientdepartments", "id name", $clientdepartments_conditions);
$clientdepartments_count = db_num_rows($clientdepartments);

// Required for selectbox: costplaces
$costplaces_conditions["order_by"] = "value";
$costplaces = db_read("costplaces", "id value", $costplaces_conditions);
$costplaces_count = db_num_rows($costplaces);

// Required for selectbox: functions
$functions_conditions["order_by"] = "value";
$functions = db_read("functions", "id value", $functions_conditions);
$functions_count = db_num_rows($functions);

// Required for selectbox: sizes
if (!empty($urlinfo["aid"])) {
    $sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
    $sizes_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["aid"];
    $sizes_conditions["where"]["2"] = "arsimos.deleted_on is null";
    $sizes_conditions["order_by"] = "sizes.position";
    $sizes_conditions["group_by"] = "arsimos.size_id";
    $sizes_data = db_read("arsimos", "arsimos.size_id sizes.name", $sizes_conditions);
    if (!empty($sizes_data)) {
        while ($row = db_fetch_num($sizes_data)) {
            $sizes[$row[0]] = $row[1];
        }   
    } else {
        $sizes = null;
    }
    
    if (!empty($urlinfo["sid"])) {
        $modifications_conditions["inner_join"]["1"] = "modifications modifications.id arsimos.modification_id";
        $modifications_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["aid"];
        $modifications_conditions["where"]["2"] = "arsimos.size_id = " . $urlinfo["sid"];
        $modifications_conditions["where"]["3"] = "arsimos.deleted_on is null";
        $modifications_conditions["order_by"] = "modifications.id";
        $modifications_conditions["group_by"] = "arsimos.modification_id";
        $modifications_data = db_read("arsimos", "arsimos.modification_id modifications.name", $modifications_conditions);
        if (!empty($modifications_data)) {
            while ($row = db_fetch_num($modifications_data)) {
                $modifications[$row[0]] = $row[1];
            }
        } else {
            $modifications = null;
        }
    } else {
        $modifications = null;
    } 
} else {
    $sizes = null;
    $modifications = null;
}

$cquery = "";
$cquery2 = "";
$cquery3 = "";

if (!empty($urlinfo["cid"])) {$cquery2 .= " AND `g`.`circulationgroup_id` = ". $urlinfo["cid"] ." ";}

if (!empty($urlinfo["clientdepartment_id"])) {$cquery .= " AND `gu`.`clientdepartment_id` = ". $urlinfo["clientdepartment_id"];}
if (!empty($urlinfo["costplace_id"])) {$cquery .= " AND `gu`.`costplace_id` = ". $urlinfo["costplace_id"];}
if (!empty($urlinfo["function_id"])) {$cquery .= " AND `gu`.`function_id` = ". $urlinfo["function_id"];}

if (!empty($urlinfo["aid"])) {
    $squery = " WHERE `articles`.`id` = ". $urlinfo["aid"] . " AND ISNULL(`arsimos`.`deleted_on`)";
    if (!empty($urlinfo["sid"])) {
        $squery .= " AND `sizes`.`id` = ". $urlinfo["sid"];
        
        if (!empty($urlinfo["mid"])) {
            $squery .= " AND `modifications`.`id` = ". $urlinfo["mid"];
        }
    } 
} else {
    if (isset($_GET["search"]) && !empty($_GET["search"])) {
        $sstring = trim($_GET["search"]);
        $squery = " WHERE (`articles`.`description` LIKE '%". $sstring ."%' OR
                          `sizes`.`name` LIKE '%". $sstring ."%' OR
                          `modifications`.`name` LIKE '%". $sstring ."%')
                      AND ISNULL(`arsimos`.`deleted_on`)";
    } else {
        $squery = " WHERE ISNULL(`arsimos`.`deleted_on`)";
    }
}

if (!empty($urlinfo["cid"])) {
	$cquery3 .= " INNER JOIN last_month_users l ON l.garmentuser_id = gu.id AND l.`circulationgroup_id` = ". $urlinfo["cid"] ." ";
	
	mysql_query("TRUNCATE last_month_users");
	
	$last_month_users_query = "INSERT INTO last_month_users
	SELECT lg.garmentuser_id, dl.circulationgroup_id FROM log_garmentusers_garments lg
	INNER JOIN distributors d ON d.id = lg.distributor_id
	INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
	WHERE lg.starttime > DATE_SUB(NOW(), INTERVAL 1 YEAR)
	GROUP BY lg.garmentuser_id,dl.circulationgroup_id";
	
	mysql_query($last_month_users_query);
}

/**
 * Collect page content
 */
$query = "SELECT
           `arsimos`.`id` AS 'arsimo_id',
           `articles`.`articlenumber` AS 'articlenumber',
           `articles`.`description` AS 'article',
           `sizes`.`name` AS 'size',
           `modifications`.`name` AS 'modification',
	   (
                SELECT COUNT(DISTINCT `ga`.`garmentuser_id`)
                  FROM `garmentusers_arsimos` `ga`
            INNER JOIN `garmentusers` `gu` ON `gu`.`id` = `ga`.`garmentuser_id`
            ". $cquery3 ."  
                 WHERE `ga`.`arsimo_id` = `arsimos`.`id`
                   AND `ga`.`enabled` = 1
                   AND `ga`.`userbound` = 0
                   ". $cquery ." 
            AND ISNULL(`gu`.`deleted_on`)
           )
           AS 'c_s_gu',
           (
                SELECT COUNT(*)
                  FROM `garments` `g`
            INNER JOIN `scanlocations` `s` ON `s`.`id` = `g`.`scanlocation_id`
                 WHERE `s`.`circulationgroup_id` IS NOT NULL
                   ". $cquery2 ."
                   AND `g`.`arsimo_id` = `arsimos`.`id`
           AND (ISNULL(`g`.`garmentuser_id`) OR `g`.`garmentuser_id` = '')
            AND ISNULL(`g`.`deleted_on`)
           )
           AS 'c_s_g',
           (
                SELECT COUNT(*)
                  FROM `garments` `g`
            INNER JOIN `scanlocations` `s` ON `s`.`id` = `g`.`scanlocation_id`
          WHERE ISNULL(`s`.`circulationgroup_id`)
                   ". $cquery2 ."
                   AND `g`.`arsimo_id` = `arsimos`.`id`
           AND (ISNULL(`g`.`garmentuser_id`) OR `g`.`garmentuser_id` = '')
            AND ISNULL(`g`.`deleted_on`)
           )
           AS 'c_so_g'
      FROM `arsimos`
INNER JOIN `articles` ON `arsimos`.`article_id` = `articles`.`id`
INNER JOIN `sizes` ON `arsimos`.`size_id` = `sizes`.`id`
 LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`" . $squery;

$listdata = db_query($query);

/**
 * Export
 */
if ($hassubmit == "export") {
    $export_filename = "export_garmentusers_arsimos_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = "";
    $headera = "";
    
    $header.=$lang["article"]."\t"." \t"." \t";
    $headera.=$lang["articlenumber"]."\t".$lang["description"]."\t".$lang["size"]."\t";
        $header.=" \t";
        $headera.=$lang["modification"]."\t";
    
    $header.=$lang["sizebound"]."\t"." \t"." \t"." \t";
    $headera.=$lang["garmentusers"]."\t".$lang["garments"]."\t".$lang["average"]."\t".$lang["out_circulation_garments"]."\t";

    
    
    $data = "";
    while($row = db_fetch_array($listdata)) {
        if ($row["c_s_gu"] > 0 || $row["c_s_g"] > 0 || $row["c_so_g"] > 0) {
            $line = "";
            $in = array();
            array_push($in, !empty($row["articlenumber"])?$row["articlenumber"]:"-");
            array_push($in, !empty($row["article"])?$row["article"]:"");
            array_push($in, !empty($row["size"])?$row["size"]:"");
            array_push($in, ($row["modification"]) ? $row["modification"] : $lang["none"]);
            array_push($in, !empty($row["c_s_gu"])?$row["c_s_gu"]:"");
            array_push($in, !empty($row["c_s_g"])?$row["c_s_g"]:"");
            array_push($in, (($row["c_s_g"]!=0 && $row["c_s_gu"]!=0)?(round(($row["c_s_g"]/$row["c_s_gu"]),1)):'0'));
            array_push($in, !empty($row["c_so_g"])?$row["c_so_g"]:"");
            
            foreach($in as $value) {
                if (!isset($value) || $value == "") {
                    $value = "\t";
                } else {
                    $value = str_replace('"', '""', $value);
                    $value = $value . "\t";
                }
                $line .= $value;
            }
            $data .= trim($line)."\n";
        }
    }
    $data_r = str_replace("\r","",$data);

    print "$header\n$headera\n$data_r";
    die();
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "circulationgroups" => $circulationgroups,
    "circulationgroup_count" => $circulationgroup_count,
    "clientdepartments_count" => $clientdepartments_count,
    "clientdepartments" => $clientdepartments,
    "costplaces" => $costplaces,
    "costplaces_count" => $costplaces_count,
    "functions" => $functions,
    "functions_count" => $functions_count,
    "sizes" => $sizes,
    "modifications" => $modifications,
    "articles" => $articles
);

template_parse($pi, $urlinfo, $cv);

?>
