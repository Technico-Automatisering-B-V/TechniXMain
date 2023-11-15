<?php

/**
 * SterimaIso15693Converter Converter
 *
 * PHP version 5
 *
 * @author    Gabor Voros <gabor@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

// Require tag converter
require_once "library/converters/tagconverter.php";

// SimpleTagConverter Class
class SterimaIso15693Converter implements TagConverter
{
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
            return substr($t, -10);
        }
        return $t;
    }
}
