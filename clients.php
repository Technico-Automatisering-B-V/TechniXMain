<?php

/**
 * Clients
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
$pi["title"] = $lang["locations"];
$pi["template"] = "layout/pages/clients.tpl";
$pi["filename_list"] = "clients.php";
$pi["filename_details"] = "client_details.php";
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
$table = "clients";
$columns = "name id address_city";

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

$sortlinks["name"] = generate_sortlink("name", $lang["name"], $pi, $urlinfo);
$sortlinks["address_city"] = generate_sortlink("address_city", $lang["city"], $pi, $urlinfo);

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

    $header = $lang["name"]."\t".$lang["city"]."\t";
    $data = "";
    while ($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["name"],
            $row["address_city"]
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
