<?php

/**
 * Welcome page
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
$pi["title"] = $lang["welcome"];
$pi["filename_list"] = "";
$pi["filename_this"] = "login.php";
$pi["filename_redirect"] = "welcome.php";
$pi["template"] = "layout/pages/welcome.tpl";
$pi["page"] = "simple";

/**
 * Collect page content
 */
$urlinfo = array();
$cv = array();

/**
 * Generate the page
 */
if (!isset($_SESSION["username"])) {
    redirect("/technix/");
} 
template_parse($pi, $urlinfo, $cv);

?>
