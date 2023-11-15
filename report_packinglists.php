<?php

/**
 * Report packinglists history
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
$pi["access"] = array("lists", "packinglists");
$pi["group"] = $lang["lists"];
$pi["title"] = $lang["packinglists"];
$pi["filename_list"] = "report_packinglists.php";
$pi["filename_details"] = "report_packinglists_history_print.php";
$pi["template"] = "layout/pages/report_packinglists.tpl";
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
if (!empty($_GET["cid"])) {
    $urlinfo["cid"] = $_GET["cid"];
} else {
    //we use the circulationgroup_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_circulationgroup_conditions["order_by"] = "name";
    $selected_circulationgroup_conditions["limit_start"] = 0;
    $selected_circulationgroup_conditions["limit_num"] = 1;
    $urlinfo["cid"] = db_fetch_row(db_read("circulationgroups", "id", $selected_circulationgroup_conditions));
    $urlinfo["cid"] = $urlinfo["cid"][0];
}

// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

$table = "packinglists";

$columns = "date id";

$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction("DESC");

$urlinfo["where"]["1"] = "circulationgroup_id = " . $urlinfo["cid"];

$urlinfo["limit_start"] = geturl_limit_start();
$urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["date"] = generate_sortlink("date", $lang["date"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "resultinfo" => $resultinfo,
    "sortlinks" => $sortlinks,
    "listdata" => $listdata,
    "pagination" => $pagination,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups
);

template_parse($pi, $urlinfo, $cv);

?>
