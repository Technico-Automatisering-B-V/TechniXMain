<?php

/**
 * LipsICode Converter
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

// Require tag converter
require_once "library/converters/tagconverter.php";

// LipsICodeConverter Class
class LipsICodeConverter implements TagConverter
{
    const PATTERN = "|<[a-zA-Z0-9]{24}>|";

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
            $c = hexdec($t[2].$t[3] . $t[0].$t[1]);

            $sn = hexdec($t[14] . $t[15] . $t[12] . $t[13] . $t[10] . $t[11] .
                         $t[8] . $t[9] . $t[22] . $t[23] . $t[20] . $t[21] .
                         $t[18] . $t[19] . $t[16] . $t[17]);

            $s = str_pad($sn, 11, "0", STR_PAD_LEFT);

            return $c . $s;
        }
        return $t;
    }
}