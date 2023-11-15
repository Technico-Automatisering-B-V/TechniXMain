<?php

/**
 * Pocket control add
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
$pi["access"] = array("linen_service", "pocket_control_add");
$pi["group"] = $lang["pocket_control"];
$pi["filename_list"] = "pocket_control_add.php";
$pi["filename_details"] = "pocket_control_add.php";
$pi["template"] = "layout/pages/pocket_control_add.tpl";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$garment = array(
    "garments_tag" => (!empty($_POST["tag"])) ? preg_replace("/[^a-z0-9]/i", "", convertTag($_POST["tag"])) : "",
);

$detailsdata = array(
    "found_item_type_id" => (!empty($_POST["found_item_type_id"])) ? convertTag(trim($_POST["found_item_type_id"])) : null
);

$error = "0";
$requiredfields = array();
$urlinfo = array();
$tag_found = false;
$tag_comments = null;

//if submit or having a log_garments_tagreplacements id
if (isset($_POST["sent_a_tag"])) {
    //if we have a tag
    if (!empty($garment["garments_tag"])) {
        //get the garment_id
        $garment_id = tag_to_garment_id($garment["garments_tag"]);

        //then set tag_comments
        if ($garment_id) {
            $tag_found = true;
        } else {
            $tag_found = false;
            $tag_comments = "<font color=\"red\">" . $lang["unknown_tag"] . "</font>";
        }
    } else {
        //alert user we need a tag
        array_push($requiredfields, $lang["tag"]);
    }
}

//required for: some garment details
if ($tag_found) {
    
    
    //required for selectbox: found_item_types
    $found_item_types_conditions["order_by"] = "value";
    $found_item_types = db_read("found_item_types", "id value", $found_item_types_conditions);
    $found_item_type_count = db_num_rows($found_item_types);
    
    $garment_columns = "garments.tag garments.lastscan articles.description sizes.name modifications.name garments.garmentuser_id scanlocations.translate circulationgroups.name";
    $garment_conditions["left_join"]["1"] = "arsimos garments.arsimo_id arsimos.id";
    $garment_conditions["left_join"]["2"] = "articles arsimos.article_id articles.id";
    $garment_conditions["left_join"]["3"] = "sizes arsimos.size_id sizes.id";
    $garment_conditions["left_join"]["4"] = "modifications arsimos.modification_id modifications.id";
    $garment_conditions["left_join"]["5"] = "scanlocations scanlocations.id garments.scanlocation_id";
    $garment_conditions["left_join"]["6"] = "circulationgroups circulationgroups.id garments.circulationgroup_id";
    $garment_conditions["where"]["1"] = "garments.id = " . $garment_id;
    $garment_res = db_read("garments", $garment_columns, $garment_conditions);
    $garment = db_fetch_assoc($garment_res);
    
    $garmentuser_query = "SELECT 
            `garmentusers`.`id` AS 'id',
            `garmentusers`.`surname` AS 'surname',
            `garmentusers`.`name` AS 'name',
            `garmentusers`.`personnelcode` AS 'personnelcode',
            `clientdepartments`.`name` AS 'clientdepartment',
            `professions`.`name` AS 'profession',
            `garmentusers`.`email` AS 'email'
            FROM
            (
                SELECT `log_garmentusers_garments`.*
                  FROM `log_garmentusers_garments`
                 WHERE `log_garmentusers_garments`.`garment_id` = ". $garment_id ."
              ORDER BY `log_garmentusers_garments`.`starttime` DESC
                 LIMIT 0, 1
            ) `last_distributions`
            INNER JOIN `garments` ON `last_distributions`.`garment_id` = `garments`.`id`
            INNER JOIN `garmentusers` ON `last_distributions`.`garmentuser_id` = `garmentusers`.`id`
            LEFT JOIN `clientdepartments` ON `clientdepartments`.`id` = `garmentusers`.`clientdepartment_id`
            LEFT JOIN `professions` ON `professions`.`id` = `garmentusers`.`profession_id`
            GROUP BY `garmentusers`.`id`
            ORDER BY `last_distributions`.`starttime` DESC";

    $garmentuserdata = db_query($garmentuser_query);
    $garmentuser = db_fetch_assoc($garmentuserdata);
    
}

if (isset($_POST["detailssubmit"])) {
        //if we have no new_tag, alert user we need a new tag
        if (empty($detailsdata["found_item_type_id"])) {
            $error = "1";
            array_push($requiredfields, $lang["found_item"]);
            $pi["note"] = html_requiredfields($requiredfields);
        }
        
        if (empty($garmentuser["id"])) {
            $error = "1";
            array_push($requiredfields, $lang["garmentuser"]);
            $pi["note"] = html_warning($lang["unknown"]. " " .$lang["garmentuser"]);
        }

        if ($tag_found && $error == "0") {
            
            $detailsdata["garmentuser_id"] = $garmentuser["id"];
            $detailsdata["garment_id"] = $garment_id;
            $detailsdata["date"] = date("Y-m-d H:i:s");
            
            //insert found item
            db_insert("found_items", $detailsdata);
            
            //redirect to list
            redirect($pi["filename_list"]);

        }
    } elseif (isset($_POST["detailssubmitnone"])) {
        redirect($pi["filename_list"]);
    }

    $pi["page"] = "add";
    $pi["title"] = $lang["pocket_control"];

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata,
    "tag_found" => $tag_found,
    "tag_comments" => $tag_comments,
    "garment" => $garment,
    "garmentuser" => $garmentuser,
    "found_item_type_count" => $found_item_type_count,
    "found_item_types" => $found_item_types
);

template_parse($pi, $urlinfo, $cv);

?>
