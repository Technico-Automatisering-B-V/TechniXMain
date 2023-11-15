<?php

/**
 * Dashboard 1
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

$sql_distributiontime = "select c.id AS circulationgroup_id,
IF(ISNULL(t1.average),0,t1.average) average,
IF(ISNULL(t1.max),0,t1.max) max,
IF(ISNULL(t1.min),0,t1.min) min,
IF(ISNULL(t1.distributions),0,t1.distributions) distributions
FROM circulationgroups c
LEFT JOIN (SELECT dl.circulationgroup_id,ROUND(AVG(TIMESTAMPDIFF(SECOND,lg.starttime, lg.endtime)),1) average, 
        MAX(TIMESTAMPDIFF(SECOND,lg.starttime, lg.endtime)) max,
        MIN(TIMESTAMPDIFF(SECOND,lg.starttime, lg.endtime)) min, COUNT(*) AS distributions
        FROM
        log_garmentusers_garments lg
        INNER JOIN distributors d ON d.id = lg.distributor_id
        INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
        WHERE lg.starttime > DATE(NOW())
        GROUP BY dl.circulationgroup_id) t1 ON t1.circulationgroup_id = c.id";

$distributiontime = db_query($sql_distributiontime);

$sql_distributiontime_week = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributiontime_week'";

$distributiontime_week = db_query($sql_distributiontime_week);

$sql_distributiontime_month = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributiontime_month'";

$distributiontime_month = db_query($sql_distributiontime_month);

$sql_distributiontime_year = "SELECT circulationgroup_id,date,day, value AS 'average' FROM technix_log.log_dashboard WHERE type = 'distributiontime_year'";

$distributiontime_year = db_query($sql_distributiontime_year);

while ($row = db_fetch_assoc($distributiontime_week)){ 
    if(!isset($data_distributiontimeweek[$row["circulationgroup_id"]]["date"])) {$data_distributiontimeweek[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributiontimeweek[$row["circulationgroup_id"]]["average"])) {$data_distributiontimeweek[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributiontimeweek[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributiontimeweek[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributiontime_month)){ 
    if(!isset($data_distributiontimemonth[$row["circulationgroup_id"]]["date"])) {$data_distributiontimemonth[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributiontimemonth[$row["circulationgroup_id"]]["average"])) {$data_distributiontimemonth[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributiontimemonth[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributiontimemonth[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributiontime_year)){ 
    if(!isset($data_distributiontimeyear[$row["circulationgroup_id"]]["date"])) {$data_distributiontimeyear[$row["circulationgroup_id"]]["date"] = "";}
    if(!isset($data_distributiontimeyear[$row["circulationgroup_id"]]["average"])) {$data_distributiontimeyear[$row["circulationgroup_id"]]["average"] = "";}

    $data_distributiontimeyear[$row["circulationgroup_id"]]["date"] .= isset($row["day"])?($lang[strtolower($row["day"])]. ","):",";
    $data_distributiontimeyear[$row["circulationgroup_id"]]["average"] .= $row["average"]. ",";
}

while ($row = db_fetch_assoc($distributiontime)){ 
    $data_distributiontime[$row["circulationgroup_id"]] = $row["max"];
    $data_html .= "<input type=\"hidden\" id=\"data_distributiontime_average".$row["circulationgroup_id"]."\" value=\"". $row["average"] ."\" />";
    $data_html .= "<input type=\"hidden\" id=\"data_distributiontime_max".$row["circulationgroup_id"]."\" value=\"". $row["max"] ."\" />"; 
    $data_html .= "<input type=\"hidden\" id=\"data_distributiontime_min".$row["circulationgroup_id"]."\" value=\"". $row["min"] ."\" />"; 
    $data_html .= "<input type=\"hidden\" id=\"data_distributiontimeweek_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributiontimeweek[$row["circulationgroup_id"]]["date"], ",") ."\" />";
    $data_html .= "<input type=\"hidden\" id=\"data_distributiontimeweek_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributiontimeweek[$row["circulationgroup_id"]]["average"], ",") ."\" />";
    $data_html .= "<input type=\"hidden\" id=\"data_distributiontimemonth_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributiontimemonth[$row["circulationgroup_id"]]["date"], ",") ."\" />";
    $data_html .= "<input type=\"hidden\" id=\"data_distributiontimemonth_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributiontimemonth[$row["circulationgroup_id"]]["average"], ",") ."\" />";
    $data_html .= "<input type=\"hidden\" id=\"data_distributiontimeyear_date".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributiontimeyear[$row["circulationgroup_id"]]["date"], ",") ."\" />";
    $data_html .= "<input type=\"hidden\" id=\"data_distributiontimeyear_average".$row["circulationgroup_id"]."\" value=\"". rtrim($data_distributiontimeyear[$row["circulationgroup_id"]]["average"], ",") ."\" />";    
}

$result["data_distributiontime"]   = $data_distributiontime;
$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
