<?php

/**
 * Choose distributor for garmentusers auto
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

$garmentusers_sql = "SELECT `gu`.`id`, `g`.`circulationgroup_id`,
                    case g.circulationgroup_id
                        when 1 then 'distributor_id'
                        when 2 then 'distributor_id2'
                        when 3 then 'distributor_id3'
                        when 4 then 'distributor_id4'
                        when 5 then 'distributor_id5'
                        when 6 then 'distributor_id6'
                        when 7 then 'distributor_id7'
                        when 8 then 'distributor_id8'
                        when 9 then 'distributor_id9'
                        when 10 then 'distributor_id10'
                    end AS 'distributor_id'
                             FROM `garmentusers` `gu`
                       INNER JOIN `garments` `g` ON `g`.`garmentuser_id` = `gu`.`id`
                       INNER JOIN `garmentusers_userbound_arsimos` `ga` ON `ga`.`garmentuser_id` = `gu`.`id` AND `ga`.`arsimo_id` = `g`.`arsimo_id` 
                     WHERE ISNULL(`gu`.`deleted_on`) AND ISNULL(`g`.`deleted_on`) AND
                        case g.circulationgroup_id
                            when 1 then ISNULL(`gu`.`distributor_id`)
                            when 2 then ISNULL(`gu`.`distributor_id2`)
                            when 3 then ISNULL(`gu`.`distributor_id3`)
                            when 4 then ISNULL(`gu`.`distributor_id4`)
                            when 5 then ISNULL(`gu`.`distributor_id5`)
                            when 6 then ISNULL(`gu`.`distributor_id6`)
                            when 7 then ISNULL(`gu`.`distributor_id7`)
                            when 8 then ISNULL(`gu`.`distributor_id8`)
                            when 9 then ISNULL(`gu`.`distributor_id9`)
                            when 10 then ISNULL(`gu`.`distributor_id10`)
                        end
                         GROUP BY `gu`.`id`";
$garmentusers = db_query($garmentusers_sql);

while ($row = db_fetch_assoc($garmentusers)){
    $garmentuser_id      = $row['id'];
    $circulationgroup_id = $row['circulationgroup_id'];
    $distributor_id = $row['distributor_id'];
    
    $sql = "UPDATE `garmentusers`,
           (SELECT `d`.`id` AS 'distributor_id'
              FROM `distributors` `d`
        INNER JOIN `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
        LEFT JOIN (
                SELECT `gu`.". $distributor_id ." AS 'distributor_id', SUM(`ga`.`max_positions`) AS 'count'
                  FROM `garmentusers_userbound_arsimos` `ga`
            INNER JOIN `garmentusers` `gu` ON `gu`.`id` = `ga`.`garmentuser_id`
                 WHERE !ISNULL(`gu`.". $distributor_id .") AND ISNULL(`gu`.`deleted_on`)
              GROUP BY `gu`.". $distributor_id .") `t1` ON `t1`.`distributor_id` = `d`.`id`
            WHERE `dl`.`circulationgroup_id` = ". $circulationgroup_id. " 
         ORDER BY `t1`.`count`
         LIMIT 1) `t`
              SET `garmentusers`.". $distributor_id ." = `t`.`distributor_id`
            WHERE `garmentusers`.`id` = ". $garmentuser_id;
    
    db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error());  
}
