<?php

/**
 * Report packinglists history print
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
$pi["access"] = array("lists", "packinglists");
$pi["title"] = $lang["lists"] . ": " . $lang["packinglists"];
$pi["filename_list"] = "report_packinglists.php";
$pi["filename_details"] = "report_packinglists.php";
$pi["template"] = "layout/pages/report_packinglists_history_print.tpl";
$pi["page"] = "report";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
if (is_numeric($_GET["p"])) {
    $packinglist_sql = "SELECT
        `articles`.`articlenumber` AS 'articlecode',
        `articles`.`description` AS 'description',
        IF(ISNULL(`arsimos`.`modification_id`), `sizes`.`name`, CONCAT(`sizes`.`name`, ' ', `modifications`.`name`)) AS 'size',
        IF(ISNULL(`garments`.`garmentuser_id`), '" . $lang["size"] . "', '" . $lang["garmentuser"] . "') AS 'userbound',
        COUNT(`garments`.`id`) AS 'count'

        FROM
        `packinglists`
        INNER JOIN `packinglists_depositbatches` ON `packinglists`.`id` = `packinglists_depositbatches`.`packinglist_id`
        INNER JOIN `depositbatches_garments` ON `packinglists_depositbatches`.`depositbatch_id` = `depositbatches_garments`.`depositbatch_id`
        INNER JOIN `garments` ON `depositbatches_garments`.`garment_id` = `garments`.`id`
        INNER JOIN `arsimos` ON `garments`.`arsimo_id` = `arsimos`.`id`
        INNER JOIN `articles` ON `arsimos`.`article_id` = `articles`.`id`
        INNER JOIN `sizes` ON `arsimos`.`size_id` = `sizes`.`id`
        INNER JOIN `sizegroups` ON `sizes`.`sizegroup_id` = `sizegroups`.`id`
        LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`

        WHERE `packinglists`.`id` = " . $_GET["p"] . "

        GROUP BY
        `arsimos`.`id`,
        ISNULL(`garments`.`garmentuser_id`)
        ORDER BY
        `articles`.`description`,
        `sizes`.`position`,
        `modifications`.`name`
    ";

    $listdata = db_query($packinglist_sql);
} else {
    $listdata = null;
}

// Required for header: total
if ($listdata) {
    $header["total"] = 0;
    while ($row = db_fetch_assoc($listdata)) {
        $header["total"] += $row["count"];
    }
    db_data_seek($listdata, 0);
} else {
    $header["total"] = null;
}

/**
 * Generate the page
 */
include($pi["template"]);

?>
