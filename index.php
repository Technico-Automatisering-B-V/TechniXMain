<?php

/**
 * Index page
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
 * Page settings we need at first hand
 */
$pi["filename_this"] = "login.php";
$pi["filename_redirect"] = "login.php";

redirect($pi["filename_redirect"]);

?>
