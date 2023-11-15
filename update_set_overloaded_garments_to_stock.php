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
   
$sql = "DELETE gg.* FROM `log_garmentusers_garments` l
    INNER JOIN garments g ON g.id = l.garment_id
    INNER JOIN garmentusers_garments gg ON gg.garment_id = l.garment_id
    INNER JOIN scanlocations s ON s.id = g.scanlocation_id
	INNER JOIN scanlocationstatuses ss ON ss.id = s.scanlocationstatus_id
    WHERE ISNULL(l.superuser_id) AND ss.name = 'distributed'";
db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error());

$sql = "UPDATE `log_garmentusers_garments` l
    INNER JOIN garments g ON g.id = l.garment_id
    INNER JOIN scanlocations s ON s.id = g.scanlocation_id
	INNER JOIN scanlocationstatuses ss ON ss.id = s.scanlocationstatus_id
    SET g.scanlocation_id = 3, g.lastscan = NOW()
    WHERE ISNULL(l.superuser_id) AND ss.name = 'distributed'";
db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error());  

$sql = "DELETE l.* FROM `log_garmentusers_garments` l
    INNER JOIN garments g ON g.id = l.garment_id
    WHERE ISNULL(l.superuser_id) AND g.scanlocation_id = 3";
db_query($sql) or die("ERROR LINE ". __LINE__ .": ". db_error()); 

