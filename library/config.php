<?php

/**
 * Configuration Class
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 *
 * @example
 *
 * $config = Config::getEmailSettings("json");
 */

// Configuration Class
class Config
{
    /**
     * Database object
     * @var object
     * @static
     */
    private static $db;

    /**
     * Initiate
     *
     * @access private
     * @return void
     * @static
     */
    private static function init()
    {
        self::$db = Database::getInstance();
    }

    /**
     * getAll
     *
     * @access public
     * @param  string $f response format
     * @return mixed
     * @throws Exception
     * @static
     */
    public static function getAll($f = "array")
    {
        $r = array();

        try {
            $r["client"] = self::getClientSettings();
            $r["distributors"] = self::getDistributors();
            $r["email"] = self::getEmailSettings();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return self::respond($f, $r);
    }

    /**
     * getClientSettings
     *
     * @access public
     * @param  string $f response format
     * @return mixed
     * @throws Exception
     * @static
     */
    public static function getClientSettings($f = "array")
    {
        self::init();

        $q = "SELECT COUNT(*) AS 'count', `value` FROM `settings` WHERE `name`='client'";

        try {
            $a = self::$db->prepare($q);
            $a->execute();
        } catch (PDOException $e) {
            throw new Exception(__METHOD__ . ": " . $e->getMessage());
        }

        $r = $a->fetch(PDO::FETCH_ASSOC);

        if ($r["count"] === "0") {
            throw new Exception(__METHOD__ . ": Record 'client' not found in settings table!");
        }

        return self::respond($f, $r["value"]);
    }

    /**
     * getEmailSettings
     *
     * @access public
     * @param  string $f response format
     * @return mixed
     * @throws Exception
     * @static
     */
    public static function getEmailSettings($f = "array")
    {
        self::init();

        $q = "SELECT COUNT(*) AS 'count', `value` FROM `settings` WHERE `name`='email'";

        try {
            $a = self::$db->prepare($q);
            $a->execute();
        } catch (PDOException $e) {
            throw new Exception(__METHOD__ . ": " . $e->getMessage());
        }

        $r = $a->fetch(PDO::FETCH_ASSOC);

        if ($r["count"] === "0") {
            throw new Exception(__METHOD__ . ": Record 'email' not found in settings table!");
        }

        return self::respond($f, $r["value"]);
    }

    /**
     * getDistributors
     *
     * @access public
     * @param  string $f response format
     * @return mixed
     * @throws Exception
     * @static
     */
    public static function getDistributors($f = "array")
    {
        self::init();

        $q = "SELECT * FROM `distributors` ORDER BY `id` ASC";

        try {
            $a = self::$db->prepare($q);
            $a->execute();
        } catch (PDOException $e) {
            throw new Exception(__METHOD__ . ": " . $e->getMessage());
        }

        $r = $a->fetchAll(PDO::FETCH_ASSOC);

        if (count($r) === 0) {
            throw new Exception(__METHOD__ . ": No distributors found!");
        }

        return self::respond($f, $r);
    }

   /**
    * setClientSettings
    *
    * @access public
    * @param  array $d data array
    * @return bool
    * @throws Exception
    * @static
    */
    public static function setClientSettings($d)
    {
        self::init();

        if (is_array($d)) {
            $c = json_encode($d);
            $q = "UPDATE `settings` SET `value`='$c' WHERE `name`='client'";

            try {
                $a = self::$db->prepare($q);
                $a->execute();
            } catch (PDOException $e) {
                throw new Exception(__METHOD__ . ": " . $e->getMessage());
            }
        } else {
            throw new Exception(__METHOD__ . ": Data is not an array!");
        }

        return true;
    }

   /**
    * setEmailSettings
    *
    * @access public
    * @param  array $d data array
    * @return bool
    * @throws Exception
    * @static
    */
    public static function setEmailSettings($d)
    {
        self::init();

        if (is_array($d)) {
            $c = json_encode($d);
            $q = "UPDATE `settings` SET `value`='$c' WHERE `name`='email'";

            try {
                $a = self::$db->prepare($q);
                $a->execute();
            } catch (PDOException $e) {
                throw new Exception(__METHOD__ . ": " . $e->getMessage());
            }
        } else {
            throw new Exception(__METHOD__ . ": Data is not an array!");
        }

        return true;
    }

    /**
     * Respond
     *
     * Data can be an array or a JSON string
     *
     * @access private
     * @param  string $f response format
     * @param  mixed  $d data
     * @return mixed
     * @throws Exception
     * @static
     */
    private static function respond($f, $d)
    {
        if (!is_array($d)) {
            $r = json_decode($d, true);
        } else {
            $r = $d;
        }

        if ($f === "array") {
            return $r;
        } elseif ($f === "json") {
            return json_encode($r);
        } else {
            throw new Exception(__METHOD__ . ": Invalid response format!");
        }
    }

}

?>
