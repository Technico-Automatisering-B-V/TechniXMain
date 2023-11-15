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

$sql_login_week = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'login_week'";

$login_week = db_query($sql_login_week);

$sql_login_month = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'login_month'";

$login_month = db_query($sql_login_month);

$sql_login_year = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'login_year'";

$login_year = db_query($sql_login_year);

while ($row = db_fetch_assoc($login_week)){ 
    if(!isset($data_loginweek[$row["circulationgroup_id"]]["date"])) {$data_loginweek[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_loginweek[$row["circulationgroup_id"]]["average"])) {$data_loginweek[$row["circulationgroup_id"]]["average"] = "";}

    $data_loginweek[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_loginweek[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($login_month)){ 
    if(!isset($data_loginmonth[$row["circulationgroup_id"]]["date"])) {$data_loginmonth[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_loginmonth[$row["circulationgroup_id"]]["average"])) {$data_loginmonth[$row["circulationgroup_id"]]["average"] = "";}

    $data_loginmonth[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_loginmonth[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($login_year)){ 
    if(!isset($data_loginyear[$row["circulationgroup_id"]]["date"])) {$data_loginyear[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_loginyear[$row["circulationgroup_id"]]["average"])) {$data_loginyear[$row["circulationgroup_id"]]["average"] = "";}

    $data_loginyear[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_loginyear[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributionstation)){ 
	$data_html .= "<input type=\"hidden\" id=\"data_loginweek_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loginweek[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loginweek_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loginweek[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loginmonth_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loginmonth[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loginmonth_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loginmonth[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loginyear_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loginyear[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_loginyear_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_loginyear[$row["circulationgroup_id"]]["average"], ",") ."\" />";	
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
