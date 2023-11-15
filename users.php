<?php

/**
 * Users
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
$pi["group"] = "Technico";
$pi["title"] = $lang["users"];
$pi["filename_list"] = "users.php";
$pi["filename_details"] = "user_details.php";
$pi["template"] = "layout/pages/users.tpl";
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
$table = "users";
$columns = "users.username users.id users.locale_id";
$columns .= " locales.name";

$urlinfo["join"]["1"] = "locales users.locale_id locales.id";

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();
$urlinfo["limit_start"] = geturl_limit_start();
$urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);

$urlinfo["where"]["1"] = "users.id not 1";

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0];


$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["username"] = generate_sortlink("users.username", $lang["username"], $pi, $urlinfo);
$sortlinks["locale"] = generate_sortlink("locales.name", $lang["locale"], $pi, $urlinfo);

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
