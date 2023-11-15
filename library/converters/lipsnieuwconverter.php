<?php

/**
 * LipsNieuw Converter (Isala)
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

// Require tag converter
require_once "library/converters/tagconverter.php";

// LipsNieuwConverter Class
class LipsNieuwConverter implements TagConverter
{
    const PATTERN = "|<00000000E0[a-zA-Z0-9]{14}>|";

    /**
     * Convert tag
     *
     * @access public
     * @param  string $t tag
     * @return string
     */
    public static function convert($t)
    {
        if(strtoupper(substr($t, 0, 4)) === "SCEM") { 
            $t = substr($t, 4);
            return substr($t, 16);
        }
        return $t; 
    }
}
