<?php

/**
 * Report deposits by date
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
$pi["access"] = array("lists", "deposited_garments");
$pi["group"] = $lang["lists"];
$pi["title"] = $lang["deposited_garments"];
$pi["template"] = "layout/pages/report_deposits_by_date.tpl";
$pi["page"] = "list";
$pi["filename_this"] = "report_deposits_by_date.php";
$pi["filename_list"] = "report_deposits_by_date.php";
$pi["toolbar"]["no_new"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["view"]){ $hassubmit = "show"; }
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
    if ($_GET["hassubmit"] == $lang["print"]){ $hassubmit = "print"; }
} else {
    $hassubmit = "";
}

$urlinfo = array(
    "cid" => null,
    "did" => null,
    "from_date" => (!empty($_GET["from_date"])) ? trim($_GET["from_date"]) : date("Y-m-d"),
    "to_date" => (!empty($_GET["to_date"])) ? trim($_GET["to_date"]) : "",
    "lotsadays" => (!empty($_GET["lotsadays"])) ? trim($_GET["lotsadays"]) : "",
    "hassubmit" => (!empty($_GET["hassubmit"])) ? trim($_GET["hassubmit"]) : ""
);

if (!empty($urlinfo["lotsadays"]) && empty($urlinfo["to_date"])){ $urlinfo["to_date"] = $urlinfo["from_date"]; }
if (!empty($_GET["cid"])){ $urlinfo["cid"] = trim($_GET["cid"]); }
if (!empty($_GET["did"])){ $urlinfo["did"] = trim($_GET["did"]); }

if (!empty($urlinfo["from_date"]) && !empty($urlinfo["to_date"])) {
    if ($urlinfo["to_date"] < $urlinfo["from_date"]) {
        $pi["note"] = html_error($lang["error_date_from_greater_then_to"]);
    }
}
if (!empty($_GET["dsearch"])){ $urlinfo["dsearch"] = $_GET["dsearch"]; }else{ $urlinfo["dsearch"] = ""; }

// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

// Required for selectbox: depositlocations
$depositlocations_conditions["order_by"] = "name";
$depositlocations = db_read("depositlocations", "id name", $depositlocations_conditions);
$depositlocation_count = db_num_rows($depositlocations);

if (empty($pi["note"])) {
    $table = "log_depositlocations_garments";
    $columns = "log_depositlocations_garments.date log_depositlocations_garments.garment_id log_depositlocations_garments.depositlocation_id";
    $columns .= " articles.description sizes.name modifications.name garments.tag";
    $columns .= " depositlocations.name";

    $urlinfo["inner_join"]["1"] = "garments log_depositlocations_garments.garment_id garments.id";
    $urlinfo["inner_join"]["2"] = "arsimos garments.arsimo_id arsimos.id";
    $urlinfo["inner_join"]["3"] = "articles arsimos.article_id articles.id ";
    $urlinfo["inner_join"]["4"] = "sizes arsimos.size_id sizes.id";
    $urlinfo["inner_join"]["5"] = "depositlocations log_depositlocations_garments.depositlocation_id depositlocations.id";
    $urlinfo["inner_join"]["6"] = "circulationgroups depositlocations.circulationgroup_id circulationgroups.id";
    $urlinfo["left_join"]["1"] = "modifications arsimos.modification_id modifications.id";

    if (!empty($urlinfo["lotsadays"])) {
        $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
        $to_date_db = str_replace("-", "", $urlinfo["to_date"]) ."235959";

        $urlinfo["where"]["1"] = "log_depositlocations_garments.date >= ". $from_date_db;
        $urlinfo["where"]["2"] = "log_depositlocations_garments.date <= ". $to_date_db;
    } else {
        $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
        $to_date_db = str_replace("-", "", $urlinfo["from_date"]) ."235959";

        $urlinfo["where"]["1"] = "log_depositlocations_garments.date >= ". $from_date_db;
        $urlinfo["where"]["2"] = "log_depositlocations_garments.date <= ". $to_date_db;
    }

    if (!empty($urlinfo["cid"])) {
        $urlinfo["where"]["3"] = "circulationgroups.id = " . $urlinfo["cid"];
    }

    if (!empty($urlinfo["did"])) {
        $urlinfo["where"]["4"] = "log_depositlocations_garments.depositlocation_id = " . $urlinfo["did"];
    }

	$urlinfo["search"] = trim(geturl_search(true, $urlinfo["cid"]),"'");

    $urlinfo["order_by"] = geturl_order_by($columns);
    $urlinfo["order_direction"] = geturl_order_direction("DESC");

    if ($hassubmit == "export") {
        $urlinfo["limit_start"] = 0;
        $urlinfo["limit_num"] = "65535";
    } else {
        $urlinfo["limit_start"] = geturl_limit_start();
        $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
    }

    $urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
    $urlinfo["limit_total"] = $urlinfo["limit_total"]["0"]; //array->string

    $listdata = db_read($table, $columns, $urlinfo);
    $ldata = db_read($table, $columns, $urlinfo);

    $resultinfo = result_infoline($pi, $urlinfo);

    $sortlinks["date"] = generate_sortlink("log_depositlocations_garments.date", $lang["date"], $pi, $urlinfo);
    $sortlinks["name"] = generate_sortlink("articles.description", $lang["name"], $pi, $urlinfo);
    $sortlinks["size"] = generate_sortlink("sizes.position", $lang["size"], $pi, $urlinfo);
    $sortlinks["tag"] = generate_sortlink("garments.tag", $lang["tag"], $pi, $urlinfo);
    $sortlinks["last_used_by"] = generate_sortlink($history[$row["garments_tag"]], $lang["last_used_by"], $pi, $urlinfo);//It doesn't work.
    $sortlinks["depositlocation"] = generate_sortlink("depositlocations.name", $lang["depositlocation"], $pi, $urlinfo);

    $pagination = generate_pagination($pi, $urlinfo);


    $history = array();
    //required for history
    while ($ld = db_fetch_assoc($ldata)) {
        $history_query = "SELECT `last_distributions`.`starttime` AS 'log_garmentusers_garments_starttime',
            `garmentusers`.`id` AS 'id',
            `garmentusers`.`surname` AS 'surname',
            `garmentusers`.`title` AS 'title',
            `garmentusers`.`name` AS 'name',
            `garmentusers`.`maidenname` AS 'maidenname',
            `garmentusers`.`initials` AS 'initials',
            `garmentusers`.`gender` AS 'gender',
            `garmentusers`.`intermediate` AS 'intermediate',
            `garmentusers`.`personnelcode` AS 'personnelcode'
            FROM
            (
                SELECT `log_garmentusers_garments`.*
                  FROM `log_garmentusers_garments`
                 WHERE `log_garmentusers_garments`.`garment_id` = ". $ld["log_depositlocations_garments_garment_id"] ."
                   AND `log_garmentusers_garments`.`starttime` <  '". $ld["log_depositlocations_garments_date"] ."'
              ORDER BY `log_garmentusers_garments`.`starttime` DESC
                 LIMIT 0, 1
            ) `last_distributions`
            INNER JOIN `garments` ON `last_distributions`.`garment_id` = `garments`.`id`
            INNER JOIN `garmentusers` ON `last_distributions`.`garmentuser_id` = `garmentusers`.`id`
            GROUP BY `garmentusers`.`id`
            ORDER BY `last_distributions`.`starttime` DESC";

        $historydata = db_query($history_query);
        $history_temp = db_fetch_assoc($historydata);
        $name = generate_garmentuser_label($history_temp["title"], $history_temp["gender"], $history_temp["initials"], $history_temp["intermediate"], $history_temp["surname"], $history_temp["maidenname"], $history_temp["personnelcode"]);
        if (trim($name) !== ",") {
            $history[$ld["garments_tag"].trim($ld["log_depositlocations_garments_date"])] = $name;
            $garmentusers[$ld["garments_tag"].trim($ld["log_depositlocations_garments_date"])] = $history_temp["id"];
        } else {
            $history[$ld["garments_tag"]] = "<span class=\"empty\">" . $lang["unknown"] . "</span>";
        }
    }


    if ($hassubmit == "export") {
        $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=". $export_filename);
        header("Pragma: no-cache");
        header("Expires: 0");

        $header = $lang["date"]."\t".$lang["article"]."\t".$lang["size"]."\t".$lang["tag"]."\t".$lang["last_used_by"]."\t".$lang["depositlocation"]."\t";
        $data = "";
        while ($row = db_fetch_array($listdata)) {
            $line = "";
            $last_used_unknown = "<span class=\"empty\">" . $lang["unknown"] . "</span>";
            $in = array(
                $row["log_depositlocations_garments_date"],
                $row["articles_description"],
                $row["sizes_name"],
                "'".$row["garments_tag"],
                ($history[$row["garments_tag"].trim($row["log_depositlocations_garments_date"])]!=$last_used_unknown)?$history[$row["garments_tag"].trim($row["log_depositlocations_garments_date"])]:$lang["unknown"],
                $row["depositlocations_name"]
            );

            foreach ($in as $value) {
                if (!isset($value) || $value == "") {
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
};


/**
 * Generate the page
 */
$cv = array(
    "hassubmit" => $hassubmit,
    "history" => $history,
    "pi" => $pi,
    "lotsadays" => ($urlinfo["lotsadays"] == true) ? "checked=\"checked\"" : "",
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "resultinfo" => $resultinfo,
    "pagination" => $pagination,
    "sortlinks" => $sortlinks,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "depositlocation_count" => $depositlocation_count,
    "depositlocations" => $depositlocations,
    "garmentusers" => $garmentusers
);

template_parse($pi, $urlinfo, $cv);

?>
