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

$sql_or = "SELECT
    circulationgroups.name AS circulationgroup_name,
    circulationgroups.id AS circulationgroup_id,
    SUM( scanlocationstatuses.name = 'never_scanned') AS 'sum_unknown',
    SUM( scanlocationstatuses.name = 'missing') AS 'sum_missing',
    SUM( scanlocationstatuses.name = 'stock_hospital') AS 'sum_stock_hospital',
    SUM( scanlocationstatuses.name = 'stock_laundry') AS 'sum_stock_laundry',
    SUM( scanlocationstatuses.name = 'homewash') AS 'sum_selfcleaning',
    SUM( scanlocationstatuses.name = 'repair') AS 'sum_repair',
    SUM( scanlocationstatuses.name = 'despeckle') AS 'sum_despeckle',
    SUM( scanlocationstatuses.name = 'disconnected_from_garmentuser') AS 'sum_disconnected',
    SUM( scanlocations.circulationgroup_id IS NULL ) AS 'total'
    FROM
    arsimos
    INNER JOIN garments ON garments.arsimo_id = arsimos.id
    INNER JOIN scanlocations ON garments.scanlocation_id = scanlocations.id
    INNER JOIN scanlocationstatuses ON scanlocations.scanlocationstatus_id = scanlocationstatuses.id
    INNER JOIN circulationgroups ON circulationgroups.id = garments.circulationgroup_id
    WHERE arsimos.deleted_on IS NULL AND garments.deleted_on IS NULL
    GROUP BY circulationgroups.id";

$out_roulation = db_query($sql_or);

while ($row = db_fetch_assoc($out_roulation)){ 
       $data_or[$row["circulationgroup_id"]] =  $row["sum_unknown"] . "," . 
                $row["sum_missing"] . "," .
                $row["sum_stock_hospital"] . "," .
                $row["sum_stock_laundry"] . "," .
                $row["sum_selfcleaning"] . "," .
                $row["sum_repair"] . "," .
                $row["sum_despeckle"]. "," .
                $row["sum_disconnected"];
        $data_html .= "<input type=\"hidden\" id=\"data_or".$row["circulationgroup_id"]."\" value=\"". $data_or[$row["circulationgroup_id"]] ."\" />"; 
} 

$result["data_out_roulation"]   = $data_or;
$result["data_html"]   = $data_html;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
