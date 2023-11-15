<?php

/**
 * Garment washcount
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi["access"] = array("linen_service", "washcount_garments");
$pi["group"] = $lang["linen_service"];
$pi["title"] = $lang["washcount_garments"];
$pi["template"] = "layout/pages/garmentwashcount.tpl";
$pi["filename_list"] = "garmentwashcount.php";
$pi["filename_details"] = "garmentwashcount_details.php";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$table = "distributorlocations";
$columns = "id name washcount_check_enabled washcount_check_from washcount_check_to";

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

$listdata = db_read($table, $columns, $urlinfo);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata
);

template_parse($pi, $urlinfo, $cv);

?>
