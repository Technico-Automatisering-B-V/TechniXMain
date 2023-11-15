<?php

/**
 * Report MUPAPU MUD
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
$pi["group"] = "Technico";
$pi["title"] = "MUPAPU " . strtolower($lang["periods"]);
$pi["template"] = "layout/pages/report_mupapu_mud.tpl";
$pi["filename_list"] = "report_mupapu_mud.php";
$pi["filename_details"] = "report_mupapu_mud_details.php";
$pi["page"] = "list";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Collect page content
 */
$sql = "SELECT `id`, `day`, `hours`, `minutes`, `description` FROM `loadadvice_mupapu` ORDER BY `day`, `hours`, `minutes`";

$listdata = db_query($sql);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "sortlinks" => $sortlinks,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
