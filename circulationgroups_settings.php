<?php

/**
 * Distributorlocations
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
$pi["access"] = array("manager", "settings");
$pi["group"] = $lang["manager"];
$pi["title"] = $lang["settings"];
$pi["filename_list"] = "circulationgroups_settings.php";
$pi["filename_details"] = "circulationgroup_settings_details.php";
$pi["template"] = "layout/pages/circulationgroups_settings.tpl";
$pi["page"] = "list";
$pi["toolbar"]["no_new"] = "yes";
$pi["toolbar"]["no_delete"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$table = "circulationgroups";
$columns = "name id fifo_distribution optimal_load credit_free_distribution";

$ui["search"] = geturl_search();
$ui["order_by"] = geturl_order_by($columns);
$ui["order_direction"] = geturl_order_direction();
$ui["limit_start"] = geturl_limit_start();
$ui["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);

$ui["limit_total"] = db_fetch_row(db_count($table, $columns, $ui));
$ui["limit_total"] = $ui["limit_total"][0];

$ld = db_read($table, $columns, $ui);
$ri = result_infoline($pi, $ui);

$sl["location"] = generate_sortlink("name", $lang["location"], $pi, $ui);
$sl["fifo_distribution"] = generate_sortlink("fifo_distribution", $lang["fifo_distribution"], $pi, $ui);
$sl["loaded"] = generate_sortlink("optimal_load", $lang["loaded"], $pi, $ui);
$sl["credit_free_distribution"] = generate_sortlink("credit_free_distribution", $lang["credit_free_distribution"], $pi, $ui);

$pn = generate_pagination($pi, $ui);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $ui,
    "sortlinks" => $sl,
    "resultinfo" => $ri,
    "listdata" => $ld,
    "pagination" => $pn
);

template_parse($pi, $ui, $cv);

?>
