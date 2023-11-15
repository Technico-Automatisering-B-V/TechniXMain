<?php

/**
 * Distributors
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
$pi["group"] = $lang["technico"];
$pi["title"] = $lang["distributors"];
$pi["template"] = "layout/pages/distributors.tpl";
$pi["filename_list"] = "distributors.php";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

$did = (!empty($_POST["id"])) ? $_POST["id"] : null;

if (!empty($did)) {
    $sql = "UPDATE `garments` `g`
        INNER JOIN `distributors_load` `dl` ON `g`.`id` = `dl`.`garment_id`
               SET `g`.`scanlocation_id` = (SELECT `s`.`id` FROM `scanlocations` `s` WHERE `s`.`name` = 'Vermist')
             WHERE `dl`.`distributor_id` = ". $did;
    db_query($sql);

    $sql = "DELETE FROM `distributors_load` WHERE `distributor_id` = ". $did;
    db_query($sql);
}

/**
 * Collect page content
 */
$table = "distributors";
$columns = "distributors.doornumber distributorlocations.name distributors.id distributors.distributorlocation_id distributors.hooks";
$columns .= " distributors.distributorname_id distributors.comments";

$ui["join"]["1"] = "distributorlocations distributors.distributorlocation_id distributorlocations.id";
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
$sl["distributors_distributorname_id"] = generate_sortlink("distributors.distributorname_id", $lang["distributor"], $pi, $ui);
$sl["distributors_doornumber"] = generate_sortlink("distributors.doornumber", $lang["door_number"], $pi, $ui);
$sl["distributors_hooks"] = generate_sortlink("distributors.hooks", $lang["number_of_hooks"], $pi, $ui);

$pagination = generate_pagination($pi, $ui);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $ui,
    "sortlinks" => $sl,
    "resultinfo" => $ri,
    "listdata" => $ld,
    "pagination" => $pagination
);

template_parse($pi, $ui, $cv);

?>
