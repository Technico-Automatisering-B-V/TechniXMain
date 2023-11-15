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


$sql_loadtime = "SELECT ld.circulationgroup_id,FLOOR(AVG(ld.count)) average, 
        MAX(ld.count) max, MIN(ld.count) min
        FROM (
SELECT t2.circulationgroup_id,SUM(count) count FROM (
SELECT t1.circulationgroup_id, COUNT(*) AS count, timekey,FLOOR(timekey/6) timekey_hour FROM (
        SELECT   FLOOR(UNIX_TIMESTAMP(starttime)/(10 * 60)) AS timekey, ddl.circulationgroup_id
        FROM     log_distributors_load l
        INNER JOIN distributors dd ON dd.id = l.distributor_id
                    INNER JOIN distributorlocations ddl ON ddl.id = dd.distributorlocation_id 
                    WHERE starttime > DATE(NOW()) 
                   GROUP BY ddl.circulationgroup_id, starttime, garment_id) t1
        GROUP BY t1.circulationgroup_id, t1.timekey
        HAVING COUNT(*) > 1) t2
GROUP BY t2.circulationgroup_id,t2.timekey_hour
HAVING COUNT(*) > 4) ld
        GROUP BY ld.circulationgroup_id";

$loadtime = db_query($sql_loadtime);

$sql_loadtime_reject = "SELECT ld.circulationgroup_id,FLOOR(AVG(ld.count)) average, 
        MAX(ld.count) max, MIN(ld.count) min
        FROM (
SELECT t2.circulationgroup_id,SUM(count) count FROM (
SELECT t1.circulationgroup_id, COUNT(*) AS count,timekey,FLOOR(timekey/6) timekey_hour FROM (SELECT timekey, circulationgroup_id FROM (
SELECT   starttime as date, FLOOR(UNIX_TIMESTAMP(starttime)/(10 * 60)) AS timekey, ddl.circulationgroup_id, l.garment_id
FROM     log_distributors_load l
INNER JOIN distributors dd ON dd.id = l.distributor_id
            INNER JOIN distributorlocations ddl ON ddl.id = dd.distributorlocation_id 
            WHERE starttime > DATE(NOW())
 UNION ALL
			SELECT date, FLOOR(UNIX_TIMESTAMP(date)/(10 * 60)) AS timekey, dl.circulationgroup_id, lr.garment_id
			FROM log_rejected_garments lr
			INNER JOIN distributorlocations dl ON dl.id = lr.distributorlocation_id
			WHERE date > DATE(NOW())) t2
           GROUP BY t2.circulationgroup_id, t2.date, garment_id) t1
GROUP BY t1.circulationgroup_id, t1.timekey
HAVING COUNT(*) > 1 
) t2
GROUP BY t2.circulationgroup_id,t2.timekey_hour
HAVING COUNT(*) > 4) ld
GROUP BY ld.circulationgroup_id;";

$loadtime_reject = db_query($sql_loadtime_reject);

while ($row = db_fetch_assoc($loadtime)){ 
    $data_html .= "<input type=\"hidden\" id=\"data_loadtime_average".$row["circulationgroup_id"]."\" value=\"". $row["average"] ."\" />";
    $data_html .= "<input type=\"hidden\" id=\"data_loadtime_max".$row["circulationgroup_id"]."\" value=\"". $row["max"] ."\" />"; 
    $data_html .= "<input type=\"hidden\" id=\"data_loadtime_min".$row["circulationgroup_id"]."\" value=\"". $row["min"] ."\" />";
}

while ($row = db_fetch_assoc($loadtime_reject)){ 
	$data_html .= "<input type=\"hidden\" id=\"data_loadtime_reject_average".$row["circulationgroup_id"]."\" value=\"". $row["average"] ."\" />";
    $data_html .= "<input type=\"hidden\" id=\"data_loadtime_reject_max".$row["circulationgroup_id"]."\" value=\"". $row["max"] ."\" />"; 
    $data_html .= "<input type=\"hidden\" id=\"data_loadtime_reject_min".$row["circulationgroup_id"]."\" value=\"". $row["min"] ."\" />";  	
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
