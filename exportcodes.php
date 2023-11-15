<?php

/**
 * Exportcodes
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
$pi = array();
$pi["group"] = "Technico";
$pi["title"] = $lang["exportcodes"];
$pi["filename_list"] = "exportcodes.php";
$pi["filename_this"] = "exportcodes.php";
$pi["template"] = "layout/pages/exportcodes.tpl";
$pi["page"] = "simple";

$urlinfo = array();

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}

/**
 * When form is submitted
 */
if (isset($_POST["submit"])) {
    foreach ($_POST["arsimo"] as $arsimo_id => $a_exportcode) {

        db_query("DELETE FROM `arsimos_importcodes` WHERE `arsimo_id` = ". $arsimo_id);

        if (!empty($a_exportcode)) {
            foreach ($a_exportcode as $num => $exportcode) {
                if (!empty($exportcode)) {
                    $q = "INSERT INTO `arsimos_importcodes` (`arsimo_id`, `importcode`) VALUES ('". $arsimo_id ."', '". $exportcode ."')";
                    db_query($q) or die("ERROR LINE ". __LINE__ .": ". db_error());
                }
            }
        }
    }
} elseif($_POST["fill"]) {
    /*add all exportcodes via article id*/
    db_query("INSERT INTO `arsimos_importcodes`(`arsimo_id`, `importcode`)
            SELECT `ar`.`id`, IF(ISNULL(`m`.`name`),CONCAT(`a`.`articlenumber`,`s`.`name`),CONCAT(`a`.`articlenumber`,`s`.`name`,`m`.`name`))
            FROM `arsimos` `ar`
            INNER JOIN `articles` `a` ON `a`.`id` = `ar`.`article_id`
            INNER JOIN `sizes` `s` ON `s`.`id` = `ar`.`size_id`
            LEFT JOIN `modifications` `m` ON `m`.`id` = `ar`.`modification_id`
            WHERE `a`.`id` = ". $_POST["aid"] ."
            AND `ar`.`id` NOT IN (SELECT `arsimo_id` FROM `arsimos_importcodes`)");
}

/**
 * Get all articles
 */
$articles_query = "SELECT `id`, `articlenumber`, `description` FROM `articles` ORDER BY `description` ASC";
$articles_sql = db_query($articles_query) or die("ERROR LINE ". __LINE__);

while ($articles_result = db_fetch_row($articles_sql)) {
    $articles[$articles_result[0]] = $articles_result[2] ." (". $articles_result[1] .")";
}

/**
 * Generate the page
 */
$cv = array(
    "articles" => $articles
);

template_parse($pi, $urlinfo, $cv);

?>
