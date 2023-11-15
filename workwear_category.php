<?php

/**
 * Articles
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2009 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Require necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi = array();
$pi["access"] = array("workwearmanagement", "categories");
$pi["group"] = $lang["master_data"];
$pi["title"] = $lang["categories"];
$pi["filename_list"] = "workwear_category.php";
$pi["filename_details"] = "workwear_category_details.php";
$pi["template"] = "layout/pages/workwear_category.tpl";
$pi["toolbar"]["export"] = "yes";
$pi["page"] = "list";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$table = "workwear_categories";
$columns = "id name";

$ui = array();
$ui["search"] = geturl_search();
$ui["order_by"] = geturl_order_by($columns);
$ui["order_direction"] = geturl_order_direction();

if (isset($_POST["export"]) && $_POST["export"] == "yes") {
    $ui["limit_start"] = 0;
    $ui["limit_num"] = "65535";
} else {
    $ui["limit_start"] = geturl_limit_start();
    $ui["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

$ui["limit_total"] = db_fetch_row(db_count($table, $columns, $ui));
$ui["limit_total"] = $ui["limit_total"][0];

$listdata = db_read($table, $columns, $ui);

$ri = result_infoline($pi, $ui);

$sl = array();
$sl["name"] = generate_sortlink("name", $lang["name"], $pi, $ui);

$pagination = generate_pagination($pi, $ui);

/**
 * Export
 */
if (isset($_POST["export"]) && $_POST["export"] == "yes") {
    $exname = "export_articles_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $exname);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["name"]."\t".$lang["name"]."\t";
    $data = "";
    while($row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["name"]
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

    print "$header\n$data_r";
    die();
}

/**
 * Generate the page
 */

$cv = array(
    "pi" => $pi,
    "urlinfo" => $ui,
    "sortlinks" => $sl,
    "resultinfo" => $ri,
    "listdata" => $listdata,
    "pagination" => $pagination
);

template_parse($pi, $ui, $cv);