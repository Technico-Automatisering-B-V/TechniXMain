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

$sql_misseized = "SELECT d.circulationgroup_id,
IF(ISNULL(t1.date),DATE(NOW()),t1.date) AS date,
IF(ISNULL(t1.misseized) OR ISNULL(t1.distributed),0,ROUND(SUM(t1.misseized)/SUM(t1.distributed)*100,2)) misseized
FROM distributorlocations d
LEFT JOIN (
            SELECT l1.distributorlocation_id, DATE(l1.date) date,COUNT(*) distributed, IF(ISNULL(l2.cc),'0',l2.cc) misseized
            FROM log_distributorclients l1
            LEFT JOIN (SELECT DATE(date) date, COUNT(DISTINCT(garmentuser_id)) cc, distributorlocation_id 
                        FROM log_distributorclients
                        WHERE userbound = 0 AND numgarments = 0 AND superuser_id = 0 AND date BETWEEN DATE(NOW()) AND NOW()) l2 
            ON l2.date = DATE(l1.date) AND l2.distributorlocation_id = l1.distributorlocation_id
            WHERE l1.date BETWEEN DATE(NOW()) AND NOW() AND l1.numgarments > 0  AND l1.userbound = 0  AND l1.superuser_id = 0
            GROUP BY l1.distributorlocation_id,DATE(l1.date)) t1 ON d.id = t1.distributorlocation_id
            GROUP BY d.circulationgroup_id";

$misseized = db_query($sql_misseized);

while ($row = db_fetch_assoc($misseized)){ 
    $data_html .= "<input type=\"hidden\" id=\"data_misseized_average".$row["circulationgroup_id"]."\" value=\"". $row["misseized"] ."\" />"; 
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
