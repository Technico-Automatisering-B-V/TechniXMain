<?php

/**
 * Page control functions
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

function redirect($uri)
{
	Header("Location: " . $uri);
	die("Failed to commit redirect unexpectedly. Please contact Technico Automatisering.");
}

function lang($string, $vars = array())
{
    global $lang;

    if (isset($lang[$string]))
    {
        $string = $lang[$string];
    }
    else
    {
        $string = str_replace("_", " ", $string);
    }

    if ($vars && count($vars > 0))
    {
        $string = str_replace(array_keys($vars), array_values($vars), $string);
    }

    return $string;
}

?>
