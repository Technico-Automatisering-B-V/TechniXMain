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
$pi = array();
$pi["group"] = "Technico";
$pi["title"] = $lang["location_details"];
$pi["filename_list"] = "circulationgroups.php";
$pi["filename_details"] = "circulationgroup_details.php";
$pi["template"] = "layout/pages/circulationgroup_details.tpl";
$pi["toolbar"]["no_delete"] = "yes";
$pi["toolbar"]["no_new"] = "yes";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){ redirect("login.php"); }

/**
 * Collect page content
 */
$dd = array(
    "fifo_distribution" => (!empty($_POST["fifo_distribution"])) ? trim($_POST["fifo_distribution"]) : "n"
);

$requiredfields = array();
$urlinfo = array();

$table = "circulationgroups";

if (empty($dd["fifo_distribution"])){ array_push($requiredfields, $lang["fifo_distribution"]); }

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "add") {
        if (empty($requiredfields)) {
            //insert the given distributorlocation
            db_insert($table, $dd);

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["title"] = $lang["add_distributorlocation"];

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields)) {
            db_update($table, $_POST["id"], $dd);
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                db_delete($table, $_POST["id"]);
                redirect($pi["filename_list"]);

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["id"], $lang["circulationgroup"]);
            }
        }

        $pi["page"] = "details";
        $pi["title"] = $lang["location_details"];

        //continue showing the page with details
        $dd = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

        //we need the id for toolbar buttons
        $urlinfo["id"] = $_POST["id"];
    }

} else {
    redirect($pi["filename_list"]);
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
