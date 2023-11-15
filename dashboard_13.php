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


$sql_loadtime_week = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'loadtime_week'";

$loadtime_week = db_query($sql_loadtime_week);

$sql_loadtime_reject_week = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'loadtime_reject_week'";

$loadtime_reject_week = db_query($sql_loadtime_reject_week);

$sql_loadtime_month = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'loadtime_month'";

$loadtime_month = db_query($sql_loadtime_month);

$sql_loadtime_reject_month = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'loadtime_reject_month'";

$loadtime_reject_month = db_query($sql_loadtime_reject_month);

$sql_loadtime_year = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'loadtime_year'";

$loadtime_year = db_query($sql_loadtime_year);

$sql_loadtime_reject_year = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'loadtime_reject_year'";

$loadtime_reject_year = db_query($sql_loadtime_reject_year);

while ($row = db_fetch_assoc($loadtime_week)){ 
    if(!isset($data_loadtimeweek[$row["circulationgroup_id"]]["date"])) {$data_loadtimeweek[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_loadtimeweek[$row["circulationgroup_id"]]["average"])) {$data_loadtimeweek[$row["circulationgroup_id"]]["average"] = "";}

    $data_loadtimeweek[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_loadtimeweek[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($loadtime_month)){ 
    if(!isset($data_loadtimemonth[$row["circulationgroup_id"]]["date"])) {$data_loadtimemonth[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_loadtimemonth[$row["circulationgroup_id"]]["average"])) {$data_loadtimemonth[$row["circulationgroup_id"]]["average"] = "";}

    $data_loadtimemonth[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_loadtimemonth[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($loadtime_year)){ 
    if(!isset($data_loadtimeyear[$row["circulationgroup_id"]]["date"])) {$data_loadtimeyear[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_loadtimeyear[$row["circulationgroup_id"]]["average"])) {$data_loadtimeyear[$row["circulationgroup_id"]]["average"] = "";}

    $data_loadtimeyear[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_loadtimeyear[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($loadtime_reject_week)){ 
    if(!isset($data_loadtime_reject_week[$row["circulationgroup_id"]]["date"])) {$data_loadtime_reject_week[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_loadtime_reject_week[$row["circulationgroup_id"]]["average"])) {$data_loadtime_reject_week[$row["circulationgroup_id"]]["average"] = "";}

    $data_loadtime_reject_week[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_loadtime_reject_week[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($loadtime_reject_month)){ 
    if(!isset($data_loadtime_reject_month[$row["circulationgroup_id"]]["date"])) {$data_loadtime_reject_month[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_loadtime_reject_month[$row["circulationgroup_id"]]["average"])) {$data_loadtime_reject_month[$row["circulationgroup_id"]]["average"] = "";}

    $data_loadtime_reject_month[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_loadtime_reject_month[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($loadtime_reject_year)){ 
    if(!isset($data_loadtime_reject_year[$row["circulationgroup_id"]]["date"])) {$data_loadtime_reject_year[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_loadtime_reject_year[$row["circulationgroup_id"]]["average"])) {$data_loadtime_reject_year[$row["circulationgroup_id"]]["average"] = "";}

    $data_loadtime_reject_year[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_loadtime_reject_year[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributionstation)){ 
	$data_html .= "<input type=\"hidden\" id=\"data_loadtimeweek_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtimeweek[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtimeweek_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtimeweek[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtimemonth_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtimemonth[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtimemonth_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtimemonth[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtimeyear_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtimeyear[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtimeyear_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtimeyear[$row["circulationgroup_id"]]["average"], ",") ."\" />";    		
	
	$data_html .= "<input type=\"hidden\" id=\"data_loadtime_reject_week_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtime_reject_week[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtime_reject_week_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtime_reject_week[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtime_reject_month_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtime_reject_month[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtime_reject_month_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtime_reject_month[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtime_reject_year_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtime_reject_year[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loadtime_reject_year_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loadtime_reject_year[$row["circulationgroup_id"]]["average"], ",") ."\" />";    	
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
