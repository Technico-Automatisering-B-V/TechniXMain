<?php

/**
 * Client Details
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2012 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi = array();
$pi["group"] = "Technico";
$pi["filename_this"] = "client_details.php";
$pi["filename_list"] = "clients.php";
$pi["filename_details"] = "client_details.php";
$pi["template"] = "layout/pages/client_details.tpl";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}

/**
 * Collect page content
 */
$active_tab = (!empty($_POST["active_tab"])) ? trim($_POST["active_tab"]) : "tab1";

$detailsdata = array(
    "name" => (!empty($_POST["name"])) ? trim(ucfirst($_POST["name"])) : "",
    "phone" => (!empty($_POST["phone"])) ? trim($_POST["phone"]) : "",
    "fax" => (!empty($_POST["fax"])) ? trim($_POST["fax"]) : "",
    "country" => (!empty($_POST["country"])) ? trim(ucfirst($_POST["country"])) : "",
    "address_street" => (!empty($_POST["address_street"])) ? trim(ucfirst($_POST["address_street"])) : "",
    "address_zipcode" => (!empty($_POST["address_zipcode"])) ? trim(strtoupper($_POST["address_zipcode"])) : "",
    "address_city" => (!empty($_POST["address_city"])) ? trim(ucfirst($_POST["address_city"])) : "",
);

$requiredfields = array();
$urlinfo = array();

$table = "clients";

if (empty($detailsdata["name"])){ array_push($requiredfields, $lang["name"]); }

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "add") {
        if (empty($requiredfields)) {
            db_insert($table, $detailsdata);

            redirect($pi["filename_list"]);

        } else if (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["title"] = $lang["add_location"];

	} else if (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
            if (empty($requiredfields)) {

                db_update($table, $_POST["id"], $detailsdata);
                redirect($pi["filename_list"]);

            } else if (isset($_POST["detailssubmit"])) {
                $pi["note"] = html_requiredfields($requiredfields);
            }

            if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
                if (isset($_POST["confirmed"])) {

                    db_delete($table, $_POST["id"]);
                    redirect($pi["filename_list"]);

                } else if (!isset($_POST["abort"])) {
                    $pi["note"] = html_delete($_POST["id"], $lang["location"]);
                }
            }

            $pi["page"] = "details";
            $pi["title"] = $lang["location_details"];

            //continue showing the page with details
            $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

            //we need the id for toolbar buttons
            $urlinfo["id"] = $_POST["id"];
	}

} else {
    //we haven't got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "active_tab" => $active_tab,
    "detailsdata" => $detailsdata
);

template_parse($pi, $urlinfo, $cv);

?>
