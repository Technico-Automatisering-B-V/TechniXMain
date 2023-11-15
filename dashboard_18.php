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

$sql_distributionstation_week = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributionstation_week'";

$distributionstation_week = db_query($sql_distributionstation_week);

$sql_distributionstation_month = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributionstation_month'";

$distributionstation_month = db_query($sql_distributionstation_month);

$sql_distributionstation_year = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributionstation_year'";

$distributionstation_year = db_query($sql_distributionstation_year);

while ($row = db_fetch_assoc($distributionstation_week)){ 
    if(!isset($data_distributionstationweek[$row["circulationgroup_id"]]["date"])) {$data_distributionstationweek[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributionstationweek[$row["circulationgroup_id"]]["average"])) {$data_distributionstationweek[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributionstationweek[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributionstationweek[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}
    
while ($row = db_fetch_assoc($distributionstation_month)){ 
    if(!isset($data_distributionstationmonth[$row["circulationgroup_id"]]["date"])) {$data_distributionstationmonth[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributionstationmonth[$row["circulationgroup_id"]]["average"])) {$data_distributionstationmonth[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributionstationmonth[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributionstationmonth[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributionstation_year)){ 
    if(!isset($data_distributionstationyear[$row["circulationgroup_id"]]["date"])) {$data_distributionstationyear[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributionstationyear[$row["circulationgroup_id"]]["average"])) {$data_distributionstationyear[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributionstationyear[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributionstationyear[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributionstation)){ 
	$data_html .= "<input type=\"hidden\" id=\"data_distributionstationweek_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionstationweek[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributionstationweek_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionstationweek[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributionstationmonth_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionstationmonth[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributionstationmonth_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionstationmonth[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributionstationyear_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionstationyear[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributionstationyear_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionstationyear[$row["circulationgroup_id"]]["average"], ",") ."\" />";
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
