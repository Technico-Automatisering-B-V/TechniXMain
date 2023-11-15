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

$sql_distributionuser_month = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributionuser_month'";

$distributionuser_month = db_query($sql_distributionuser_month);

$sql_distributionuser_year = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributionuser_year'";

$distributionuser_year = db_query($sql_distributionuser_year);
    
while ($row = db_fetch_assoc($distributionuser_month)){ 
    if(!isset($data_distributionusermonth[$row["circulationgroup_id"]]["date"])) {$data_distributionusermonth[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributionusermonth[$row["circulationgroup_id"]]["average"])) {$data_distributionusermonth[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributionusermonth[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang["week"] . " " . strtolower($row["day"]). ","):",";
    $data_distributionusermonth[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributionuser_year)){ 
    if(!isset($data_distributionuseryear[$row["circulationgroup_id"]]["date"])) {$data_distributionuseryear[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributionuseryear[$row["circulationgroup_id"]]["average"])) {$data_distributionuseryear[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributionuseryear[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributionuseryear[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributionstation)){
	$data_html .= "<input type=\"hidden\" id=\"data_distributionusermonth_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionusermonth[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributionusermonth_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionusermonth[$row["circulationgroup_id"]]["average"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributionuseryear_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionuseryear[$row["circulationgroup_id"]]["date"], ",") ."\" />";
	$data_html .= "<input type=\"hidden\" id=\"data_distributionuseryear_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributionuseryear[$row["circulationgroup_id"]]["average"], ",") ."\" />";
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
