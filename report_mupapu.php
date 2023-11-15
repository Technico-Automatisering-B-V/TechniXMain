<?php

/**
 * Report MUPAPU
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require_once "include/mupapu.php";

/**
 * Page settings
 */
$pi["group"] = "Technico";
$pi["title"] = "MUPAPU";
$pi["filename_list"] = "report_mupapu.php";
$pi["filename_this"] = "report_mupapu.php";

if (isset($_GET["print"])) {
	$pi["template"] = "layout/pages/report_mupapu.print.tpl";
	$pi["page"] = "report";
} else {
	$pi["template"] = "layout/pages/report_mupapu.tpl";
	$pi["page"] = "simple";
}

$urlinfo = array();

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Use circulationgroup
 */
if (!empty($_POST["cid"]) && is_numeric($_POST["cid"])) $urlinfo["cid"] = $_POST["cid"];
else {
        // We use the circulationgroup_id of the top name in our selectbox (which is alphabetically sorted).
        $selected_circulationgroup_conditions["order_by"] = "name";
        $selected_circulationgroup_conditions["limit_start"] = 0;
        $selected_circulationgroup_conditions["limit_num"] = 1;
        $urlinfo["cid"] = db_fetch_row(db_read("circulationgroups", "id", $selected_circulationgroup_conditions));
        $urlinfo["cid"] = $urlinfo["cid"][0];
}

// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);

$circulationgroups_distributorlocations_conditions["order_by"] = "name";
$circulationgroups_distributorlocations_conditions["where"]["1"] = "circulationgroup_id = " . $urlinfo["cid"];
$circulationgroups_distributorlocations_res = db_read("distributorlocations", "id name hostname", $circulationgroups_distributorlocations_conditions);
$circulationgroups_distributorlocations = array();
while ($row = db_fetch_assoc($circulationgroups_distributorlocations_res)) {
	$circulationgroups_distributorlocations[$row["id"]] = array();
	$circulationgroups_distributorlocations[$row["id"]]["name"] = $row["name"];
	$circulationgroups_distributorlocations[$row["id"]]["hostname"] = $row["hostname"];
}

$periods_num_sql = "SELECT COUNT(*)
        FROM distributionperiods dp
        WHERE dp.circulationgroup_id = " . $urlinfo["cid"];
$periods_num = db_fetch_num(db_query($periods_num_sql));
$periods_num = $periods_num[0];

$last_period_sql = "SELECT p.from_dayofweek AS 'last_period'
        FROM distributionperiods p
        WHERE p.circulationgroup_id = " . $urlinfo["cid"] . " 
        AND (IF(
            p.from_dayofweek < p.to_dayofweek,
            p.from_dayofweek * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
            AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60  < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60,
            IF(
                            WEEKDAY(NOW())+1 >= p.from_dayofweek,
                                            p.from_dayofweek  * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                                            AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < 691200,
                                                            86400 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                            AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60
            ))) = 1
        ORDER BY p.from_dayofweek ";
$last_period = db_fetch_num(db_query($last_period_sql));
$last_period = $last_period[0];

/**
 * Generate MUPAPU
 */
$numweeks = (isset($_POST["numweeks"]) && is_numeric($_POST["numweeks"])) ? $_POST["numweeks"] : 4;
$calculate = (isset($_POST["generate"]) || isset($_POST["write_to_dist"]) || isset($_POST["print"]));
$mupapu = mupapu_generate($urlinfo["cid"], $numweeks, $calculate);

/**
 * Generate the page
 */
if(isset($_GET["print"])) {
	include($pi["template"]);
} else {
	$cv = array(
		"pi" => $pi,
		"urlinfo" => $urlinfo,
		"circulationgroups" => $circulationgroups,
		"circulationgroups_distributorlocations" => $circulationgroups_distributorlocations,
		"mupapu" => $mupapu,
                "periods_num" => $periods_num,
                "last_period" => $last_period,
		"calculate" => $calculate
	);

	template_parse($pi, $urlinfo, $cv);
}

?>
