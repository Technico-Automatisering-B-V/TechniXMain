<?php

/**
 * SimpleTag Converter
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
class SimpleTagConverter implements TagConverter
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
            $t      = substr($t, 4);
            $iso    = "43";
            $icode  = "40";

            $type = substr($t, 0, 2);
            switch ($type) {
                case $iso:
                    return substr($t, 2);
                    break;
                case $icode: 
                    $t  = substr($t, 2);
                    $ft = substr($t, -2);
                    for($i=1;$i>=-11;$i--) {
                        $i--;
                        $ft .= substr($t, 12+$i, -2+$i);
                    }
                    return $ft;
                    break;
                default:
                    return $t;
            }
        }
	return substr($t, -24);
        //return $t;
    }
}
