<?php

/**
 * FiFo distribution
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2012 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi = array();
$pi["group"] = "Technico";
$pi["title"] = $lang["fifo_distribution"];
$pi["template"] = "layout/pages/fifo_distribution.tpl";
$pi["filename_list"] = "fifo_distribution.php";
$pi["filename_details"] = "fifo_distribution_details.php";
$pi["page"] = "list";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}

/**
 * Collect page content
 */
$table = "circulationgroups_fifo_distribution";
$urlinfo["left_join"]["1"] = "circulationgroups circulationgroups.id circulationgroups_fifo_distribution.circulationgroup_id";
$columns = "circulationgroups.name circulationgroups_fifo_distribution.id circulationgroups_fifo_distribution.dayofweek ";
$columns .= "circulationgroups_fifo_distribution.from_hours ";
$columns .= "circulationgroups_fifo_distribution.to_hours";

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

if (isset($_GET["tool"]) && $_GET["tool"] == $lang["export"]) {
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0];

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["location"] = generate_sortlink("circulationgroups.name", $lang["location"], $pi, $urlinfo);
$sortlinks["dayofweek"] = generate_sortlink("circulationgroups_fifo_distribution.dayofweek", $lang["dayofweek"], $pi, $urlinfo);
$sortlinks["distribution_from"] = generate_sortlink("circulationgroups_fifo_distribution.from_hours", $lang["distribution_from"], $pi, $urlinfo);
$sortlinks["distribution_to"] = generate_sortlink("circulationgroups_fifo_distribution.to_hours", $lang["distribution_to"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
 * Export
 */
if (isset($_GET["tool"]) && $_GET["tool"] == $lang["export"]) {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=$export_filename");
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["location"]."\t".$lang["dayofweek"]."\t".$lang["distribution_from"]."\t".$lang["distribution_to"]."\t";
    $data = "";
    while ($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["circulationgroups.name"],
            $row["circulationgroups_fifo_distribution.dayofweek"],
            $row["circulationgroups_fifo_distribution.from_hours"],
            $row["circulationgroups_fifo_distribution.to_hours"]
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
    "sortlinks" => $sortlinks,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
