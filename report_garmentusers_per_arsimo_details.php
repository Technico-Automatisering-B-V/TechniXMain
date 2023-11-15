<?php

/**
 * Report garmentusers per arsimo details
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
$pi["access"] = array("reports", "garmentusers_per_arsimo");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["garmentusers_per_arsimo"];
$pi["filename_list"] = "report_garmentusers_per_arsimo.php";
$pi["filename_details"] = "report_garmentusers_per_arsimo_details.php";
$pi["template"] = "layout/pages/report_garmentusers_per_arsimo_details.tpl";
$pi["page"] = "details";
$pi["toolbar"]["no_delete"] = "yes";

$urlinfo = array();

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Check if arsimo id is posted
 */
if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $arsimo_id = trim($_POST["id"]);
} else {
    redirect($pi["filename_list"]);
}

$cquery = "";
$cquery2 = "";
if (!empty($_POST["cid"])) {
    $cquery .= " AND `garmentusers`.`id` IN (SELECT `garmentuser_id` FROM `circulationgroups_garmentusers` WHERE `circulationgroup_id` = ". $_POST["cid"] ." ) ";
    $cquery2 .= " AND `garments`.`circulationgroup_id` = ". $_POST["cid"] ." ";
}
if (!empty($_POST["clientdepartment_id"])) {$cquery .= " AND `garmentusers`.`clientdepartment_id` = ". $_POST["clientdepartment_id"];}
if (!empty($_POST["costplace_id"])) {$cquery .= " AND `garmentusers`.`costplace_id` = ". $_POST["costplace_id"];}
if (!empty($_POST["function_id"])) {$cquery .= " AND `garmentusers`.`function_id` = ". $_POST["function_id"];}

// Required for arsimo info
$arsimo_query = "SELECT
            `arsimos`.`id` AS 'arsimo_id',
            `articles`.`description` AS 'article',
            `sizes`.`name` AS 'size',
            `modifications`.`name` AS 'modification'
       FROM `arsimos`
 INNER JOIN `articles` ON `arsimos`.`article_id` = `articles`.`id`
 INNER JOIN `sizes` ON `arsimos`.`size_id` = `sizes`.`id`
  LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`
      WHERE `arsimos`.`id` = " . $arsimo_id ."
      LIMIT 1";

$arsimo_data = db_query($arsimo_query);

$query = "SELECT garments.tag AS 'garments_tag',
                 garments.id AS 'g_id',
                 articles.description AS 'articles_description',
                 sizes.name AS 'sizes_name',
                 modifications.name AS 'modifications_name',
                 garments.washcount AS 'garments_washcount',
                 garmentusers.surname AS 'garmentusers_surname',
                 garmentusers.title AS 'garmentusers_title',
                 garmentusers.gender AS 'garmentusers_gender',
                 garmentusers.initials AS 'garmentusers_initials',
                 garmentusers.intermediate AS 'garmentusers_intermediate', 
                 garmentusers.maidenname AS 'garmentusers_maidenname', 
                 scanlocations.translate AS 'scanlocations_translate'
FROM garments
INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
INNER JOIN articles ON arsimos.article_id = articles.id
INNER JOIN sizes ON arsimos.size_id = sizes.id
INNER JOIN scanlocations ON scanlocations.id = garments.scanlocation_id
LEFT JOIN modifications ON arsimos.modification_id = modifications.id
LEFT JOIN garmentusers ON garments.garmentuser_id = garmentusers.id
WHERE `scanlocations`.`circulationgroup_id` IS NOT NULL 
". $cquery2 ." 
AND ISNULL(`garments`.`deleted_on`) AND (ISNULL(`garments`.`garmentuser_id`) OR `garments`.`garmentuser_id` = '')
AND arsimos.id = " . $arsimo_id;

$listdata_garments = db_query($query);

/**
 * Collect page content
 */
$query = "SELECT
            `garmentusers_arsimos`.`garmentuser_id` AS 'gu_id',
            `garmentusers`.`name` AS 'gu_name',
            `garmentusers`.`surname` AS 'gu_surname',
            `garmentusers`.`maidenname` AS 'gu_maidenname',
            `garmentusers`.`initials` AS 'gu_initials',
            `garmentusers`.`title` AS 'gu_title',
            `garmentusers`.`gender` AS 'gu_gender',
            `garmentusers`.`intermediate` AS 'gu_intermediate',
            `garmentusers`.`personnelcode` AS 'gu_personnelcode'
       FROM `arsimos`
 INNER JOIN `garmentusers_arsimos` ON `arsimos`.`id` = `garmentusers_arsimos`.`arsimo_id`
 INNER JOIN `garmentusers` ON `garmentusers_arsimos`.`garmentuser_id` = `garmentusers`.`id`
      WHERE `arsimos`.`id` = " . $arsimo_id ."
        AND `garmentusers_arsimos`.`enabled` = 1
        AND `garmentusers_arsimos`.`userbound` = 0
        AND ISNULL(`garmentusers`.`deleted_on`)
        ". $cquery ." 
    GROUP BY `garmentusers`.`id`";

$listdata = db_query($query);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "listdata_garments" => $listdata_garments,
    "arsimodata" => $arsimo_data
);

template_parse($pi, $urlinfo, $cv);

?>
