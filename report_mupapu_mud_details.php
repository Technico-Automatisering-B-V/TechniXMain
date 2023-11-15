<?php

/**
 * Report MUPAPU MUD details
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
$pi["group"] = "Technico";
$pi["filename_this"] = "report_mupapu_mud_details.php";
$pi["filename_list"] = "report_mupapu_mud.php";
$pi["filename_details"] = "report_mupapu_mud_details.php";
$pi["template"] = "layout/pages/report_mupapu_mud_details.tpl";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Collect page content
 */
$detailsdata = array(
    "day" => (!empty($_POST["day"])) ? $_POST["day"] : "",
    "hours" => (!empty($_POST["hours"])) ? $_POST["hours"] : "0",
    "minutes" => (!empty($_POST["minutes"])) ? $_POST["minutes"] : "0",
    "description" => (!empty($_POST["description"])) ? $_POST["description"] : ""
);

$requiredfields = array();
$urlinfo = array();

$table = "loadadvice_mupapu";

if (empty($detailsdata["day"])) array_push($requiredfields, $lang["day"]);
if (empty($detailsdata["description"])) array_push($requiredfields, $lang["description"]);

if (isset($_POST["gosubmit"]) || isset($_POST["detailssubmit"])) {
    if (isset($_POST["page"]) && $_POST["page"] == "add") {
        if (empty($requiredfields)) {
            //insert the given period
            db_insert($table, $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        $pi["page"] = "add";
        $pi["title"] = "MUPAPU " . strtolower($lang["add_period"]);

    } elseif (isset($_POST["page"]) && $_POST["page"] == "details" && !empty($_POST["id"])) {
        if (empty($requiredfields)) {
            //update the period
            db_update($table, $_POST["id"], $detailsdata);

            //redirect to list
            redirect($pi["filename_list"]);

        } elseif (isset($_POST["detailssubmit"])) {
            $pi["note"] = html_requiredfields($requiredfields);
        }

        if (isset($_POST["delete"]) && $_POST["delete"] == "yes") {
            if (isset($_POST["confirmed"])) {
                //delete the period
                db_delete($table, $_POST["id"]);

                //redirect to list
                redirect($pi["filename_list"]);

            } elseif (!isset($_POST["abort"])) {
                    $pi["note"] = html_delete($_POST["id"], $lang["period"]);
            }
        }

        $pi["page"] = "details";
        $pi["title"] = "MUPAPU " . strtolower($lang["period_details"]);

        //continue showing the page with details
        $detailsdata = db_fetch_assoc(db_read_row_by_id($table, $_POST["id"]));

        //we need the id for toolbar buttons
        $urlinfo["id"] = $_POST["id"];
    }

} else {
    redirect($pi["filename_list"]);
}

// Required for selectbox: days
$days[1] = $lang["monday"];
$days[2] = $lang["tuesday"];
$days[3] = $lang["wednesday"];
$days[4] = $lang["thursday"];
$days[5] = $lang["friday"];
$days[6] = $lang["saturday"];
$days[7] = $lang["sunday"];

// Required for selectbox: hours
$hours[0] = "00";
$hours[1] = "01";
$hours[2] = "02";
$hours[3] = "03";
$hours[4] = "04";
$hours[5] = "05";
$hours[6] = "06";
$hours[7] = "07";
$hours[8] = "08";
$hours[9] = "09";
$hours[10] = "10";
$hours[11] = "11";
$hours[12] = "12";
$hours[13] = "13";
$hours[14] = "14";
$hours[15] = "15";
$hours[16] = "16";
$hours[17] = "17";
$hours[18] = "18";
$hours[19] = "19";
$hours[20] = "20";
$hours[21] = "21";
$hours[22] = "22";
$hours[23] = "23";

$minutes[0] = "00";
$minutes[1] = "01";
$minutes[2] = "02";
$minutes[3] = "03";
$minutes[4] = "04";
$minutes[5] = "05";
$minutes[6] = "06";
$minutes[7] = "07";
$minutes[8] = "08";
$minutes[9] = "09";
$minutes[10] = "10";
$minutes[11] = "11";
$minutes[12] = "12";
$minutes[13] = "13";
$minutes[14] = "14";
$minutes[15] = "15";
$minutes[16] = "16";
$minutes[17] = "17";
$minutes[18] = "18";
$minutes[19] = "19";
$minutes[20] = "20";
$minutes[21] = "21";
$minutes[22] = "22";
$minutes[23] = "23";
$minutes[24] = "24";
$minutes[25] = "25";
$minutes[26] = "26";
$minutes[27] = "27";
$minutes[28] = "28";
$minutes[29] = "29";
$minutes[30] = "30";
$minutes[31] = "31";
$minutes[32] = "32";
$minutes[33] = "33";
$minutes[34] = "34";
$minutes[35] = "35";
$minutes[36] = "36";
$minutes[37] = "37";
$minutes[38] = "38";
$minutes[39] = "39";
$minutes[40] = "40";
$minutes[41] = "41";
$minutes[42] = "42";
$minutes[43] = "43";
$minutes[44] = "44";
$minutes[45] = "45";
$minutes[46] = "46";
$minutes[47] = "47";
$minutes[48] = "48";
$minutes[49] = "49";
$minutes[50] = "50";
$minutes[51] = "51";
$minutes[52] = "52";
$minutes[53] = "53";
$minutes[54] = "54";
$minutes[55] = "55";
$minutes[56] = "56";
$minutes[57] = "57";
$minutes[58] = "58";
$minutes[59] = "59";

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "detailsdata" => $detailsdata,
    "days" => $days,
    "hours" => $hours,
    "minutes" => $minutes
);

template_parse($pi, $urlinfo, $cv);

?>
