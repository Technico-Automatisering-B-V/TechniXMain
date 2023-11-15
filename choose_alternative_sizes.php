<?php

/**
 * Choose alternative sizes for garmentusers auto
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

$sql = "UPDATE `garmentusers_arsimos` `ga`
    INNER JOIN `alt_arsimos` `aa` ON `aa`.`arsimo_id` = `ga`.`arsimo_id`
    INNER JOIN `arsimos` `a` ON `a`.`id` = `aa`.`alt_arsimo_id`
    INNER JOIN `garmentusers` `gu` ON `gu`.`id` = `ga`.`garmentuser_id`
           SET `ga`.`alt_arsimo_id` = `aa`.`alt_arsimo_id`
         WHERE `ga`.`userbound` = 0 AND ISNULL(`ga`.`alt_arsimo_id`) AND ISNULL(`a`.`deleted_on`) AND ISNULL(`gu`.`deleted_on`)";
 
db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error());  

