<?php

/**
 * Garment tagreplacement details
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
$pi["access"] = array("common", "garments");
$pi["group"] = $lang["common"];
$pi["filename_list"] = "extra_garment_tag.php";
$pi["filename_details"] = "extra_garment_tag.php";
$pi["template"] = "layout/pages/extra_garment_tag.tpl";

/**
 * Collect page content
 */
$garment = array(
    "garments_tag" => (!empty($_POST["old_tag"])) ? preg_replace("/[^a-z0-9]/i", "", convertTag($_POST["old_tag"])) : "",
);

$detailsdata = array(
    "new_tag" => (!empty($_POST["new_tag"])) ? convertTag(trim($_POST["new_tag"])) : null
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

   if (isset($_POST["detailssubmit"])) {
        //if we have no new_tag, alert user we need a new tag
        if (empty($detailsdata["new_tag"])) {
            $error = "1";
            array_push($requiredfields, $lang["new_tag"]);
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if ($error == "0") {
            if ($garment["garments_tag"] == $detailsdata["new_tag"]) {
                $error = "1";
                $pi["note"] = html_error($lang["error_both_tags_must_be_different"]);
            }
        }

        if ($tag_found && $error == "0") {
            //update the garment"s tag
            $garment_update_data["tag2"] = $detailsdata["new_tag"];
            db_update("garments", $garment_id, $garment_update_data);

            //redirect to list
            redirect($pi["filename_list"]);

        }
    } elseif (isset($_POST["detailssubmitnone"])) {
        redirect($pi["filename_list"]);
    }

    $pi["page"] = "add";
    $pi["title"] = "Chipcode toevoegen";


//required for: some garment details
if ($tag_found) {
    $garment_columns = "garments.tag garments.tag2 articles.description sizes.name modifications.name garments.washcount garments.scanlocation_id";
    $garment_conditions["left_join"]["1"] = "arsimos garments.arsimo_id arsimos.id";
    $garment_conditions["left_join"]["2"] = "articles arsimos.article_id articles.id";
    $garment_conditions["left_join"]["3"] = "sizes arsimos.size_id sizes.id";
    $garment_conditions["left_join"]["4"] = "modifications arsimos.modification_id modifications.id";
    $garment_conditions["where"]["1"] = "garments.id = " . $garment_id;
    $garment_res = db_read("garments", $garment_columns, $garment_conditions);
    $garment = db_fetch_assoc($garment_res);
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata,
    "tag_found" => $tag_found,
    "tag_comments" => $tag_comments,
    "garment" => $garment
);

template_parse($pi, $urlinfo, $cv);

?>
