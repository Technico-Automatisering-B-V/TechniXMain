<?php

/**
 * Bootstrap
 *
 * This file is included everythere thoughout the software
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

// Default Autoload
function defaultAutoloader($c)
{
    $ds = array(
        __DIR__ . "/",
        __DIR__ . "/converters/"
    );

    $f = strtolower($c) . ".php";

    foreach ($ds as $d) {
        // See if the file exists
        if (file_exists($d . $f)) {
            require_once $d . $f;
            return true;
        }
    }
}

spl_autoload_register("defaultAutoloader");

?>
