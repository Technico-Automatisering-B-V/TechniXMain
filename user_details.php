<?php

/**
 * User details
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
$pi["group"] = $lang["accounts"];
$pi["filename_list"] = "users.php";
$pi["filename_details"] = "user_details.php";
$pi["template"] = "layout/pages/user_details.tpl";

if (isset($_POST["detailssubmitnone"])){ redirect($pi["filename_list"]); }

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}

/**
 * Collect page content
 */
$detailsdata = array(
    "username" => (!empty($_POST["username"])) ? $_POST["username"] : "",
    "password" => (!empty($_POST["password"])) ? $_POST["password"] : "",
    "locale_id" => (!empty($_POST["locale_id"])) ? $_POST["locale_id"] : ""
);

$q = db_query("SELECT `privileges` FROM `users` WHERE `id`='".$_POST["id"]."'");
$r = db_fetch_row($q);
$ar = json_decode($r[0], true);

$optiondata = array(
    "cpw" => (!empty($_POST["cpw"])) ? true : false,
    "change" => $_POST["change"]
);

$requiredfields = array();
$urlinfo = array();

$table = "users";

if (empty($detailsdata["username"]) && isset($_POST["page"]) && $_POST["page"] != "details") array_push($requiredfields, $lang["username"]);
if (empty($detailsdata["password"]) && ($optiondata["change"] == "true" || (isset($_POST["page"]) && $_POST["page"] == "add"))) array_push($requiredfields, $lang["password"]);
if (empty($detailsdata["locale_id"])) array_push($requiredfields, $lang["locale"]);

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["detailssubmitnone"])) {
        redirect($pi["filename_list"]);
        $pi["page"] = "add";
        $pi["title"] = $lang["add_user"];
    }

    if (isset($_POST["page"]) && $_POST["page"] == "add") {
        if (empty($requiredfields)) {
            //insert the given user
            $detailsdata["password"] = userdata_hash($detailsdata["username"], $detailsdata["password"]);

            $s = $_POST["privileges"];
            $json = json_encode($s);
            $detailsdata["privileges"] = $json;
            db_insert($table, $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);
        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["title"] = $lang["add_user"];

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if ($_POST["id"] == 1) {
            //the username Administrator should have ID 1 and cannot be deleted
            $pi["note"] = html_warning($lang["cannot_delete_administrator"]);
        }

        if (empty($requiredfields) && isset($_POST["detailssubmit"])) {
            if ($optiondata["change"] == "true") $detailsdata["password"] = userdata_hash($detailsdata["username"], $detailsdata["password"]);
            elseif (isset($detailsdata["password"])) unset($detailsdata["password"]);

            //get rid of the username - not the cleanest way, but safest we can do against hacking the form for now
            if (isset($detailsdata["username"])){ unset($detailsdata["username"]); }

            $s = $_POST["privileges"];
            $json = json_encode($s);
            $detailsdata["privileges"] = $json;

            //update the user
            db_update($table, $_POST["id"], $detailsdata);

            //we set the new locale_id for the session and unset locale_map. this way,
            //the localeparser will set a new locale_map based on the new locale_id.
            //all of that is done only if we are the user that we're updating right now.
            if ($_POST["id"] == $_SESSION["userid"]) {
                $_SESSION["locale_id"] = $detailsdata["locale_id"];
                if (isset($_SESSION["locale_map"])){ unset($_SESSION["locale_map"]); }
            }

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])){
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                //verify it is not the user Administrator we are trying to delete
                if ($_POST["id"] == 1) {
                    $pi["note"] = html_error($lang["cannot_delete_administrator"]);
                } else {
                    //delete the user
                    db_delete_where("users_circulationgroups", "user_id", $_POST["id"]);
                    db_delete($table, $_POST["id"]);

                    //redirect to list
                    redirect($pi["filename_list"]);
                }

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["id"], $lang["user"]);
            }
        }

        $pi["page"] = "details";
        $pi["title"] = $lang["user_details"];

        //continue showing the page with details
        $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

        //we need the id for toolbar buttons
        $urlinfo["id"] = $_POST["id"];
    }
} else {
    //we haven't got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

//required for selectbox: locales
$locales_conditions["order_by"] = "name";
$locales = db_read("locales", "id name", $locales_conditions);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata,
    "optiondata" => $optiondata,
    "locales" => $locales,
    "ar" => $ar
);

template_parse($pi, $urlinfo, $cv);

?>
