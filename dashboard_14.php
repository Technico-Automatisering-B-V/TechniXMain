<?php

/**
 * Dashboard 12
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @return    json $result
 * @version   $$Id$$
 */

// Require bootstrap
require_once "include/engine.php";
$data_html = "";

$sql_distributionstation = "SELECT id AS 'circulationgroup_id'
							FROM circulationgroups";

$distributionstation = db_query($sql_distributionstation);

$sql_misseized_week = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'misseized_week'";

$misseized_week = db_query($sql_misseized_week);

$sql_misseized_month = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'misseized_month'";

$misseized_month = db_query($sql_misseized_month);

$sql_misseized_year = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'misseized_year'";

$misseized_year = db_query($sql_misseized_year);

while ($row = db_fetch_assoc($misseized_week)){ 
    if(!isset($data_misseizedweek[$row["circulationgroup_id"]]["date"])) {$data_misseizedweek[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_misseizedweek[$row["circulationgroup_id"]]["average"])) {$data_misseizedweek[$row["circulationgroup_id"]]["average"] = "";}

    $data_misseizedweek[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_misseizedweek[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($misseized_month)){ 
    if(!isset($data_misseizedmonth[$row["circulationgroup_id"]]["date"])) {$data_misseizedmonth[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_misseizedmonth[$row["circulationgroup_id"]]["average"])) {$data_misseizedmonth[$row["circulationgroup_id"]]["average"] = "";}

    $data_misseizedmonth[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_misseizedmonth[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($misseized_year)){ 
    if(!isset($data_misseizedyear[$row["circulationgroup_id"]]["date"])) {$data_misseizedyear[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_misseizedyear[$row["circulationgroup_id"]]["average"])) {$data_misseizedyear[$row["circulationgroup_id"]]["average"] = "";}

    $data_misseizedyear[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_misseizedyear[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributionstation)){ 
	$data_html .= "<input type=\"hidden\" id=\"data_misseizedweek_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_misseizedweek[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_misseizedweek_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_misseizedweek[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_misseizedmonth_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_misseizedmonth[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_misseizedmonth_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_misseizedmonth[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_misseizedyear_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_misseizedyear[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_misseizedyear_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_misseizedyear[$row["circulationgroup_id"]]["average"], ",") ."\" />";   		
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
