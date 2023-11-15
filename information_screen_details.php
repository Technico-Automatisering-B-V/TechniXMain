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
$pi["access"] = array("master_data", "information_screens");
$pi["group"] = $lang["master_data"];
$pi["title"] = $lang["information_screens"];
$pi["filename_list"] = "information_screens.php";
$pi["filename_details"] = "information_screen_details.php";
$pi["template"] = "layout/pages/information_screen_details.tpl";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
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
    "sort" => (!empty($_POST["sort"])) ? trim($_POST["sort"]) : "",
    "show_fullname" => (!empty($_POST["show_fullname"])) ? trim($_POST["show_fullname"]) : ""
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
