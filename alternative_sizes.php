<?php

/**
 * Alternative sizes
 *
 * @author    G. I. Voros <gabor@technico.nl>
 * @copyright (c) 2014-${date} Technico Automatisering B.V.
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
$pi["title"] = $lang["alternative_sizes"];
$pi["filename_list"] = "alternative_sizes.php";
$pi["filename_this"] = "alternative_sizes.php";
$pi["template"] = "layout/pages/alternative_sizes.tpl";
$pi["toolbar"]["export"] = "no";
$pi["page"] = "simple";

$ui = array();

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
    foreach ($_POST["arsimo"] as $arsimo_id => $alt_arsimo_id) {
        if ($arsimo_id != $alt_arsimo_id) {
            db_query("DELETE FROM `alt_arsimos` WHERE `arsimo_id` = ". $arsimo_id);

            if (!empty($alt_arsimo_id)) {
                $query = "INSERT INTO `alt_arsimos` (`arsimo_id`, `alt_arsimo_id`) VALUES (". $arsimo_id .", ". $alt_arsimo_id .")";
                db_query($query) or die("ERROR LINE ". __LINE__ .": ". db_error());
            }
        } else {
            $pi["note"] = html_warning($lang["use_alternative_sizes"]);
        }
    }
}

/**
 * Get all articles
 */
$articles_query = "SELECT `id`, `articlenumber`, `description` FROM `articles` ORDER BY `description` ASC";
$articles_sql = db_query($articles_query) or die("ERROR LINE ". __LINE__);

while ($articles_result = db_fetch_row($articles_sql)) {
    $articles[$articles_result[0]] = $articles_result[2] ." (". $articles_result[1] .")";

    $article_sizes_query = "
        SELECT `arsimos`.`id` AS 'arsimo_id',
               IFNULL(CONCAT(`sizes`.`name`, ' ', `modifications`.`name`),`sizes`.`name`) AS 'size_name'
          FROM `arsimos`
    INNER JOIN `sizes` ON `arsimos`.`size_id` = `sizes`.`id`
     LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`
         WHERE `arsimos`.`deleted_on` IS NULL
           AND `arsimos`.`article_id` = ". $articles_result[0] ."
      ORDER BY `sizes`.`position`, `modifications`.`id` ASC";
    $article_sizes_sql = db_query($article_sizes_query) or die("ERROR LINE ". __LINE__);

    while ($article_sizes_result = db_fetch_row($article_sizes_sql)) {
        $article_sizes[$articles_result[0]][$article_sizes_result[0]] = $article_sizes_result[1];
    }

}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "articles" => $articles,
    "article_sizes" => $article_sizes
);

template_parse($pi, $ui, $cv);

?>
