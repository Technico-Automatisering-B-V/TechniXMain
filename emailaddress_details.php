<?php

/**
 * Emailaddress Details
 *
 * @author    G. I. Voros <gaborvoros@technico.nl>
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
$pi["group"] = $lang["technico"];
$pi["filename_list"] = "emailaddresses.php";
$pi["filename_details"] = "emailaddress_details.php";
$pi["template"] = "layout/pages/emailaddress_details.tpl";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Collect page content
 */
$detailsdata = array(
    "name" => (!empty($_POST["name"])) ? trim(ucfirst($_POST["name"])) : "",
    "email_address" => (!empty($_POST["email_address"])) ? trim(ucfirst($_POST["email_address"])) : "",
    "group" => (!empty($_POST["group"])) ? $_POST["group"] : "",
    "locale_id" => (!empty($_POST["locale_id"])) ? $_POST["locale_id"] : ""
);

$requiredfields = array();
$validfields = null;
$urlinfo = array();

$table = "emailaddresses";

if (empty($detailsdata["email_address"])){ array_push($requiredfields, $lang["email_address"]); }
if (empty($detailsdata["group"])){ array_push($requiredfields, $lang["group"]); }

if (!empty($detailsdata["email_address"]) && (!filter_var($detailsdata["email_address"], FILTER_VALIDATE_EMAIL))) {
    $validfields = $lang["please_enter_a_valid_email_address"];
}

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "add") {
	if (empty($requiredfields) && empty($validfields)) {
            $detailsdata["email_address"] = strtolower($detailsdata["email_address"]);
            $detailsdata["name"] = ucfirst($detailsdata["name"]);

            //insert the given emailaddress
            db_insert($table, $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            if (!empty($requiredfields)) {
                $pi["note"] = html_requiredfields($requiredfields);
            }
            if (!empty($validfields)) {
                $pi["note"] = html_warning($validfields);
            }
        }

        $pi["page"] = "add";
        $pi["title"] = $lang["add_email_address"];

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields) && empty($validfields)) {
            $detailsdata["email_address"] = strtolower($detailsdata["email_address"]);
            $detailsdata["name"] = ucfirst($detailsdata["name"]);

            //update the emailaddress
            db_update($table, $_POST["id"], $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            if (!empty($requiredfields)) {
                $pi["note"] = html_requiredfields($requiredfields);
            }
            if (!empty($validfields)) {
                $pi["note"] = html_warning($validfields);
            }
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                $sql = "SELECT `id`
                        FROM `emailaddresses`
                        WHERE `emailaddresses`.`group` = (SELECT emailaddresses.group FROM emailaddresses WHERE id = ".$_POST["id"].")";
                $count_by_group = db_num_rows(db_query($sql));

                if ($count_by_group > 1) {
                    // Delete the emailaddress
                    db_delete($table, $_POST["id"]);

                    // Redirect to list
                    redirect($pi["filename_list"]);
                } else {
                    $pi["note"] = html_warning($lang["cannot_delete_the_last_one_from_the_group"]);
                }

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["id"], $lang["email_address"]);
            }
        }

        $pi["page"] = "details";
        $pi["title"] = $lang["emailaddress_details"];

        //continue showing the page with details
        $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

        //we need the id for toolbar buttons
        $urlinfo["id"] = $_POST["id"];
    }
} else {
    redirect($pi["filename_list"]);
}


//required for selectbox: groups
$groups["ALERT"] = $lang["ALERT"];
$groups["BACKUP"] = $lang["BACKUP"];
$groups["CALIBRATION"] = $lang["CALIBRATION"];
$groups["CONTACT_FORM"] = $lang["CONTACT_FORM"];
$groups["FAILURE"] = $lang["FAILURE"];
$groups["GARMENT_WARNING"] = $lang["GARMENT_WARNING"];
$groups["IMPORTER"] = $lang["IMPORTER"];
$groups["LOAD_FAILURE"] = $lang["LOAD_FAILURE"];
$groups["MANAGEMENT_INFO"] = $lang["MANAGEMENT_INFO"];
$groups["PACKINGLIST"] = $lang["PACKINGLIST"];
$groups["SYNCHRONISER"] = $lang["SYNCHRONISER"];
$groups["TESTMSG"] = $lang["TESTMSG"];
$groups["WARNING"] = $lang["WARNING"];

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
    "groups" => $groups,
    "locales" => $locales
);

template_parse($pi, $urlinfo, $cv);

?>
