<?php

/**
 * Dashboard 11
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

$sql_distributionuser = "SELECT c.id AS circulationgroup_id,
IF(ISNULL(t2.year),YEAR(NOW()),t2.year) year,
IF(ISNULL(t2.date),DATE(NOW()),t2.date) date,
IF(ISNULL(t2.cc),0,t2.cc) cc,
IF(ISNULL(t2.distributionuser),0,t2.distributionuser) distributionuser
FROM circulationgroups c
LEFT JOIN (
SELECT *, ROUND(AVG(cc),2) distributionuser
                    FROM (SELECT dl.circulationgroup_id, YEAR(lg.endtime) year, WEEK(lg.endtime, 1) date, COUNT(*) cc
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime BETWEEN subdate(curdate(),dayofweek(curdate())+5)
					AND subdate(curdate(),dayofweek(curdate())-2)
					AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0) 
                    GROUP BY dl.circulationgroup_id, lg.garmentuser_id,WEEK(lg.endtime, 1)) t1
					GROUP BY t1.circulationgroup_id,t1.year,t1.date) t2 ON t2.circulationgroup_id = c.id";

$distributionuser = db_query($sql_distributionuser);

while ($row = db_fetch_assoc($distributionuser)){ 
        $data_html .= "<input type=\"hidden\" id=\"data_distributionuser_average".$row["circulationgroup_id"]."\" value=\"". $row["distributionuser"] ."\" />";
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
