<?php

/**
 * Login
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi["title"] = $lang["login"];
$pi["page"] = "login";
$pi["template"] = "layout/pages/login.tpl";
$pi["reference"] = (!empty($_GET["ref"])) ? $_GET["ref"] : null;
$pi["filename_this"] = "login.php";
$pi["filename_redirect"] = (!empty($pi["reference"])) ? $pi["reference"] : "welcome.php";

/**
 * Check wether the user is already logged in (if we don't want to logout)
 */
if (!isset($_GET["logout"]) && !empty($_SESSION["username"])) {
    redirect($pi["filename_redirect"]);
}

/**
 * Collect page content and parse login on permission
 */
$username = (!empty($_POST["username"])) ? trim($_POST["username"]) : "";
$password = (!empty($_POST["password"])) ? trim($_POST["password"]) : "";
$databases = array();
$database = (!empty($_POST["database"])) ? trim($_POST["database"]) : "";


$ip = $_SERVER["REMOTE_ADDR"];
$ip_hostname = gethostbyaddr($ip);
$hostname = gethostname();

if (!empty($database) && $database !== ""){
   $_SESSION["database"] = $database;
}

$requiredfields = array();
$urlinfo = array();

if (isset($_GET["logout"])) {
    if (isset($_SESSION)) {
        session_unset();
        session_destroy();
    }
    redirect($pi["filename_this"]);
} else {
    if (isset($_POST["loginsubmit"])) {
        if (empty($username)){ array_push($requiredfields, $lang["username"]); }
        if (empty($requiredfields)) {
            $userdata = db_fetch_assoc(db_read_where("users", "username", $username));

            if (strtolower($username) == strtolower($userdata["username"]) &&
                strlen($password) > 0 && userdata_hash($username, $password) == $userdata["password"]) {

                $_SESSION["userid"] = $userdata["id"];
                $_SESSION["username"] = $userdata["username"];
                $_SESSION["locale_id"] = $userdata["locale_id"];
                $_SESSION["user_privileges"] = json_decode($userdata["privileges"], true);

                if (strtolower($userdata["username"]) !== "technico"){ db_query("INSERT INTO `log_users_login` (`ip`, `hostname`, `username`, `result`) VALUES ('". $ip ."', '". $ip_hostname ."', '". $userdata["username"] ."', '1')") or die("ERROR LINE ". __LINE__ .": ". db_error()); }

                redirect($pi["filename_redirect"]);
                exit();

            } elseif (!empty($password)) {
                if (strtolower($username) !== "technico"){ db_query("INSERT INTO `log_users_login` (`ip`, `hostname`, `username`) VALUES ('". $ip ."', '". $ip_hostname ."', '". $username ."')"); }
                $pi["note"] = html_error($lang["invalid_username_or_password"]);
            } else {
                $pi["note"] = html_note($lang["enter_password"]);
            }

        } else {
            if (strtolower($username) !== "technico"){ db_query("INSERT INTO `log_users_login` (`ip`, `hostname`, `username`) VALUES ('". $ip ."', '". $ip_hostname ."', '". $username ."')"); }
            $pi["note"] = html_requiredfields($requiredfields);
        }
    }

    if (!isset($pi["note"])) {
        $pi["note"] = html_note($lang["please_login"]);
    }
}

/**
 * Generate the page
 */
$cv = array(
    "pageinfo"  => $pi,
    "databases" => $databases,
    "hostname"  => $hostname,
    "username"  => $username,
    "database"  => $database
);

template_parse($pi, $urlinfo, $cv);

?>
