<?php

/**
 * Report misseized garments
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
$pi["access"] = array("reports", "misseized_garments");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["misseized_garments"];
$pi["template"] = "layout/pages/report_misseized_garments.tpl";
$pi["page"] = "simple";
$pi["filename_this"] = "report_misseized_garments.php";
$pi["filename_list"] = "report_misseized_garments.php";
$pi["toolbar"]["no_new"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$urlinfo = array();

$ai_sql = "SELECT
        articles.articlenumber AS 'number',
        articles.description AS 'description',
        sizes.`name` AS 'size',
        modifications.`name` AS 'modification'
    FROM
        arsimos
    INNER JOIN articles ON arsimos.article_id = articles.id
    INNER JOIN sizes ON arsimos.size_id = sizes.id
    LEFT JOIN modifications ON arsimos.modification_id = modifications.id
    WHERE arsimos.id = " . $_POST['arsimo_id'] . " LIMIT 1";

$article_info = db_query($ai_sql);

$sql = "SELECT
        log_distributorclients.`date` AS 'date',
        log_distributorclients.`alt_loaded` AS 'alt_loaded',
        garmentusers.`id` AS 'id',
        garmentusers.`name` AS 'name',
        garmentusers.`title` AS 'title',
        garmentusers.`initials` AS 'initials',
        garmentusers.`gender` AS 'gender',
        garmentusers.`intermediate` AS 'intermediate',
        garmentusers.`maidenname` AS 'maidenname',
        garmentusers.`personnelcode` AS 'personnelcode',
        garmentusers.`surname` AS 'surname',
        distributorlocations.`name` AS 'distributorlocation_name'
    FROM
        log_distributorclients
    INNER JOIN arsimos ON log_distributorclients.arsimo_id = arsimos.id
    INNER JOIN garmentusers ON log_distributorclients.garmentuser_id = garmentusers.id
    INNER JOIN distributorlocations ON log_distributorclients.distributorlocation_id = distributorlocations.id
    WHERE arsimos.id = " . $_POST['arsimo_id'] . "
      AND `log_distributorclients`.`numgarments` = '0'
      AND `log_distributorclients`.`date` LIKE '". $_POST['date'] ."%'
    ORDER BY
        log_distributorclients.`date` DESC
";

$listdata = db_query($sql);

/**
 * Generate the page
 */
$cv = array(
    "article_info" => $article_info,
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata
);

template_parse($pi, $urlinfo, $cv);

?>
