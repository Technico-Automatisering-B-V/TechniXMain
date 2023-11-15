<?php

/**
 * Disconnect garmentusers
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
$pi["access"] = array("manager", "disconnect_garmentusers");
$pi["group"] = $lang["manager"];
$pi["title"] = $lang["disconnect_garmentusers"];
$pi["template"] = "layout/pages/disconnect_garmentusers.tpl";
$pi["page"] = "simple";
$pi["filename_list"] = "disconnect_garmentusers.php";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

$did = (!empty($_POST["id"])) ? $_POST["id"] : null;

if (!empty($did)) {
    $sql = "UPDATE
                `garmentusers_garments` `gg`
            INNER JOIN
                `distributors` `d` ON `d`.`id` = `gg`.`distributor_id`
            INNER JOIN
                `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
            INNER JOIN
                `garments` `g` ON `g`.`id` = `gg`.`garment_id`
            SET
                `g`.`scanlocation_id` = (SELECT `s`.`id` FROM `scanlocations` `s` WHERE `s`.`name` = 'Ontkoppeld van drager'),
                `g`.`updated_on` = NOW()
            WHERE
                `dl`.`id` = ". $did;
    db_query($sql);

    $sql = "DELETE
                `gg`.*
            FROM
                `garmentusers_garments` `gg`
            INNER JOIN
                `distributors` `d` ON `d`.`id` = `gg`.`distributor_id`
            INNER JOIN
                `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
            WHERE
                `dl`.`id` = ". $did;
    db_query($sql);
}

/**
 * Collect page content
 */
$table = "tmp_garmentusers_garments";

/**
 * Create view
 */
$query = "CREATE VIEW `". $table ."` AS (SELECT
            `dl`.`id` AS 'distributorlocation_id',
            `c`.`name` AS 'circulationgroup_name',
            `dl`.`name` AS 'distributorlocation_name',
            COUNT(`gg`.`garment_id`) AS 'count'
        FROM
            `distributors` `d`
        INNER JOIN
            `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
        INNER JOIN
            `circulationgroups` `c` ON `c`.`id` = `dl`.`circulationgroup_id`
        LEFT JOIN
            `garmentusers_garments` `gg` ON `d`.`id` = `gg`.`distributor_id`
        GROUP BY 
            `dl`.`id`)";

db_query("DROP VIEW IF EXISTS `". $table ."`");
db_query($query);

$columns = "circulationgroup_name distributorlocation_name distributorlocation_id count";

$ui["search"] = geturl_search();
$ui["order_by"] = geturl_order_by($columns);
$ui["order_direction"] = geturl_order_direction();
$ui["limit_start"] = geturl_limit_start();
$ui["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);

$ui["limit_total"] = db_fetch_row(db_count($table, $columns, $ui));
$ui["limit_total"] = $ui["limit_total"][0];

$ld = db_read($table, $columns, $ui);

$ri = result_infoline($pi, $ui);

$sl["circulationgroup_name"] = generate_sortlink("circulationgroup_name", $lang["circulationgroup"], $pi, $ui);
$sl["distributorlocation_name"] = generate_sortlink("distributorlocation_name", $lang["location"], $pi, $ui);
$sl["count"] = generate_sortlink("count", $lang["distributed"], $pi, $ui);

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
