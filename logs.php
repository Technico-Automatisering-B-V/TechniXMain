<?php

/**
 * Logs
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
$pi["group"] = "Technico";
$pi["title"] = $lang["logs"];
$pi["filename_list"] = "logs.php";
$pi["template"] = "layout/pages/logs.tpl";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
    redirect("login.php");
}

/**
 * Authorisation
 */
if (isset($_GET["emptyUserlogin"])){ db_query("TRUNCATE TABLE `log_users_login`"); }

$auth_query = "SELECT * FROM `log_users_login` ORDER BY `datetime` DESC";
$auth_sql = db_query($auth_query) or die ("ERROR [". __LINE__ ."]");
$auth_num_results = db_num_rows($auth_sql);

/**
 * Importer garments
 */
$start_imp_garments_result = "";
if (isset($_POST["startImporterGarments"])){
    ob_start();
    passthru("cd /var/www/edwin.dev.technico.nl/importerv2/ && php xgs_import.php", $result);
    $start_imp_garments_result = ob_get_contents();
    ob_end_clean();
}

$imp_garments_query = "SELECT * FROM `log_importer_garments` ORDER BY `datetime` DESC";
$imp_garments_sql = db_query($imp_garments_query) or die ("ERROR [". __LINE__ ."]");
$imp_garments_num_results = db_num_rows($imp_garments_sql);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "auth_num_results" => $auth_num_results,
    "auth_data" => $auth_sql,
    "imp_garments_results" => $imp_garments_results,
    "imp_garments_data" => $imp_garments_sql,
    "start_imp_garments_result" => $start_imp_garments_result
);

template_parse($pi, $urlinfo, $cv);

?>
