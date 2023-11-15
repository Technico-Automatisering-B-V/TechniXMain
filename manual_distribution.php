<?php

/**
 * Manual distribution
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
$pi["access"] = array("manually", "distribution");
$pi["group"] = $lang["manually"];
$pi["title"] = $lang["distribution"];
$pi["template"] = "layout/pages/manual_distribution.tpl";
$pi["page"] = "simple";
$pi["filename_this"] = "manual_distribution.php";
$pi["filename_list"] = "manual_distribution.php";

/**
 * Authorization
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Used variables
 */
$error = null;
$garment_details = null;
$garmentuser_count = 0;
$garmentuser_id = "";
$garmentuser_garments_inuse = null;
$garmentuser_multiple = 0;
$garmentuser_data = null;
$garmentusers_data = null;
$urlinfo = "";
$articles_all = array();
$userbound_articles = array();
$requiredfields = array();

// Cancel button
if (isset($_POST["cancel"])){
    redirect($pi["filename_list"]);
}

/**
 * Values
 */
$garment_id_to_cancel = (!empty($_POST["garment_id_to_cancel"])) ? trim($_POST["garment_id_to_cancel"]) : false;
$garment_tag = (!empty($_POST["garment_tag"])) ? convertTag(trim($_POST["garment_tag"])) : "";
$remove_from_list = (!empty($_POST["remove_from_list"])) ? trim($_POST["remove_from_list"]) : "";

if (!empty($_POST["garmentuser_id"])) {
    $garmentuser_id = trim($_POST["garmentuser_id"]);
} elseif (!empty($_GET["garmentuser_id"])) {
    $garmentuser_id = trim($_GET["garmentuser_id"]);
}

$scanned_garments = (!empty($_POST["scanned_garments"])) ? $_POST["scanned_garments"] : array();

$searchdata = array(
    "scanpas" => (!empty($_POST["searchScanPas"])) ? trim($_POST["searchScanPas"]) : "",
    "personnelcode" => (!empty($_POST["searchPersonnelcode"])) ? trim($_POST["searchPersonnelcode"]) : "",
    "garmentuser" => (!empty($_POST["searchGarmentuser"])) ? trim($_POST["searchGarmentuser"]) : ""
);

$kredit_warning = "";

/**
 * Cancel garment
 */
if (is_numeric($garment_id_to_cancel)) {
    $sql = db_query("
        SELECT `scanlocations`.`id`
          FROM `scanlocationstatuses`
    INNER JOIN `scanlocations` ON `scanlocations`.`scanlocationstatus_id` = `scanlocationstatuses`.`id`
           AND `scanlocationstatuses`.`name` = 'missing'");
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
 * Remove garment from list
 */
if (!empty($remove_from_list)) {
    foreach ($scanned_garments as $key => $value) {
        if ($value == $remove_from_list) {
            unset($scanned_garments[$key]);
        }
    }

    $scanned_garments = array_values($scanned_garments);
}

/**
 * Get userdata
 */
if (empty($garmentuser_id) && (!empty($searchdata["scanpas"]) || !empty($searchdata["personnelcode"]) || !empty($searchdata["garmentuser"]))) {
    if (!empty($searchdata["scanpas"])) {
        $garmentuser_where = "`garmentusers`.`code` = '". $searchdata["scanpas"] ."'";
    } elseif (!empty($searchdata["personnelcode"])) {
        $garmentuser_where = "`garmentusers`.`personnelcode` = '". $searchdata["personnelcode"] ."'";
    } elseif (!empty($searchdata["garmentuser"])) {
        $garmentuser_where = "`garmentusers`.`surname` LIKE '%". $searchdata["garmentuser"] ."%'";
    }

    $garmentuser_query = "
        SELECT `garmentusers`.`id` AS 'garmentusers_id',
               `garmentusers`.`name` AS 'garmentusers_name',
               `garmentusers`.`title` AS 'garmentusers_title',
               `garmentusers`.`gender` AS 'garmentusers_gender',
               `garmentusers`.`initials` AS 'garmentusers_initials',
               `garmentusers`.`intermediate` AS 'garmentusers_intermediate',
               `garmentusers`.`surname` AS 'garmentusers_surname',
               `garmentusers`.`maidenname` AS 'garmentusers_maidenname',
               `garmentusers`.`personnelcode` AS 'garmentusers_personnelcode',
               `garmentusers`.`code` AS 'garmentusers_code',
               `garmentusers`.`active` AS 'garmentusers_active',
               `garmentusers`.`deleted_on` AS 'garmentusers_deleted_on',
               `professions`.`name` AS 'profession_name',
               (
                    SELECT COUNT(*)
                      FROM `garmentusers_garments`
                     WHERE `garmentusers_garments`.`garmentuser_id` = `garmentusers`.`id`
               ) AS 'garments_in_use'
          FROM `garmentusers`
     LEFT JOIN `professions` ON `garmentusers`.`profession_id` = `professions`.`id`
         WHERE (`garmentusers`.`deleted_on` IS NULL) AND (". $garmentuser_where .")";

    $garmentuser_sql = db_query($garmentuser_query);
    $garmentuser_count = db_num_rows($garmentuser_sql);

    if ($garmentuser_count == 0) {
        $error = $lang["no_items_found"];
    } elseif ($garmentuser_count == 1) {
        $garmentuser_data = db_fetch_assoc($garmentuser_sql);
        $garmentuser_id = $garmentuser_data["garmentusers_id"];
    } else {
        $garmentuser_multiple = 1;
        $garmentusers_data = $garmentuser_sql;
    }
}

/**
 * Main
 */
if (!empty($garmentuser_id)) {
    // Database data
    $garmentuser_query = "
        SELECT `garmentusers`.`id` AS 'garmentusers_id',
               `garmentusers`.`name` AS 'garmentusers_name',
               `garmentusers`.`title` AS 'garmentusers_title',
               `garmentusers`.`gender` AS 'garmentusers_gender',
               `garmentusers`.`initials` AS 'garmentusers_initials',
               `garmentusers`.`intermediate` AS 'garmentusers_intermediate',
               `garmentusers`.`surname` AS 'garmentusers_surname',
               `garmentusers`.`maidenname` AS 'garmentusers_maidenname',
               `garmentusers`.`personnelcode` AS 'garmentusers_personnelcode',
               `garmentusers`.`code` AS 'garmentusers_code',
               `garmentusers`.`active` AS 'garmentusers_active',
               `garmentusers`.`deleted_on` AS 'garmentusers_deleted_on',
               `professions`.`name` AS 'profession_name',
               (
                    SELECT COUNT(*)
                    FROM `garmentusers_garments`
                   WHERE `garmentusers_garments`.`garmentuser_id` = `garmentusers`.`id`
               ) AS 'garments_in_use',
               (
                    SELECT SUM(`garmentusers_arsimos`.`max_credit`)
                      FROM `garmentusers_arsimos`
                     WHERE `garmentusers_arsimos`.`garmentuser_id` = " . $garmentuser_id . "
               ) AS 'maxcredit'
          FROM `garmentusers`
     LEFT JOIN `professions` ON `garmentusers`.`profession_id` = `professions`.`id`
         WHERE (`garmentusers`.`deleted_on` IS NULL) AND (`garmentusers`.`id` = " . $garmentuser_id . ")
         LIMIT 1";
    $garmentuser_sql = db_query($garmentuser_query);
    $garmentuser_data = db_fetch_assoc($garmentuser_sql);

    // Kleding ophalen
    $articles_sql = db_query("
        SELECT DISTINCT
            `articles`.`description` AS 'article',
            `sizes`.`name` AS 'size',
            `garmentusers_arsimos`.`userbound` AS 'userbound',
            `modifications`.`name` AS 'modifications'
       FROM `garmentusers_arsimos`
 INNER JOIN `garmentusers` ON `garmentusers_arsimos`.`garmentuser_id` = `garmentusers`.`id`
 INNER JOIN `arsimos` ON `garmentusers_arsimos`.`arsimo_id` = `arsimos`.`id`
 INNER JOIN `articles` ON `articles`.`id` = `arsimos`.`article_id`
 INNER JOIN `sizes` ON `sizes`.`id` = `arsimos`.`size_id`
  LEFT JOIN `modifications` ON `modifications`.`id` = `arsimos`.`modification_id`
      WHERE `garmentusers_arsimos`.`garmentuser_id` = ". $garmentuser_id ."
        AND `garmentusers_arsimos`.`enabled` = 1
   ORDER BY `articles`.`description`, `sizes`.`position`, `modifications`.`name`");

    $articles_all_count = 0;
    $userbound_articles_count = 0;

    while ($row = db_fetch_assoc($articles_sql)){
        if ($row["userbound"] == "1") {
            $userbound_articles[$userbound_articles_count] = $row;
            $userbound_articles_count++;
        } else {
            $articles_all[$articles_all_count] = $row;
            $articles_all_count++;
        }
    }

    // Kleding in gebruik ophalen
    $garmentuser_garments_inuse_columns = "garments.id garments.tag articles.description sizes.name modifications.name garmentusers_garments.date_received";
    $garmentuser_garments_inuse_conditions["left_join"]["1"] = "garments garments.id garmentusers_garments.garment_id";
    $garmentuser_garments_inuse_conditions["left_join"]["2"] = "arsimos garments.arsimo_id arsimos.id";
    $garmentuser_garments_inuse_conditions["left_join"]["3"] = "articles arsimos.article_id articles.id";
    $garmentuser_garments_inuse_conditions["left_join"]["4"] = "sizes arsimos.size_id sizes.id";
    $garmentuser_garments_inuse_conditions["left_join"]["5"] = "modifications arsimos.modification_id modifications.id";
    $garmentuser_garments_inuse_conditions["left_join"]["6"] = "garmentusers garments.garmentuser_id garmentusers.id";
    $garmentuser_garments_inuse_conditions["order_by"] = "articles.description";
    $garmentuser_garments_inuse_conditions["where"]["1"] = "garmentusers_garments.garmentuser_id " . $garmentuser_id;
    $garmentuser_garments_inuse = db_read("garmentusers_garments", $garmentuser_garments_inuse_columns, $garmentuser_garments_inuse_conditions);

    $garmentuser_data['current_credit'] = db_num_rows($garmentuser_garments_inuse);

    // Kijken of de TAG is ingevuld, en voeg deze inclusief data toe aan scanned_garments array
    if (isset($_POST["garment_tag_submit"]) && !empty($garment_tag)) {
        $garment_id = tag_to_garment_id($garment_tag);
        if ($garment_id){
            if (!in_array($garment_id, $scanned_garments)) {
                array_push($scanned_garments, $garment_id);
            }
        } else {
            $error = $lang["garment_not_found"];
        }
    }

    // Haalt alle waarde van scanned_garments op
    foreach ($scanned_garments as $garment_id) {
        $garment_details_sql = db_query("
        SELECT `articles`.`description` AS 'article',
               `sizes`.`name` AS 'size',
               `modifications`.`name` AS 'modifications',
               `garments`.`tag` AS 'tag'
          FROM `garments`
    INNER JOIN `arsimos` ON `garments`.`arsimo_id` = `arsimos`.`id`
    INNER JOIN `articles` ON `articles`.`id` = `arsimos`.`article_id`
    INNER JOIN `sizes` ON `sizes`.`id` = `arsimos`.`size_id`
     LEFT JOIN `modifications` ON `modifications`.`id` = `arsimos`.`modification_id`
         WHERE `garments`.`id` = ". $garment_id ."
         LIMIT 1");

        $row = db_fetch_assoc($garment_details_sql);
        $garment_details[$garment_id] = $row;
    }

    // Kijkt of het maximale krediet word overschreden
    $krediet = $garmentuser_data["maxcredit"] - $garmentuser_data["garments_in_use"] - count($scanned_garments);

    if ($krediet < 0){
        $error = "<strong>". $lang["notice"] ."</strong> Het maximale krediet word overschreden!";
    }

}

/**
 * Insert into database
 */
if (!empty($garmentuser_id) && isset($_POST["saveclose"]) && !empty($scanned_garments))
{
    $ip_addr = $_SERVER["REMOTE_ADDR"];
    $hostname = gethostbyaddr($ip_addr);

    $did_sql = db_query("SELECT d.id FROM distributors d INNER JOIN distributorlocations l ON l.id = d.distributorlocation_id WHERE l.hostname = '". $hostname ."'");

    if (db_num_rows($did_sql) == 0) {
        $did_id = 1;
    } else {
        $did_data = db_fetch_assoc($did_sql);
        $did_id = $did_data["id"];
    }

    foreach ($scanned_garments as $garment_id) {
        db_delete_where("distributors_load", "garment_id", $garment_id);
        db_delete_where("garmentusers_garments", "garment_id", $garment_id);
        db_query("UPDATE garments g,
                            distributorlocations d
                        SET g.scanlocation_id = d.scanlocation_id_out,
                            g.washcount = g.washcount+1,
                            g.lastscan = NOW()
                      WHERE d.id LIKE 1
                        AND g.id = " . $garment_id);
        db_query("INSERT INTO garmentusers_garments (distributor_id, garmentuser_id, garment_id, date_received) VALUES (". $did_id .", ". $garmentuser_id .", ". $garment_id .", NOW()) ON DUPLICATE KEY UPDATE date_received = NOW()");
        db_query("INSERT INTO log_garmentusers_garments (distributor_id, hook, garment_id, garmentuser_id, starttime, endtime) VALUES (". $did_id .", ". $garment_id .", ". $garment_id .", ". $garmentuser_id .", NOW(), NOW())");
    }

    db_free_result($did_sql);
    redirect($pi["filename_list"]);

}

if (!empty($requiredfields)) {
    $pi["note"] = html_requiredfields($requiredfields);
} elseif (!empty($existence_note)) {
    $pi["note"] = html_requirednote($existence_note);
}

/**
 * Generate the page
 */
$cv = array(
    "articles_all"               => $articles_all,
    "garment_details"            => $garment_details,
    "garmentuser_count"          => $garmentuser_count,
    "garmentuser_data"           => $garmentuser_data,
    "garmentuser_garments_inuse" => $garmentuser_garments_inuse,
    "garmentuser_id"             => $garmentuser_id,
    "garmentuser_multiple"       => $garmentuser_multiple,
    "garmentusers_data"          => $garmentusers_data,
    "error"                      => $error,
    "pageinfo"                   => $pi,
    "userbound_articles"         => $userbound_articles,
    "scanned_garments"           => $scanned_garments,
    "searchdata"                 => $searchdata
);

template_parse($pi, $urlinfo, $cv);

?>
