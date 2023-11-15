<?php

/**
 * Update garmentusers garments
 *
 * @author    G. I. Voros <gabor@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require_once "include/mupapu.php";
require_once "library/bootstrap.php";

$deletable_garments_sql = "SELECT garmentusers_garments.garment_id AS garment_id, COUNT(*) AS cc, garmentusers_arsimos.max_credit AS max
                        FROM (SELECT * FROM garmentusers_garments ORDER BY date_received) AS garmentusers_garments
                        INNER JOIN garments ON garments.id = garmentusers_garments.garment_id
                        INNER JOIN arsimos ON arsimos.id = garments.arsimo_id
                        INNER JOIN garmentusers_arsimos ON (garments.arsimo_id = garmentusers_arsimos.arsimo_id OR garments.arsimo_id = garmentusers_arsimos.alt_arsimo_id) AND garmentusers_garments.garmentuser_id = garmentusers_arsimos.garmentuser_id
                        GROUP BY garmentusers_garments.garmentuser_id, arsimos.article_id
                        HAVING cc = max";
$deletable_garments = db_query($deletable_garments_sql);
while ($row = db_fetch_assoc($deletable_garments)){
    $garment_id = $row['garment_id'];
    
    $sql = "UPDATE garments SET garments.scanlocation_id = 14, garments.lastscan = NOW(), garments.clean = 0, garments.active = 1 WHERE garments.id = " . $garment_id . " AND garments.deleted_on IS NULL ";
    db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error());
    
    $sql = "DELETE garmentusers_garments.* FROM garmentusers_garments WHERE garmentusers_garments.garment_id = " . $garment_id;
    db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error());  
    
    $sql = "DELETE distributors_load.* FROM distributors_load WHERE distributors_load.garment_id = " . $garment_id;
    db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error()); 
    
    $sql = "INSERT INTO log_depositlocations_garments SELECT YEAR(NOW()), DAYOFYEAR(NOW()), 1, ".$garment_id.", NOW() ON DUPLICATE KEY UPDATE log_depositlocations_garments.`date` = NOW()";
    db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error());   
}
