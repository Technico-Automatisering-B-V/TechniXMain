<?php

/**
 * Repair details
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2009 Technico Automatisering B.V.
 * @version   1.0
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi["access"] = array("linen_service", "despecklesandrepairs");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["repairs"];
$pi["filename_list"] = "repairs.php";
$pi["filename_details"] = "repair_details.php";
$pi["template"] = "layout/pages/repair_details.tpl";

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
    "description" => (!empty($_POST["description"])) ? trim($_POST["description"]) : ""
);

$requiredfields = array();
$urlinfo = array();

$table = "repairs";

if (empty($detailsdata["description"])){ array_push($requiredfields, $lang["description"]); }

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"]))
{
    if (isset($_POST["page"]) && $_POST["page"] == "add")
    {
        if (empty($requiredfields))
        {
            //insert the given repair
            db_insert($table, $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["subtitle"] = $lang["add_repairing_method"];

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields))
        {
            //update the repair
            db_update($table, $_POST["id"], $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes")
        {
            if (isset($_POST["confirmed"]))
            {
                //delete the repair
                db_delete($table, $_POST["id"]);

                //redirect to list
                redirect($pi["filename_list"]);

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["id"], $lang["repair"]);
            }
        }

        $pi["page"] = "details";
        $pi["subtitle"] = $lang["repairing_method_details"];

        //continue showing the page with details
        $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

        //we need the id for toolbar buttons
        $urlinfo["id"] = $_POST["id"];
    }

} else {
    //we haven"t got the correct page info, redirect to list
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
