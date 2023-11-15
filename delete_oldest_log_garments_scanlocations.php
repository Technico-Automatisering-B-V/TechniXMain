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
require_once "library/bootstrap.php";

$r = 1;
$d_sql = "DELETE `lgs`.*
        FROM `technix_log`.`log_garments_scanlocations` `lgs`
        INNER JOIN 
        (
         SELECT `garment_id`, min(`date`) as 'oldest'
         FROM `technix_log`.`log_garments_scanlocations`
         GROUP BY `garment_id`
         HAVING COUNT(*) > 15
        ) AS todelete ON `todelete`.`garment_id` = `lgs`.`garment_id` AND `todelete`.`oldest` = `lgs`.`date`";

while ($r > 0){
    db_query($d_sql);
    $r = db_affected_rows();
}
