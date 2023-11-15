<?php

/**
 * RentexFloronNew Converter
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

// Require tag converter
require_once "library/converters/tagconverter.php";

// RentexFloronConverter Class
class RentexFloronNewConverter implements TagConverter
{
    const PATTERN_ISO15693   = "/^0{8}E0[A-F0-9]{14}$/";
    const PATTERN_ISO15693_2 = "/E0[A-F0-9]{14}$/";
    const PATTERN_C210       = "/^0{8}27A1[A-F0-9]{12}$/";

    /**
     * Convert tag
     *
     * C210 ario 10tl, 	15 chr DEC (00000000 27A10100 02C7917E -> 8000000****).
     * ISO15693, 		14 chr HEX (00000000 E0040001 00000123 -> 04000100000123).
     *
     * @access public
     * @param  string $t tag
     * @return string
     */
    public static function convert($t)
    {
        if(strtoupper(substr($t, 0, 4)) === "SCEM") { 
            $t = substr($t, 4);
            if (preg_match(self::PATTERN_ISO15693 , $t)) {
                return '00000'.substr($t, -10);
            } elseif (preg_match(self::PATTERN_ISO15693_2, $t)) {
                return '00000'.substr($t, -10);
            } elseif (preg_match(self::PATTERN_C210, $t)) {
                return '0000000' . substr($t, -8);
            } else {
                return $t;
            }
        }
        return $t; 
    }
}
