<?php

/**
 * Despeckles
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
$pi["access"] = array("linen_service", "despecklesandrepairs");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["despeckles"];
$pi["subtitle"] = $lang["despeckle_methods"];
$pi["filename_list"] = "despeckles.php";
$pi["filename_details"] = "despeckle_details.php";
$pi["template"] = "layout/pages/despeckles.tpl";
$pi["page"] = "list";
$pi["toolbar_extra"] = "<form name=\"garmentdespecklesandrepairs\" enctype=\"multipart/form-data\" method=\"GET\"><input type=\"submit\" name=\"goto_garmentdespecklesandrepairs\" value=\"". $lang["back_to_despeckles"] . "\" title=\"" . $lang["back_to_despeckles"] . "\" onclick=\"this.form.action='garmentdespecklesandrepairs.php'; this.form.target='_self';\"></form>";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */

$table = "despeckles";
$columns = "description id";

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();
$urlinfo["limit_start"] = geturl_limit_start();
$urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["description"] = generate_sortlink("description", $lang["description"], $pi, $urlinfo);

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
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
