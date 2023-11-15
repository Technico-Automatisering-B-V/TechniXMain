<?php

/**
 * Garmentrepair details
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
$pi["access"] = array("linen_service", "repairs");
$pi["group"] = $lang["linen_service"];
$pi["filename_list"] = "garmentrepairs.php";
$pi["filename_details"] = "garmentrepair_details.php";
$pi["template"] = "layout/pages/garmentrepair_details.tpl";
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
    "garments_id" => (!empty($_POST["garments_id"])) ? trim($_POST["garments_id"]) : null,
    "garments_tag" => (!empty($_POST["garments_tag"])) ? convertTag(trim($_POST["garments_tag"])) : null
);

$detailsdata = array(
    "id" => (!empty($_POST["id"])) ? trim($_POST["id"]) : null,
    "garment_id" => (!empty($_POST["garments_id"])) ? trim($_POST["garments_id"]) : null,
    "halt" => (!empty($_POST["halt"])) ? 1 : null,
    "repair_id" => (isset($_POST["repair_id"])) ? trim($_POST["repair_id"]) : null
);

$requiredfields = array();
$urlinfo = array();
$tag_comments = null;

//if submit or having a garments_repairs id
if (isset($_POST["detailssubmit"]) || isset($_POST["search"]) || isset($_POST["finalize"])) {
    //if we have a tag
    if (!empty($garment["garments_tag"])) {
        //get the garment_id
        $garment_id = tag_to_garment_id($garment["garments_tag"]);

        //then set tag_comments
        if (!$garment_id) {
            $tag_comments = "<font color=\"red\">" . $lang["unknown_tag"] . "</font>";
        }
    } else {
        //alert user we need a tag
        array_push($requiredfields, $lang["tag"]);
    }
}

if (isset($_POST["page"]) && $_POST["page"] == "add") {
    if (isset($_POST["search"])) {
        $garment_id = tag_to_garment_id($garment["garments_tag"]);
        $garment["garments_id"] = $garment_id;
    } elseif (isset($_POST["detailssubmit"])) {
        //if we have no repair_id, alert user we need a repair
        if (!is_numeric($detailsdata["repair_id"])){ array_push($requiredfields, $lang["repair"]); }

        $garment_id = $garment["garments_id"];
        
        $garment_status_repair_sql = "SELECT COUNT(*)
                            FROM garments_repairs gr 
                            WHERE gr.date_out IS NULL
                            AND gr.status = 1
                            AND gr.garment_id = " . $garment_id;
        $garment_status_repair = db_fetch_num(db_query($garment_status_repair_sql));
        $garment_status_repair = $garment_status_repair[0];

        if(isset($garment_status_repair) && $garment_status_repair == 0) {
            if (empty($requiredfields)) {
                $insert_data["garment_id"] = $garment_id;
                $insert_data["repair_id"] = $detailsdata["repair_id"];
                $insert_data["date_in"] = "NOW()";
                $insert_data["status"] = 1;
                $insert_data["halt"] = $detailsdata["halt"];

                //insert the given garmentrepair
                db_insert("garments_repairs", $insert_data);

                //update the garment status (scanlocation_id)
                $garment_update_data["scanlocation_id"] = 5;
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
    $pi["title"] = $lang["add_repair"];

} elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
    if (isset($_POST["finalize"])) {
        if (isset($_POST["finalize"])) {
            $update_data["status"] = null;
            $update_data["date_out"] = "NOW()";

            //update the garmentrepair as repaired
            db_update("garments_repairs", $_POST["id"], $update_data);

            //redirect to list
            redirect($pi["filename_list"]);
        }
    } elseif (isset($_POST["detailssubmitnone"])) {
        redirect($pi["filename_list"]);
    }

    $pi["page"] = "details";
    $pi["title"] = $lang["repair_details"];

    //continue showing the page with details
    $detailsdata = db_fetch_assoc(db_query("SELECT * FROM `garments_repairs` WHERE `id` = ". $_POST["id"] ." LIMIT 1"));
    $garment_id = $detailsdata["garment_id"];

    //we need the id for toolbar buttons
    $urlinfo["id"] = $garment_id;
} else {
    //we haven"t got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

//required for: some garment details
if ($garment_id) {
    $garment_columns = "garments.id garments.tag articles.description sizes.name modifications.name garments.washcount";
    $garment_conditions["left_join"]["1"] = "arsimos garments.arsimo_id arsimos.id";
    $garment_conditions["left_join"]["2"] = "articles arsimos.article_id articles.id";
    $garment_conditions["left_join"]["3"] = "sizes arsimos.size_id sizes.id";
    $garment_conditions["left_join"]["4"] = "modifications arsimos.modification_id modifications.id";
    $garment_conditions["where"]["1"] = "garments.id = " . $garment_id;
    $garment_res = db_read("garments", $garment_columns, $garment_conditions);
    $garment = db_fetch_assoc($garment_res);
}

//required for: repairs count
if ($garment_id) {
    $repairs_count_conditions["where"]["1"] = "garment_id = " . $garment_id;
    $repairs_count_res = db_count("garments_repairs", "garment_id", $repairs_count_conditions);
    $repairs_count_arr = db_fetch_num($repairs_count_res);
    $counts["repairs"] = $repairs_count_arr[0];
} else {
    $counts["repairs"] = null;
}

//required for: despeckles count
if ($garment_id) {
    $despeckles_count_conditions["where"]["1"] = "garment_id = " . $garment_id;
    $despeckles_count_res = db_count("garments_despeckles", "garment_id", $despeckles_count_conditions);
    $despeckles_count_arr = db_fetch_num($despeckles_count_res);
    $counts["despeckles"] = $despeckles_count_arr[0];
} else {
    $counts["despeckles"] = null;
}

//required for selectbox: repairs
$repairs_conditions["order_by"] = "description";
$repairs_res = db_read("repairs", "id description", $repairs_conditions);
if (!empty($repairs_res)) {
    while ($row = db_fetch_row($repairs_res)) {
        $repairs[$row[0]] = $row[1];
    }
} else {
    $repairs = null;
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata,
    "tag_comments" => $tag_comments,
    "repairs" => $repairs,
    "garment" => $garment,
    "counts" => $counts
);

template_parse($pi, $urlinfo, $cv);

?>