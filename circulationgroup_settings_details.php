<?php

/**
 * Distributorlocation Details
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
$pi["access"] = array("manager", "settings");
$pi["group"] = $lang["manager"];
$pi["title"] = $lang["settings"];
$pi["filename_list"] = "circulationgroups_settings.php";
$pi["filename_details"] = "circulationgroup_settings_details.php";
$pi["template"] = "layout/pages/circulationgroup_settings_details.tpl";
$pi["toolbar"]["no_new"] = "yes";
$pi["toolbar"]["no_delete"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$dd = array(
    "fifo_distribution" => (!empty($_POST["fifo_distribution"])) ? trim($_POST["fifo_distribution"]) : "n",
    "optimal_load" => (!empty($_POST["optimal_load"])) ? trim($_POST["optimal_load"]) : "n",
    "credit_free_distribution" => (!empty($_POST["credit_free_distribution"])) ? trim($_POST["credit_free_distribution"]) : "n"
);

$requiredfields = array();
$urlinfo = array();

$table = "circulationgroups";

if (empty($dd["fifo_distribution"])){ array_push($requiredfields, $lang["fifo_distribution"]); }
if (empty($dd["optimal_load"])){ array_push($requiredfields, $lang["loaded"]); }
if (empty($dd["credit_free_distribution"])){ array_push($requiredfields, $lang["credit_free_distribution"]); }

if (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields) && isset($_POST["detailssubmit"])) {
            db_update($table, $_POST["id"], $dd);
            redirect($pi["filename_list"]);
        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        } elseif (isset($_POST["detailssubmitnone"])) {
			redirect($pi["filename_list"]);
		}

        $pi["page"] = "details";
        $pi["title"] = $lang["location_details"];

        //continue showing the page with details
        $dd = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

        //we need the id for toolbar buttons
        $urlinfo["id"] = $_POST["id"];
    }


/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $dd
);

template_parse($pi, $urlinfo, $cv);

?>
