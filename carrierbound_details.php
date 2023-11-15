<?php

/**
 * Carrierbound Details
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
$pi["filename_list"] = "carrierbound.php";
$pi["filename_details"] = "carrierbound_details.php";
$pi["template"] = "layout/pages/carrierbound_details.tpl";
$pi["page"] = "details";
$pi["toolbar"]["no_new"] = "yes";
$pi["toolbar"]["no_delete"] = "yes";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Collect page content
 */
$ui = "";

$ts = db_query("SELECT `name` FROM `distributorlocations` WHERE `id` = ". $_POST["id"] ." LIMIT 1");
$td = db_fetch_assoc($ts);
$te = $td["name"];

$ld = db_query("SELECT `distributors`.`id`,
                          `cg`.`name`,
                          `distributors`.`doornumber` AS 'doornumber',
                          `distributors`.`hooks` AS 'hooks',
                          `distributors`.`hooks` - COALESCE(`tmp`.`max_positions`,0) AS 'hooks_sizebound',
                          `tmp`.`max_positions` AS 'hooks_userbound'
                     FROM `distributors`
               INNER JOIN `distributorlocations` `dl` ON `distributors`.`distributorlocation_id` = `dl`.`id`
               INNER JOIN `circulationgroups` `cg` ON `dl`.`circulationgroup_id` = `cg`.`id`
                LEFT JOIN (
                           SELECT `garmentusers`.`distributor_id`,
                                    `garmentusers`.`distributor_id2`,
                                    `garmentusers`.`distributor_id3`,
                                    `garmentusers`.`distributor_id4`,
                                    `garmentusers`.`distributor_id5`,
                                    `garmentusers`.`distributor_id6`,
                                    `garmentusers`.`distributor_id7`,
                                    `garmentusers`.`distributor_id8`,
                                    `garmentusers`.`distributor_id9`,
                                    `garmentusers`.`distributor_id10`,
                                  SUM(`gua`.`max_positions`) AS 'max_positions'
                             FROM `garmentusers_userbound_arsimos` gua
		       INNER JOIN `distributorlocations` dl ON dl.id = ". $_POST["id"] ." AND dl.circulationgroup_id = gua.circulationgroup_id
                       INNER JOIN `garmentusers` ON `gua`.`garmentuser_id` = `garmentusers`.`id`
			    WHERE `gua`.`enabled` = 1
                         GROUP BY `garmentusers`.`distributor_id`,
                                    `garmentusers`.`distributor_id2`,
                                    `garmentusers`.`distributor_id3`,
                                    `garmentusers`.`distributor_id4`,
                                    `garmentusers`.`distributor_id5`,
                                    `garmentusers`.`distributor_id6`,
                                    `garmentusers`.`distributor_id7`,
                                    `garmentusers`.`distributor_id8`,
                                    `garmentusers`.`distributor_id9`,
                                    `garmentusers`.`distributor_id10`
                          ) `tmp` ON `tmp`.`distributor_id` = `distributors`.`id`
                                    OR `tmp`.`distributor_id2` = `distributors`.`id`
                                    OR `tmp`.`distributor_id3` = `distributors`.`id`
                                    OR `tmp`.`distributor_id4` = `distributors`.`id`
                                    OR `tmp`.`distributor_id5` = `distributors`.`id`
                                    OR `tmp`.`distributor_id6` = `distributors`.`id`
                                    OR `tmp`.`distributor_id7` = `distributors`.`id`
                                    OR `tmp`.`distributor_id8` = `distributors`.`id`
                                    OR `tmp`.`distributor_id9` = `distributors`.`id`
                                    OR `tmp`.`distributor_id10` = `distributors`.`id`
                   WHERE `distributorlocation_id` = ". $_POST["id"] ."
                ORDER BY `doornumber` ASC");

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $ui,
    "title" => $te,
    "listdata" => $ld
);

template_parse($pi, $ui, $cv);

?>
