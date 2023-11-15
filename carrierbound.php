<?php

/**
 * Carrierbound
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
$pi["group"] = $lang["technico"];
$pi["title"] = $lang["userbound"];
$pi["template"] = "layout/pages/carrierbound.tpl";
$pi["filename_list"] = "carrierbound.php";
$pi["filename_details"] = "carrierbound_details.php";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Collect page content
 */
$table = "distributorlocations";
$columns = "id name washcount_check_enabled washcount_check_from washcount_check_to";

$ui = array();
$ui["limit_total"] = db_fetch_row(db_count($table, $columns, $ui));
$ui["limit_total"] = $ui["limit_total"][0]; //array->string

$ld = db_query("
        SELECT `dl`.`id`,
               `dl`.`name`,
               (
                    SELECT SUM(`ga`.`max_positions`)
                      FROM `garmentusers_userbound_arsimos` `ga`
                INNER JOIN `garmentusers` `gu` ON `ga`.`garmentuser_id` = `gu`.`id`
                INNER JOIN `circulationgroups_garmentusers` `cg` ON `cg`.`garmentuser_id` = `gu`.`id`
                INNER JOIN `distributorlocations` `dla` ON `dla`.`circulationgroup_id` = `cg`.`circulationgroup_id`
                     WHERE `ga`.`enabled` = 1
                       AND `dla`.`id` = `dl`.`id`
               ) AS 'has_userbound'
          FROM `distributorlocations` `dl`
    INNER JOIN `distributors` `d` ON `d`.`distributorlocation_id` = `dl`.`id`
      GROUP BY `dl`.`id`");

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $ui,
    "listdata" => $ld
);

template_parse($pi, $ui, $cv);

?>
