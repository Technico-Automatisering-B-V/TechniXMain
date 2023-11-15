<?php

/**
 * Loading screen Details
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
$pi["access"] = array("master_data", "loading_screens");
$pi["group"] = $lang["master_data"];
$pi["title"] = $lang["loading_screens"];
$pi["filename_list"] = "loading_screens.php";
$pi["filename_details"] = "loading_screen_details.php";
$pi["template"] = "layout/pages/loading_screen_details.tpl";
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
    "sort" => (!empty($_POST["sort"])) ? trim($_POST["sort"]) : ""
);

$urlinfo = array();

$table = "loading_screens";

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
            if (isset($_POST["detailssubmit"])) {
                db_update($table, $_POST["id"], $detailsdata);
                redirect($pi["filename_list"]);
            }

            $pi["page"] = "details";
            $pi["title"] = $lang["loading_screen_details"];

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
