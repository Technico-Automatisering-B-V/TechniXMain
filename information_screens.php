<?php

/**
 * Information screens
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
$pi["access"] = array("master_data", "information_screens");
$pi["group"] = $lang["master_data"];
$pi["title"] = $lang["information_screens"];
$pi["filename_list"] = "information_screens.php";
$pi["filename_details"] = "information_screen_details.php";
$pi["template"] = "layout/pages/information_screens.tpl";
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
$table    = "information_screens";
$columns  = "information_screens.id information_screens.message information_screens.color information_screens.size information_screens.speed information_screens.sort ";
$columns .= "circulationgroups.id circulationgroups.name";

$urlinfo["left_join"]["5"]  = "circulationgroups information_screens.circulationgroup_id circulationgroups.id";
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
$sortlinks["message"] = generate_sortlink("information_screens.message", $lang["message_text"], $pi, $urlinfo);
$sortlinks["color"] = generate_sortlink("information_screens.color", $lang["message_color"], $pi, $urlinfo);
$sortlinks["size"] = generate_sortlink("information_screens.size", $lang["message_size"], $pi, $urlinfo);
$sortlinks["speed"] = generate_sortlink("information_screens.speed", $lang["message_speed"], $pi, $urlinfo);
$sortlinks["sort"] = generate_sortlink("information_screens.sort", $lang["stations_sort"], $pi, $urlinfo);

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
