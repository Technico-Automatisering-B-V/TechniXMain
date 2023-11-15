<?php

/**
 * Delete inactive garmentusers
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

$d_sql = "UPDATE `garmentusers` `gu`
        INNER JOIN (SELECT * FROM (SELECT * FROM `log_distributorclients` ORDER BY `date` DESC) t1 GROUP BY `t1`.`garmentuser_id`) l ON `l`.`garmentuser_id` = `gu`.`id`
        LEFT JOIN `supergarmentusers` `s` ON `s`.`garmentuser_id` = `gu`.`id`
        SET `gu`.`deleted_on` = NOW()
        WHERE ISNULL(`s`.`garmentuser_id`) AND `l`.`date` < DATE_SUB(DATE(NOW()), INTERVAL 60 DAY) AND ISNULL(`gu`.`deleted_on`)";

db_query($d_sql);
