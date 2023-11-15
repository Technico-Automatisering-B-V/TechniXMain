<?php

/**
 * Garment details
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
$pi["filename_list"] = "garments.php";
$pi["filename_details"] = "garment_details.php";
$pi["template"] = "layout/pages/garment_details.tpl";
$pi["note"] = "";
$pi["toolbar"]["full_delete"] = "yes";

/**
 * Authorization
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

// Cancel button
if (isset($_POST["detailssubmitnone"])) {
    redirect($pi["filename_list"]);
}

/**
 * Functions
 */
function check_garment_undelete($tag)
{
    if (!tag) return false;

    $garment_id_conditions["where"]["1"] = "tag LIKE " . $tag ." AND deleted_on is NULL";
    $garment_id_res = db_read("garments", "id", $garment_id_conditions);
    $garment_id_arr = db_fetch_num($garment_id_res);
    $garment_id = $garment_id_arr[0];

    return (($garment_id) ? $garment_id : false);
}

/**
 * Array's
 */
$requiredfields = array();
$urlinfo = array();

$bindingdata = array(
    "userbound" => (!empty($_POST["userbound"])) ? trim($_POST["userbound"]) : 2,
    "article_id" => (!empty($_POST["article_id"])) ? trim($_POST["article_id"]) : "",
    "homewash" => (!empty($_POST["homewash"])) ? trim($_POST["homewash"]) : 2,
    "size_id" => (!empty($_POST["size_id"])) ? trim($_POST["size_id"]) : "",
    "modification_id" => (!empty($_POST["modification_id"])) ? trim($_POST["modification_id"]) : ""
);

$detailsdata = array(
    "tag" => (!empty($_POST["tag"])) ? convertTag(trim($_POST["tag"])) : "",
    "maxwashcount" => (!empty($_POST["maxwashcount"])) ? trim($_POST["maxwashcount"]) : "0",
    "garmentuser_id" => (!empty($_POST["garmentuser_id"])) ? trim($_POST["garmentuser_id"]) : null,
    "circulationgroup_id" => (!empty($_POST["circulationgroup_id"])) ? trim($_POST["circulationgroup_id"]) : 1
);

/**
 * Variables
 */
$error = "0";
$active_tab = (!empty($_POST["active_tab"])) ? trim($_POST["active_tab"]) : "tab1";

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $garment_post_id = $_POST["id"]; $page = $_POST["page"];
} elseif (isset($_GET["ref"]) && !empty($_GET["ref"])) {
    $garment_post_id = $_GET["ref"]; $page = "details";
} elseif (isset($_GET["sec"]) && !empty($_GET["sec"])) {
    $garment_post_id = ""; $page = "add"; $detailsdata["tag"] = $_GET["sec"];
} else {
    $garment_post_id = ""; $page = $_POST["page"];
}

/**
 * Add garment
 */
if (isset($page) && $page == "add") {
    $pi["page"]  = "add";
    $pi["title"] = $lang["add_garment"];

    if (isset($_POST["detailssubmit"]) || isset($_POST["detailssubmitnew"]) || isset($_POST["detailssubmitcopy"])) {
        if (empty($detailsdata["tag"])) {
            $error = "1";
            array_push($requiredfields, $lang["tag"]);
        } else {
            $garment_id = tag_to_garment_id($detailsdata["tag"]);
            if ($garment_id) {
                $error = "1";
                array_push($requiredfields, $lang["nonexisting_tag"]);
            }
        }

        if (empty($bindingdata["article_id"])){ $error = "1"; array_push($requiredfields, $lang["article"]); }
        if (empty($bindingdata["size_id"])){ $error = "1"; array_push($requiredfields, $lang["size"]); }
        if (empty($detailsdata["circulationgroup_id"])){ $error = "1"; array_push($requiredfields, $lang["circulationgroup"]); }

        if ($error == "0") {
            //retrieve the arsimo_id from bindingdata
            $arsimo_conditions["where"]["1"] = "article_id = " . $bindingdata["article_id"];
            $arsimo_conditions["where"]["2"] = "size_id = " . $bindingdata["size_id"];
            $arsimo_conditions["where"]["3"] = "modification_id " . ((!empty($bindingdata["modification_id"])) ? "= " . $bindingdata["modification_id"] : "is NULL");
            $arsimo_data = db_read("arsimos", "id", $arsimo_conditions);
            $arsimo_id = db_fetch_num($arsimo_data);
            $detailsdata["arsimo_id"] = $arsimo_id[0];
            $detailsdata["created_on"] = date("Y-m-d H:i:s");
            $detailsdata["lastscan"] = date("Y-m-d H:i:s");
            $detailsdata["scanlocation_id"] = 1;
            
            //insert the given garment
            db_insert("garments", $detailsdata);
            
            //insert the garmentuser_
            //arsimo
            if (!empty($detailsdata["garmentuser_id"]) && !empty($detailsdata["arsimo_id"]))
            {
                
                $arsimo_connected_to_garmentuser_sql = "SELECT count(*) FROM garmentusers_arsimos
                                    WHERE garmentuser_id = ".$detailsdata["garmentuser_id"]."
                                    AND arsimo_id = ".$detailsdata["arsimo_id"]." 
                                    AND garmentusers_arsimos.userbound = 1";
                $arsimo_connected_to_garmentuser = db_fetch_num(db_query($arsimo_connected_to_garmentuser_sql));
                $arsimo_connected_to_garmentuser = $arsimo_connected_to_garmentuser[0];
                
                if(isset($arsimo_connected_to_garmentuser) && $arsimo_connected_to_garmentuser == 0)
                {
                    $data["garmentuser_id"] = $detailsdata["garmentuser_id"];
                    $data["arsimo_id"] = $detailsdata["arsimo_id"];
                    $data["enabled"] = 1;
                    $data["userbound"] = 1;
                    $data["max_positions"] = 2;
                    $data["max_credit"] = null;

                    db_insert("garmentusers_arsimos", $data);
                    unset($data["garmentuser_id"]);
                    unset($data["arsimo_id"]);
                    unset($data["enabled"]);
                    unset($data["userbound"]);
                    unset($data["max_positions"]);
                    unset($data["max_credit"]);
                }
            }
            

            if (isset($_POST["detailssubmitnew"])) {
                //we stay in details but we clear the detailsdata
                $bindingdata = array(
                    "userbound" => "2",
                    "article_id" => "",
                    "size_id" => "",
                    "modification_id" => "",
                    "homewash" => 2
                );
                $detailsdata = array(
                    "tag" => "",
                    "maxwashcount" => "0",
                    "garmentuser_id" => null,
                    "circulationgroup_id" => ""
                );
            } elseif (isset($_POST["detailssubmitcopy"])) {
                $detailsdata["tag"] = "";
            } else {
                redirect($pi["filename_list"]);
            }

        } elseif (isset($_POST["detailssubmit"]) || isset($_POST["detailssubmitnew"]) || isset($_POST["detailssubmitcopy"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }
    }

} elseif (isset($page) && $page == "details" && !empty($garment_post_id)) {
    $pi["page"] = "details";
    $pi["title"] = $lang["garment_details"];

    $detailsdata_db = db_fetch_assoc(db_read_row_by_id("garments", $garment_post_id));

    if (isset($_POST["editsubmit"])) {
        $detailsdata = array_merge($detailsdata_db, $detailsdata);
    } else {
        $detailsdata = array_merge($detailsdata, $detailsdata_db);
    }

    //we need the id for toolbar buttons
    $urlinfo["id"] = $garment_post_id;

    if (isset($_POST["undelete"]) && $_POST["undelete"] == "1") {
        $garment_check = check_garment_undelete($detailsdata["tag"]);
        if ($garment_check) {
            $pi["note"] = html_requirednote($lang["garment_cannot_undelete"]);
        } else {
            $update_arsimo_sql = "
                    UPDATE `arsimos`
                    SET `arsimos`.`deleted_on` = NULL
                    WHERE `arsimos`.`id` = (SELECT `arsimo_id` FROM `garments` WHERE `garments`.`id` = ". $urlinfo["id"] .")";
            db_query($update_arsimo_sql);

            db_update("garments", $garment_post_id, array("deleted_on" => NULL, "delete_reason" => NULL,"scanlocation_id" => 2));
            
            redirect($pi["filename_list"]);
        }

    } elseif (!isset($_POST["detailssubmit"])) {
        if (!isset($_POST["editsubmit"])) {
            if (!empty($detailsdata["garmentuser_id"])) {
                $bindingdata["userbound"] = 1;
            } else {
                $bindingdata["userbound"] = 2;
            }
        }

        if ($detailsdata["scanlocation_id"] == "4") {
            $bindingdata["homewash"] = 1;
        } else {
            $bindingdata["homewash"] = 2;
        }

    } elseif (isset($_POST["detailssubmit"])) {
        if (empty($requiredfields) && empty($pi["note"])) {
            //$homewash_conditions["where"]["1"] = "tag = " . $detailsdata["tag"];
            //$homewash_data = db_read("garments", "scanlocation_id", $homewash_conditions);
            //$homewash_id = db_fetch_num($homewash_data);
            //$scanlocation_id = $homewash_id[0];

            if ($bindingdata["userbound"] == 1) {
                if ($detailsdata["scanlocation_id"] == "4" && $bindingdata["homewash"] == 2) {
                    $detailsdata["scanlocation_id"] = "1";
                } elseif ($detailsdata["scanlocation_id"] !== "4" && $bindingdata["homewash"] == 1) {
                    $detailsdata["scanlocation_id"] = "4";
                }
            } else {
                $detailsdata["garmentuser_id"] = null;
                
                if ($detailsdata["scanlocation_id"] == "4") {
                    $detailsdata["scanlocation_id"] = "1";
                }  
            }

            /** Retrieve the arsimo_id from bindingdata **/
            $arsimo_conditions["where"]["1"] = "article_id = " . $bindingdata["article_id"];
            $arsimo_conditions["where"]["2"] = "size_id = " . $bindingdata["size_id"];
            $arsimo_conditions["where"]["3"] = "modification_id " . ((!empty($bindingdata["modification_id"])) ? "= " . $bindingdata["modification_id"] : "is NULL");
            $arsimo_data = db_read("arsimos", "id", $arsimo_conditions);
            $arsimo_id = db_fetch_num($arsimo_data);
            $detailsdata["arsimo_id"] = $arsimo_id[0];

            unset($detailsdata["tag"]);

            /** Update the garment **/
            db_update("garments", $garment_post_id, $detailsdata);
            
            //insert the garmentuser_arsimo
            if (!empty($detailsdata["garmentuser_id"]) && !empty($detailsdata["arsimo_id"]))
            {
                $arsimo_connected_to_garmentuser_sql = "SELECT count(*) FROM garmentusers_arsimos
                                    WHERE garmentuser_id = ".$detailsdata["garmentuser_id"]."
                                    AND arsimo_id = ".$detailsdata["arsimo_id"]." 
                                    AND garmentusers_arsimos.userbound = 1";
                $arsimo_connected_to_garmentuser = db_fetch_num(db_query($arsimo_connected_to_garmentuser_sql));
                $arsimo_connected_to_garmentuser = $arsimo_connected_to_garmentuser[0];
                
                if(isset($arsimo_connected_to_garmentuser) && $arsimo_connected_to_garmentuser == 0)
                {
                    $data["garmentuser_id"] = $detailsdata["garmentuser_id"];
                    $data["arsimo_id"] = $detailsdata["arsimo_id"];
                    $data["enabled"] = 1;
                    $data["userbound"] = 1;
                    $data["max_positions"] = 2;
                    $data["max_credit"] = null;

                    db_insert("garmentusers_arsimos", $data);
                    unset($data["garmentuser_id"]);
                    unset($data["arsimo_id"]);
                    unset($data["enabled"]);
                    unset($data["userbound"]);
                    unset($data["max_positions"]);
                    unset($data["max_credit"]);
                }
            }

            /** Redirect to list **/
            redirect($pi["filename_list"]);
        }
    }

    // Delete
    if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
        if (isset($_POST["full_delete"]) && $_POST["full_delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                if(garment_id_to_garment_status($garment_post_id) != 'loaded') {
                    //full delete
                    db_delete_where("depositbatches_garments", "garment_id", $garment_post_id);
                    db_delete_where("log_depositlocations_garments", "garment_id", $garment_post_id);
                    db_delete_where("log_distributors_load", "garment_id", $garment_post_id);
                    db_delete_where("log_garmentusers_garments", "garment_id", $garment_post_id);
                    db_delete_where("log_garments_tagreplacements", "garment_id", $garment_post_id);
                    db_delete_where("garmentusers_garments", "garment_id", $garment_post_id);
                    db_delete_where("distributors_load", "garment_id", $garment_post_id);
                    db_delete_where("log_rejected_garments", "garment_id", $garment_post_id);
                    db_delete_where("garments_despecklesandrepairs", "garment_id", $garment_post_id);
                    db_delete_where("garments", "id", $garment_post_id);
                    
                    if (!empty($detailsdata["garmentuser_id"]) && !empty($detailsdata["arsimo_id"]))
                    {
                        $last_garmentusers_arsimos_sql = "SELECT count(*) FROM garments
                                        INNER JOIN garmentusers_arsimos ON garmentusers_arsimos.arsimo_id = garments.arsimo_id
                                            AND garmentusers_arsimos.garmentuser_id = garments.garmentuser_id
                                            AND garmentusers_arsimos.userbound = 1
                                        WHERE garmentusers_arsimos.garmentuser_id = ".$detailsdata["garmentuser_id"]." 
                                        AND garmentusers_arsimos.arsimo_id = ".$detailsdata["arsimo_id"]." 
                                        AND garments.deleted_on IS NULL
                                        AND garments.id != ". $garment_post_id;
                        $last_garmentusers_arsimos = db_fetch_num(db_query($last_garmentusers_arsimos_sql));
                        $last_garmentusers_arsimos = $last_garmentusers_arsimos[0];
                        
                        if(isset($last_garmentusers_arsimos) && $last_garmentusers_arsimos == 0){
                            $delete_garmentusers_arsimos_sql = "DELETE
                                            FROM garmentusers_arsimos
                                            WHERE garmentusers_arsimos.userbound = 1
                                            AND garmentusers_arsimos.garmentuser_id = ".$detailsdata["garmentuser_id"]." 
                                            AND garmentusers_arsimos.arsimo_id = ". $detailsdata["arsimo_id"];

                            db_query($delete_garmentusers_arsimos_sql);
                        }
                    }
                    
                    //redirect to list
                    redirect($pi["filename_list"]);
                } else {
                    $pi["note"] = html_error($lang["error_the_garment_is_loaded"]);
                }

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_full_delete($garment_post_id, $lang["garment"]);
            }
        } else {
            if (isset($_POST["confirmed"])) {
                if(garment_id_to_garment_status($garment_post_id) != 'loaded') {
                    //disconnect the garment from any garmentuser it was in use by
                    db_delete_where("garmentusers_garments", "garment_id", $garment_post_id);

                    $delete_scanlocation_id_sql = "SELECT `scanlocations`.`id`
                                      FROM `scanlocations`
                                      INNER JOIN `scanlocationstatuses` ON `scanlocations`.`scanlocationstatus_id` = `scanlocationstatuses`.`id`
                                      WHERE `scanlocationstatuses`.`name` = 'deleted'";

                    $delete_scanlocation_id_r = db_fetch_row(db_query($delete_scanlocation_id_sql));
                    $delete_scanlocation_id = $delete_scanlocation_id_r[0];

                    //mark the garment as deleted
                    $garment_conditions["left_join"]["1"] = "scanlocations scanlocations.id garments.scanlocation_id";
                    $garment_conditions["left_join"]["2"] = "scanlocationstatuses scanlocationstatuses.id scanlocations.scanlocationstatus_id";
                    $garment_conditions["where"]["1"] = "scanlocationstatuses.name != 'loaded'";
                    $garment_conditions = array("garmentuser_id" => null, "deleted_on" => "NOW()", "delete_reason" => $_POST["d_reason"], "scanlocation_id" => $delete_scanlocation_id, "sub_scanlocation_id" => null, "deleted_by" => "user");
                    db_update("garments", $garment_post_id, $garment_conditions);

                    if (!empty($detailsdata["garmentuser_id"]) && !empty($detailsdata["arsimo_id"]))
                    {
                        $last_garmentusers_arsimos_sql = "SELECT count(*) FROM garments
                                        INNER JOIN garmentusers_arsimos ON garmentusers_arsimos.arsimo_id = garments.arsimo_id
                                            AND garmentusers_arsimos.garmentuser_id = garments.garmentuser_id
                                            AND garmentusers_arsimos.userbound = 1
                                        WHERE garmentusers_arsimos.garmentuser_id = ".$detailsdata["garmentuser_id"]." 
                                        AND garmentusers_arsimos.arsimo_id = ".$detailsdata["arsimo_id"]." 
                                        AND garments.deleted_on IS NULL
                                        AND garments.id != ". $garment_post_id;
                        $last_garmentusers_arsimos = db_fetch_num(db_query($last_garmentusers_arsimos_sql));
                        $last_garmentusers_arsimos = $last_garmentusers_arsimos[0];

                        if(isset($last_garmentusers_arsimos) && $last_garmentusers_arsimos == 0){
                            $delete_garmentusers_arsimos_sql = "DELETE
                                            FROM garmentusers_arsimos
                                            WHERE garmentusers_arsimos.userbound = 1
                                            AND garmentusers_arsimos.garmentuser_id = ".$detailsdata["garmentuser_id"]." 
                                            AND garmentusers_arsimos.arsimo_id = ". $detailsdata["arsimo_id"];

                            db_query($delete_garmentusers_arsimos_sql);
                        }
                    }
                    
                    //redirect to list
                    redirect($pi["filename_list"]);
                } else {
                    $pi["note"] = html_error($lang["error_the_garment_is_loaded"]);
                }
                
            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete_garment($garment_post_id, $lang["garment"]);
            }
        }
    }
}

// To missing
if (isset($_POST["missing"]) && $_POST["missing"] == "yes") {
    if (isset($_POST["confirmed"])) {
        //put the garment to missing
        $garment_conditions = array("scanlocation_id" => 2);
        db_update("garments", $garment_post_id, $garment_conditions);

        //redirect to list
        redirect($pi["filename_list"]);

    } elseif (!isset($_POST["abort"])) {

        $pi["note"] = html_to_missing($garment_post_id);
    }
}

// To stock
if (isset($_POST["stock"]) && $_POST["stock"] == "yes") {
    if (isset($_POST["confirmed"])) {
        //put the garment to stock
        $garment_conditions = array("scanlocation_id" => 3);
        db_update("garments", $garment_post_id, $garment_conditions);

        //redirect to list
        redirect($pi["filename_list"]);

    } elseif (!isset($_POST["abort"])) {

        $pi["note"] = html_to_stock($garment_post_id);
    }
}

// To laundry
if (isset($_POST["laundry"]) && $_POST["laundry"] == "yes") {
    if (isset($_POST["confirmed"])) {
        //put the garment to laundry 
        $laundry_scanlocation_id_sql = "SELECT `scanlocations`.`id`
                                      FROM `scanlocations`
                                      INNER JOIN `scanlocationstatuses` ON `scanlocations`.`scanlocationstatus_id` = `scanlocationstatuses`.`id`
                                      WHERE `scanlocationstatuses`.`name` = 'laundry' and scanlocations.circulationgroup_id = ". $detailsdata["circulationgroup_id"];

        $laundry_scanlocation_id_r = db_fetch_row(db_query($laundry_scanlocation_id_sql));
        $laundry_scanlocation_id = $laundry_scanlocation_id_r[0];
        
        $garment_conditions = array("scanlocation_id" => $laundry_scanlocation_id);
        db_update("garments", $garment_post_id, $garment_conditions);

        //redirect to list
        redirect($pi["filename_list"]);

    } elseif (!isset($_POST["abort"])) {

        $pi["note"] = html_to_laundry($garment_post_id);
    }
}

//select the correct article, size and modification, in case we are editing an existing garment, only if we have an arsimo_id and no article_id
if (!empty($garment_post_id) && !empty($detailsdata["arsimo_id"]) && empty($bindingdata["article_id"])) {
    $article_select_conditions["left_join"]["1"] = "garments arsimos.id garments.arsimo_id";
    #$article_select_conditions["left_join"]["2"] = "circulationgroups garments.circulationgroup_id circulationgroups.id";
    $article_select_conditions["where"]["1"] = "garments.arsimo_id = " . $detailsdata["arsimo_id"];
    $article_select = db_fetch_row(db_read("arsimos", "arsimos.article_id arsimos.size_id arsimos.modification_id garments.circulationgroup_id", $article_select_conditions));
    $bindingdata["article_id"] = $article_select[0];
    $bindingdata["size_id"] = $article_select[1];
    $bindingdata["modification_id"] = $article_select[2];
    //$bindingdata["circulationgroup_id"] = $article_select[3];
}

//required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

//required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles_data = db_read("articles", "id articlenumber description", $articles_conditions);

if (!empty($articles_data)) {
    while ($row = db_fetch_num($articles_data)) {
        $articles[$row[0]] = $row[2] . ((!empty($row[1])) ? " (" . $row[1] . ") " : "");
    }
} else {
    $articles = null;
}

//required for selectbox: sizes
if (!empty($bindingdata["article_id"])) {
    $sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
    $sizes_conditions["where"]["1"] = "arsimos.article_id = " . $bindingdata["article_id"];
    $sizes_conditions["where"]["2"] = "arsimos.deleted_on is null";
    $sizes_conditions["order_by"] = "sizes.position";
    $sizes_conditions["group_by"] = "arsimos.size_id";
    $sizes_data = db_read("arsimos", "arsimos.size_id sizes.name", $sizes_conditions);
    if (!empty($sizes_data)) {
        while ($row = db_fetch_num($sizes_data)) {
            $sizes[$row[0]] = $row[1];
        }
    } else {
        $sizes = null;
    }
} else {
    $sizes = null;
}

//required for selectboxes: modifications
$modifications = array();

if (!empty($bindingdata["article_id"]) && !empty($bindingdata["size_id"])) {
    $modifications_conditions["left_join"]["1"] = "modifications modifications.id arsimos.modification_id";
    $modifications_conditions["where"]["1"] = "arsimos.article_id = " . $bindingdata["article_id"];
    $modifications_conditions["where"]["2"] = "arsimos.size_id = " . $bindingdata["size_id"];
    $modifications_conditions["where"]["3"] = "arsimos.modification_id isnot NULL";
    $modifications_conditions["where"]["4"] = "arsimos.deleted_on is null";
    $modifications_data = db_read("arsimos", "arsimos.modification_id modifications.name", $modifications_conditions);
    if (!empty($modifications_data)) {
        while ($row = db_fetch_num($modifications_data)) {
            $modifications[$row[0]] = $row[1];
            $modifications[$row[0]] = $row[1];
        }
        $showempty_mod_sql = "SELECT * FROM arsimos WHERE arsimos.article_id = ". $bindingdata["article_id"] ." AND arsimos.size_id = ". $bindingdata["size_id"] ." AND arsimos.modification_id IS NULL";
        $showempty_mod_query = db_query($showempty_mod_sql);
        if (db_num_rows($showempty_mod_query) == 0) {
            $showempty_mod = false;
        } else {
            $showempty_mod = true;
        }
    } else {
        $modifications = null;
    }
} else {
    $modifications = null;
}

//required for radiobuttons (garmentuser bound to garment or not)
$userboundswitch[1] = $lang["yes"];
$userboundswitch[2] = $lang["no"];

//required for radiobuttons (homewash or not)
$homewashswitch[1] = $lang["yes"];
$homewashswitch[2] = $lang["no"];

//required for history
$history_query = "SELECT `last_distributions`.`starttime` AS 'log_garmentusers_garments_starttime',
    `garmentusers`.`id` AS 'garmentusers_id',
    `garmentusers`.`surname` AS 'garmentusers_surname',
    `garmentusers`.`title` AS 'garmentusers_title',
    `garmentusers`.`name` AS 'garmentusers_name',
    `garmentusers`.`maidenname` AS 'garmentusers_maidenname',
    `garmentusers`.`initials` AS 'garmentusers_initials',
    `garmentusers`.`gender` AS 'garmentusers_gender',
    `garmentusers`.`intermediate` AS 'garmentusers_intermediate',
    `garmentusers`.`personnelcode` AS 'garmentusers_personnelcode'
    FROM
    (
        SELECT `log_garmentusers_garments`.*
        FROM
        `log_garmentusers_garments`
        WHERE `log_garmentusers_garments`.`garment_id` = '". $garment_post_id ."'
        ORDER BY `log_garmentusers_garments`.`starttime` DESC
        LIMIT 0, 5
    ) `last_distributions`
    INNER JOIN `garments` ON `last_distributions`.`garment_id` = `garments`.`id`
    INNER JOIN `garmentusers` ON `last_distributions`.`garmentuser_id` = `garmentusers`.`id`
    GROUP BY `garmentusers`.`id`
    ORDER BY `last_distributions`.`starttime` DESC";
$historydata = db_query($history_query);

//required for status (only in case we are editing an existing garment)
if (!empty($garment_post_id)) {
    $status_conditions["where"]["1"] = "id = " . $detailsdata["scanlocation_id"];
    $status = db_fetch_num(db_read("scanlocations", "translate", $status_conditions));
    $status = $status[0];
} else {
    $status = null;
}

//required for sub_status (only in case we are editing an existing garment)
if (!empty($garment_post_id)) {
    $sub_status_conditions["where"]["1"] = "id = " . $detailsdata["sub_scanlocation_id"];
    $sub_status = db_fetch_num(db_read("sub_scanlocations", "translate", $sub_status_conditions));
    $sub_status = $sub_status[0];
} else {
    $sub_status = null;
}


//required for: repairs count
if (!empty($garment_post_id)) {
    $repairs_count_conditions["where"]["1"] = "garment_id = " . $garment_post_id;
    $repairs_count_conditions["where"]["2"] = "type = repair";
    $repairs_count_res = db_count("garments_despecklesandrepairs", "garment_id", $repairs_count_conditions);
    $repairs_count_arr = db_fetch_num($repairs_count_res);
    $counts["repairs"] = $repairs_count_arr[0];
} else {
    $counts["repairs"] = null;
}

//required for: despeckles count
if (!empty($garment_post_id)) {
    $despeckles_count_conditions["where"]["1"] = "garment_id = " . $garment_post_id;
    $despeckles_count_conditions["where"]["2"] = "type = despeckle";
    $despeckles_count_res = db_count("garments_despecklesandrepairs", "garment_id", $despeckles_count_conditions);
    $despeckles_count_arr = db_fetch_num($despeckles_count_res);
    $counts["despeckles"] = $despeckles_count_arr[0];
} else {
    $counts["despeckles"] = null;
}

//required for: lastdeposit date
if (!empty($garment_post_id)) {
    $lastdeposit_query = "SELECT `date` FROM `log_depositlocations_garments` WHERE `garment_id` = '". $garment_post_id ."' ORDER BY `date` DESC LIMIT 0,1";
    $lastdeposit_res = db_query($lastdeposit_query);
    if (!($lastdeposit = db_fetch_assoc($lastdeposit_res))){ $lastdeposit["date"] = "-"; }
} else {
    $lastdeposit["date"] = null;
}

//required for scanlocation history
$scanlocation_history_query = "SELECT `s`.`translate` AS 'status', `lgs`.`date` AS 'date'
                                 FROM `technix_log`.`log_garments_scanlocations` `lgs`
                           INNER JOIN `scanlocations` `s` ON `lgs`.`scanlocation_id` = `s`.`id`
                                WHERE `lgs`.`garment_id` = ". $garment_post_id ."
                             ORDER BY `lgs`.`date` DESC";
$scanlocation_history = db_query($scanlocation_history_query);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "articles" => $articles,
    "sizes" => $sizes,
    "modifications" => $modifications,
    "showempty_mod" => $showempty_mod,
    "userboundswitch" => $userboundswitch,
    "homewashswitch" => $homewashswitch,
    "bindingdata" => $bindingdata,
    "detailsdata" => $detailsdata,
    "historydata" => $historydata,
    "scanlocation_history" => $scanlocation_history,
    "status" => $status,
    "sub_status" => $sub_status,
    "active_tab" => $active_tab,
    "counts" => $counts,
    "lastdeposit" => $lastdeposit
);

template_parse($pi, $urlinfo, $cv);

?>
