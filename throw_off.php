<?php

/**
 * Throw Off Garments
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2013 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi["access"] = array("linen_service", "throw_off_garments");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["throw_off_garments"];
$pi["filename_list"] = "throw_off.php";
$pi["filename_details"] = "throw_off_details.php";
$pi["template"] = "layout/pages/throw_off.tpl";
$pi["page"] = "list";
$pi["toolbar"]["no_search"] = "yes";

$urlinfo = array();

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}
/**
 * Cancel extra load on request
 */
$id_to_cancel = (!empty($_POST["id_to_cancel"])) ? trim($_POST["id_to_cancel"]) : false;

if (is_numeric($id_to_cancel)) {
    $sql = "DELETE FROM `throw_off_rules` WHERE `id` = ". $id_to_cancel ." LIMIT 1";
    db_query($sql);
}

/**
 * Collect page content
 */
$query = "SELECT
throw_off_rules.id,
throw_off_rules_tmp.rule,
throw_off_rules.active,
throw_off_rules.date_from,
throw_off_rules.date_to,
distributorlocations.`name` AS 'distributor_name',
throw_off_rules_tmp.description AS 'article_description',
throw_off_rules_tmp.size AS 'size',
throw_off_rules_tmp.modification AS 'modification',
throw_off_rules_tmp.tag AS 'tag',
throw_off_rules.max_washcount,
throw_off_rules_tmp.amount AS 'amount',
throw_off_rules_tmp.counter AS 'counter'
FROM
throw_off_rules
INNER JOIN (
    (
        SELECT
        'article' AS 'rule',
        throw_off_rules.id  AS 'id',
        throw_off_rules.article_id AS 'article_id',
        NULL AS 'arsimo_id',
        NULL AS 'garment_id',
        articles.articlenumber AS 'articlenumber',
        articles.description AS 'description',
        NULL AS 'size',
        NULL AS 'modification',
        NULL AS 'tag',
        COALESCE(throw_off_rules.amount,0) AS 'amount',
        COALESCE(throw_off_rules.counter,0) AS 'counter'
        FROM throw_off_rules
        INNER JOIN articles ON  throw_off_rules.article_id = articles.id
        WHERE throw_off_rules.article_id IS NOT NULL AND throw_off_rules.arsimo_id IS NULL AND throw_off_rules.garment_id IS NULL
    )
    UNION
    (
        SELECT
        'arsimo' AS 'rule',
        throw_off_rules.id  AS 'id',
        NULL AS 'article_id',
        throw_off_rules.arsimo_id AS 'arsimo_id',
        NULL AS 'garment_id',
        articles.articlenumber AS 'articlenumber',
        articles.description AS 'description',
        sizes.`name` AS 'size',
        modifications.`name` AS 'modification',
        NULL AS 'tag',
        COALESCE(throw_off_rules.amount,0) AS 'amount',
        COALESCE(throw_off_rules.counter,0) AS 'counter'
        FROM throw_off_rules
        INNER JOIN arsimos ON throw_off_rules.arsimo_id = arsimos.id
        INNER JOIN articles ON arsimos.article_id = articles.id
        INNER JOIN sizes ON arsimos.size_id = sizes.id
        LEFT JOIN modifications ON arsimos.modification_id = modifications.id
        WHERE throw_off_rules.arsimo_id IS NOT NULL AND throw_off_rules.garment_id IS NULL
    )
    UNION
    (
        SELECT
        'garment' AS 'rule',
        throw_off_rules.id  AS 'id',
        NULL AS 'article_id',
        NULL AS 'arsimo_id',
        throw_off_rules.garment_id AS 'garment_id',
        articles.articlenumber AS 'articlenumber',
        articles.description AS 'description',
        sizes.`name` AS 'size',
        modifications.`name` AS 'modification',
        garments.tag AS 'tag',
        COALESCE(throw_off_rules.amount,0) AS 'amount',
        COALESCE(throw_off_rules.counter,0) AS 'counter'
        FROM throw_off_rules
        INNER JOIN garments ON throw_off_rules.garment_id = garments.id
        INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
        INNER JOIN articles ON arsimos.article_id = articles.id
        INNER JOIN sizes ON arsimos.size_id = sizes.id
        LEFT JOIN modifications ON arsimos.modification_id = modifications.id
        WHERE throw_off_rules.garment_id IS NOT NULL
    )
) `throw_off_rules_tmp` ON throw_off_rules.id = throw_off_rules_tmp.id
INNER JOIN distributorlocations ON throw_off_rules.distributorlocation_id = distributorlocations.id
ORDER BY distributorlocations.`name`, throw_off_rules.date_from ASC
";

$detailsdata = db_query($query) or die ("ERROR LINE ". __LINE__);
$detailsdata_count = db_num_rows($detailsdata);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "detailsdata" => $detailsdata,
    "detailsdata_count" => $detailsdata_count
);

template_parse($pi, $urlinfo, $cv);

?>
