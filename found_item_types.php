<?php

/**
 * Found items
 *
 * @author    G. I. Voros <gaborvoros@technico.nl>
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
$pi = array();
$pi["access"] = array("master_data", "found_item_types");
$pi["filename_list"] = "found_item_types.php";
$pi["filename_details"] = "found_item_type_details.php";
$pi["group"] = $lang["master_data"];
$pi["page"] = "list";
$pi["title"] = $lang["found_item_type"];
$pi["template"] = "layout/pages/found_item_types.tpl";
$pi["toolbar"]["export"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$table = "found_item_types";
$columns = "found_item_types.value found_item_types.id";

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

if (isset($_POST["export"]) && $_POST["export"] == "yes") {
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["value"] = generate_sortlink("found_item_types.value", $lang["found_item_type"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
 * Export
 */
if (isset($_POST["export"]) && $_POST["export"] == "yes") {
    $export_filename = "export_functions_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $data = $lang["found_item"]."\t\n";
    while($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["found_item_types_value"]
        );

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
    $data_r = str_replace("\r","",$data);

    print "$data_r";
    die();
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "resultinfo" => $resultinfo,
    "sortlinks" => $sortlinks,
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
