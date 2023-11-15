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


$sql_login = "SELECT c.id AS circulationgroup_id,
COUNT(t1.circulationgroup_id) login, IF(ISNULL(t1.date),DATE(NOW()),t1.date) date
           FROM circulationgroups c
LEFT JOIN (
 SELECT dl.circulationgroup_id, DATE(ld.date) date
                    FROM log_distributorclients ld
                    INNER JOIN distributorlocations dl ON dl.id = ld.distributorlocation_id
                    WHERE ld.date BETWEEN DATE(NOW()) AND DATE(NOW()) + INTERVAL 1 DAY AND (ISNULL(ld.superuser_id) OR ld.superuser_id = 0)
                    GROUP BY dl.circulationgroup_id, ld.garmentuser_id) t1 ON t1.circulationgroup_id = c.id
            GROUP BY c.id";

$login = db_query($sql_login);

while ($row = db_fetch_assoc($login)){ 
    $data_html .= "<input type=\"hidden\" id=\"data_login_average".$row["circulationgroup_id"]."\" value=\"". $row["login"] ."\" />";
}

$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
