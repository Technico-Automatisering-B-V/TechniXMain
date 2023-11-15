<?php

/**
 * Garmentdespeckle details
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
$pi["access"] = array("linen_service", "despeckles");
$pi["group"] = $lang["linen_service"];
$pi["filename_list"] = "garmentdespeckles.php";
$pi["filename_details"] = "garmentdespeckle_details.php";
$pi["template"] = "layout/pages/garmentdespeckle_details.tpl";
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
    "garments_tag" => (!empty($_POST["tag"])) ? convertTag(preg_replace("/[^a-z0-9]/i", "", $_POST["tag"])) : "",
);

$detailsdata = array(
    "despeckle_id" => (isset($_POST["despeckle_id"])) ? trim($_POST["despeckle_id"]) : null,
    "halt" => (!empty($_POST["halt"])) ? 1 : null
);

$requiredfields = array();
$urlinfo = array();
$tag_found = false;
$tag_comments = null;

//if submit or having a garments_despeckles id
if (isset($_POST["sent_a_tag"]) || isset($_POST["finalize"])) {
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
        array_push($requiredfields, $lang["tag"]);
    }
}

if (isset($_POST["page"]) && $_POST["page"] == "add") {
    if (isset($_POST["detailssubmit"])) {
        // If we have no despeckle_id, alert user we need a despeckle
        if (!is_numeric($detailsdata["despeckle_id"])) { array_push($requiredfields, $lang["despeckle"]); }

        $garment_status_repair_sql = "SELECT COUNT(*)
                            FROM garments_despeckles gd 
                            WHERE gd.date_out IS NULL
                            AND gd.status = 1
                            AND gd.garment_id = " . $garment_id;
        $garment_status_repair = db_fetch_num(db_query($garment_status_repair_sql));
        $garment_status_repair = $garment_status_repair[0];

        if(isset($garment_status_repair) && $garment_status_repair == 0) {
            if ($tag_found && empty($requiredfields)) {
                $insert_data["garment_id"] = $garment_id;
                $insert_data["despeckle_id"] = $detailsdata["despeckle_id"];
                $insert_data["date_in"] = "NOW()";
                $insert_data["status"] = 1;
                $insert_data["halt"] = $detailsdata["halt"];

                //insert the given garmentdespeckle
                db_insert("garments_despeckles", $insert_data);

                //update the garment status (scanlocation_id)
                $garment_update_data["scanlocation_id"] = 6;
                db_update("garments", $garment_id, $garment_update_data);

                //redirect to list
                redirect($pi["filename_list"]);

            } elseif (isset($_POST["detailssubmit"])) {
                $pi["note"] = html_requiredfields($requiredfields);
            }
        } else {
            $pi["note"] = html_error($lang["error_the_garment_is_already_added"]);
        }
    } elseif (isset($_POST["detailssubmitnone"])) {
        redirect($pi["filename_list"]);
    }

    $pi["page"] = "add";
    $pi["title"] = $lang["add_despeckle"];

} elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
    if (isset($_POST["finalize"])) {
        if (isset($_POST["finalize"])) {
            $update_data["status"] = null;
            $update_data["date_out"] = "NOW()";

            //update the garmentdespeckle as despeckleed
            db_update("garments_despeckles", $_POST["id"], $update_data);

            //redirect to list
            redirect($pi["filename_list"]);
        }
    } elseif (isset($_POST["detailssubmitnone"])){
        redirect($pi["filename_list"]);
    }

    $pi["page"] = "details";
    $pi["title"] = $lang["despeckle_details"];

    //continue showing the page with details
    $detailsdata = db_fetch_assoc(db_read_row_by_id("garments_despeckles", $_POST["id"]));
    $tag_found = true;

    //if we weren"t able to fetch a garment_id by tag earlier, set it now
    if (!isset($garment_id)){ $garment_id = $detailsdata["garment_id"]; }

    //we need the id for toolbar buttons
    $urlinfo["id"] = $_POST["id"];
} else {
    //we haven"t got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

// Required for: some garment details
if ($tag_found) {
    $garment_columns = "garments.tag articles.description sizes.name modifications.name garments.washcount";
    $garment_conditions["left_join"]["1"] = "arsimos garments.arsimo_id arsimos.id";
    $garment_conditions["left_join"]["2"] = "articles arsimos.article_id articles.id";
    $garment_conditions["left_join"]["3"] = "sizes arsimos.size_id sizes.id";
    $garment_conditions["left_join"]["4"] = "modifications arsimos.modification_id modifications.id";
    $garment_conditions["where"]["1"] = "garments.id = " . $garment_id;
    $garment_res = db_read("garments", $garment_columns, $garment_conditions);
    $garment = db_fetch_assoc($garment_res);
}

// Required for: repairs count
if ($tag_found) {
    $repairs_count_conditions["where"]["1"] = "garment_id = " . $garment_id;
    $repairs_count_res = db_count("garments_repairs", "garment_id", $repairs_count_conditions);
    $repairs_count_arr = db_fetch_num($repairs_count_res);
    $counts["repairs"] = $repairs_count_arr[0];
} else {
    $counts["repairs"] = null;
}

// Required for: despeckles count
if ($tag_found) {
    $despeckles_count_conditions["where"]["1"] = "garment_id = " . $garment_id;
    $despeckles_count_res = db_count("garments_despeckles", "garment_id", $despeckles_count_conditions);
    $despeckles_count_arr = db_fetch_num($despeckles_count_res);
    $counts["despeckles"] = $despeckles_count_arr[0];
} else {
    $counts["despeckles"] = null;
}

//required for selectbox: despeckles
$despeckles_conditions["order_by"] = "description";
$despeckles_res = db_read("despeckles", "id description", $despeckles_conditions);
if (!empty($despeckles_res)) {
    while ($row = db_fetch_row($despeckles_res)) {
        $despeckles[$row[0]] = $row[1];
    }
} else {
    $despeckles = null;
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
    "despeckles" => $despeckles,
    "counts" => $counts
);

template_parse($pi, $urlinfo, $cv);

?>
