<?php

/**
 * Garment washcount details
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
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
$pi["access"] = array("linen_service", "washcount_garments");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["washcount_garments"];
$pi["filename_list"] = "garmentwashcount.php";
$pi["filename_details"] = "garmentwashcount_details.php";
$pi["template"] = "layout/pages/garmentwashcount_details.tpl";
$pi["page"] = "details";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$requiredfields = array();
$table = "distributorlocations";

if (isset($_POST["detailssubmitnone"])){
    redirect($pi["filename_list"]);
}

/**
 * Update form fields
 */
if (!isset($_POST["detailssubmit"])) {
    if (!isset($_POST["hassubmit"])) {

        $details = db_fetch_assoc(db_query("SELECT * FROM `" . $table . "` WHERE `id` = '" . $_POST["id"] . "' LIMIT 1"));

        if (empty($details["washcount_check_from"])){ $certain_date = "0"; }else{ $certain_date = "1"; }
        if ($details["washcount_check_from"] == $details["washcount_check_to"]){ $multiple_days = "0"; }else{ $multiple_days = "1"; }

        $detailsdata = array(
            "id" => $_POST["id"],
            "washcount_check_enabled" => $details["washcount_check_enabled"],
            "washcount_check_max" => $details["washcount_check_max"],
            "certain_date" => $certain_date,
            "multiple_days" => $multiple_days,
            "washcount_check_from" => $details["washcount_check_from"],
            "washcount_check_to" => $details["washcount_check_to"]
        );
    } else {
        $detailsdata = array(
            "id" => $_POST["id"],
            "washcount_check_enabled" => (isset($_POST["washcount_check_enabled"])) ? trim($_POST["washcount_check_enabled"]) : "0",
            "washcount_check_max" => (!empty($_POST["washcount_check_max"])) ? trim($_POST["washcount_check_max"]) : "0",
            "certain_date" => (isset($_POST["certain_date"])) ? trim($_POST["certain_date"]) : "0",
            "multiple_days" => (isset($_POST["multiple_days"])) ? trim($_POST["multiple_days"]) : "0",
            "washcount_check_from" => (isset($_POST["washcount_check_from"])) ? trim($_POST["washcount_check_from"]) : "",
            "washcount_check_to" => (isset($_POST["washcount_check_to"])) ? trim($_POST["washcount_check_to"]) : ""
        );
    }
}

/**
 * Form is realy submitted
 */
if (isset($_POST["detailssubmit"])) {

    $detailsdata = array(
        "id" => $_POST["id"],
        "washcount_check_enabled" => (isset($_POST["washcount_check_enabled"])) ? trim($_POST["washcount_check_enabled"]) : "0",
        "washcount_check_max" => (!empty($_POST["washcount_check_max"])) ? trim($_POST["washcount_check_max"]) : "0",
        "certain_date" => (isset($_POST["certain_date"])) ? trim($_POST["certain_date"]) : "0",
        "multiple_days" => (isset($_POST["multiple_days"])) ? trim($_POST["multiple_days"]) : "0",
        "washcount_check_from" => (isset($_POST["washcount_check_from"])) ? trim($_POST["washcount_check_from"]) : "",
        "washcount_check_to" => (isset($_POST["washcount_check_to"])) ? trim($_POST["washcount_check_to"]) : ""
    );

    if (!empty($detailsdata["washcount_check_from"]) && !empty($detailsdata["washcount_check_to"])){
        if ($detailsdata["washcount_check_to"] < $detailsdata["washcount_check_from"]){
            $pi["note"] = html_error($lang["error_date_from_greater_then_to"]);
        }
    }

    if (empty($pi["note"])) {
        function parseNull($s) {
            if (chop($s) != "") {
                if (strtolower(chop($s)) == "null") {
                    return "NULL";
                }else{
                    return "'" . $s . "'";
                }
            } else {
                return "NULL";
            }
        }

        if (empty($detailsdata["washcount_check_to"])) {
            $detailsdata["washcount_check_to"] = $detailsdata["washcount_check_from"];
        }

        //update the record
        $update_query = "UPDATE `". $table ."` SET `washcount_check_enabled` = '". $detailsdata["washcount_check_enabled"] ."', `washcount_check_max` = '". $detailsdata["washcount_check_max"] ."', `washcount_check_from` = ". parseNull($detailsdata["washcount_check_from"]) .", `washcount_check_to` = ". parseNull($detailsdata["washcount_check_to"]) ." WHERE `id` = '". $detailsdata["id"] ."'";
        $update_sql = db_query($update_query);

        print($update_query);

        //redirect to list
        redirect($pi["filename_list"]);

        $pi["page"] = "details";
        $pi["title"] = $lang["linen_service"];
    }
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
