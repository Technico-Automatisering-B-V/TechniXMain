<?php

/**
 * Clear depositlocation
 *
 * @author    G. I. Voros <gabor@technico.nl>
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
$pi["access"] = array("linen_service", "clear_depositlocation");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["clear_depositlocation"];
$pi["template"] = "layout/pages/clear_depositlocation.tpl";
$pi["page"] = "simple";
$pi["filename_list"] = "clear_depositlocation.php";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

$did = (!empty($_POST["id"])) ? $_POST["id"] : null;

if (!empty($did)) { 
    $sql = "INSERT INTO `depositbatches` (`depositlocation_id`, `date`, `final`) VALUES ($did, NOW(), 0)";
    db_query($sql);

    $insertedKeyValue = db_fetch_row(db_read_last_insert_id());
    $insertedKeyValue = $insertedKeyValue[0];
  
    $sql_dbg = "
        INSERT INTO `depositbatches_garments`
            SELECT $insertedKeyValue, `garments`.`id`
              FROM `garments`
        INNER JOIN `depositlocations` ON `depositlocations`.`scanlocation_id` = `garments`.`scanlocation_id`
             WHERE `depositlocations`.`id` = $did";
    db_query($sql_dbg);

    $sql_ug = "
        UPDATE `garments`
    INNER JOIN `depositlocations` ON `depositlocations`.`scanlocation_id` = `garments`.`scanlocation_id`
           SET `garments`.`scanlocation_id` = `depositlocations`.`scanlocation_id_transport`,
               `garments`.`lastscan` = NOW()
         WHERE `depositlocations`.`id` = $did";
    db_query($sql_ug);
}

/**
 * Collect page content
 */
$table = "depositlocations";
$columns = "depositlocations.name distributorlocations.name depositlocations.id depositlocations.distributorlocation_id depositlocations.scanlocation_id";

$ui["join"]["1"] = "distributorlocations depositlocations.distributorlocation_id distributorlocations.id";
$ui["search"] = geturl_search();
$ui["order_by"] = geturl_order_by($columns);
$ui["order_direction"] = geturl_order_direction();
$ui["limit_start"] = geturl_limit_start();
$ui["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);

$ui["limit_total"] = db_fetch_row(db_count($table, $columns, $ui));
$ui["limit_total"] = $ui["limit_total"][0];

$ld = db_read($table, $columns, $ui);

$ri = result_infoline($pi, $ui);

$sl["distributorlocations_name"] = generate_sortlink("distributorlocations.name", $lang["location"], $pi, $ui);
$sl["depositlocations_name"] = generate_sortlink("depositlocations.name", $lang["depositlocation"], $pi, $ui);

$pagination = generate_pagination($pi, $ui);

$deposit_garments_query = "SELECT `s`.`id` AS 'id', COUNT(*) AS 'count'
                             FROM `garments` `g`
                       INNER JOIN `scanlocations` `s` ON `s`.`id` = `g`.`scanlocation_id`
                       INNER JOIN `scanlocationstatuses` `ss` ON `ss`.`id` = `s`.`scanlocationstatus_id`
                            WHERE `ss`.`name` = 'deposited' AND `g`.`deleted_on` IS NULL
                         GROUP BY `s`.`id`";
$deposit_garments_sql = db_query($deposit_garments_query) or die("ERROR LINE ". __LINE__);


if (!empty($deposit_garments_sql)) {
    while ($deposit_garments_result = db_fetch_row($deposit_garments_sql)) {
        $deposit_garments[$deposit_garments_result[0]] = $deposit_garments_result[1];
    }
} else {
    $deposit_garments = null;
} 

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $ui,
    "sortlinks" => $sl,
    "resultinfo" => $ri,
    "listdata" => $ld,
    "pagination" => $pagination,
    "deposit_garments" => $deposit_garments
);

template_parse($pi, $ui, $cv);

?>
