<?php

/**
 * Tag Converter Factory
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

// Require boostrap
require_once "library/bootstrap.php";

// TagConverterFactory class
class TagConverterFactory
{
    /**
     * Create Tag Converter
     *
     * @access public
     * @param  string $p
     * @return LipsICode|ClfIso15693|RentexFloron|SimpleTag|SterimaIso15693
     */
    public static function createTagConverter($p)
    {
        switch ($p)
        {
            case "Lips"             :
            case "LipsICode"        : return new LipsICodeConverter();
            case "LipsNieuw"        : return new LipsNieuwConverter();
            case "CleanLeaseFortex" :
            case "ClfIso15693"      : return new ClfIso15693Converter();
            case "RentexFloron"     : return new RentexFloronConverter();
            case "RentexFloronNew"  : return new RentexFloronNewConverter();
            case "Simple"           : return new SimpleTagConverter();
            case "SterimaIso15693"  :
            case "SterimaVanguard"  : return new SterimaIso15693Converter();
            default                 : return new SimpleTagConverter();
        }
    }
}
