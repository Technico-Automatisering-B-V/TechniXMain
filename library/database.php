<?php

/**
 * MySQL database Class
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 *
 * @example
 *
 * $db = Database::getInstance();
 */

// Database Class
class Database
{
    /**
     * Database instance
     * @var object
     * @static
     */
    private static $i = null;

    /**
     * getInstance
     *
     * @access public
     * @return object Database instance
     * @static
     */
    public static function getInstance()
    {
        if (!isset($_SESSION["database"])){
            $_SESSION["database"] = "technix_workwear";
        }

        if (is_null(self::$i)) {
            self::$i = new PDO(
                "mysql:host=localhost;port=3306;dbname=".$_SESSION["database"],
                "root", "t3chn1x.sql",
                array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"
                )
            );
            self::$i->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION);
        }

        return self::$i;
    }
}

?>
