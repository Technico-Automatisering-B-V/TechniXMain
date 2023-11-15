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

$sql_distributionstation = "SELECT c.id AS circulationgroup_id,
IF(ISNULL(t1.date),DATE(NOW()),t1.date) date,
IF(ISNULL(t1.cc),0,t1.cc) cc,
IF(ISNULL(AVG(cc)),0,ROUND(AVG(cc))) distributionstation
                    FROM circulationgroups c
LEFT JOIN (SELECT dl.circulationgroup_id, DATE(lg.endtime) date, COUNT(*) cc
                    FROM log_garmentusers_garments lg
                    INNER JOIN distributors d ON d.id = lg.distributor_id
                    INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id
                    WHERE lg.endtime BETWEEN DATE(NOW()) AND NOW() AND (ISNULL(lg.superuser_id) OR lg.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, lg.distributor_id) t1 ON t1.circulationgroup_id = c.id
                    GROUP BY c.id";

$distributionstation = db_query($sql_distributionstation);

while ($row = db_fetch_assoc($distributionstation)){ 
        $data_html .= "<input type=\"hidden\" id=\"data_distributionstation_average".$row["circulationgroup_id"]."\" value=\"". $row["distributionstation"] ."\" />";
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
