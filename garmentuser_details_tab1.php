<?php

// Required for selectbox: genders
$genders[1] = $lang["male"];
$genders[2] = $lang["female"];

// Required for selectbox: distribution
$distribution[1] = $lang["allow"];
$distribution[2] = $lang["deny"];

// Required for radiobuttons: date_service_on_switch
$service_on_switches["unlimited"] = $lang["unlimited"];
$service_on_switches["date"] = $lang["by_date"];

// Required for radiobuttons: date_service_off_switch
$service_off_switches["unlimited"] = $lang["unlimited"];
$service_off_switches["date"] = $lang["by_date"];

if (isset($_GET["ref"]) || (isset($_POST["gosubmit"]) && !isset($_POST["detailssubmitnew"]))) {
    if (!empty($gu_data["maxcredit"])){ $creditoption = "owncredit"; }
    if (!empty($gu_data["timelock"])){ $timelockoption = "owntimelock"; }
    if (!empty($gu_data["daysbeforelock"])){ $blockageoption = "ownblockage"; }
    if (!empty($gu_data["daysbeforewarning"])){ $warningoption = "ownwarning"; }

    if (!isset($_POST["editsubmit"])){
        $date_service_on = $gu_data["date_service_on"];
        $date_service_off = $gu_data["date_service_off"];

        $service_on_selected = (!empty($gu_data["date_service_on"])) ? "date" : "unlimited";
        $service_off_selected = (!empty($gu_data["date_service_off"])) ? "date" : "unlimited";
    }

    if ($gu_data["deleted_on"]) {
        $pi["toolbar"]["no_delete"] = true;
        $pi["toolbar_extra"] = "<form name=\"undelete\" enctype=\"multipart/form-data\" method=\"POST\">"
            . "<input type=\"hidden\" name=\"currentTab\" value=\"". $_POST["currentTab"] ."\">"
            . "<input type=\"hidden\" name=\"page\" value=\"details\" />"
            . "<input type=\"hidden\" name=\"gosubmit\" value=\"true\" />"
            . "<input type=\"hidden\" name=\"id\" value=\"" . $gu_data["id"] . "\" />"
            . "<input type=\"submit\" name=\"undelete\" value=\"" . $lang["undelete"] . "\" title=\"" . $lang["undelete"] . "\" onclick=\"this.form.action='garmentuser_details.php'; this.form.target='_self';\">"
            . "</form>";
    }
}

?>
