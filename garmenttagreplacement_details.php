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
$pi["access"] = array("linen_service", "tag_replacements");
$pi["group"] = $lang["linen_service"];
$pi["filename_list"] = "garmenttagreplacements.php";
$pi["filename_details"] = "garmenttagreplacement_details.php";
$pi["template"] = "layout/pages/garmenttagreplacement_details.tpl";
$pi["toolbar"]["no_delete"] = true;

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

if (isset($_POST["page"]) && $_POST["page"] == "add") {
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
            $insert_data["datetime"] = "NOW()";
            $insert_data["garment_id"] = $garment_id;
            $insert_data["old_tag"] = $garment["garments_tag"];
            $insert_data["new_tag"] = $detailsdata["new_tag"];

            //insert the given tagreplacement
            db_insert("log_garments_tagreplacements", $insert_data);

            //update the garment"s tag
            $garment_update_data["tag"] = $detailsdata["new_tag"];
            db_update("garments", $garment_id, $garment_update_data);

            //redirect to list
            redirect($pi["filename_list"]);

        }
    } elseif (isset($_POST["detailssubmitnone"])) {
        redirect($pi["filename_list"]);
    }

    $pi["page"] = "add";
    $pi["title"] = $lang["replace_tag"];

} elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {

    if (isset($_POST["detailssubmitnone"])) {
        redirect($pi["filename_list"]);
    }

    $pi["page"] = "details";
    $pi["title"] = $lang["replaced_tag_details"];

    //continue showing the page with details
    $detailsdata = db_fetch_assoc(db_read_row_by_id("log_garments_tagreplacements", $_POST["id"]));
    $tag_found = true;

    //if we weren"t able to fetch a garment_id by tag earlier, set it now
    if (!isset($garment_id)) { $garment_id = $detailsdata["garment_id"]; }

    //we need the id for toolbar buttons
    $urlinfo["id"] = $_POST["id"];
} else {
    //we haven"t got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

//required for: some garment details
if ($tag_found) {
    $garment_columns = "garments.tag articles.description sizes.name modifications.name garments.washcount garments.scanlocation_id";
    $garment_conditions["left_join"]["1"] = "arsimos garments.arsimo_id arsimos.id";
    $garment_conditions["left_join"]["2"] = "articles arsimos.article_id articles.id";
    $garment_conditions["left_join"]["3"] = "sizes arsimos.size_id sizes.id";
    $garment_conditions["left_join"]["4"] = "modifications arsimos.modification_id modifications.id";
    $garment_conditions["where"]["1"] = "garments.id = " . $garment_id;
    $garment_res = db_read("garments", $garment_columns, $garment_conditions);
    $garment = db_fetch_assoc($garment_res);
}

//required for: repairs count
if ($tag_found) {
    $repairs_count_conditions["where"]["1"] = "garment_id = " . $garment_id;
    $repairs_count_conditions["where"]["2"] = "type = repair";
    $repairs_count_res = db_count("garments_despecklesandrepairs", "garment_id", $repairs_count_conditions);
    $repairs_count_arr = db_fetch_num($repairs_count_res);
    $counts["repairs"] = $repairs_count_arr[0];
} else {
    $counts["repairs"] = null;
}

//required for: despeckles count
if ($tag_found) {
    $despeckles_count_conditions["where"]["1"] = "garment_id = " . $garment_id;
    $despeckles_count_conditions["where"]["2"] = "type = despeckle";
    $despeckles_count_res = db_count("garments_despecklesandrepairs", "garment_id", $despeckles_count_conditions);
    $despeckles_count_arr = db_fetch_num($despeckles_count_res);
    $counts["despeckles"] = $despeckles_count_arr[0];
} else {
    $counts["despeckles"] = null;
}

//required for: circulationgroup
if ($tag_found) {
    $circulationgroup_conditions["left_join"]["1"] = "scanlocations garments.scanlocation_id scanlocations.id";
    $circulationgroup_conditions["left_join"]["2"] = "circulationgroups scanlocations.circulationgroup_id circulationgroups.id";
    $circulationgroup_conditions["where"]["1"] = "garments.id = " . $garment_id;
    $circulationgroup_res = db_read("garments", "circulationgroups.name", $circulationgroup_conditions);
    $circulationgroup_arr = db_fetch_num($circulationgroup_res);
    $circulationgroup = $circulationgroup_arr[0];
} else {
    $circulationgroup = null;
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
    "garment" => $garment,
    "circulationgroup" => $circulationgroup,
    "counts" => $counts
);

template_parse($pi, $urlinfo, $cv);

?>
