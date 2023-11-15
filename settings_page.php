<?php

/**
 * Settings
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
require_once "library/bootstrap.php";

/**
 * Page settings
 */
$pi = array();
$pi["group"] = "Technico";
$pi["title"] = $lang["settings"];
$pi["template"] = "layout/pages/settings.tpl";
$pi["filename_list"] = "settings.php";
$pi["page"] = "details";
$pi["toolbar"] = "no";
$pi["note"] = "";

$ui = array();

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
    "message" => (!empty($_POST["message"])) ? trim(ucfirst($_POST["message"])) : "",
    "color" => (!empty($_POST["color"])) ? trim($_POST["color"]) : "",
    "size" => (!empty($_POST["size"])) ? trim($_POST["size"]) : "",
    "speed" => (!empty($_POST["speed"])) ? trim($_POST["speed"]) : "",
    "sort" => (!empty($_POST["sort"])) ? trim($_POST["sort"]) : ""
);

$urlinfo = array();

$table = "information_screens";

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
            if (isset($_POST["detailssubmit"])) {
                db_update($table, $_POST["id"], $detailsdata);
                redirect($pi["filename_list"]);
            }

            $pi["page"] = "details";
            $pi["title"] = $lang["information_screen_details"];

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
