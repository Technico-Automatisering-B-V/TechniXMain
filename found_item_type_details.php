<?php

/**
 * Found item Details
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
$pi = array();
$pi["access"] = array("master_data", "found_item_types");
$pi["group"] = $lang["master_data"];
$pi["filename_list"] = "found_item_types.php";
$pi["filename_details"] = "found_item_type_details.php";
$pi["template"] = "layout/pages/found_item_type_details.tpl";

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
    "value" => (!empty($_POST["value"])) ? trim(ucfirst($_POST["value"])) : ""
);

$requiredfields = array();
$urlinfo = array();

$table = "found_item_types";

if (empty($detailsdata["value"])) array_push($requiredfields, $lang["value"]);

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "add") {
        if (empty($requiredfields)) {
            //insert the given function
            db_insert($table, $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);

	} elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
	}

        $pi["page"] = "add";
        $pi["title"] = $lang["add_found_item_type"];

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields)) {
            //update the function
            db_update($table, $_POST["id"], $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);

	} elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
	}

	if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                // Delete the function
		db_delete($table, $_POST["id"]);

		// Redirect to list
		redirect($pi["filename_list"]);

            } elseif (!isset($_POST["abort"])) {
                $pi["note"] = html_delete($_POST["id"], $lang["found_item"]);
            }
	}

        $pi["page"] = "details";
        $pi["title"] = $lang["found_item_details"];

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
