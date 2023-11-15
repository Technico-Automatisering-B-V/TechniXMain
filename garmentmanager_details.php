<?php

/**
 * Garmentmanager details
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
$pi["access"] = array("manager", "supercard");
$pi["group"] = $lang["manager"];
$pi["filename_list"] = "garmentmanagers.php";
$pi["filename_details"] = "garmentmanager_details.php";
$pi["template"] = "layout/pages/garmentmanager_details.tpl";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
if (isset($_POST["id"])){
    $_POST["garmentuser_id"] = trim($_POST["id"]);
}

$bindingdata = array(
    "client_id" => (!empty($_POST["garmentuser_id"])) ? trim($_POST["garmentuser_id"]) : "1"
);

$detailsdata = array(
    "garmentuser_id" => (!empty($_POST["garmentuser_id"])) ? trim($_POST["garmentuser_id"]) : null,
    "limit_to_articles" => ($_POST["limitation"] === "to_articles") ? 1 : 0,
    "limit_to_profession" => ($_POST["limitation"] === "to_profession") ? 1 : 0,
    "maxcredit" => (!empty($_POST["maxcredit"])) ? trim($_POST["maxcredit"]) : null,
    "allow_normaluser" => (isset($_POST["allow_normaluser"])) ? "y" : "n",
    "allow_station" => (isset($_POST["allow_station"])) ? "y" : "n",
    "allow_supercard" => (isset($_POST["allow_supercard"])) ? "y" : "n",
    "allow_supername" => (isset($_POST["allow_supername"])) ? "y" : "n",
    "allow_overloaded" => (isset($_POST["allow_overloaded"])) ? "y" : "n",
    "deleted_on" => (!empty($_POST["deleted"])) ? trim($_POST["deleted_on"]) : null
);

if ($_POST["limitation"] === "to_articles") {
    //$detailsdata["allow_supercard"] = "y";
}

if (isset($_POST["chkArsimos"]) && !empty($_POST["chkArsimos"])) {
    $chkArsimos = $_POST["chkArsimos"];
} else {
    $chkArsimos = "";
}

// Get all articles
$tablea = "articles";
$columnsa = "description id articlenumber";
$urlinfoa["order_by"] = "description";
$urlinfoa["order_direction"] = "ASC";
$urlinfoa["limit_total"] = db_fetch_row(db_count($tablea, $columnsa, $urlinfoa));
$urlinfoa["limit_total"] = $urlinfoa["limit_total"][0];

$articles = db_read($tablea, $columnsa, $urlinfoa);

$requiredfields = array();
$urlinfo = array();

$table = "supergarmentusers";

if (empty($detailsdata["garmentuser_id"])) array_push($requiredfields, $lang["garmentuser"]);
if (empty($detailsdata["maxcredit"])) array_push($requiredfields, $lang["credit"]);

if (isset($_POST["page"]) && $_POST["page"] == "add") {
    if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
        if (empty($requiredfields)) {
            //insert the given garmentmanager
            db_insert($table, $detailsdata);

            //insert all the arsimos
            if (!empty($chkArsimos)) {
                foreach ($chkArsimos as $num => $arsId) {
                    $arsData = array(
                        "garmentuser_id" => $_POST["garmentuser_id"],
                        "arsimo_id" => $arsId
                    );
                    db_insert("supergarmentusers_arsimos", $arsData);
                }
            }

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }
    }

    $pi["page"] = "add";
    $pi["title"] = $lang["add_garmentmanager"];

} elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["garmentuser_id"])) {
    if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"]) || isset($_POST["submit"])) {
        if (empty($requiredfields)) {
            //update the garmentmanager
            db_update_where($table, "garmentuser_id", $_POST["old_garmentuser_id"], $detailsdata);

            //delete all the supergarmentuser arsimos
            db_delete_where("supergarmentusers_arsimos", "garmentuser_id", $_POST["garmentuser_id"]);
            
           
            //insert all the arsimos if limited to articles
            if ($detailsdata["limit_to_articles"]) {
                if (!empty($chkArsimos)) {
                    foreach ($chkArsimos as $num => $arsId) {
                        $arsData = array(
                            "garmentuser_id" => $_POST["garmentuser_id"],
                            "arsimo_id" => $arsId
                        );
                        db_insert("supergarmentusers_arsimos", $arsData);
                    }
                }
            }

            //redirect to list
            if(!isset($_POST["submit"])) {
                redirect($pi["filename_list"]);
            }
        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                //mark the garmentmanager as deleted
                $garmentmanager_conditions = array("deleted_on" => "NOW()");
                db_update_where($table, "garmentuser_id", $_POST["garmentuser_id"], $garmentmanager_conditions);

                //delete all the supergarmentuser arsimos
                db_delete_where("supergarmentusers_arsimos", "garmentuser_id", $_POST["garmentuser_id"]);
                
                //redirect to list
                redirect($pi["filename_list"]);

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["garmentuser_id"], $lang["garmentmanager"]);
            }
        }
    }

    if (isset($_POST["detailssubmitnone"])) {
        redirect($pi["filename_list"]);
    }

    $pi["page"] = "details";
    $pi["title"] = $lang["garmentmanager_details"];

    //continue showing the page with details
    $detailsdata = db_fetch_assoc(db_read_where($table, "garmentuser_id", $_POST["garmentuser_id"]));

    //get all the enabled arsimos
    $ear = db_read_where("supergarmentusers_arsimos", "garmentuser_id", $_POST["garmentuser_id"]);
    while ($row = db_fetch_assoc($ear)){
        $arsimosEnabled[$row["arsimo_id"]] = 1;
    }

    //we need the id for toolbar buttons
    $urlinfo["id"] = $_POST["garmentuser_id"];
} else {
    //we haven"t got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "bindingdata" => $bindingdata,
    "detailsdata" => $detailsdata,
    "articles" => $articles,
    "arsimosEnabled" => $arsimosEnabled
);

template_parse($pi, $urlinfo, $cv);

?>
