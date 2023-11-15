<?php

/**
 * System access
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
$pi["title"] = $lang["access_denied"];
$pi["template"] = "layout/pages/system_access.tpl";
$pi["page"] = "simple";
$pi["filename_this"] = "system_access.php";

/**
 * Collect page content
 */
$urlinfo = array();

/**
 * Generate the page
 */
$cv = array(
    "pageinfo" => $pi
);

template_parse($pi, $urlinfo, $cv);

?>
