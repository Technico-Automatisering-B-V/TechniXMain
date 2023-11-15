<?php

/**
 * Clientdepartement Details
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
$pi["access"] = array("master_data", "departments");
$pi["group"] = $lang["master_data"];
$pi["filename_list"] = "clientdepartments.php";
$pi["filename_details"] = "clientdepartment_details.php";
$pi["template"] = "layout/pages/clientdepartment_details.tpl";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}
/**
 * Collect page content
 */
$detailsdata = array(
    "name" => (!empty($_POST["name"])) ? trim(ucfirst($_POST["name"])) : ""
);

$requiredfields = array();
$urlinfo = array();

$table = "clientdepartments";

if (empty($detailsdata["name"])) array_push($requiredfields, $lang["name"]);

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "add") {
        if (empty($requiredfields)) {
            db_insert($table, $detailsdata);
            redirect($pi["filename_list"]);
        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["title"] = $lang["add_clientdepartment"];

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields)) {
            db_update($table, $_POST["id"], $detailsdata);
            redirect($pi["filename_list"]);
        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                db_delete($table, $_POST["id"]);
                redirect($pi["filename_list"]);
            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["id"], $lang["clientdepartment"]);
            }
        }

        $pi["page"] = "details";
        $pi["title"] = $lang["clientdepartment_details"];

        //continue showing the page with details
        $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

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
    "detailsdata" => $detailsdata
);

template_parse($pi, $urlinfo, $cv);

?>
