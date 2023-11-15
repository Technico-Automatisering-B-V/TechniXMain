<?php

/**
 * Tag Converter Interface
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

require_once "library/converters/tagconverter.php";

interface TagConverter
{
    public static function convert($t);
}
