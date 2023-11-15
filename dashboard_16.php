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

$sql_distributioncount_week = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributioncount_week'";

$distributioncount_week = db_query($sql_distributioncount_week);

$sql_distributioncount_month = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributioncount_month'";

$distributioncount_month = db_query($sql_distributioncount_month);

$sql_distributioncount_year = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributioncount_year'";

$distributioncount_year = db_query($sql_distributioncount_year);

while ($row = db_fetch_assoc($distributioncount_week)){ 
    if(!isset($data_distributioncountweek[$row["circulationgroup_id"]]["date"])) {$data_distributioncountweek[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributioncountweek[$row["circulationgroup_id"]]["average"])) {$data_distributioncountweek[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributioncountweek[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributioncountweek[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}
    
while ($row = db_fetch_assoc($distributioncount_month)){ 
    if(!isset($data_distributioncountmonth[$row["circulationgroup_id"]]["date"])) {$data_distributioncountmonth[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributioncountmonth[$row["circulationgroup_id"]]["average"])) {$data_distributioncountmonth[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributioncountmonth[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributioncountmonth[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributioncount_year)){ 
    if(!isset($data_distributioncountyear[$row["circulationgroup_id"]]["date"])) {$data_distributioncountyear[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributioncountyear[$row["circulationgroup_id"]]["average"])) {$data_distributioncountyear[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributioncountyear[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributioncountyear[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributionstation)){ 
	$data_html .= "<input type=\"hidden\" id=\"data_distributioncountweek_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributioncountweek[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributioncountweek_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributioncountweek[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributioncountmonth_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributioncountmonth[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributioncountmonth_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributioncountmonth[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributioncountyear_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributioncountyear[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributioncountyear_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributioncountyear[$row["circulationgroup_id"]]["average"], ",") ."\" />";
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
