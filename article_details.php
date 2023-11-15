<?php

/**
 * Article details
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
$pi["access"] = array("master_data", "articles");
$pi["group"] = $lang["master_data"];
$pi["filename_list"] = "articles.php";
$pi["filename_details"] = "article_details.php";
$pi["template"] = "layout/pages/article_details.tpl";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Recursive xss prevention
 */
 function xss_sanitize($x)
 {
     if(is_array($x)) {
         foreach($x as $k=>$v) {
             $x[$k] = xss_sanitize($v);
         }
     }
     elseif(is_object($x)) {
         foreach($x as $k=>$v) {
             $x->$k = xss_sanitize($v);
         }
     }
     else {
         $x = htmlspecialchars($x, ENT_QUOTES);
     }
     
     return $x;
 }

if (isset($_POST["detailssubmitnone"])){ redirect($pi["filename_list"]); }

/**
 * Define used variables
 */
$requiredfields = array();
$sizes_inuse = null;
$sizes_inuse_data = null;
$sizes_present_data = null;
$sizes_selected_data = null;
$urlinfo = array();

/**
 * Collect page content
 */
$detailsdata = array(
    "id" => (!empty($_POST["id"])) ? trim($_POST["id"]) : false,
    "description" => (!empty($_POST["description"])) ? trim($_POST["description"]) : "",
    "extra_info" => (!empty($_POST["extra_info"])) ? trim($_POST["extra_info"]) : "",
    "credit" => (!empty($_POST["credit"])) ? trim($_POST["credit"]) : "2",
    "distribution_from" => (!empty($_POST["distribution_from"])) ? trim($_POST["distribution_from"]) : "",
    "distribution_to" => (!empty($_POST["distribution_to"])) ? trim($_POST["distribution_to"]) : "",
    "articlenumber" => (!empty($_POST["articlenumber"])) ? trim($_POST["articlenumber"]) : "",
    "sizegroup_id" => (!empty($_POST["sizegroup_id"])) ? trim($_POST["sizegroup_id"]) : "",
    "distributor_id" => (!empty($_POST["distributor_id"])) ? trim($_POST["distributor_id"]) : "",
    "distributorlocation_id" => (!empty($_POST["distributorlocation_id"])) ? trim($_POST["distributorlocation_id"]) : "",
    "workwear_category_id" => (!empty($_POST["workwear_category_id"])) ? trim($_POST["workwear_category_id"]) : "",
);

$sizes_selected = (!empty($_POST["sizes_selected"])) ? $_POST["sizes_selected"] : array();

/** Required for selectbox: distribution_from/to **/
$hours = array_combine(range(1,24),range(1,24));

$table = "articles";

if (empty($detailsdata["description"])){ array_push($requiredfields, $lang["description"]); }
if (empty($detailsdata["distributor_id"]) || $detailsdata["distributor_id"] == "0"){ $detailsdata["distributor_id"] = null; }
if (empty($detailsdata["distributorlocation_id"]) || $detailsdata["distributorlocation_id"] == "0"){ $detailsdata["distributorlocation_id"] = null; }
if (empty($detailsdata["distribution_from"]) || $detailsdata["distribution_from"] == "0"){ $detailsdata["distribution_from"] = null; }
if (empty($detailsdata["distribution_to"]) || $detailsdata["distribution_to"] == "0"){ $detailsdata["distribution_to"] = null; }
if (empty($detailsdata["workwear_category_id"]) || $detailsdata["workwear_category_id"] == "0") { $detailsdata["workwear_category_id"] = null; }

$sizes_inuse_sql = "
    SELECT
        arsimos.size_id,
        arsimos.modification_id,
        arsimos.deleted_on
    FROM
    (
        SELECT
                tmp1.arsimo_id,
                tmp1.userbound,
                tmp2.scanlocation_id
        FROM
        (
            (
                SELECT DISTINCT
                    garments.arsimo_id,
                    IF( ISNULL( garments.garmentuser_id ), 0, 1 ) 'userbound'
                FROM garments
            )
            UNION
            (
                SELECT DISTINCT
                    log_distributorclients.arsimo_id,
                    log_distributorclients.userbound
                FROM log_distributorclients
            )
            UNION
            (
                SELECT DISTINCT
                    garmentusers_arsimos.arsimo_id,
                    garmentusers_arsimos.userbound
                FROM garmentusers_arsimos
            )
            UNION
            (
                SELECT DISTINCT
                    distributorlocations_loadadvice.arsimo_id,
                    1 AS 'userbound'
                FROM distributorlocations_loadadvice
            )
        ) tmp1
        LEFT JOIN (
            SELECT
                garments.arsimo_id,
                IF( ISNULL( garments.garmentuser_id ), 0, 1 ) 'userbound',
                garments.scanlocation_id 'scanlocation_id'
            FROM garments
            INNER JOIN scanlocations ON garments.scanlocation_id = scanlocations.id
        ) tmp2 ON tmp1.arsimo_id = tmp2.arsimo_id
            AND tmp1.userbound = tmp2.userbound
    ) tmp3
    INNER JOIN arsimos ON tmp3.arsimo_id = arsimos.id
    WHERE arsimos.article_id = " . $detailsdata["id"] . "
    GROUP BY arsimos.id
";

/**
 * Workwear Fetching
 */
if(isset($detailsdata['id']) && !empty($detailsdata['id'])) {
$workwear_article_info_sql = 'SELECT * FROM workwearmanagement_prices wp WHERE wp.article_id = '. $detailsdata["id"];
$arsimos_for_workwear_sql = 'SELECT *, s.name AS "size_name", ar.id AS "arsimo_id" FROM arsimos ar INNER JOIN sizes s ON s.id = ar.size_id WHERE ar.article_id = '. $detailsdata["id"]. ' AND ar.deleted_on IS NULL';
$workwear_arsimo_info_sql = 'SELECT *, s.name AS "size_name" FROM arsimos ar INNER JOIN sizes s ON s.id = ar.size_id INNER JOIN workwearmanagement_prices wp ON wp.arsimo_id = ar.id WHERE ar.article_id = '. $detailsdata["id"].' AND ar.deleted_on IS NULL';

$workwear_article_info_result = db_query($workwear_article_info_sql);
$arsimos_for_workwear_result = db_query($arsimos_for_workwear_sql);
$workwear_arsimo_info_result = db_query($workwear_arsimo_info_sql);

$workwear_article_data = array();
$arsimos_for_workwear_data = array();
$workwear_arsimo_data = array();

while($aRow = $workwear_article_info_result->fetch_assoc()) {
    $workwear_article_data[] = $aRow;
}
while($aRow = $arsimos_for_workwear_result->fetch_assoc()) {
    $arsimos_for_workwear_data[] = $aRow;
}
while($aRow = $workwear_arsimo_info_result->fetch_assoc()) {
    $workwear_arsimo_data[$aRow['arsimo_id']] = $aRow;
}
}
else {
    $workwear_article_data = array();
    $arsimos_for_workwear_data = array();
    $workwear_arsimo_data = array();
}

//echo "<pre>";
//print_r($workwear_article_data);
//echo "<hr/>";
//print_r($arsimos_for_workwear_data);
//echo "<hr/>";
//print_r($workwear_arsimo_data);
//echo "</pre>";

// Add article
if (isset($_POST["page"]) && $_POST["page"] == "add") {
    if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
        if (empty($requiredfields)) {
unset($detailsdata["id"]);	 
 	 // Insert the given article
            db_insert($table, $detailsdata);
            // Insert the arsimos
            $articles_last_insert_id = db_fetch_row(db_read_last_insert_id());
            $data["article_id"] = $articles_last_insert_id[0];

            // Required for checkboxlist: sizes - all sizes that are linked to the given sizegroup_id
            $sizes_all_conditions1["where"]["1"] = "sizegroup_id = " . $detailsdata["sizegroup_id"];
            $sizes_all_conditions1["order_by"] = "position";
            $sizes_all1 = db_read("sizes", "id name", $sizes_all_conditions1);

            // Required for checkboxlist: sizes, modifications - all modifications
            $modifications_all_conditions1["order_by"] = "name";
            $modifications_all1 = db_read("modifications", "id name", $modifications_all_conditions1);


            if (!empty($sizes_all1)) {
                while ($row = db_fetch_row($sizes_all1)) {
                    $data["size_id"] = $row[0];
                    $data["modification_id"] =  null;
                    $data["deleted_on"] =  "NOW()";
                    db_insert("arsimos", $data);

                    if (!empty($modifications_all1)) {
                        while ($mod = db_fetch_row($modifications_all1)) {
                           $data["modification_id"] =  $mod[0];
                           db_insert("arsimos", $data);
                        }
                        db_data_seek($modifications_all1, 0);
                    }
                }
            }


            if (!empty($sizes_selected)) {
                foreach ($sizes_selected as $num => $size) {
                    $temp = explode("_", $size);
                    $data["size_id"] = $temp[0];
                    $data["modification_id"] = ((!empty($temp[1])) ? $temp[1] : null);

                    // Update arsimo
                    $update_arsimo_sql = "
                           UPDATE `arsimos`
                           SET `deleted_on` = NULL
                           WHERE `article_id` = '" . $data["article_id"] . "' AND
                                 `size_id` = '" . $data["size_id"] . "' AND
                                 `modification_id` " . ((!empty($data["modification_id"])) ? "= " . $data["modification_id"] : "IS NULL");
                    db_query($update_arsimo_sql);
                }
            }
            /*
            if (!empty($sizes_selected))
            {
                foreach ($sizes_selected as $num => $size)
                {
                    $temp = explode("_", $size);
                    $data["size_id"] = $temp[0];
                    $data["modification_id"] = ((!empty($temp[1])) ? $temp[1] : null);
                    db_insert("arsimos", $data);
                }
            }
            */

            // Redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }
    }

    $pi["page"] = "add";
    $pi["title"] = $lang["add_article"];

} elseif ((isset($_POST["page"])) && ($_POST["page"] == "details") && ($detailsdata["id"])) {
    if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
        if (empty($requiredfields)) {
            $data["article_id"] = $detailsdata["id"];

            // Update the article
            db_update($table, $detailsdata["id"], $detailsdata);

            // Fetch all arsimos in use for this article that cannot be deleted
            $sizes_inuse_data = db_query($sizes_inuse_sql);
            if (!empty($sizes_inuse_data)) {
                while ($row = db_fetch_num($sizes_inuse_data)) {
                    $sizes_inuse[$row[0] . "_" . $row[1]] = $row[0] . "_" . $row[1];
                }
            }

            // Fetch all arsimos for this article
            $sizes_present_sql = "SELECT `size_id`, `modification_id` FROM `arsimos` WHERE `article_id` = " . $data["article_id"];
            $sizes_present_data = db_query($sizes_present_sql);

            if (!empty($sizes_present_data)) {
                while ($row = db_fetch_num($sizes_present_data)) {
                    $sizes_present[$row[0] . "_" . $row[1]] = $row[0] . "_" . $row[1];
                }

                // Delete arsimos that are present in the database but no longer necessary, testing if they are not in use first
                if (!empty($sizes_present)) {
                    foreach ($sizes_present as $num => $size) {
                        // For each present arsimo not in use and not selected
                        if (empty($sizes_inuse[$size]) && empty($sizes_selected[$size])) {
                            // Prepare more id's
                            $temp = explode("_", $size);
                            $data["size_id"] = $temp[0];
                            $data["modification_id"] = ((!empty($temp[1])) ? $temp[1] : null);

                            $update_arsimo_sql = "
                                UPDATE `arsimos`
                                SET `deleted_on` = ". "NOW()" ."
                                WHERE `article_id` = '" . $data["article_id"] . "' AND
                                 `size_id` = '" . $data["size_id"] . "' AND
                                 `modification_id` " . ((!empty($data["modification_id"])) ? "= " . $data["modification_id"] : "IS NULL");
                            db_query($update_arsimo_sql);

                            /*
                            // Delete arsimo
                            $delete_arsimo_sql = "
                               DELETE FROM `arsimos`
                                WHERE `article_id` = " . $data["article_id"] . "
                                  AND `size_id` = " . $data["size_id"] . "
                                  AND `modification_id` " . ((!empty($data["modification_id"])) ? "= " . $data["modification_id"] : "IS NULL");

                            db_query($delete_arsimo_sql);
                            */
                        }
                    }
                }
            }

            // Insert arsimos that are selected but not present in the database
            if (!empty($sizes_selected)) {
                foreach ($sizes_selected as $num => $size) {
                    $temp = explode("_", $size);
                    $data["size_id"] = $temp[0];
                    $data["modification_id"] = ((!empty($temp[1])) ? $temp[1] : null);

                    $update_arsimo_sql = "
                           UPDATE `arsimos`
                           SET `deleted_on` = NULL
                           WHERE `article_id` = '" . $data["article_id"] . "' AND
                                 `size_id` = '" . $data["size_id"] . "' AND
                                 `modification_id` " . ((!empty($data["modification_id"])) ? "= " . $data["modification_id"] : "IS NULL");
                    db_query($update_arsimo_sql);

                    /*
                    // Update arsimo
                    $update_arsimo_sql = "
                           INSERT INTO `arsimos` (`article_id`, `size_id`, `modification_id`)
                           VALUES ('" . $data["article_id"] . "',
                                   '" . $data["size_id"] . "',
                                  " . ((!empty($data["modification_id"])) ? "'" . $data["modification_id"] ."'" : "NULL") ."
                           )";
                    db_query($update_arsimo_sql);

                    */
                }
            }
            
            /**
            * Workwear Update
            */
            if(isset($_POST['fHuurPrijs']) && $_POST['fHuurPrijs'] > 0) {
                $update_rental_price = "UPDATE workwearmanagement_prices SET rental_price=". $_POST['fHuurPrijs']. " WHERE id='". $workwear_article_data[0]['id']."'";
                db_query($update_rental_price);
            }
            if(isset($_POST['fExtension']) && $_POST['fExtension'] > 0) {
                $update_extension = "UPDATE workwear_article_extensions SET extension='". $_POST['fExtension']."' WHERE article_id='". $workwear_article_data[0]['article_id']."'";
                db_query($update_extension);
            }
            if(isset($_POST['fKostPrijs']) && $_POST['fKostPrijs'] > 0) {
                //echo "<pre>";
                //print_r($_POST['fKostPrijs']);
                //echo "<br/>";
               //print_r($workwear_article_data[0]["price"]);
                if($_POST['fKostPrijs'] !== $workwear_article_data[0]["price"]) {
                    //echo "<br/>";
                    $update_article_pricing = "UPDATE workwearmanagement_prices SET price=". $_POST['fKostPrijs']. " WHERE id='". $workwear_article_data[0]['id']. "'";
                    db_query($update_article_pricing);
                    $update_fullpricing = "UPDATE workwearmanagement_prices wp, workwearmanagement_data wd SET wp.fullprice = ((wp.price + wd.insert_price) * (1 + (wd.increment / 100)));";
                    db_query($update_fullpricing);
                    //print("Updated!");
                }
                //echo "</pre>";
            }
            if(isset($_POST['maxWasbeurtenArtikel']) && $_POST['maxWasbeurtenArtikel'] > 0) {
                if($_POST['maxWasbeurtenArtikel'] !== $workwear_article_data[0]["maxwashcount"]) {
                    $update_article_washcount = "UPDATE workwearmanagement_prices SET maxwashcount=". $_POST['maxWasbeurtenArtikel']. " WHERE id='". $workwear_article_data[0]['id']."'";
                    db_query($update_article_washcount);
                }
            }
            
            /**
             * Workwear arsimo manipulation
             */
            if(isset($_POST['arsimocost']) && !empty($_POST['arsimocost'])) {
                $bHasRemovals = false;
                $bHasInserts = false;
                $bHasUpdates = false;
                $aRemovals = array();
                $aInserts = array();
                $aUpdates = array();
                
                //echo "<pre>";
                foreach($_POST['arsimocost'] as $iID=>$fValue) {
                    //echo "[". $iID."]";
                    if(array_key_exists($iID, $workwear_arsimo_data)) {
                        if(empty($fValue) || $fValue == null) {
                            //echo " => Remove arsimo\r\n";
                            $bHasRemovals = true;
                            $aRemovals[] = $iID;
                        }
                        else {
                            //echo " => Update arsimo\r\n";
                            $bHasUpdates = true;
                            $aUpdates[$iID] = $fValue;
                        }
                    }
                    else {
                        if(!empty($fValue) && $fValue > 0) {
                            //echo " => Insert arsimo\r\n";
                            $bHasInserts = true;
                            $aInserts[$iID] = $fValue;
                        }
                        else {
                            //echo "\r\n";
                        }
                    }
                }
                
                if($bHasRemovals) {
                    $sRemovals = "(";
                    $sRemovals.= implode($aRemovals, ",");
                    $sRemovals.= ")";
                    $sRemovals = "DELETE FROM workwearmanagement_prices WHERE arsimo_id IN ". $sRemovals;
                    //echo $sRemovals. "\r\n";
                    db_query($sRemovals);
                }
                if($bHasInserts) {
                    $sInserts = "";
                    $iIDs = array_keys($aInserts);
                    $iLastID = end($iIDs);
                    foreach($aInserts as $iID=>$fValue) {
                        $sInserts.= "(NULL, ". $iID. ", ". $fValue. ", ". $workwear_article_data[0]["maxwashcount"]. ")";
                        if($iID !== $iLastID) {
                            $sInserts.= ",";
                        }
                    }
                    $sInserts = "INSERT INTO workwearmanagement_prices (article_id, arsimo_id, price, maxwashcount) VALUES ". $sInserts;
                    //echo $sInserts. "\r\n";
                    db_query($sInserts);
                }
                if($bHasUpdates) {
                    foreach($aUpdates as $iID=>$fValue) {
                        $sUpdate = "UPDATE workwearmanagement_prices SET price=". $fValue. " WHERE arsimo_id=". $iID;
                        //echo $sUpdate. "\r\n";
                        db_query($sUpdate);
                    }
                }
                if($bHasInserts || $bHasUpdates) {
                    $update_fullpricing = "UPDATE workwearmanagement_prices wp, workwearmanagement_data wd SET wp.fullprice = ((wp.price + wd.insert_price) * (1 + (wd.increment / 100)));";
                    //echo $update_fullpricing;
                    db_query($update_fullpricing);
                }
                //echo "</pre>";
                //die();
            }


            // Redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                // Delete the arsimos
                db_delete_where("arsimos", "article_id", $detailsdata["id"]);

                // Delete the article
                db_delete($table, $detailsdata["id"]);

                // Redirect to list
                redirect($pi["filename_list"]);

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["id"], $lang["article"]);
            }
        }
    }

    $pi["page"]  = "details";
    $pi["title"] = $lang["article_details"];

    // Continue showing the page with details
    $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $detailsdata["id"]));

    // We need the id for toolbar buttons
    $urlinfo["id"] = $detailsdata["id"];
} else {
    // We haven't got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

if (isset($_POST["sizegroup_id"])) {
    $detailsdata["sizegroup_id"] = $_POST["sizegroup_id"];
} elseif (empty($detailsdata["sizegroup_id"])) {
    // Use the sizegroup_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_sizegroup_conditions["order_by"] = "name";
    $selected_sizegroup_conditions["limit_start"] = 0;
    $selected_sizegroup_conditions["limit_num"] = 1;
    $detailsdata["sizegroup_id"] = db_fetch_row(db_read("sizegroups", "id", $selected_sizegroup_conditions));
    $detailsdata["sizegroup_id"] = $detailsdata["sizegroup_id"][0];
}

// Required for selectbox: sizegroups
$sizegroups_conditions["order_by"] = "name";
$sizegroups = db_read("sizegroups", "id name", $sizegroups_conditions);

// Required for checkboxlist: sizes - selected (only when editing)
if ($detailsdata["id"]) {
    $sizes_selected_data_conditions["where"]["1"] = "article_id = " . $detailsdata["id"];
    $sizes_selected_data_conditions["where"]["2"] = "deleted_on is null";

    $sizes_selected_data = db_read("arsimos", "size_id modification_id", $sizes_selected_data_conditions);
}

if ($sizes_selected_data) {
    while ($row = db_fetch_row($sizes_selected_data)) {
        $sizes_selected[$row[0] . "_" . $row[1]] = $row[0] . "_" . $row[1];
    }
}

// Required for checkboxlist: sizes - in use (only when editing)
if ($detailsdata["id"]) {
    $sizes_inuse_data = db_query($sizes_inuse_sql);

    while ($row = db_fetch_num($sizes_inuse_data)) {
        $sizes_inuse[$row[0] . "_" . $row[1]] = $row[0] . "_" . $row[1];
    }
}

if (!empty($detailsdata["id"]) && !empty($detailsdata["articleimage_id"])) {
    $selected_image_conditions["where"]["1"] = "id = " . $detailsdata["articleimage_id"];
    $detailsdata["image_data"] = db_fetch_row(db_read("articleimages", "bin_data", $selected_image_conditions));
    $detailsdata["image_data"] = $detailsdata["image_data"][0];
}

// Required for checkboxlist: sizes - all sizes that are linked to the given sizegroup_id
$sizes_all_conditions["where"]["1"] = "sizegroup_id = " . $detailsdata["sizegroup_id"];
$sizes_all_conditions["order_by"] = "position";
$sizes_all = db_read("sizes", "id name", $sizes_all_conditions);

// Required for checkboxlist: sizes, modifications - all modifications
$modifications_all_conditions["order_by"] = "name";
$modifications_all = db_read("modifications", "id name", $modifications_all_conditions);

//required for selectbox: stations
$stations_columns = "distributors.id distributorlocations.name distributors.doornumber";
$stations_conditions["inner_join"]["1"] = "distributorlocations distributorlocations.id distributors.distributorlocation_id";
$stations_conditions["order_by"] = geturl_order_by($stations_columns);
$stations = db_read("distributors", $stations_columns, $stations_conditions);


// Required for selectbox: distributorlocations
$distributorlocations_conditions["order_by"] = "name";
$distributorlocations = db_read("distributorlocations", "id name", $distributorlocations_conditions);

// Required for selectbox: artikel/ARSIMO
$priceassignmenttype = ["Artikel", "ARSIMO"];

// Required for selectbox: category
$workwear_category_columns = "id name";
$workwear_category_conditions["order_by"] = geturl_order_by($workwear_category_columns);
$workwear_categories = db_read("workwear_categories", $workwear_category_columns, $workwear_category_conditions);



/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata,
    "sizegroups" => $sizegroups,
    "sizes_selected" => $sizes_selected,
    "sizes_inuse" => $sizes_inuse,
    "sizes_all" => $sizes_all,
    "modifications_all" => $modifications_all,
    "stations" => $stations,
    "distributorlocations" => $distributorlocations,
    "hours" => $hours,
    "workwear_article_data" => $workwear_article_data,
    "arsimos_for_workwear_data" => $arsimos_for_workwear_data,
    "workwear_arsimo_data" => $workwear_arsimo_data,
    "$priceassignmenttype" => $priceassignmenttype,
    "workwear_categories" => $workwear_categories
);

template_parse($pi, $urlinfo, $cv);

?>
