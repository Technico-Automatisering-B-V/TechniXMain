<?php

/**
 * Hardware monitor
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once 'include/engine.php';

/**
 * Page settings
 */
$pi["group"] = "Technico";
$pi["filename_this"] = "monitor-hard.php";
$pi["filename_list"] = "monitor-hard.php";
$pi["template"] = "layout/pages/monitor-hard.tpl";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Collect page content
 */
$urlinfo = array();

$table = "distributorlocations";
$columns = "id name external_ip_address";

$urlinfo["limit_start"] = 0;
$urlinfo["limit_num"] = "65535";
$urlinfo["limit_total"] = "65535"; //array->string

$distributorlocations = db_read($table, $columns, $urlinfo);

/**
 * Generate the page
 */
$cv = array(
	"pi" => $pi,
        "distributorlocations" => $distributorlocations
);

template_parse($pi, $urlinfo, $cv);

?>
