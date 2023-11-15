<?php

/**
 * FiFo distribution details
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
$pi["filename_this"] = "fifo_distribution_details.php";
$pi["filename_list"] = "fifo_distribution.php";
$pi["filename_details"] = "fifo_distribution_details.php";
$pi["template"] = "layout/pages/fifo_distribution_details.tpl";

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
    "circulationgroup_id" => (!empty($_POST["circulationgroup_id"])) ? trim($_POST["circulationgroup_id"]) : "",
    "dayofweek" => (!empty($_POST["dayofweek"])) ? trim($_POST["dayofweek"]) : "",
    "from_hours" => (!empty($_POST["from_hours"])) ? trim(ucfirst($_POST["from_hours"])) : "",
    "to_hours" => (!empty($_POST["to_hours"])) ? trim(ucfirst($_POST["to_hours"])) : ""
);

$requiredfields = array();
$urlinfo = array();

$table = "circulationgroups_fifo_distribution";

if (empty($detailsdata["circulationgroup_id"])){ array_push($requiredfields, $lang["location"]); }
if (empty($detailsdata["from_hours"])){ array_push($requiredfields, $lang["distribution_from"]); }
if (empty($detailsdata["to_hours"])){ array_push($requiredfields, $lang["distribution_to"]); }

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "add") {
        if (empty($requiredfields)) {
            db_insert($table, $detailsdata);

            redirect($pi["filename_list"]);

        } else if (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["title"] = $lang["add_fifo_distribution"];

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
                    $pi["note"] = html_delete($_POST["id"], $lang["fifo_distribution"]);
                }
            }

            $pi["page"] = "details";
            $pi["title"] = $lang["fifo_distribution_details"];

            //continue showing the page with details
            $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

            //we need the id for toolbar buttons
            $urlinfo["id"] = $_POST["id"];
	}

} else {
    //we haven't got the correct page info, redirect to list
    redirect($pi["filename_list"]);
}


// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);

/** Required for selectbox: daysofweek **/
$days = array_combine(range(1,7),range(1,7));

/** Required for selectbox: distribution_from/to **/
$hours = array_combine(range(1,24),range(1,24));

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "active_tab" => $active_tab,
    "detailsdata" => $detailsdata,
    "circulationgroups" => $circulationgroups,
    "days" => $days,
    "hours" => $hours
);

template_parse($pi, $urlinfo, $cv);

?>
