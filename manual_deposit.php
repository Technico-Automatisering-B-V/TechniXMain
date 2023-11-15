<?php

/**
 * Manual deposit
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2009 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi["access"] = array("manually", "deposit");
$pi["group"] = $lang["manually"];
$pi["title"] = $lang["deposit"];
$pi["template"] = "layout/pages/manual_deposit.tpl";
$pi["page"] = "simple";
$pi["filename_this"] = "manual_deposit.php";
$pi["filename_list"] = "manual_deposit.php";

/**
 * Authorization
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

// Cancel button
if (isset($_POST["cancel"])){
    redirect($pi["filename_list"]);
}

/**
 * Used variables
 */
$error = null;
$garment_details = array();
$urlinfo = "";

/**
 * Values
 */
$garment_id_to_cancel = (!empty($_POST["garment_id_to_cancel"])) ? trim($_POST["garment_id_to_cancel"]) : false;
$garment_tag = (!empty($_POST["searchScanPas"])) ? convertTag(trim($_POST["searchScanPas"])) : "";
$scanned_garments = (!empty($_POST["scanned_garments"])) ? $_POST["scanned_garments"] : array();

/**
 * Cancel garment
 */
if (is_numeric($garment_id_to_cancel)) {
    $sql = db_query("SELECT `scanlocations`.`id` FROM `scanlocationstatuses` INNER JOIN `scanlocations` ON `scanlocations`.`scanlocationstatus_id` = `scanlocationstatuses`.`id` AND `scanlocationstatuses`.`name` = 'missing'");
    $scanresult = db_fetch_row($sql);
    $scanlocation_id = $scanresult[0];

    if (db_verify_existence("garmentusers_garments", "garment_id", $garment_id_to_cancel)) {
        db_delete_where("garmentusers_garments", "garment_id", $garment_id_to_cancel);
        $g_update = array("scanlocation_id" => $scanlocation_id, "active" => 0, "lastscan" => "NOW()" );
        db_update("garments", $garment_id_to_cancel, $g_update);
    }

    db_free_result($sql);
}

/**
 * Main
 */

if (!empty($garment_tag)) {
    $garment_id = tag_to_garment_id($garment_tag);
    if ($garment_id) {
        if (!in_array($garment_tag, $scanned_garments)) {
            array_push($scanned_garments, $garment_tag);
        }
    } else {
        $error = $lang["garment_not_found"];
    }
}

// Haalt alle waarde van scanned_garments op
foreach ($scanned_garments as $garment_tag) {
    $garment_details_sql = db_query("
    SELECT `articles`.`description` AS 'article',
           `sizes`.`name` AS 'size',
           `modifications`.`name` AS 'modifications',
           `garmentusers`.`id` AS 'garmentuser_id',
           `garmentusers`.`title` AS 'title',
           `garmentusers`.`gender` AS 'gender',
           `garmentusers`.`initials` AS 'initials',
           `garmentusers`.`intermediate` AS 'intermediate',
           `garmentusers`.`surname` AS 'surname',
           `garmentusers`.`maidenname` AS 'maidenname',
           `garmentusers`.`personnelcode` AS 'personnelcode',
           `garmentusers_garments`.`date_received` AS 'date_received'
      FROM `garments`
INNER JOIN `arsimos` ON garments.arsimo_id = arsimos.id
INNER JOIN `articles` ON articles.id = arsimos.article_id
INNER JOIN `sizes` ON sizes.id = arsimos.size_id
 LEFT JOIN `modifications` ON modifications.id = arsimos.modification_id
 LEFT JOIN `garmentusers_garments` ON garmentusers_garments.garment_id = garments.id
 LEFT JOIN `garmentusers` ON garmentusers.id = garmentusers_garments.garmentuser_id
     WHERE `garments`.`tag` = '". $garment_tag ."'
     LIMIT 1");

    $row = db_fetch_assoc($garment_details_sql);
    $garment_details[$garment_tag] = $row;
    db_free_result($garment_details_sql);
}

/**
 * Insert in database
 */
if (!empty($scanned_garments) && isset($_POST["saveclose"])) {
    $ip_addr  = $_SERVER["REMOTE_ADDR"];
    $hostname = gethostbyaddr($ip_addr);

    $dlocation_sql  = db_query("SELECT `id` FROM `depositlocations` WHERE `id` = 1 LIMIT 1");
    $dlocation_data = db_fetch_assoc($dlocation_sql);
    $depositlocation_id = $dlocation_data["id"];

    foreach ($scanned_garments as $tag) {
        db_query("UPDATE garments, depositlocations SET garments.scanlocation_id = depositlocations.scanlocation_id, garments.lastscan = NOW(), garments.clean = 0, garments.active = 1 WHERE garments.tag LIKE '". $tag ."' AND garments.deleted_on IS NULL AND depositlocations.id = ". $depositlocation_id);
        db_query("DELETE garmentusers_garments.* FROM garmentusers_garments INNER JOIN garments ON garmentusers_garments.garment_id = garments.id WHERE garments.tag LIKE '". $tag ."' AND garments.deleted_on IS NULL");
        db_query("DELETE distributors_load.* FROM distributors_load INNER JOIN garments ON distributors_load.garment_id = garments.id WHERE garments.tag LIKE '". $tag ."' AND garments.deleted_on IS NULL");
        db_query("INSERT INTO log_depositlocations_garments SELECT YEAR(NOW()), DAYOFYEAR(NOW()), depositlocations.id, garments.id, NOW() FROM garments, depositlocations WHERE garments.tag LIKE '". $tag ."' AND garments.deleted_on IS NULL AND depositlocations.id = ". $depositlocation_id ." ON DUPLICATE KEY UPDATE log_depositlocations_garments.`date` = NOW()");
    }

    redirect($pi["filename_list"]);
}

/**
 * Generate the page
 */
$cv = array(
    "garment_details"  => $garment_details,
    "error"            => $error,
    "pageinfo"         => $pi,
    "scanned_garments" => $scanned_garments
);

template_parse($pi, $urlinfo, $cv);

?>
