<?php

/**
 * errormessages
 *
 * @author    G. I. Voros <gabor@technico.nl>
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
$pi["group"] = "Technico";
$pi["filename_this"] = "errormessages.php";
$pi["filename_list"] = "errormessages.php";
$pi["template"] = "layout/pages/errormessages.tpl";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Collect page content
 */
if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["view"]){ $hassubmit = "show"; }
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
} else {
    $hassubmit = null;
}

//required for selectbox: error types
$types["ALL"]     = $lang["(all)"];
$types["alert"]   = $lang["alert"];
$types["general"] = $lang["general"];
$types["load"]    = $lang["load_storing"];
$types["info"]    = $lang["info"];
$types["station"] = $lang["station_storing"];
$types["warning"] = $lang["warning"];

if (!empty($_GET["errormessages_type"])) {
    $urlinfo["errormessages_type"] = $_GET["errormessages_type"];
} else {
    $urlinfo["errormessages_type"] = "ALL";
}

if (!empty($_GET["dlid"])) {$urlinfo["dlid"] = trim($_GET["dlid"]);} else {$urlinfo["dlid"] = null;}
if (!empty($_GET["did"])) {$urlinfo["did"] = trim($_GET["did"]);} else {$urlinfo["did"] = null;}

if (!empty($_GET["from_date"])) {$urlinfo["from_date"] = trim($_GET["from_date"]);} else {$urlinfo["from_date"] = null;}
if (!empty($_GET["to_date"])) {$urlinfo["to_date"] = trim($_GET["to_date"]);} else {$urlinfo["to_date"] = null;}
if (!empty($_GET["lotsadays"])) {$urlinfo["lotsadays"] = trim($_GET["lotsadays"]);} else {$urlinfo["lotsadays"] = null;}
if (!empty($_GET["showall"])) {$urlinfo["showall"] = trim($_GET["showall"]);} else {$urlinfo["showall"] = null;}

if (!empty($urlinfo["lotsadays"]) && empty($urlinfo["to_date"])){ $urlinfo["to_date"] = $urlinfo["from_date"]; }

if (!empty($urlinfo["from_date"]) && !empty($urlinfo["to_date"])) {
    if ($urlinfo["to_date"] < $urlinfo["from_date"]) {
        $pi["note"] = html_error($lang["error_date_from_greater_then_to"]);
    }
}

// Required for selectbox: distributorlocations
$distributorlocations_conditions["order_by"] = "name";
$distributorlocations = db_read("distributorlocations", "id name", $distributorlocations_conditions);
$distributorlocation_count = db_num_rows($distributorlocations);

if (!empty($urlinfo["dlid"])) {
        $distributors_conditions["where"]["1"] = "distributorlocation_id = " . $urlinfo["dlid"];
        $distributors_conditions["order_by"] = "doornumber";
        $distributors = db_read("distributors", "id doornumber", $distributors_conditions);
} else {
        $distributors = null;
}


$table = "errormessages";
$columns = "errormessages.date errormessages.message errormessages.type errormessages.distributor_id errormessages.distributorlocation_id errormessages.id ";
$columns .= " distributors.doornumber distributorlocations.id distributorlocations.name";

$urlinfo["left_join"]["1"] = "distributors distributors.id errormessages.distributor_id";
$urlinfo["left_join"]["2"] = "distributorlocations distributorlocations.id errormessages.distributorlocation_id";

if($urlinfo["errormessages_type"] != "ALL") {
    $urlinfo["where"]["1"] = "errormessages.type = " . $urlinfo["errormessages_type"];
}

if (isset($urlinfo["dlid"])){
    $urlinfo["where"]["2"] = "distributorlocations.id = " . $urlinfo["dlid"];

    if (isset($urlinfo["did"])){
        $urlinfo["where"]["3"] = "errormessages.distributor_id = " . $urlinfo["did"];
    }
}

if (!isset($urlinfo["where"]["4"])) {
        $urlinfo["where"]["4"] = "errormessages.date isnot NULL";
        if (!empty($urlinfo["lotsadays"])) {
            $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
            $to_date_db = str_replace("-", "", $urlinfo["to_date"]) ."235959";

            $urlinfo["where"]["5"] = "errormessages.date >= ". $from_date_db;
            $urlinfo["where"]["6"] = "errormessages.date <= ". $to_date_db;
        } else {
            $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
            $to_date_db = str_replace("-", "", $urlinfo["from_date"]) ."235959";

            $urlinfo["where"]["5"] = "errormessages.date >= ". $from_date_db;
            $urlinfo["where"]["6"] = "errormessages.date <= ". $to_date_db;
        }
    }

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction("DESC");

if (!empty($_GET["hassubmit"]) && $hassubmit == "export") {
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

if (empty($urlinfo["showall"]) && $urlinfo["limit_total"] > 300) {
    $urlinfo["limit_total"] = "300";
}

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);
$urlinfostring = generate_urlinfo_string($pi);

$sortlinks["distributor"] = generate_sortlink("distributors.doornumber", $lang["distributor"], $pi, $urlinfo);
$sortlinks["distributorlocation"] = generate_sortlink("distributorlocations.name", $lang["distributorlocation"], $pi, $urlinfo);
$sortlinks["error"] = generate_sortlink("errormessages.message", $lang["error"], $pi, $urlinfo);
$sortlinks["type"] = generate_sortlink("errormessages.type", $lang["type"], $pi, $urlinfo);
$sortlinks["date"] = generate_sortlink("errormessages.date", $lang["date"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

// Export
if (!empty($_GET["hassubmit"]) && $hassubmit == "export")
{
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["type"]."\t".$lang["error"]."\t".$lang["distributorlocation"]."\t".$lang["distributor"]."\t".$lang["date"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata))
    {
        $line = "";
        $in = array(
            $lang[$row["errormessages_type"]],
            $row["errormessages_message"],
            $row["distributorlocations_name"],
            $row["distributors_doornumber"],
            $row["errormessages_date"]
        );

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

    $data = str_replace("\r","",$data);

    print "$header\n$data";
    die();
 }

 /*
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "types" => $types,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "pagination" => $pagination,
    "urlinfostring" => $urlinfostring,
    "distributorlocations" => $distributorlocations,
    "distributorlocation_count" => $distributorlocation_count,
    "lotsadays" => ($urlinfo["lotsadays"] == true) ? "checked=\"checked\"" : "",
    "showall" => ($urlinfo["showall"] == true) ? "checked=\"checked\"" : "",
    "distributors" => $distributors,
    "sortlinks" => $sortlinks
);

template_parse($pi, $urlinfo, $cv);

?>
