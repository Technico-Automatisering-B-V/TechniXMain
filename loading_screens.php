<?php

/**
 * Loading screens
 *
 * PHP version 5
 *
 * @author    Gabor Voros <gabor@technico.nl>
 * @copyright 2006- Technico Automatisering B.V.
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
$pi["access"] = array("master_data", "loading_screens");
$pi["group"] = $lang["master_data"];
$pi["title"] = $lang["loading_screens"];
$pi["filename_list"] = "loading_screens.php";
$pi["filename_details"] = "loading_screen_details.php";
$pi["template"] = "layout/pages/loading_screens.tpl";
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
$table    = "loading_screens";
$columns  = "loading_screens.id loading_screens.sort ";
$columns .= "circulationgroups.id circulationgroups.name";

$urlinfo["left_join"]["5"]  = "circulationgroups loading_screens.circulationgroup_id circulationgroups.id";
$urlinfo["search"]          = geturl_search();
$urlinfo["order_by"]        = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();
$urlinfo["limit_start"]     = geturl_limit_start();
$urlinfo["limit_num"]       = geturl_limit_num($config["list_rows_per_page"]);
$urlinfo["limit_total"]     = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"]     = $urlinfo["limit_total"][0];

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["circulationgroup"] = generate_sortlink("circulationgroups.name", $lang["circulationgroup"], $pi, $urlinfo);
$sortlinks["sort"] = generate_sortlink("loading_screens.sort", $lang["sort"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

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
