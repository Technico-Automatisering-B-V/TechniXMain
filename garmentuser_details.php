<?php

/**
 * Garmentuser details
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
$pi = array(
    "access" => array("common", "garmentusers"),
    "group" => $lang["common"],
    "filename_list" => "garmentusers.php",
    "filename_details" => "garmentuser_details.php",
    "template" => "layout/pages/garmentuser_details.tpl",
    "filename_next" => "garment_details.php"
);

/**
 * Authorization
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/** Cancel button **/
if (isset($_POST["detailssubmitnone"])){ redirect($pi["filename_list"]); }


/**
 * Variables
 */
$error = "0";

$alt_modifications_selected = (!empty($_POST["alt_modifications_selected"])) ? $_POST["alt_modifications_selected"] : array();
$alt_sizes_selected = (!empty($_POST["alt_sizes_selected"])) ? $_POST["alt_sizes_selected"] : array();
$alternatives = (!empty($_POST["alternatives"])) ? trim($_POST["alternatives"]) : "1";
$articles_selected = (!empty($_POST["articles_selected"])) ? $_POST["articles_selected"] : array();
$clientdepartments_all = array();
$clientdepartment_id = (!empty($_POST["clientdepartment_id"])) ? trim($_POST["clientdepartment_id"]) : null;
$costplaces_all = array();
$costplace_id = (!empty($_POST["costplace_id"])) ? trim($_POST["costplace_id"]) : null;
$functions_all = array();
$function_id = (!empty($_POST["function_id"])) ? trim($_POST["function_id"]) : null;

$select_article_id = (!empty($_POST["select_article_id"])) ? trim($_POST["select_article_id"]) : null;

$circulationgroups_all = array();
$circulationgroups_all_active = array();
$circulationgroups_selected = (!empty($_POST["circulationgroups_selected"])) ? $_POST["circulationgroups_selected"] : array();
$circulationgroups_name_selected = (!empty($_POST["circulationgroups_name_selected"])) ? $_POST["circulationgroups_name_selected"] : array();
$count = (!empty($_POST["count"])) ? $_POST["count"] : array();
$timelockoption = (isset($_POST["timelock"])) ? "owntimelock" : "professiondefault";
$blockageoption = (!empty($_POST["daysbeforelock"])) ? "ownblockage" : "professiondefault";
$warningoption = (!empty($_POST["daysbeforewarning"])) ? "ownwarning" : "professiondefault";
$profession_timelock = "";
$profession_blockage = "";
$profession_warning = "";
$date_service_off = (!empty($_POST["date_service_off"])) ? trim($_POST["date_service_off"]) : null;
$date_service_on = (!empty($_POST["date_service_on"])) ? trim($_POST["date_service_on"]) : null;
$garment_id_to_cancel = (!empty($_POST["garment_id_to_cancel"])) ? trim($_POST["garment_id_to_cancel"]) : false;
$garment_id_to_cancel_comments = (!empty($_POST["garment_id_to_cancel_comments"])) ? trim($_POST["garment_id_to_cancel_comments"]) : null;
$garment_id_to_unbound = (!empty($_POST["garment_id_to_unbound"])) ? trim($_POST["garment_id_to_unbound"]) : false;
$article_id_to_unbound = (!empty($_POST["article_id_to_unbound"])) ? trim($_POST["article_id_to_unbound"]) : false;
$modifications_selected = (!empty($_POST["modifications_selected"])) ? $_POST["modifications_selected"] : array();
$requiredfields = array();
$service_off_selected = (!empty($date_service_off)) ? "date" : "unlimited";
$service_on_selected = (!empty($date_service_on)) ? "date" : "unlimited";
$sizes_selected = (!empty($_POST["sizes_selected"])) ? $_POST["sizes_selected"] : array();
$sizes_selected_history = (!empty($_POST["sizes_selected_history"])) ? $_POST["sizes_selected_history"] : array();

$urlinfo = array();
    
/** User garments **/
$user_articles_id = (!empty($_POST["user_articles_id"])) ? $_POST["user_articles_id"] : array();
$user_articles_selected = (!empty($_POST["user_articles_selected"])) ? $_POST["user_articles_selected"] : array();
$user_count = (!empty($_POST["user_count"])) ? $_POST["user_count"] : array();

/** Circulation groups **/
$circulationgroups_all_conditions["order_by"] = "circulationgroups.name";
$circulationgroups_all_resource = db_read("circulationgroups", "circulationgroups.id circulationgroups.name", $circulationgroups_all_conditions);

if (!empty($circulationgroups_all_resource) && db_num_rows($circulationgroups_all_resource))
{
    while ($row = db_fetch_num($circulationgroups_all_resource))
    {
        $circulationgroups_all[$row[0]] = $row[1];
    }
}

/** Circulation groups active **/
$circulationgroups_all_active_query = "SELECT DISTINCT cg.id,cg.name FROM circulationgroups cg
INNER JOIN distributorlocations dl ON dl.circulationgroup_id = cg.id
INNER JOIN distributors d ON d.distributorlocation_id = dl.id";

$circulationgroups_all_active_data = db_query($circulationgroups_all_active_query);
if (!empty($circulationgroups_all_active_data))
{
	while($row = db_fetch_array($circulationgroups_all_active_data)) {
		$circulationgroups_all_active[$row['id']] = $row['name'];
	}        
}

/** Client departments **/
$clientdepartments_all_conditions["order_by"] = "clientdepartments.name";
$clientdepartments_all_resource = db_read("clientdepartments", "clientdepartments.id clientdepartments.name", $clientdepartments_all_conditions);
if (!empty($clientdepartments_all_resource) && db_num_rows($clientdepartments_all_resource))
{
    while ($row = db_fetch_num($clientdepartments_all_resource))
    {
        $clientdepartments_all[$row[0]] = $row[1];
    }
}

/** Cost places **/
$costplaces_all_conditions["order_by"] = "costplaces.value";
$costplaces_all_resource = db_read("costplaces", "costplaces.id costplaces.value", $costplaces_all_conditions);
if (!empty($costplaces_all_resource) && db_num_rows($costplaces_all_resource))
{
    while ($row = db_fetch_num($costplaces_all_resource))
    {
        $costplaces_all[$row[0]] = $row[1];
    }
}

/** Functions **/
$functions_all_conditions["order_by"] = "functions.value";
$functions_all_resource = db_read("functions", "functions.id functions.value", $functions_all_conditions);
if (!empty($functions_all_resource) && db_num_rows($functions_all_resource))
{
    while ($row = db_fetch_num($functions_all_resource))
    {
        $functions_all[$row[0]] = $row[1];
    }
}

/** Garmentlink data **/
$garmentlink = array(
    "enabled" => (!empty($_POST["garmentlink_enabled"])) ? trim($_POST["garmentlink_enabled"]) : "2",
    "tag" => (!empty($_POST["garmentlink_tag"])) ? convertTag(trim($_POST["garmentlink_tag"])) : null,
    "circulationgroup_id" => (!empty($_POST["garmentlink_circulationgroup_id"])) ? trim($_POST["garmentlink_circulationgroup_id"]) : "",
    "article_id" => (!empty($_POST["garmentlink_article_id"])) ? trim($_POST["garmentlink_article_id"]) : "",
    "size_id" => (!empty($_POST["garmentlink_size_id"])) ? trim($_POST["garmentlink_size_id"]) : "",
    "modification_id" => (!empty($_POST["garmentlink_modification_id"])) ? trim($_POST["garmentlink_modification_id"]) : "",
    "buttontext" => $lang["link_and_save"],
    "message" => ""
);

/** Articlelink data **/
$articlelink = array(
    "enabled" => (!empty($_POST["articlelink_enabled"])) ? trim($_POST["articlelink_enabled"]) : "2",
    "article_1_id" => (!empty($_POST["articlelink_article_1_id"])) ? trim($_POST["articlelink_article_1_id"]) : "",
    "article_2_id" => (!empty($_POST["articlelink_article_2_id"])) ? trim($_POST["articlelink_article_2_id"]) : "",
    "combined_credit" => (!empty($_POST["articlelink_combined_credit"])) ? $_POST["articlelink_combined_credit"] : "0",
    "extra_credit" => (!empty($_POST["articlelink_extra_credit"])) ? $_POST["articlelink_extra_credit"] : "0",
    "buttontext" => $lang["link_and_save"],
    "message" => ""
);

/** Search button for garmentlink **/
if (isset($_POST["garmentlink_search"]) && !empty($_POST["garmentlink_tag"]))
{
    $garment_id = tag_to_garment_id(convertTag($_POST["garmentlink_tag"]));
    $garmentlink = array();

    if ($garment_id)
    {
        $garmentdata = db_fetch_assoc(db_read_row_by_id("garments", $garment_id));

        if ($garmentdata)
        {
            /** Retrieve the arsimo data from garmentlink **/
            $arsimo_conditions["where"]["1"] = "id = " . $garmentdata["arsimo_id"];
            $arsimo_data = db_read("arsimos", "id article_id size_id modification_id", $arsimo_conditions);
            $arsimo_data = db_fetch_assoc($arsimo_data);

            $garmentlink["enabled"] = "1";
            $garmentlink["tag"] = $garmentdata["tag"];
            $garmentlink["circulationgroup_id"] = $garmentdata["circulationgroup_id"];
            $garmentlink["article_id"] = $arsimo_data["article_id"];
            $garmentlink["size_id"] = $arsimo_data["size_id"];
            $garmentlink["modification_id"] = $arsimo_data["modification_id"];
            $garmentlink["buttontext"] = $lang["link"];
        }
    }
    else
    {
        $garmentlink["enabled"] = "1";
        $garmentlink["circulationgroup_id"] = "";
        $garmentlink["article_id"] = "";
        $garmentlink["size_id"] = "";
        $garmentlink["modification_id"] = "";
        $garmentlink["message"] = $lang["no_items_found"];
        $garmentlink["buttontext"] = $lang["link_and_save"];
    }
}

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $garmentuser_post_id = $_POST["id"]; $page = $_POST["page"];
} elseif (isset($_GET["ref"]) && !empty($_GET["ref"])) {
    $garmentuser_post_id = $_GET["ref"]; $page = "details";
} elseif (isset($_POST["gu_id"]) && !empty($_POST["gu_id"])) {
    $garmentuser_post_id =  $_POST["gu_id"]; $page = "details";
} else {
    $garmentuser_post_id = ""; $page = $_POST["page"];
}

/** Garment data **/
$gu_data = array(
    "id" => (!empty($garmentuser_post_id)) ? $garmentuser_post_id : "",
    "gender" => (!empty($_POST["gender"])) ? $_POST["gender"] : null,
    "lockernumber" => (!empty($_POST["lockernumber"])) ? $_POST["lockernumber"] : null,
    "title" => (!empty($_POST["title"])) ? $_POST["title"] : null,
    "initials" => (!empty($_POST["initials"])) ? $_POST["initials"] : null,
    "name" => (!empty($_POST["name"])) ? ucfirst($_POST["name"]) : null,
    "intermediate" => (!empty($_POST["intermediate"])) ? $_POST["intermediate"] : null,
    "maidenname" => (!empty($_POST["maidenname"])) ? $_POST["maidenname"] : null,
    "surname" => (!empty($_POST["surname"])) ? $_POST["surname"] : null,
    "profession_id" => (!empty($_POST["profession_id"])) ? $_POST["profession_id"] : null,
    "personnelcode" => (!empty($_POST["personnelcode"])) ? $_POST["personnelcode"] : null,
    "exportcode" => (!empty($_POST["exportcode"])) ? $_POST["exportcode"] : null,
    "email" => (!empty($_POST["email"])) ? $_POST["email"] : null,
    "comments" => (!empty($_POST["comments"])) ? $_POST["comments"] : null,
    "show_comments" => (!empty($_POST["show_comments"])) ? "y" : "n",
    "code" => (!empty($_POST["code"])) ? $_POST["code"] : null,
    "code2" => (!empty($_POST["code2"])) ? $_POST["code2"] : null,
    "code3" => (!empty($_POST["code3"])) ? $_POST["code3"] : null,
    "costplace_id" => (!empty($_POST["costplace_id"])) ? $_POST["costplace_id"] : null,
    "function_id" => (!empty($_POST["function_id"])) ? $_POST["function_id"] : null,
    "active" => (!empty($_POST["active"])) ? $_POST["active"] : "1",
    "timelock" => (isset($_POST["timelock"]) && $_POST["timelockoption"] == "owntimelock") ? $_POST["timelock"] : null,
    "daysbeforelock" => (isset($_POST["daysbeforelock"]) && $_POST["blockageoption"] == "ownblockage") ? $_POST["daysbeforelock"] : null,
    "daysbeforewarning" => (isset($_POST["daysbeforewarning"]) && $_POST["warningoption"] == "ownwarning") ? $_POST["daysbeforewarning"] : null,
    "clientdepartment_id" => (!empty($_POST["clientdepartment_id"])) ? $_POST["clientdepartment_id"] : null,
    "distributor_id" => (!empty($_POST["distributor_id"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id"] : null,
    "distributor_id2" => (!empty($_POST["distributor_id2"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id2"] : null,
    "distributor_id3" => (!empty($_POST["distributor_id3"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id3"] : null,
    "distributor_id4" => (!empty($_POST["distributor_id4"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id4"] : null,
    "distributor_id5" => (!empty($_POST["distributor_id5"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id5"] : null,
    "distributor_id6" => (!empty($_POST["distributor_id6"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id6"] : null,
    "distributor_id7" => (!empty($_POST["distributor_id7"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id7"] : null,
    "distributor_id8" => (!empty($_POST["distributor_id8"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id8"] : null,
    "distributor_id9" => (!empty($_POST["distributor_id9"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id9"] : null,
    "distributor_id10" => (!empty($_POST["distributor_id10"]) && $_POST["station_bound_yesno"] == "1") ? $_POST["distributor_id10"] : null,
    "date_service_on" => $date_service_on,
    "date_service_off" => $date_service_off
);

if ($gu_data["timelock"] == "0"){ $gu_data["timelock"] = "-1"; }
if ($gu_data["daysbeforelock"] == "0"){ $gu_data["daysbeforelock"] = "-1"; }
if ($gu_data["daysbeforewarning"] == "0"){ $gu_data["daysbeforewarning"] = "-1"; }

/**
 * General actions
 */

/** Cancel distribution on request **/
if (is_numeric($garment_id_to_cancel))
{
    $sql = db_query("SELECT `scanlocations`.`id` FROM `scanlocationstatuses` INNER JOIN `scanlocations` ON `scanlocations`.`scanlocationstatus_id` = `scanlocationstatuses`.`id` AND `scanlocationstatuses`.`name` = 'disconnected_from_garmentuser'");
    $scanresult = db_fetch_row($sql);
    $scanlocation_id = $scanresult[0];

    if (db_verify_existence("garmentusers_garments", "garment_id", $garment_id_to_cancel)) {
        db_delete_where("garmentusers_garments", "garment_id", $garment_id_to_cancel);
        $g_update = array(
            "scanlocation_id" => $scanlocation_id,
            "active" => 0,
            "lastscan" => "NOW()"
        );
        db_update("garments", $garment_id_to_cancel, $g_update);
        
        $dg_data["garment_id"] = $garment_id_to_cancel;
        $dg_data["garmentuser_id"] = $gu_data["id"];
        $dg_data["comments"] = $garment_id_to_cancel_comments;
        $dg_data["date"] = "NOW()";

        db_insert("log_disconnected_garments", $dg_data);
        unset($dg_data["garment_id"]);
        unset($dg_data["garmentuser_id"]);
        unset($dg_data["comments"]);
        unset($dg_data["date"]);
    }

    db_free_result($sql);
}


/** Cancel userbound on request **/
if (is_numeric($garment_id_to_unbound)) {
    db_delete_where("garmentusers_garments", "garment_id", $garment_id_to_unbound);
    
    $garment_scanlocation_id_sql = "SELECT `garments`.`scanlocation_id`
                                FROM `garments`
                                WHERE `garments`.`id` = ".$garment_id_to_unbound;

    $garment_scanlocation_id_r = db_fetch_row(db_query($garment_scanlocation_id_sql));
    $garment_scanlocation_id = $garment_scanlocation_id_r[0];
    
    if (isset($garment_scanlocation_id) && $garment_scanlocation_id == "4") {
        $garment_fields = array("garmentuser_id" => null, "scanlocation_id" => 1);
    } else {
        $garment_fields = array("garmentuser_id" => null);
    }
    
    db_update("garments", $garment_id_to_unbound, $garment_fields);

    $sql_d = db_query("SELECT `arsimo_id` FROM `garments` WHERE `id` = " . $garment_id_to_unbound);
    $result_d = db_fetch_row($sql_d);

    //db_query("DELETE FROM `garmentusers_arsimos`
    //                   WHERE `garmentuser_id` = " . $gu_data["id"] . "
    //                     AND `arsimo_id` = " . $result_d[0] . "
    //                     AND `userbound` = 1");
}

/** Cancel linked articles on request **/
if (is_numeric($article_id_to_unbound)) {
    $delete_garmentusers_articles_sql = "
    DELETE FROM `garmentusers_articles`
          WHERE `garmentuser_id` = " . $gu_data["id"] . "
            AND `article_1_id` = " . $article_id_to_unbound;

    db_query($delete_garmentusers_articles_sql);                          
}


/**
 * Add garmentuser
 */
if (isset($page) && $page == "add")
{
    $pi["page"] = "add";
    $pi["title"] = $lang["add_garmentuser"];

    if (isset($_POST["detailssubmit"]) || isset($_POST["detailssubmitnew"]))
    {
        if (empty($circulationgroups_selected)){ $error = "1"; array_push($requiredfields, $lang["location"]); }
        if (empty($gu_data["surname"])){ $error = "1"; array_push($requiredfields, $lang["surname"]); }

        // Check code
        if (!empty($gu_data["code"]))
        {
            if (db_verify_existence("garmentusers", "code", $gu_data["code"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_passcode_already_exists_for_garmentuser"];
            }
            
            if (db_verify_existence("garmentusers", "code2", $gu_data["code"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_passcode_already_exists_for_garmentuser"];
            }
            
            if (db_verify_existence("garmentusers", "code3", $gu_data["code"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_passcode_already_exists_for_garmentuser"];
            }
        }
        
        // Check code2
        if (!empty($gu_data["code2"]))
        {
            if (db_verify_existence("garmentusers", "code", $gu_data["code2"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_passcode2_already_exists_for_garmentuser"];
            }
            
            if (db_verify_existence("garmentusers", "code2", $gu_data["code2"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_passcode2_already_exists_for_garmentuser"];
            }
            
            if (db_verify_existence("garmentusers", "code3", $gu_data["code2"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_passcode2_already_exists_for_garmentuser"];
            }
        }


	// Check code3
        if (!empty($gu_data["code3"]))
        {
            if (db_verify_existence("garmentusers", "code", $gu_data["code3"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_passcode3_already_exists_for_garmentuser"];
            }
            
            if (db_verify_existence("garmentusers", "code2", $gu_data["code3"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_passcode3_already_exists_for_garmentuser"];
            }
            
            if (db_verify_existence("garmentusers", "code3", $gu_data["code3"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_passcode3_already_exists_for_garmentuser"];
            }
        }

        // Check personnelcode
        if (!empty($gu_data["personnelcode"]))
        {
            if (db_verify_existence("garmentusers", "personnelcode", $gu_data["personnelcode"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_personelcode_already_exists_for_garmentuser"];
            }
        }
        
        // Check exportcode
        if (!empty($gu_data["exportcode"]))
        {
            if (db_verify_existence("garmentusers", "exportcode", $gu_data["exportcode"], "deleted_on"))
            {
                $error = "1";
                $existence_note = $lang["the_laundrynumber_already_exists_for_garmentuser"];
            }
        }
        
        // Check alternative sizes
        if ($alternatives == 1 && !empty($sizes_selected)) foreach ($sizes_selected as $article => $size) {
            if ($error == "0"
                && !empty($size)
                && !empty($alt_sizes_selected[$article])
                && $size == $alt_sizes_selected[$article]
                && ((empty($modifications_selected[$article]) && empty($alt_modifications_selected[$article]))
                        || (!empty($modifications_selected[$article]) && !empty($alt_modifications_selected[$article]) && $modifications_selected[$article] == $alt_modifications_selected[$article]))) {
                    $error = "1";
                    $existence_note = $lang["the_alternative_cannot_be_the_same_as_the_first_choice"];
            }
        }

        if ($error == "0")
        {
            /** Insert the given garmentuser **/
            db_insert("garmentusers", $gu_data);

            /** Set to use the last inserted garmentuser id **/
            $garmentusers_last_insert_id = db_fetch_row(db_read_last_insert_id());
            $garmentuser_id = $garmentusers_last_insert_id[0];

            /** insert the garmentusers arsimos **/
            if (!empty($sizes_selected)) foreach ($sizes_selected as $article => $size)
            {
                if (!empty($size))
                {
                    $arsimos_conditions["where"]["1"] = "article_id = " . $article;
                    $arsimos_conditions["where"]["2"] = "size_id = " . $size;
                    $arsimos_conditions["where"]["3"] = "modification_id " . ((!empty($modifications_selected[$article])) ? "= " . $modifications_selected[$article] : "is NULL");
                    $arsimos_resource = db_read("arsimos", "id", $arsimos_conditions);
                    $arsimo = db_fetch_row($arsimos_resource);
                    $data["garmentuser_id"] = $garmentuser_id;
                    $data["arsimo_id"] = $arsimo[0];
                    $data["enabled"] = (!empty($articles_selected[$article])) ? 1 : 0;
                    $data["userbound"] = 0;
                    $data["max_credit"] = (!empty($count[$article])) ? $count[$article] : 2;
                    $data["max_positions"] = null;

                    $alt_arsimos_conditions["where"]["1"] = "article_id = " . $article;
                    $alt_arsimos_conditions["where"]["2"] = "size_id = " . $alt_sizes_selected[$article];
                    $alt_arsimos_conditions["where"]["3"] = "modification_id " . ((!empty($alt_modifications_selected[$article])) ? "= " . $alt_modifications_selected[$article] : "is NULL");
                    $alt_arsimos_resource = db_read("arsimos", "id", $alt_arsimos_conditions);
                    $alt_arsimo = db_fetch_row($alt_arsimos_resource);
                    $data["alt_arsimo_id"] = $alt_arsimo[0];                   

                    db_insert("garmentusers_arsimos", $data);
                    unset($data["garmentuser_id"]);
                    unset($data["arsimo_id"]);
                    unset($data["enabled"]);
                    unset($data["userbound"]);
                    unset($data["max_credit"]);
                    unset($data["max_positions"]);
                    unset($data["alt_arsimo_id"]);
                }
            }

            /** Insert the garmentusers circulationgroups **/
            if (!empty($circulationgroups_selected)) foreach ($circulationgroups_selected as $num => $circulationgroup_id)
            {
                $data["garmentuser_id"] = $garmentuser_id;
                $data["circulationgroup_id"] = $circulationgroup_id;
                db_insert("circulationgroups_garmentusers", $data);
                unset($data["garmentuser_id"]);
                unset($data["circulationgroup_id"]);
            }
        }

        if (!isset($_POST["detailssubmitnew"]) && $error == "0")
        {
            redirect($pi["filename_list"]);
        }
    }
}


/**
 * Add garmentuser + new
 */
if (isset($page) && $page == "add")
{
    if ($error == "0")
    {
        /** Create new **/
        if (isset($_POST["detailssubmitnew"]))
        {
            unset($gu_data);
            $alternatives = "1";
            $garment["enabled"] = "2";

            /** Normal garments **/
            $circulationgroups_selected = array();
            $circulationgroups_name_selected = array();
            $articles_selected = array();
            $sizes_selected = array();
            $modifications_selected = array();
            $alt_sizes_selected = array();
            $alt_modifications_selected = array();
            $count = array();

            /** User garments **/
            $user_articles_selected = array();
            $user_count = array();
        }
    }
}

/**
 * Garmentuser details
 */
if ($page == "details" && !empty($garmentuser_post_id))
{
    $pi["page"] = "details";
    $pi["title"] = $lang["garmentuser_details"];

    $garmentuser_id = trim($garmentuser_post_id);

    $gu_data_db = db_fetch_assoc(db_read_row_by_id("garmentusers", $garmentuser_id));

    if (isset($_POST["editsubmit"]))
    {
        $gu_data = array_merge($gu_data_db, $gu_data);
    }
    else
    {
        $gu_data = array_merge($gu_data, $gu_data_db);
    }

    //Set garmentuser name for subtitle
    $fn = "";
    if (!empty($gu_data["name"])) {
        $fn .= ucfirst($gu_data["name"]) ." ";
    } else if (!empty($gu_data["initials"])) {
        $fn .= strtoupper($gu_data["initials"]) ." ";
    }
    if (!empty($gu_data["intermediate"])) {
        $fn .= $gu_data["intermediate"] ." ";
    }
    $fn .= $gu_data["surname"];
    
    $pi["subtitle"] = $fn;
    
    /** We need the id for toolbar buttons **/
    $urlinfo["id"] = $garmentuser_id;

    if (isset($_POST["undelete"]) && $_POST["undelete"] == "1")
    {
        // Check code 
        $q = "SELECT COUNT(*)
            FROM `garmentusers`
           WHERE `code` = (SELECT `code` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
             AND `deleted_on` IS NULL
             AND `id` != " . $garmentuser_id;
        $s = db_fetch_num(db_query($q));
        $c = $s[0];

        if(isset($c) && $c > 0) {
            $error = "1";
            $existence_note = $lang["the_passcode_already_exists_for_garmentuser"];
        }
        
        // Check code2 
        $q = "SELECT COUNT(*)
            FROM `garmentusers`
           WHERE `code2` = (SELECT `code` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
             AND `deleted_on` IS NULL
             AND `id` != " . $garmentuser_id;
        $s = db_fetch_num(db_query($q));
        $c = $s[0];

        if(isset($c) && $c > 0) {
            $error = "1";
            $existence_note = $lang["the_passcode_already_exists_for_garmentuser"];
        }

	// Check code3
        $q = "SELECT COUNT(*)
            FROM `garmentusers`
           WHERE `code3` = (SELECT `code` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
             AND `deleted_on` IS NULL
             AND `id` != " . $garmentuser_id;
        $s = db_fetch_num(db_query($q));
        $c = $s[0];
        
        if(isset($c) && $c > 0) {
            $error = "1";
            $existence_note = $lang["the_passcode_already_exists_for_garmentuser"];
        }
        
        // Check code 
        $q = "SELECT COUNT(*)
            FROM `garmentusers`
           WHERE `code` = (SELECT `code2` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
             AND `deleted_on` IS NULL
             AND `id` != " . $garmentuser_id;
        $s = db_fetch_num(db_query($q));
        $c = $s[0];

        if(isset($c) && $c > 0) {
            $error = "1";
            $existence_note = $lang["the_passcode2_already_exists_for_garmentuser"];
        }
        
        // Check code2 
        $q = "SELECT COUNT(*)
            FROM `garmentusers`
           WHERE `code2` = (SELECT `code2` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
             AND `deleted_on` IS NULL
             AND `id` != " . $garmentuser_id;
        $s = db_fetch_num(db_query($q));
        $c = $s[0];

        if(isset($c) && $c > 0) {
            $error = "1";
            $existence_note = $lang["the_passcode2_already_exists_for_garmentuser"];
        }

	// Check code3
        $q = "SELECT COUNT(*)
            FROM `garmentusers`
           WHERE `code3` = (SELECT `code2` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
             AND `deleted_on` IS NULL
             AND `id` != " . $garmentuser_id;
        $s = db_fetch_num(db_query($q));
        $c = $s[0];

        if(isset($c) && $c > 0) {
            $error = "1";
            $existence_note = $lang["the_passcode2_already_exists_for_garmentuser"];
        }
        
        // Check code 
        $q = "SELECT COUNT(*)
            FROM `garmentusers`
           WHERE `code` = (SELECT `code3` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
             AND `deleted_on` IS NULL
             AND `id` != " . $garmentuser_id;
        $s = db_fetch_num(db_query($q));
        $c = $s[0];

        if(isset($c) && $c > 0) {
            $error = "1";
            $existence_note = $lang["the_passcode3_already_exists_for_garmentuser"];
        }
        
        // Check code2 
        $q = "SELECT COUNT(*)
            FROM `garmentusers`
           WHERE `code2` = (SELECT `code3` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
             AND `deleted_on` IS NULL
             AND `id` != " . $garmentuser_id;
        $s = db_fetch_num(db_query($q));
        $c = $s[0];

        if(isset($c) && $c > 0) {
            $error = "1";
            $existence_note = $lang["the_passcode3_already_exists_for_garmentuser"];
        }

	// Check code3
        $q = "SELECT COUNT(*)
            FROM `garmentusers`
           WHERE `code3` = (SELECT `code3` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
             AND `deleted_on` IS NULL
             AND `id` != " . $garmentuser_id;
        $s = db_fetch_num(db_query($q));
        $c = $s[0];
        
        if(isset($c) && $c > 0) {
            $error = "1";
            $existence_note = $lang["the_passcode3_already_exists_for_garmentuser"];
        }
        
        if ($error == "0") {
            // Check exportcode 
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `exportcode` = (SELECT `exportcode` FROM `garmentusers` WHERE `id` = " . $garmentuser_id . " )
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_laundrynumber_already_exists_for_garmentuser"];
            }
        }
        
        if ($error == "0") {
            db_update("garmentusers", $garmentuser_id, array("active" => 1, "deleted_on" => null));
            db_update("garmentusers_arsimos", $garmentuser_id, array("enabled" => 1));
            redirect($pi["filename_list"]);
        }
    }
    elseif (!isset($_POST["detailssubmit"]))
    {
        if(empty($circulationgroups_selected)) {
            /** Selected circulationgroups **/
            $circulationgroups_selected_conditions["left_join"]["1"] = "circulationgroups circulationgroups.id circulationgroups_garmentusers.circulationgroup_id";
            $circulationgroups_selected_conditions["where"]["1"] = "circulationgroups_garmentusers.garmentuser_id = " . $garmentuser_post_id;
            $circulationgroups_selected_resource = db_read("circulationgroups_garmentusers", "circulationgroups.id circulationgroups.name", $circulationgroups_selected_conditions);
            if (!empty($circulationgroups_selected_resource) && db_num_rows($circulationgroups_selected_resource))
            {
                $i = 0;
                while ($row = db_fetch_num($circulationgroups_selected_resource))
                {
                    $circulationgroups_selected[$i] = $row[0];
                    $circulationgroups_name_selected[$row[0]] = $row[1];
                    $i++;
                }
            }
        }
        if (!empty($gu_data["service_date_on"])){ $gu_data["service_date_on"] = date_en_to_nl($gu_data["service_date_on"]); }
        if (!empty($gu_data["service_date_off"])){ $gu_data["service_date_off"] = date_en_to_nl($gu_data["service_date_off"]); }
    }
    elseif (isset($_POST["detailssubmit"]))
    {
        if (empty($circulationgroups_selected)){ $error = "1"; array_push($requiredfields, $lang["location"]); }
        if (empty($gu_data["surname"])){ $error = "1"; array_push($requiredfields, $lang["surname"]); }

        // Check code
        if (!empty($gu_data["code"])) {
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `code` = '" . $gu_data["code"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_passcode_already_exists_for_garmentuser"];
            }
            
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `code2` = '" . $gu_data["code"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_passcode_already_exists_for_garmentuser"];
            }
            
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `code3` = '" . $gu_data["code"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_passcode_already_exists_for_garmentuser"];
            }
        }
        
        // Check code2
        if (!empty($gu_data["code2"])) {
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `code` = '" . $gu_data["code2"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_passcode2_already_exists_for_garmentuser"];
            }
            
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `code2` = '" . $gu_data["code2"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_passcode2_already_exists_for_garmentuser"];
            }
            
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `code3` = '" . $gu_data["code2"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_passcode2_already_exists_for_garmentuser"];
            }
        }

	// Check code3
        if (!empty($gu_data["code3"])) {
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `code` = '" . $gu_data["code3"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_passcode3_already_exists_for_garmentuser"];
            }
            
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `code2` = '" . $gu_data["code3"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_passcode3_already_exists_for_garmentuser"];
            }
            
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `code3` = '" . $gu_data["code3"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_passcode3_already_exists_for_garmentuser"];
            }
        }
        
        // Check exportcode
        if ($error == "0" && !empty($gu_data["exportcode"])) {
            $q = "SELECT COUNT(*)
                FROM `garmentusers`
               WHERE `exportcode` = '" . $gu_data["exportcode"] . "'
                 AND `deleted_on` IS NULL
                 AND `id` != " . $garmentuser_id;
            $s = db_fetch_num(db_query($q));
            $c = $s[0];

            if(isset($c) && $c > 0) {
                $error = "1";
                $existence_note = $lang["the_laundrynumber_already_exists_for_garmentuser"];
            }   
        }
        
        // Check alternative sizes
        if ($error == "0" && $alternatives == 1 && !empty($sizes_selected)) foreach ($sizes_selected as $article => $size) {
            if ($error == "0"
                && !empty($size)
                && !empty($alt_sizes_selected[$article])
                && $size == $alt_sizes_selected[$article]
                && ((empty($modifications_selected[$article]) && empty($alt_modifications_selected[$article]))
                        || (!empty($modifications_selected[$article]) && !empty($alt_modifications_selected[$article]) && $modifications_selected[$article] == $alt_modifications_selected[$article]))) {
                    $error = "1";
                    $existence_note = $lang["the_alternative_cannot_be_the_same_as_the_first_choice"];
            }
        }
        
        
        if ($error == "0") {

            /** Update the circulationgroups_garmentusers **/
            db_delete_where("circulationgroups_garmentusers", "garmentuser_id", $garmentuser_id);

            if (!empty($circulationgroups_selected)) foreach ($circulationgroups_selected as $num => $circulationgroup_id) {
                $data["garmentuser_id"] = $garmentuser_id;
                $data["circulationgroup_id"] = $circulationgroup_id;
                db_insert("circulationgroups_garmentusers", $data);
                unset($data["garmentuser_id"]);
                unset($data["circulationgroup_id"]);
            }

            /** Update the garmentusers arsimos **/
            db_delete_where("garmentusers_arsimos", "garmentuser_id", $garmentuser_id);

            /** Normal garments **/
            if (!empty($sizes_selected)) foreach ($sizes_selected as $article => $size)
            {
                if (!empty($size))
                {
                    $arsimos_conditions["where"]["1"] = "article_id = " . $article;
                    $arsimos_conditions["where"]["2"] = "size_id = " . $size;
                    $arsimos_conditions["where"]["3"] = "modification_id " . ((!empty($modifications_selected[$article])) ? "= " . $modifications_selected[$article] : "is NULL");
                    $arsimos_resource = db_read("arsimos", "id", $arsimos_conditions);
                    $arsimo = db_fetch_num($arsimos_resource);
                    $data["garmentuser_id"] = $garmentuser_id;
                    $data["arsimo_id"] = $arsimo[0];
                    $data["enabled"] = (!empty($articles_selected[$article])) ? 1 : 0;
                    $data["userbound"] = 0;
                    $data["max_positions"] = null;
                    $data["max_credit"] = (!empty($count[$article]) && $count[$article] > 0) ? $count[$article] : 2;

                    $alt_arsimos_conditions["where"]["1"] = "article_id = " . $article;
                    $alt_arsimos_conditions["where"]["2"] = "size_id = " . $alt_sizes_selected[$article];
                    $alt_arsimos_conditions["where"]["3"] = "modification_id " . ((!empty($alt_modifications_selected[$article])) ? "= " . $alt_modifications_selected[$article] : "is NULL");
                    $alt_arsimos_resource = db_read("arsimos", "id", $alt_arsimos_conditions);
                    $alt_arsimo = db_fetch_row($alt_arsimos_resource);
                    $data["alt_arsimo_id"] = $alt_arsimo[0];

                    db_insert("garmentusers_arsimos", $data);

                    unset($data["garmentuser_id"]);
                    unset($data["arsimo_id"]);
                    unset($data["enabled"]);
                    unset($data["userbound"]);
                    unset($data["max_positions"]);
                    unset($data["max_credit"]);
                    unset($data["alt_arsimo_id"]);

                }
            }
 
            db_delete_where("garmentusers_userbound_arsimos", "garmentuser_id", $garmentuser_id);
            
            /** Userbound garments -- OLD
            foreach ($user_articles_id as $user_arsimo_id)
            {
                if (!empty($user_arsimo_id))
                {
                    $data["garmentuser_id"] = $garmentuser_id;
                    $data["arsimo_id"] = $user_arsimo_id;
                    $data["enabled"] = (!empty($user_articles_selected[$user_arsimo_id])) ? 1 : 0;
                    $data["userbound"] = 1;
                    $data["max_positions"] = (!empty($user_count[$user_arsimo_id]) && $user_count[$user_arsimo_id] > 0) ? $user_count[$user_arsimo_id] : null;
                    $data["max_credit"] = null;

                    db_insert("garmentusers_arsimos", $data);

                    unset($data["garmentuser_id"]);
                    unset($data["arsimo_id"]);
                    unset($data["enabled"]);
                    unset($data["userbound"]);
                    unset($data["max_positions"]);
                    unset($data["max_credit"]);
                }
            } **/
            
            foreach($user_count as $user_circulationgroup_id => $tarray)
            {
                foreach($tarray as $user_arsimo_id => $user_max_positions)     
                {
                   if (!empty($user_arsimo_id) && !empty($user_max_positions) && $user_max_positions > 0)
                   {
                        $data["garmentuser_id"] = $garmentuser_id;
                        $data["arsimo_id"] = $user_arsimo_id;
                        $data["circulationgroup_id"] = $user_circulationgroup_id;
                        $data["enabled"] = (!empty($user_articles_selected[$user_arsimo_id])) ? 1 : 0;
                        $data["max_positions"] = $user_max_positions;
                        
                        db_insert("garmentusers_userbound_arsimos", $data);
                        unset($data["garmentuser_id"]);
                        unset($data["arsimo_id"]);
                        unset($data["enabled"]);
                        unset($data["max_positions"]);
                   }
                }
            }
            
            db_update("garmentusers", $garmentuser_id, $gu_data);
            redirect($pi["filename_list"]);
        }

    }

    if (isset($_POST["delete"]) && $_POST["delete"] == "yes")
    {
        if (isset($_POST["confirmed"]))
        {
            db_update("garmentusers", $garmentuser_id, array("active" => 2, "deleted_on" => "NOW()"));
            db_update("garmentusers_arsimos", $garmentuser_id, array("enabled" => 0));

            // Redirect to list
            redirect($pi["filename_list"]);
        }
        elseif (!isset($_POST["abort"]))
        {
            $pi["note"] = html_delete($garmentuser_id, $lang["garmentuser"]);
            
            $q = "SELECT COUNT(*) FROM `garmentusers_garments` WHERE `garmentuser_id` = ". $garmentuser_id ." AND `superuser_id` = 0";
            $s = db_fetch_num(db_query($q));
            $cg = $s[0];
            
            if(isset($cg) && $cg > 0) {
                $cg_warning .= $lang["garments_in_use"].": ".$cg." </br>";
            }
            
            $q2 = "SELECT COUNT(*) FROM `distributors_load` `d`
                  INNER JOIN `garments` `g` ON `d`.`garment_id` = `g`.`id`
                  WHERE ISNULL(`g`.`deleted_on`) AND `g`.`garmentuser_id` = ". $garmentuser_id;
            $s2 = db_fetch_num(db_query($q2));
            $cg2 = $s2[0];
            
            if(isset($cg2) && $cg2 > 0) {
                $cg_warning .= $lang["__Report_current_load__loaded_userbound_garments"].": ".$cg2;
            }
            
            if(isset($cg_warning)) {
                $pi["note"] .= html_warning($cg_warning);
            }
        }
    }

}

// Required for articlelink selectbox: all articles related by profession
$articlelink_articles_columns = "articles.id articles.description";
$articlelink_articles_conditions["where"]["1"] = "garmentprofiles.profession_id = " . $gu_data["profession_id"];
$articlelink_articles_conditions["left_join"]["1"] = "garmentprofiles articles.id garmentprofiles.article_id";
$articlelink_articles_conditions["group_by"] = "articles.id";
$articlelink_articles_conditions["order_by"] = "articles.description";
$articlelink_articles_resource = db_read("articles", $articlelink_articles_columns, $articlelink_articles_conditions);
while ($row = db_fetch_num($articlelink_articles_resource)) {
    $articlelink_articles[$row[0]] = $row[1];
}
    

/** Article koppelen **/
if (isset($_POST["articlelinksubmit"]))
{
    if (empty($articlelink["article_1_id"]))
    {
        array_push($requiredfields, $lang["article"]);
    } elseif (empty($articlelink["article_2_id"])) {
        array_push($requiredfields, $lang["article"]);
    } elseif ($articlelink["article_1_id"] == $articlelink["article_2_id"]) {
        $existence_note = $lang["the_article_2_cannot_be_the_same_as_the_article_1"];
    }
    else
    {
        $q1 = "SELECT COUNT(*) FROM `garmentusers_articles` WHERE `garmentuser_id` = ". $garmentuser_id ." AND (`article_1_id` = ". $articlelink["article_1_id"] ." OR `article_2_id` = ". $articlelink["article_1_id"] .")";
        $s1 = db_fetch_num(db_query($q1));
        $ga1 = $s1[0];
        
        $q2 = "SELECT COUNT(*) FROM `garmentusers_articles` WHERE `garmentuser_id` = ". $garmentuser_id ." AND (`article_1_id` = ". $articlelink["article_2_id"] ." OR `article_2_id` = ". $articlelink["article_2_id"] .")";
        $s2 = db_fetch_num(db_query($q2));
        $ga2 = $s2[0];

        if(isset($ga1) && $ga1 > 0) {
            $existence_note = $lang["the_article_1_already_linked"];
        } elseif(isset($ga2) && $ga2 > 0) {
            $existence_note = $lang["the_article_2_already_linked"];
        } else {
            $ga["garmentuser_id"] = $garmentuser_id;
            $ga["article_1_id"] = $articlelink["article_1_id"];
            $ga["article_2_id"] = $articlelink["article_2_id"];
            $ga["extra_credit"] = $articlelink["extra_credit"];
            $ga["combined_credit"] = $articlelink["combined_credit"];

            db_insert("garmentusers_articles", $ga);

            unset($ga["garmentuser_id"]);
            unset($ga["article_1_id"]);
            unset($ga["article_2_id"]);
            unset($ga["extra_credit"]);
            unset($ga["combined_credit"]);

            $articlelink = array(
                "article_1_id" => null,
                "article_2_id" => null,
                "extra_credit" => "0",
                "combined_credit" => "0"
            );
        }
    }
}

if (!empty($requiredfields))
{
    $pi["note"] = html_requiredfields($requiredfields);
}
elseif (!empty($existence_note))
{
    $pi["note"] = html_requirednote($existence_note);
}

/** Collect page content **/
$articles_all = array();
$articles_all_credit = array();
$garmentusers_articles = array();
$articles_eachart_sizes = array();
$eachart_sizes = array();
$modifications_all = array();
$alt_modifications_all = array();

/** Userbound garments **/
$user_articles_all = array();

/** Garment koppelen **/
if (isset($_POST["garmentlinksubmit"]))
{
    if (empty($garmentlink["tag"]))
    {
        array_push($requiredfields, $lang["tag"]);
    }
    else
    {
        $garment_id = tag_to_garment_id($garmentlink["tag"]);
        if (!$garment_id)
        {
            $arsimo_conditions["where"]["1"] = "article_id = ". $garmentlink["article_id"];
            $arsimo_conditions["where"]["2"] = "size_id = ". $garmentlink["size_id"];
            $arsimo_conditions["where"]["3"] = "modification_id ". ((!empty($garmentlink["modification_id"])) ? "= " . $garmentlink["modification_id"] : "is NULL");
            $arsimo_data = db_read("arsimos", "id", $arsimo_conditions);
            $arsimo_id = db_fetch_num($arsimo_data);
            $bindingdata["arsimo_id"] = $arsimo_id[0];
            $bindingdata["circulationgroup_id"] = $garmentlink["circulationgroup_id"];
            $bindingdata["garmentuser_id"] = $garmentuser_id;
            $bindingdata["tag"] = $garmentlink["tag"];

            /** Insert the given garment **/
            db_insert("garments", $bindingdata);
            $ga["garmentuser_id"] = $garmentuser_id;
            $ga["arsimo_id"] = $bindingdata["arsimo_id"];
            $ga["enabled"] = 1;
            $ga["max_positions"] = 2;
            $ga["userbound"] = 1;

            db_insert("garmentusers_arsimos", $ga);

            unset($bindingdata["arsimo_id"]);
            unset($bindingdata["circulationgroup_id"]);
            unset($bindingdata["garmentuser_id"]);
            unset($bindingdata["tag"]);

            unset($ga["garmentuser_id"]);
            unset($ga["arsimo_id"]);
            unset($ga["enabled"]);
            unset($ga["max_positions"]);
            unset($ga["userbound"]);
        }
        else
        {
            $garment_conditions["where"]["1"] = "id = ". $garment_id;
            $garment_data = db_read("garments", "arsimo_id", $garment_conditions);
            $arsimo_id = db_fetch_num($garment_data);
            $ga["arsimo_id"] = $arsimo_id[0];
            $ga["garmentuser_id"] = $garmentuser_id;
            $ga["enabled"] = 1;
            $ga["max_positions"] = 2;
            $ga["userbound"] = 1;

            db_insert("garmentusers_arsimos", $ga);

            unset($ga["garmentuser_id"]);
            unset($ga["arsimo_id"]);
            unset($ga["enabled"]);
            unset($ga["max_positions"]);
            unset($ga["userbound"]);

            db_query("UPDATE `garments` SET `garmentuser_id` = ". $garmentuser_id ." WHERE `id` = ". $garment_id ." LIMIT 1") or die("ERROR LINE ". __LINE__);
        }

        db_query("UPDATE `garmentusers` SET `active` = 1 WHERE `id` = ". $garmentuser_id ." LIMIT 1") or die("ERROR LINE ". __LINE__);

        $garmentlink = array(
            "enabled" => "2",
            "tag" => null,
            "circulationgroup_id" => null,
            "article_id" => null,
            "size_id" => null,
            "modification_id" => null
        );

    }
}

include("garmentuser_details_tab1.php");
include("garmentuser_details_tab2.php");
include("garmentuser_details_tab3.php");
include("garmentuser_details_tab4.php");


// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);

if ($page !== "add")
{
    $user_garments_left_join_sql_query = "";
    
    /** User garments **/
    $user_garments_sql_query = "
        SELECT DISTINCT
            `arsimos`.`id` AS 'arsimo_id',
            `articles`.`description` AS 'article',
            `sizes`.`name` AS 'size',
            `modifications`.`name` AS 'modifications',
            `garmentusers_arsimos`.`enabled`,
            `garmentusers_arsimos`.`max_positions`,
            `scanlocations`.`name` AS 'status'";
    
    if (!empty($circulationgroups_all_active)) {
        foreach ($circulationgroups_all_active as $circulationgroup_id => $circulationgroup_name) {
            
            $user_garments_sql_query .= ",
                                        `gua_". $circulationgroup_id ."`.`enabled` AS 'gua_". $circulationgroup_id ."_enabled',
                                        IF(ISNULL(gua_". $circulationgroup_id .".max_positions),0,gua_". $circulationgroup_id .".max_positions) AS 'gua_". $circulationgroup_id ."_max_positions'";
            
            $user_garments_left_join_sql_query .= " LEFT JOIN `garmentusers_userbound_arsimos` gua_". $circulationgroup_id ." ON gua_". $circulationgroup_id .".garmentuser_id = garmentusers.id"
                    . " AND gua_". $circulationgroup_id .".arsimo_id = arsimos.id AND gua_". $circulationgroup_id .".circulationgroup_id = ". $circulationgroup_id;
        }
    }
    
    $user_garments_sql_query .= " FROM `garmentusers`
 INNER JOIN `garments` ON `garments`.`garmentuser_id` = `garmentusers`.`id`
 INNER JOIN `arsimos` ON `garments`.`arsimo_id` = `arsimos`.`id`
 INNER JOIN `articles` ON `articles`.`id` = `arsimos`.`article_id`
 INNER JOIN `sizes` ON `sizes`.`id` = `arsimos`.`size_id`
  LEFT JOIN `scanlocations` ON `scanlocations`.`id` = `garments`.`scanlocation_id`
  LEFT JOIN `modifications` ON `modifications`.`id` = `arsimos`.`modification_id`
  LEFT JOIN `garmentusers_arsimos` ON `garmentusers_arsimos`.`garmentuser_id` = `garmentusers`.`id` AND `garmentusers_arsimos`.`arsimo_id` = `arsimos`.`id` AND `garmentusers_arsimos`.`userbound` = 1 ";
    
    
    $user_garments_sql_query .= $user_garments_left_join_sql_query . " WHERE `garmentusers`.`id` = ". $garmentuser_id ."
        AND `garments`.`deleted_on` IS NULL
    ORDER BY `articles`.`description`, `sizes`.`position`, `modifications`.`name`";

    $user_garments_sql = db_query($user_garments_sql_query);
 
    //required for status (only in case we are editing an existing garment)
    if (!empty($garment_post_id))
    {
        $status_conditions["where"]["1"] = "id = " . $detailsdata["scanlocation_id"];
        $status = db_fetch_num(db_read("scanlocations", "name", $status_conditions));
        $status = $status[0];
    } else {
        $status = null;
    }

    while ($user_row = db_fetch_assoc($user_garments_sql)){
        $user_articles_all[$user_row["arsimo_id"]] = $user_row;
    }

}


// Required for selectbox: all articles related by profession (thru garmentprofiles)
if (!empty($gu_data["profession_id"]))
{
    $articles_all_columns = "articles.id articles.description articles.credit";
    $articles_all_conditions["left_join"]["1"] = "articles garmentprofiles.article_id articles.id";
    $articles_all_conditions["group_by"] = "articles.id";
    $articles_all_conditions["order_by"] = "articles.description";
    $articles_all_conditions["where"]["1"] = "garmentprofiles.profession_id = " . $gu_data["profession_id"];
    $articles_all_resource = db_read("garmentprofiles", $articles_all_columns, $articles_all_conditions);

    while ($row = db_fetch_num($articles_all_resource)) {
        $articles_all[$row[0]] = $row[1];
        $articles_all_credit[$row[0]] = $row[2];
        
        $sql = db_query("SELECT `id` FROM `garmentusers_articles`"
                . "WHERE (`article_1_id` = ". $row[0] ." OR `article_2_id` = ". $row[0] .") AND `garmentuser_id` = ". $garmentuser_id);
        $result = db_fetch_row($sql);
        $garmentusers_articles[$row[0]] = $result[0];
    }

    if ($page !== "add")
    {
        /** Get profession timelock and blockage **/
        $mc_profession_query = "SELECT `timelock`, `daysbeforewarning`, `daysbeforelock` FROM `professions` WHERE `id` = ". $gu_data["profession_id"] ." LIMIT 1";
        $mc_profession_sql = db_query($mc_profession_query) or die("ERROR LINE ". __LINE__);
        if (db_num_rows($mc_profession_sql) > 0)
        {
            $mc_prof = db_fetch_assoc($mc_profession_sql);
            $profession_timelock = $mc_prof["timelock"];
            $profession_warning = $mc_prof["daysbeforewarning"];
            $profession_blockage = $mc_prof["daysbeforelock"];
        }
        db_free_result($mc_profession_sql);
    }

}

// Required for selectboxes: sizes for every relevant article
if (!empty($articles_all)) foreach ($articles_all as $id => $name)
{
    $eachart_resource_sql_query = "SELECT a.size_id,
        s.name AS name
        FROM arsimos a
        LEFT JOIN sizes s ON a.size_id = s.id
        WHERE a.article_id = " . $id . " 
        AND a.deleted_on IS NULL
        GROUP BY a.id
        ORDER BY s.position";

    $eachart_resource_sql = db_query($eachart_resource_sql_query);
 
    while ($row = db_fetch_assoc($eachart_resource_sql)){
        $articles_eachart_sizes[$id][$row["size_id"]] = $row["name"];
    }
} else {
    $articles_eachart_sizes = null;
}


// Now lets see what article/size/modification should be selected for each article, only when editing an existing garmentuser
if (!empty($garmentuser_post_id) && !$sizes_selected)
{
    $selart_columns = "arsimos.article_id arsimos.size_id arsimos.modification_id garmentusers_arsimos.max_credit garmentusers_arsimos.enabled";
    $selart_conditions["left_join"]["1"] = "arsimos garmentusers_arsimos.arsimo_id arsimos.id";
    $selart_conditions["where"]["1"] = "garmentusers_arsimos.garmentuser_id = " . $garmentuser_id;
    $selart_conditions["where"]["2"] = "garmentusers_arsimos.userbound = 0";
    $selart_resource = db_read("garmentusers_arsimos", $selart_columns, $selart_conditions);

    while ($row = db_fetch_num($selart_resource))
    {
        $sizes_selected[$row[0]] = $row[1];
        $articles_selected[$row[0]] = (!empty($row[4]) && $row[4] > 0) ? 1 : 0;
        $modifications_selected[$row[0]] = (!empty($row[2]) && $row[2] > 0) ? $row[2] : 0;
        $count[$row[0]] = $row[3];
    }

    $alt_selart_columns = "arsimos.article_id arsimos.size_id arsimos.modification_id garmentusers_arsimos.max_credit";
    $alt_selart_conditions["left_join"]["1"] = "arsimos garmentusers_arsimos.alt_arsimo_id arsimos.id";
    $alt_selart_conditions["where"]["1"] = "garmentusers_arsimos.garmentuser_id = " . $garmentuser_id;
    $alt_selart_resource = db_read("garmentusers_arsimos", $alt_selart_columns, $alt_selart_conditions);

    while ($row = db_fetch_num($alt_selart_resource))
    {
        $alt_sizes_selected[$row[0]] = $row[1];
        $alt_modifications_selected[$row[0]] = (!empty($row[2]) && $row[2] > 0) ? $row[2] : 0;
    }
}

$sql = db_query("SELECT `scanlocations`.`id` FROM `scanlocationstatuses`"
            . " INNER JOIN `scanlocations` ON `scanlocations`.`scanlocationstatus_id` = `scanlocationstatuses`.`id`"
                   . " AND `scanlocationstatuses`.`name` = 'disconnected_from_garmentuser'");
$scanresult = db_fetch_row($sql);
$disc_scanlocation = $scanresult[0];

// Required for selectboxes: modifications
if (!empty($sizes_selected))
{
    foreach ($sizes_selected as $article_id => $size_id)
    {
        if (!empty($article_id) && !empty($size_id))
        {
            $modifications_all_conditions["left_join"]["1"] = "modifications modifications.id arsimos.modification_id";
            $modifications_all_conditions["where"]["1"] = "arsimos.article_id = " . $article_id;
            $modifications_all_conditions["where"]["2"] = "arsimos.size_id = " . $size_id;
            $modifications_all_conditions["where"]["3"] = "arsimos.modification_id isnot NULL";
            $modifications_all_conditions["where"]["4"] = "arsimos.deleted_on is null";
            $modifications_all_data = db_read("arsimos", "arsimos.modification_id modifications.name", $modifications_all_conditions);

            if (!empty($modifications_all_data))
            {
                while ($row = db_fetch_num($modifications_all_data))
                {
                    $modifications_all[$article_id][$size_id][$row[0]] = $row[1];
                }
            } else {
                $modifications_all = null;
            }
            
            if($alternatives == 1 && !empty($_POST["alt_sizes_selected"])
                    && (empty($alt_sizes_selected[$article_id])
                            || $select_article_id ==  $article_id)
                    && $sizes_selected[$article_id] != $sizes_selected_history[$article_id]) {
                $q = "SELECT IF(ISNULL(a2.size_id),s2.size_id,a2.size_id)
                      FROM arsimos a
                      LEFT JOIN alt_arsimos ar ON ar.arsimo_id = a.id
                      LEFT JOIN arsimos a2 ON a2.id = ar.alt_arsimo_id
                      LEFT JOIN (SELECT a.article_id, a.size_id
                      FROM arsimos a
                      LEFT JOIN sizes s ON s.id = a.size_id
                      WHERE a.article_id = ".$article_id." 
                      AND a.deleted_on IS NULL
                      AND s.position > (SELECT position FROM sizes WHERE id = ".$size_id.")
                      ORDER BY s.position
                      LIMIT 1) s2 ON s2.article_id = a.article_id
                      WHERE a.article_id = ".$article_id." 
                      AND a.deleted_on IS NULL
                      AND a.size_id = ".$size_id."
                      ";
                $s = db_fetch_num(db_query($q));
                $alt_sizes_selected[$article_id] = $s[0];
                $alt_modifications_selected[$article_id] = null;
            }
        }
    }
} else {
    $modifications_all = null;
}

// Required for selectboxes: alt modifications
if (!empty($alt_sizes_selected))
{
    foreach ($alt_sizes_selected as $alt_article_id => $alt_size_id)
    {
        if (!empty($alt_article_id) && !empty($alt_size_id))
        {
            $alt_modifications_all_conditions["left_join"]["1"] = "modifications modifications.id arsimos.modification_id";
            $alt_modifications_all_conditions["where"]["1"] = "arsimos.article_id = " . $alt_article_id;
            $alt_modifications_all_conditions["where"]["2"] = "arsimos.size_id = " . $alt_size_id;
            $alt_modifications_all_conditions["where"]["3"] = "arsimos.modification_id isnot NULL";
            $alt_modifications_all_conditions["where"]["4"] = "arsimos.deleted_on is null";
            $alt_modifications_all_data = db_read("arsimos", "arsimos.modification_id modifications.name", $alt_modifications_all_conditions);

            if (!empty($alt_modifications_all_data))
            {
                while ($row = db_fetch_num($alt_modifications_all_data))
                {
                    $alt_modifications_all[$alt_article_id][$alt_size_id][$row[0]] = $row[1];
                }
            } else {
                $alt_modifications_all = null;
            }
        }
    }
} else {
    $alt_modifications_all = null;
}


// Required for list: garmentuser_garments
if (!empty($garmentuser_post_id))
{
    $garmentuser_garments_columns = "garments.id garments.tag articles.description sizes.name modifications.name circulationgroups.id circulationgroups.name scanlocations.translate";
    $garmentuser_garments_conditions["left_join"]["1"] = "arsimos garments.arsimo_id arsimos.id";
    $garmentuser_garments_conditions["left_join"]["2"] = "articles arsimos.article_id articles.id";
    $garmentuser_garments_conditions["left_join"]["3"] = "sizes arsimos.size_id sizes.id";
    $garmentuser_garments_conditions["left_join"]["4"] = "modifications arsimos.modification_id modifications.id";
    $garmentuser_garments_conditions["left_join"]["5"] = "garmentusers garments.garmentuser_id garmentusers.id";
    $garmentuser_garments_conditions["left_join"]["6"] = "scanlocations garments.scanlocation_id scanlocations.id";
    $garmentuser_garments_conditions["left_join"]["7"] = "circulationgroups garments.circulationgroup_id circulationgroups.id";
    $garmentuser_garments_conditions["order_by"] = "circulationgroups.id articles.description";
    $garmentuser_garments_conditions["where"]["1"] = "garments.garmentuser_id " . $garmentuser_id;
    $garmentuser_garments_conditions["where"]["2"] = "garments.deleted_on IS NULL";
    $garmentuser_garments = db_read("garments", $garmentuser_garments_columns, $garmentuser_garments_conditions);
} else {
    $garmentuser_garments = null;
}

// Required for list: garmentuser_garments_inuse
if (!empty($garmentuser_post_id))
{
    $gg_inuse_columns = "garments.id garments.tag garments.tag2 articles.description sizes.name modifications.name garmentusers_garments.date_received";
    $gg_inuse_conditions["left_join"]["1"] = "garments garments.id garmentusers_garments.garment_id";
    $gg_inuse_conditions["left_join"]["2"] = "arsimos garments.arsimo_id arsimos.id";
    $gg_inuse_conditions["left_join"]["3"] = "articles arsimos.article_id articles.id";
    $gg_inuse_conditions["left_join"]["4"] = "sizes arsimos.size_id sizes.id";
    $gg_inuse_conditions["left_join"]["5"] = "modifications arsimos.modification_id modifications.id";
    $gg_inuse_conditions["left_join"]["6"] = "garmentusers garments.garmentuser_id garmentusers.id";
    $gg_inuse_conditions["order_by"] = "articles.description";
    $gg_inuse_conditions["where"]["1"] = "garmentusers_garments.garmentuser_id " . $garmentuser_id;
    $gg_inuse_conditions["where"]["2"] = "garmentusers_garments.superuser_id 0";
    $garmentuser_garments_inuse = db_read("garmentusers_garments", $gg_inuse_columns, $gg_inuse_conditions);
} else {
    $garmentuser_garments_inuse = null;
}

// Required for list: garmentuser_disconnected_garments
if (!empty($garmentuser_post_id))
{
	$garmentuser_disconnected_query = "SELECT 
		`g`.`id` AS 'garments_id',
    `g`.`tag` AS 'garments_tag',
    `g`.`lastscan` AS 'garments_lastscan',
    `ar`.`description` AS 'articles_description',
    `s`.`name` AS 'sizes_name',
    `m`.`name` AS 'modifications_name',
    `l`.`comments` AS 'log_disconnected_garments_comments',
    `l`.`date` AS 'log_disconnected_garments_date',
    `g`.`scanlocation_id` AS 'garments_scanlocation_id'
    FROM
    `log_disconnected_garments` `l`
		LEFT JOIN `garments` `g` ON `g`.`id` = `l`.`garment_id`
		LEFT JOIN `arsimos` `a` ON `g`.`arsimo_id` = `a`.`id`
		LEFT JOIN `articles` `ar` ON `ar`.`id` = `a`.`article_id`
		LEFT JOIN `sizes` `s` ON `s`.`id` = `a`.`size_id`
		LEFT JOIN `modifications` `m` ON `m`.`id` = `a`.`modification_id`
		WHERE `l`.`garmentuser_id` = '". $garmentuser_id ."' AND `g`.`scanlocation_id` = '". $disc_scanlocation ."' AND l.date >= g.lastscan
    GROUP BY `g`.`id`
    ORDER BY `ar`.`description`"; 
    
    $garmentuser_disconnected_garments = db_query($garmentuser_disconnected_query);
} else {
    $garmentuser_disconnected_garments = null;
}

// Required for list: garmentuser_garments_superuser
if (!empty($garmentuser_post_id))
{
    $ggs_columns = "garments.id garments.tag articles.description sizes.name modifications.name garmentusers_garments.date_received";
    $ggs_conditions["left_join"]["1"] = "garments garments.id garmentusers_garments.garment_id";
    $ggs_conditions["left_join"]["2"] = "arsimos garments.arsimo_id arsimos.id";
    $ggs_conditions["left_join"]["3"] = "articles arsimos.article_id articles.id";
    $ggs_conditions["left_join"]["4"] = "sizes arsimos.size_id sizes.id";
    $ggs_conditions["left_join"]["5"] = "modifications arsimos.modification_id modifications.id";
    $ggs_conditions["left_join"]["6"] = "garmentusers garments.garmentuser_id garmentusers.id";
    $ggs_conditions["order_by"] = "articles.description";
    $ggs_conditions["where"]["1"] = "garmentusers_garments.superuser_id " . $garmentuser_id;
    $garmentuser_garments_superuser = db_read("garmentusers_garments", $ggs_columns, $ggs_conditions);
} else {
    $garmentuser_garments_superuser = null;
}

// Required for list: garmentuser_garments_available
if (!empty($garmentuser_post_id))
{
    $gga_columns = "garments.id garments.tag articles.description sizes.name modifications.name distributors_load.hook distributors.doornumber";
    $gga_conditions["left_join"]["1"] = "garments garments.id distributors_load.garment_id";
    $gga_conditions["left_join"]["2"] = "arsimos garments.arsimo_id arsimos.id";
    $gga_conditions["left_join"]["3"] = "articles arsimos.article_id articles.id";
    $gga_conditions["left_join"]["4"] = "sizes arsimos.size_id sizes.id";
    $gga_conditions["left_join"]["5"] = "modifications arsimos.modification_id modifications.id";
    $gga_conditions["left_join"]["6"] = "garmentusers garments.garmentuser_id garmentusers.id";
    $gga_conditions["left_join"]["7"] = "distributors distributors_load.distributor_id distributors.id";
    $gga_conditions["order_by"] = "articles.description";
    $gga_conditions["where"]["1"] = "garments.garmentuser_id " . $garmentuser_id;
    $garmentuser_garments_available = db_read("distributors_load", $gga_columns, $gga_conditions);
} else {
    $garmentuser_garments_available = null;
}

// Required for list: history
if (!empty($garmentuser_post_id))
{
    $history_query = "SELECT `last_distributions`.`starttime` AS 'log_garmentusers_garments_starttime',
    `g`.`id` AS 'garments_id',
    `g`.`tag` AS 'garments_tag',
    `ar`.`description` AS 'articles_description',
    `s`.`name` AS 'sizes_name',
    `m`.`name` AS 'modifications_name',
    `gu`.`surname` AS 'garmentusers_surname',
    `gu`.`title` AS 'garmentusers_title',
    `gu`.`maidenname` AS 'garmentusers_maidenname',
    `gu`.`initials` AS 'garmentusers_initials',
    `gu`.`gender` AS 'garmentusers_gender',
    `gu`.`intermediate` AS 'garmentusers_intermediate',
    `gu`.`personnelcode` AS 'garmentusers_personnelcode',
    `dl`.`name` AS 'distributorlocations_name'
    FROM
    (
        SELECT `log_garmentusers_garments`.*
        FROM
        `log_garmentusers_garments`
        WHERE `log_garmentusers_garments`.`garmentuser_id` = '". $garmentuser_id ."'
        AND `log_garmentusers_garments`.superuser_id = 0
        ORDER BY `log_garmentusers_garments`.`starttime` DESC
        LIMIT 0, 30
    ) `last_distributions`
    INNER JOIN `garments` `g` ON `last_distributions`.`garment_id` = `g`.`id`
    INNER JOIN `arsimos` `a` ON `a`.`id` = `g`.`arsimo_id`
    INNER JOIN `articles` `ar` ON `ar`.`id` = `a`.`article_id`
    INNER JOIN `sizes` `s` ON `s`.`id` = `a`.`size_id`
    INNER JOIN `distributors` `d` ON `d`.`id` = `last_distributions`.`distributor_id`
    INNER JOIN `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
    LEFT JOIN `garmentusers` `gu` ON `g`.`garmentuser_id` = `gu`.`id`
    LEFT JOIN `modifications` `m` ON `m`.`id` = `a`.`modification_id`
    ORDER BY `last_distributions`.`starttime` DESC";
    
    $historydata = db_query($history_query);
} else {
    $historydata = null;
}

// Required for list: history deposited
if (!empty($garmentuser_post_id))
{
    $history_deposited_query = "SELECT  `g`.`id` AS 'garments_id',
        `g`.`tag` AS 'garments_tag',
        `ar`.`description` AS 'articles_description',
        `s`.`name` AS 'sizes_name',
        `m`.`name` AS 'modifications_name',
        `gu`.`surname` AS 'garmentusers_surname',
        `gu`.`title` AS 'garmentusers_title',
        `gu`.`maidenname` AS 'garmentusers_maidenname',
        `gu`.`initials` AS 'garmentusers_initials',
        `gu`.`gender` AS 'garmentusers_gender',
        `gu`.`intermediate` AS 'garmentusers_intermediate',
        `gu`.`personnelcode` AS 'garmentusers_personnelcode',
        `dl`.`name` AS 'distributorlocations_name',
        `deposited`.`d_name` AS 'depositlocations_name',
        `deposited`.`date` AS 'deposited_date'
    FROM (SELECT lg.garment_id, d.scanlocation_id, ls.date, d.distributorlocation_id AS 'dl_id', d.name AS 'd_name'
        FROM (SELECT * FROM log_garmentusers_garments WHERE garmentuser_id = '". $garmentuser_id ."' AND superuser_id = 0 ORDER BY `starttime` DESC LIMIT 0, 10) lg
        LEFT JOIN log_garmentusers_garments lg2 ON lg2.garment_id = lg.garment_id AND lg2.starttime > lg.starttime
        INNER JOIN log_depositlocations_garments ls ON ls.garment_id = lg.garment_id AND ls.date > lg.endtime AND (ISNULL(lg2.garment_id) OR ls.date < lg2.starttime)
        INNER JOIN depositlocations d ON d.id = `ls`.depositlocation_id
        LEFT JOIN log_distributors_load ld ON ld.garment_id = lg.garment_id AND ld.starttime > lg.starttime AND ld.starttime < ls.date
	LEFT JOIN log_rejected_garments lr ON lr.garment_id = lg.garment_id AND lr.date > lg.starttime AND lr.date < ls.date
        WHERE ISNULL(ld.garment_id) AND ISNULL(lr.garment_id)
        GROUP BY lg.garment_id,lg.starttime) `deposited`
    INNER JOIN distributorlocations dl ON dl.id = deposited.dl_id
    INNER JOIN garments g ON g.id = deposited.garment_id
    INNER JOIN arsimos a ON a.id = g.arsimo_id
    INNER JOIN articles ar ON ar.id = a.article_id
    INNER JOIN sizes s ON s.id = a.size_id
    LEFT JOIN garmentusers gu ON gu.id = g.garmentuser_id
    LEFT JOIN modifications m ON m.id = a.modification_id
    ORDER BY `deposited`.`date` DESC";
    
    $historydata_deposited = db_query($history_deposited_query);
} else {
    $historydata_deposited = null;
}


// Required for list: superuser history
if (!empty($garmentuser_post_id))
{
    $superuser_history_query = "SELECT `last_distributions`.`starttime` AS 'log_garmentusers_garments_starttime',
    `g`.`id` AS 'garments_id',
    `g`.`tag` AS 'garments_tag',
    `ar`.`description` AS 'articles_description',
    `s`.`name` AS 'sizes_name',
    `m`.`name` AS 'modifications_name',
    `gu`.`surname` AS 'garmentusers_surname',
    `gu`.`title` AS 'garmentusers_title',
    `gu`.`maidenname` AS 'garmentusers_maidenname',
    `gu`.`initials` AS 'garmentusers_initials',
    `gu`.`gender` AS 'garmentusers_gender',
    `gu`.`intermediate` AS 'garmentusers_intermediate',
    `gu`.`personnelcode` AS 'garmentusers_personnelcode',
    `dl`.`name` AS 'distributorlocations_name'
    FROM
    (
        SELECT `log_garmentusers_garments`.*
        FROM
        `log_garmentusers_garments`
        WHERE `log_garmentusers_garments`.`garmentuser_id` = '". $garmentuser_id ."'
        AND `log_garmentusers_garments`.superuser_id = '". $garmentuser_id ."'
        ORDER BY `log_garmentusers_garments`.`starttime` DESC
        LIMIT 0, 30
    ) `last_distributions`
    INNER JOIN `garments` `g` ON `last_distributions`.`garment_id` = `g`.`id`
    INNER JOIN `arsimos` `a` ON `a`.`id` = `g`.`arsimo_id`
    INNER JOIN `articles` `ar` ON `ar`.`id` = `a`.`article_id`
    INNER JOIN `sizes` `s` ON `s`.`id` = `a`.`size_id`
    INNER JOIN `distributors` `d` ON `d`.`id` = `last_distributions`.`distributor_id`
    INNER JOIN `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
    LEFT JOIN `garmentusers` `gu` ON `g`.`garmentuser_id` = `gu`.`id`
    LEFT JOIN `modifications` `m` ON `m`.`id` = `a`.`modification_id`
    ORDER BY `last_distributions`.`starttime` DESC";
    
    $superuser_historydata = db_query($superuser_history_query);
} else {
    $superuser_historydata = null;
}

// Required for list: superuser history deposited
if (!empty($garmentuser_post_id))
{
    $superuser_history_deposited_query = "SELECT  `g`.`id` AS 'garments_id',
        `g`.`tag` AS 'garments_tag',
        `ar`.`description` AS 'articles_description',
        `s`.`name` AS 'sizes_name',
        `m`.`name` AS 'modifications_name',
        `gu`.`surname` AS 'garmentusers_surname',
        `gu`.`title` AS 'garmentusers_title',
        `gu`.`maidenname` AS 'garmentusers_maidenname',
        `gu`.`initials` AS 'garmentusers_initials',
        `gu`.`gender` AS 'garmentusers_gender',
        `gu`.`intermediate` AS 'garmentusers_intermediate',
        `gu`.`personnelcode` AS 'garmentusers_personnelcode',
        `dl`.`name` AS 'distributorlocations_name',
        `deposited`.`d_name` AS 'depositlocations_name',
        `deposited`.`date` AS 'deposited_date'
    FROM (SELECT lg.garment_id, d.scanlocation_id, ls.date, d.distributorlocation_id AS 'dl_id', d.name AS 'd_name'
        FROM (SELECT * FROM log_garmentusers_garments WHERE garmentuser_id = '". $garmentuser_id ."' AND superuser_id = '". $garmentuser_id ."' ORDER BY `starttime` DESC LIMIT 0, 30) lg
        LEFT JOIN log_garmentusers_garments lg2 ON lg2.garment_id = lg.garment_id AND lg2.starttime > lg.starttime
        INNER JOIN log_depositlocations_garments ls ON ls.garment_id = lg.garment_id AND ls.date > lg.endtime AND (ISNULL(lg2.garment_id) OR ls.date < lg2.starttime)
        INNER JOIN depositlocations d ON d.id = `ls`.depositlocation_id
        LEFT JOIN log_distributors_load ld ON ld.garment_id = lg.garment_id AND ld.starttime > lg.starttime AND ld.starttime < ls.date
	LEFT JOIN log_rejected_garments lr ON lr.garment_id = lg.garment_id AND lr.date > lg.starttime AND lr.date < ls.date
        WHERE ISNULL(ld.garment_id) AND ISNULL(lr.garment_id)
        GROUP BY lg.garment_id,lg.starttime) `deposited`
    INNER JOIN distributorlocations dl ON dl.id = deposited.dl_id
    INNER JOIN garments g ON g.id = deposited.garment_id
    INNER JOIN arsimos a ON a.id = g.arsimo_id
    INNER JOIN articles ar ON ar.id = a.article_id
    INNER JOIN sizes s ON s.id = a.size_id
    LEFT JOIN garmentusers gu ON gu.id = g.garmentuser_id
    LEFT JOIN modifications m ON m.id = a.modification_id
    ORDER BY `deposited`.`date` DESC";
    
    $superuser_historydata_deposited = db_query($superuser_history_deposited_query);
} else {
    $superuser_historydata_deposited = null;
}

// Required for list: garmentuser_articles
if (!empty($garmentuser_post_id))
{
    $garmentuser_articles_query = "SELECT `a1`.`id` AS 'article_1_id',
              `a1`.`description` AS 'article_1',
              `a2`.`description` AS 'article_2',
              `ga`.`combined_credit` AS 'combined_credit',
              `ga`.`extra_credit` AS 'extra_credit'
         FROM `garmentusers_articles` `ga`
   INNER JOIN `articles` `a1` ON `a1`.`id` = `ga`.`article_1_id`
   INNER JOIN `articles` `a2` ON `a2`.`id` = `ga`.`article_2_id`
        WHERE `ga`.`garmentuser_id` = ". $garmentuser_id ."
     ORDER BY `a1`.`description`, `a2`.`description`";
    
    $garmentuser_articles = db_query($garmentuser_articles_query);
} else {
    $garmentuser_articles = null;
}

/**
* Export
*/
if (isset($_POST["export_history"])) {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = "";
    $header.=$lang["tag"]."\t";
    $header.=$lang["description"]."\t";
    $header.=$lang["size"]."\t";
    $header.=$lang["modification"]."\t";
    $header.=$lang["owner"]."\t";
    $header.=$lang["distributed"]."\t";                    

    $data = "";
    while($row = db_fetch_array($historydata)) {
        $line = "";

        $in = array();
        array_push($in,"'".$row["garments_tag"]);
        array_push($in,$row["articles_description"]);
        array_push($in,$row["sizes_name"]);
        array_push($in,($row["modifications_name"]) ? $row["modifications_name"] : "");
        array_push($in,(!empty($row["garmentusers_surname"])) ? generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]) : "");
        array_push($in,$row["log_garmentusers_garments_starttime"]);
 
        foreach($in as $value) {
            if ((!isset($value)) OR ($value == "")) {
                $value = "\t";
            } else {
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"' . "\t";
            }
            $line .= $value;
        }
        $data .= trim($line)."\n";
    }
    $data_r = str_replace("\r","",$data);

    print "$header\n$data_r";
    die();
}

if (isset($_POST["export_history_deposited"])) {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = "";
    $header.=$lang["tag"]."\t";
    $header.=$lang["description"]."\t";
    $header.=$lang["size"]."\t";
    $header.=$lang["modification"]."\t";
    $header.=$lang["owner"]."\t";
    $header.=$lang["location"]."\t";
    $header.=$lang["depositlocation"]."\t";
    $header.=$lang["deposited"]."\t";

    $data = "";
    while($row = db_fetch_array($historydata_deposited)) {
        $line = "";

        $in = array();
        array_push($in,"'".$row["garments_tag"]);
        array_push($in,$row["articles_description"]);
        array_push($in,$row["sizes_name"]);
        array_push($in,($row["modifications_name"]) ? $row["modifications_name"] : "");
        array_push($in,(!empty($row["garmentusers_surname"])) ? generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]) : "");
        array_push($in,$row["distributorlocations_name"]);
        array_push($in,$row["depositlocations_name"]);
        array_push($in,$row["deposited_date"]);
 
        foreach($in as $value) {
            if ((!isset($value)) OR ($value == "")) {
                $value = "\t";
            } else {
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"' . "\t";
            }
            $line .= $value;
        }
        $data .= trim($line)."\n";
    }
    $data_r = str_replace("\r","",$data);

    print "$header\n$data_r";
    die();
}

if (isset($_POST["export_superuser_history"])) {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = "";
    $header.=$lang["tag"]."\t";
    $header.=$lang["description"]."\t";
    $header.=$lang["size"]."\t";
    $header.=$lang["modification"]."\t";
    $header.=$lang["distributed"]."\t";                    

    $data = "";
    while($row = db_fetch_array($superuser_historydata)) {
        $line = "";

        $in = array();
        array_push($in,"'".$row["garments_tag"]);
        array_push($in,$row["articles_description"]);
        array_push($in,$row["sizes_name"]);
        array_push($in,($row["modifications_name"]) ? $row["modifications_name"] : "");
        array_push($in,$row["log_garmentusers_garments_starttime"]);
 
        foreach($in as $value) {
            if ((!isset($value)) OR ($value == "")) {
                $value = "\t";
            } else {
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"' . "\t";
            }
            $line .= $value;
        }
        $data .= trim($line)."\n";
    }
    $data_r = str_replace("\r","",$data);

    print "$header\n$data_r";
    die();
}

if (isset($_POST["export_superuser_history_deposited"])) {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = "";
    $header.=$lang["tag"]."\t";
    $header.=$lang["description"]."\t";
    $header.=$lang["size"]."\t";
    $header.=$lang["modification"]."\t";
    $header.=$lang["location"]."\t";
    $header.=$lang["depositlocation"]."\t";
    $header.=$lang["deposited"]."\t";

    $data = "";
    while($row = db_fetch_array($superuser_historydata_deposited)) {
        $line = "";

        $in = array();
        array_push($in,"'".$row["garments_tag"]);
        array_push($in,$row["articles_description"]);
        array_push($in,$row["sizes_name"]);
        array_push($in,($row["modifications_name"]) ? $row["modifications_name"] : "");
        array_push($in,$row["distributorlocations_name"]);
        array_push($in,$row["depositlocations_name"]);
        array_push($in,$row["deposited_date"]);
 
        foreach($in as $value) {
            if ((!isset($value)) OR ($value == "")) {
                $value = "\t";
            } else {
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"' . "\t";
            }
            $line .= $value;
        }
        $data .= trim($line)."\n";
    }
    $data_r = str_replace("\r","",$data);

    print "$header\n$data_r";
    die();
}


/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "articles" => $articles,
    "gu_data" => $gu_data,
    "circulationgroups" => $circulationgroups,
    "circulationgroups_all" => $circulationgroups_all,
    "circulationgroups_all_active" => $circulationgroups_all_active,
    "circulationgroups_selected" => $circulationgroups_selected,
    "circulationgroups_name_selected" => $circulationgroups_name_selected,
    "genders" => $genders,
    "date_service_on" => $date_service_on,
    "date_service_off" => $date_service_off,
    "service_on_selected" => $service_on_selected,
    "service_off_selected" => $service_off_selected,
    "distribution" => $distribution,
    "service_on_switches" => $service_on_switches,
    "service_off_switches" => $service_off_switches,
    "timelockoption" => $timelockoption,
    "blockageoption" => $blockageoption,
    "warningoption" => $warningoption,
    "timelockoptions" => $timelockoptions,
    "blockageoptions" => $blockageoptions,
    "warningoptions" => $warningoptions,
    "profession_timelock" => $profession_timelock,
    "profession_blockage" => $profession_blockage,
    "profession_warning" => $profession_warning,
    "garmentuser_garments" => $garmentuser_garments,
    "garmentuser_garments_inuse" => $garmentuser_garments_inuse,
    "garmentuser_disconnected_garments" => $garmentuser_disconnected_garments,
    "garmentuser_garments_superuser" => $garmentuser_garments_superuser,
    "garmentuser_garments_available" => $garmentuser_garments_available,
    "professions" => $professions,
    "articles_all" => $articles_all,
    "articles_all_credit" => $articles_all_credit,
    "garmentusers_articles" => $garmentusers_articles,
    "articles_selected" => $articles_selected,
    "articles_eachart_sizes" => $articles_eachart_sizes,
    "clientdepartments_all" => $clientdepartments_all,
    "costplaces_all" => $costplaces_all,
    "functions_all" => $functions_all,
    "sizes_selected" => $sizes_selected,
    "alt_sizes_selected" => $alt_sizes_selected,
    "modifications_all" => $modifications_all,
    "alt_modifications_all" => $alt_modifications_all,
    "modifications_selected" => $modifications_selected,
    "count" => $count,
    "alt_modifications_selected" => $alt_modifications_selected,
    "alternatives" => $alternatives,
    "alternativesswitch" => $alternativesswitch,
    "garmentlink" => $garmentlink,
    "articlelink" => $articlelink,
    "station_bound_yesno_options" => $station_bound_yesno_options,
    "station_max_positions" => $station_max_positions,
    "distributor_id_output" => $distributor_id_output,
    "distributor_id2_output" => $distributor_id2_output,
    "distributor_id3_output" => $distributor_id3_output,
    "distributor_id4_output" => $distributor_id4_output,
    "distributor_id5_output" => $distributor_id5_output,
    "distributor_id6_output" => $distributor_id6_output,
    "distributor_id7_output" => $distributor_id7_output,
    "distributor_id8_output" => $distributor_id8_output,
    "distributor_id9_output" => $distributor_id9_output,
    "distributor_id10_output" => $distributor_id10_output,
    "station_bound_yesno" => $station_bound_yesno,
    "user_articles_all" => $user_articles_all,
    "user_articles_selected" => $user_articles_selected,
    "user_count" => $user_count,
    "garmentlink_articles" => $garmentlink_articles,
    "garmentlink_sizes" => $garmentlink_sizes,
    "garmentlink_modifications_all" => $garmentlink_modifications_all,
    "articlelink_articles" => $articlelink_articles,
    "disc_scanlocation" => $disc_scanlocation,
    "historydata" => $historydata,
    "historydata_deposited" => $historydata_deposited,
    "superuser_historydata" => $superuser_historydata,
    "superuser_historydata_deposited" => $superuser_historydata_deposited,
    "garmentuser_articles" => $garmentuser_articles,
    "show_comments_checked" => ($gu_data["show_comments"] == "y") ? "checked=\"checked\"" : "" 
);

template_parse($pi, $urlinfo, $cv);

?>
