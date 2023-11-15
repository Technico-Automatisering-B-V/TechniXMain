<?php

/**
 * Garmentusers
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
$pi["access"] = array("common", "garmentusers");
$pi["group"] = $lang["common"];
$pi["title"] = $lang["garmentusers"];
$pi["filename_list"] = "garmentusers.php";
$pi["filename_details"] = "garmentuser_details.php";
$pi["template"] = "layout/pages/garmentusers.tpl";
$pi["page"] = "list";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

$table = "tmp_users";
$urlinfo = array();

if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
} else {
    $hassubmit = "";
}


/**
 * Create view
 */

if (!empty($_GET["cid"])){
	$urlinfo["cid"] = trim($_GET["cid"]);
    $where = " WHERE circulationgroups.id = ". trim($_GET["cid"]);
} else {
	$urlinfo["cid"] = null;
    $where = " ";
}

$query = "CREATE VIEW `". $table ."` AS (SELECT
        `garmentusers`.`surname` AS 'surname',
        `garmentusers`.`id` AS 'id',
        `garmentusers`.`lockernumber` AS 'lockernumber',
        `garmentusers`.`title` AS 'title',
        `garmentusers`.`name` AS 'name',
        `garmentusers`.`maidenname` AS 'maidenname',
        `garmentusers`.`initials` AS 'initials',
        `garmentusers`.`gender` AS 'gender',
        `garmentusers`.`intermediate` AS 'intermediate',
        `garmentusers`.`personnelcode` AS 'personnelcode',
        `garmentusers`.`code` AS 'code',
        `garmentusers`.`code2` AS 'code2',
        `garmentusers`.`code3` AS 'code3',
        `garmentusers`.`exportcode` AS 'exportcode',
        `garmentusers`.`active` AS 'active',
        IF(!ISNULL(`garmentusers`.`distributor_id`),`garmentusers`.`distributor_id`,
	IF(!ISNULL(`garmentusers`.`distributor_id2`),`garmentusers`.`distributor_id2`,
		IF(!ISNULL(`garmentusers`.`distributor_id3`),`garmentusers`.`distributor_id3`,
			IF(!ISNULL(`garmentusers`.`distributor_id4`),`garmentusers`.`distributor_id4`,
				IF(!ISNULL(`garmentusers`.`distributor_id5`),`garmentusers`.`distributor_id5`,
					IF(!ISNULL(`garmentusers`.`distributor_id6`),`garmentusers`.`distributor_id6`,
						IF(!ISNULL(`garmentusers`.`distributor_id7`),`garmentusers`.`distributor_id7`,
							IF(!ISNULL(`garmentusers`.`distributor_id8`),`garmentusers`.`distributor_id8`,
                                                            IF(!ISNULL(`garmentusers`.`distributor_id9`),`garmentusers`.`distributor_id9`,
                                                                IF(!ISNULL(`garmentusers`.`distributor_id10`),`garmentusers`.`distributor_id10`,NULL))))))))))  AS 'distributor_id',
        `garmentusers`.`date_service_off` AS 'date_service_off',
        `garmentusers`.`deleted_on` AS 'deleted_on',
        `professions`.`id` AS 'profession_id',
        `professions`.`name` AS 'profession_name',
        `circulationgroups`.`id` AS 'circulationgroup_id',
        `clientdepartments`.`id` AS 'clientdepartment_id',
        `clientdepartments`.`name` AS 'clientdepartment_name',
        `costplaces`.`id` AS 'costplace_id',
        `costplaces`.`value` AS 'costplace_value',
        `functions`.`id` AS 'function_id',
        `functions`.`value` AS 'function_value',
        (SELECT COUNT(*) FROM `garmentusers_garments` WHERE `garmentuser_id` = `garmentusers`.`id` AND `superuser_id` = 0) AS 'garments_in_use'
    FROM `garmentusers`
    LEFT JOIN `professions` ON `garmentusers`.`profession_id` = `professions`.`id`
    LEFT JOIN `circulationgroups_garmentusers` ON `garmentusers`.`id` = `circulationgroups_garmentusers`.`garmentuser_id`
    LEFT JOIN `circulationgroups` ON `circulationgroups_garmentusers`.`circulationgroup_id` = `circulationgroups`.`id`
    LEFT JOIN `clientdepartments` ON `garmentusers`.`clientdepartment_id` = `clientdepartments`.`id`
    LEFT JOIN `costplaces` ON `garmentusers`.`costplace_id` = `costplaces`.`id`
    LEFT JOIN `functions` ON `garmentusers`.`function_id` = `functions`.`id`
    LEFT JOIN `distributorlocations` ON `circulationgroups`.`id` = `distributorlocations`.`circulationgroup_id`".
    $where
    ." GROUP BY `garmentusers`.`id`
    ORDER BY `garmentusers`.`surname` ASC)";

db_query("DROP VIEW IF EXISTS `". $table ."`");
db_query($query);

$columns = "surname id lockernumber title name maidenname initials gender intermediate personnelcode code code2 code3 exportcode active distributor_id deleted_on profession_id profession_name circulationgroup_id clientdepartment_id clientdepartment_name costplace_id costplace_value function_id function_value date_service_off garments_in_use";

/**
 * Collect page content
 */


if (!empty($_GET["ip"])){
    $urlinfo["ip"] = trim($_GET["ip"]);
} else {
    $urlinfo["ip"] = null;
}

if (!empty($_GET["pid"])) {
    $urlinfo["pid"] = trim($_GET["pid"]);
} else {
    $urlinfo["pid"] = null;
}

if (!empty($_GET["clientdepartment_id"])){
    $urlinfo["clientdepartment_id"] = trim($_GET["clientdepartment_id"]);
} else {
    $urlinfo["clientdepartment_id"] = null;
}

if (!empty($_GET["costplace_id"])) {
    $urlinfo["costplace_id"] = trim($_GET["costplace_id"]);
} else {
    $urlinfo["costplace_id"] = null;
}

if (!empty($_GET["function_id"])) {
    $urlinfo["function_id"] = trim($_GET["function_id"]);
} else {
    $urlinfo["function_id"] = null;
}

if (!empty($_GET["col-surname"])){ $urlinfo["col-surname"] = $_GET["col-surname"]; }else{ $urlinfo["col-surname"] = ""; }
if (!empty($_GET["col-name"])){ $urlinfo["col-name"] = $_GET["col-name"]; }else{ $urlinfo["col-name"] = ""; }
if (!empty($_GET["col-personnelcode"])){ $urlinfo["col-personnelcode"] = $_GET["col-personnelcode"]; }else{ $urlinfo["col-personnelcode"] = ""; }
if (!empty($_GET["col-code"])){ $urlinfo["col-code"] = $_GET["col-code"]; }else{ $urlinfo["col-code"] = ""; }
if (!empty($_GET["col-code2"])){ $urlinfo["col-code2"] = $_GET["col-code2"]; }else{ $urlinfo["col-code2"] = ""; }
if (!empty($_GET["col-clientdepartment"])){ $urlinfo["col-clientdepartment"] = $_GET["col-clientdepartment"]; }else{ $urlinfo["col-clientdepartment"] = ""; }
if (!empty($_GET["col-profession"])){ $urlinfo["col-profession"] = $_GET["col-profession"]; }else{ $urlinfo["col-profession"] = ""; }
if (!empty($_GET["col-access"])){ $urlinfo["col-access"] = $_GET["col-access"]; }else{ $urlinfo["col-access"] = ""; }
if (!empty($_GET["col-clothing"])){ $urlinfo["col-clothing"] = $_GET["col-clothing"]; }else{ $urlinfo["col-clothing"] = ""; }
if (!empty($_GET["col-garments_in_use"])){ $urlinfo["col-garments_in_use"] = $_GET["col-garments_in_use"]; }else{ $urlinfo["col-garments_in_use"] = ""; }
if (!empty($_GET["col-lockernumber"])){ $urlinfo["col-lockernumber"] = $_GET["col-lockernumber"]; }else{ $urlinfo["col-lockernumber"] = ""; }
if (!empty($_GET["col-costplace"])){ $urlinfo["col-costplace"] = $_GET["col-costplace"]; }else{ $urlinfo["col-costplace"] = ""; }
if (!empty($_GET["col-function"])){ $urlinfo["col-function"] = $_GET["col-function"]; }else{ $urlinfo["col-function"] = ""; }
if (!empty($_GET["dsearch"])){ $urlinfo["dsearch"] = $_GET["dsearch"]; }else{ $urlinfo["dsearch"] = ""; }

// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

// Required for selectbox: clientdepartment_id
$clientdepartments_conditions["order_by"] = "name";
$clientdepartments = db_read("clientdepartments", "id name", $clientdepartments_conditions);
$clientdepartments_count = db_num_rows($clientdepartments);

// Required for selectbox: costplaces
$costplaces_conditions["order_by"] = "value";
$costplaces = db_read("costplaces", "id value", $costplaces_conditions);
$costplaces_count = db_num_rows($costplaces);

// Required for selectbox: functions
$functions_conditions["order_by"] = "value";
$functions = db_read("functions", "id value", $functions_conditions);
$functions_count = db_num_rows($functions);

// Required for selectbox: professions
$professions_conditions["order_by"] = "name";
$professions = db_read("professions", "id name", $professions_conditions);

// Required for selectbox for 'yes' and 'no'
$yes_no = array(
    "yes" => $lang["yes"],
    "no" => $lang["no"]
);

$urlinfo["search"] = geturl_search();

$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

if ($hassubmit == "export") {
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
   // $urlinfo['limit_num'] = 10000;
}

if (isset($_GET["s"])) {
    $_SESSION["garmentusers"]["show"] = $_GET["s"];
} elseif (isset($_GET["custom"])) {
    $_SESSION["garmentusers"]["show"] = null;
}

if (isset($_SESSION["garmentusers"]["show"])) {
    $s[1] = false;
    $s[2] = false;
    $s[3] = false;

    foreach ($_SESSION["garmentusers"]["show"] as $show => $value) {
        $s[$show] = ((isset($_SESSION["garmentusers"]["show"][$show])) ? true : false);
    }

    if (!$s[1] && !$s[2] && !$s[3]) { $urlinfo["where"]["1"] = "true = false"; }
    if (!$s[1] && !$s[2] && $s[3]) { $urlinfo["where"]["1"] = "deleted_on isnot null"; }
    if (!$s[1] && $s[2] && !$s[3]) { $urlinfo["where"]["1"] = "active = 2"; $urlinfo["where"]["2"] = "deleted_on is null"; }
    if (!$s[1] && $s[2] && $s[3]) { $urlinfo["where"]["1"] = "active = 2 OR deleted_on isnot null"; }
    if ($s[1] && !$s[2] && !$s[3]) { $urlinfo["where"]["1"] = "active = 1"; $urlinfo["where"]["2"] = "deleted_on is null"; }
    if ($s[1] && !$s[2] && $s[3]) { $urlinfo["where"]["1"] = "active = 1 OR deleted_on isnot null"; }
    if ($s[1] && $s[2] && !$s[3]) { $urlinfo["where"]["1"] = "deleted_on is null"; }
}

if (!isset($_SESSION["garmentusers"]["custom_selection"])) {
    $urlinfo["where"]["1"] = "active = 1";
    $urlinfo["where"]["2"] = "deleted_on is null";
    $_SESSION["garmentusers"]["show"][1] = true;
}

if (isset($urlinfo["clientdepartment_id"]) && !empty($urlinfo["clientdepartment_id"])){
    $urlinfo["where"]["4"] = "clientdepartment_id = " . $urlinfo["clientdepartment_id"];
}

if (isset($urlinfo["pid"])) {
    $urlinfo["where"]["5"] = "profession_id = " . $urlinfo["pid"];
}

if ($urlinfo["ip"] == "yes") {
    $urlinfo["where"]["6"] = "garments_in_use > 0";
} elseif ($urlinfo["ip"] == "no") {
    $urlinfo["where"]["6"] = "garments_in_use = 0";
}

if (isset($urlinfo["costplace_id"]) && !empty($urlinfo["costplace_id"])) {
    $urlinfo["where"]["7"] = "costplace_id = " . $urlinfo["costplace_id"];
}

if (isset($urlinfo["function_id"]) && !empty($urlinfo["function_id"])) {
    $urlinfo["where"]["8"] = "function_id = " . $urlinfo["function_id"];
}

$_SESSION["garmentusers"]["custom_selection"] = true;

$listdata = db_read($table, $columns, $urlinfo);

//fixme! exclude GROUP BY in the remaining COUNT queries
unset($urlinfo["group_by"]);

$limit_total_res = db_count($table, $columns, $urlinfo);
if ($limit_total_res) {
    $urlinfo["limit_total"] = db_fetch_row($limit_total_res);
    $urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string
}

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["surname"] = generate_sortlink("surname", $lang["surname"], $pi, $urlinfo);
$sortlinks["clientdepartment"] = generate_sortlink("clientdepartment_name", $lang["clientdepartment"], $pi, $urlinfo);
$sortlinks["profession"] = generate_sortlink("profession_name", $lang["profession"], $pi, $urlinfo);
$sortlinks["name"] = generate_sortlink("name", $lang["first_name"], $pi, $urlinfo);
$sortlinks["personnelcode"] = generate_sortlink("personnelcode", $lang["personnelcode"], $pi, $urlinfo);
$sortlinks["code"] = generate_sortlink("code", $lang["passcode"], $pi, $urlinfo);
$sortlinks["code2"] = generate_sortlink("code2", $lang["passcode"]." 2", $pi, $urlinfo);
$sortlinks["clothing"] = generate_sortlink("distributor_id", $lang["clothing"], $pi, $urlinfo);
$sortlinks["access"] = generate_sortlink("active", $lang["access"], $pi, $urlinfo);
$sortlinks["garments_in_use"] = generate_sortlink("garments_in_use", $lang["in_possession"], $pi, $urlinfo);
$sortlinks["lockernumber"] = generate_sortlink("lockernumber", $lang["lockernumber"], $pi, $urlinfo);
$sortlinks["costplace"] = generate_sortlink("costplace_value", $lang["costplace"], $pi, $urlinfo);
$sortlinks["function"] = generate_sortlink("function_value", $lang["function"], $pi, $urlinfo);

$pagination = generate_pagination($pi, $urlinfo);

/**
 * Export
 */
if ($hassubmit == "export") {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename ."");
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = "";
    if(!empty($urlinfo["col-surname"]))$header.=$lang["surname"]."\t";
    if(!empty($urlinfo["col-name"]))$header.=$lang["first_name"]."\t";
    if(!empty($urlinfo["col-personnelcode"]))$header.=$lang["personnelcode"]."\t";
    if(!empty($urlinfo["col-code"]))$header.=$lang["passcode"]."\t";
    if(!empty($urlinfo["col-code2"]))$header.=$lang["passcode"]." 2\t";
    if(!empty($urlinfo["col-clientdepartment"]))$header.=$lang["clientdepartment"]."\t";
    if(!empty($urlinfo["col-profession"]))$header.=$lang["profession"]."\t";
    if(!empty($urlinfo["col-access"]))$header.=$lang["access"]."\t";
    if(!empty($urlinfo["col-clothing"]))$header.=$lang["clothing"]."\t";
    if(!empty($urlinfo["col-garments_in_use"]))$header.=$lang["in_possession"]."\t";
    if(!empty($urlinfo["col-lockernumber"]))$header.=$lang["lockernumber"]."\t";
    if(!empty($urlinfo["col-costplace"]))$header.=$lang["costplace"]."\t";
    if(!empty($urlinfo["col-function"]))$header.=$lang["function"]."\t";

    $data = "";
    while(!empty($listdata) && $row = db_fetch_array($listdata)) {
        $line = "";
        $in = array();
        if(!empty($urlinfo["col-surname"]))array_push($in, generate_garmentuser_label($row["title"], $row["gender"], $row["initials"], $row["intermediate"], $row["surname"], $row["maidenname"], $row["personnelcode"]));
        if(!empty($urlinfo["col-name"]))array_push($in, $row["name"]);
        if(!empty($urlinfo["col-personnelcode"]))array_push($in, $row["personnelcode"]);
        if(!empty($urlinfo["col-code"]))array_push($in, $row["code"]);
        if(!empty($urlinfo["col-code2"]))array_push($in, $row["code2"]);
        if(!empty($urlinfo["col-clientdepartment"]))array_push($in, $row["clientdepartment_name"]);
        if(!empty($urlinfo["col-profession"]))array_push($in, $row["profession_name"]);
        if(!empty($urlinfo["col-access"]))array_push($in, (($row["active"] == 1) ? $lang["yes"] : $lang["no"]));
        if(!empty($urlinfo["col-clothing"]))array_push($in, ((!empty($row["distributor_id"])) ? $lang["self"] : $lang["size"]));
        if(!empty($urlinfo["col-garments_in_use"]))array_push($in, (($row["garments_in_use"] == 0) ? "-" : $row["garments_in_use"]));
        if(!empty($urlinfo["col-lockernumber"]))array_push($in, $row["lockernumber"]);
        if(!empty($urlinfo["col-costplace"]))array_push($in, $row["costplace_value"]);
        if(!empty($urlinfo["col-function"]))array_push($in, $row["function_value"]);

        foreach($in as $value) {
            if ((!isset($value)) OR ($value == "")) {
                $value = "\t";
            } else {
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"' . "\t";
            }
            $line .= $value;
        }
        $data .= trim($line)."\n";
    }
    $data = str_replace("\r", "", $data);

    print "$header\n$data";
    die();
}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "resultinfo" => $resultinfo,
    "sortlinks" => $sortlinks,
    "listdata" => $listdata,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "clientdepartments_count" => $clientdepartments_count,
    "clientdepartments" => $clientdepartments,
    "costplaces" => $costplaces,
    "costplaces_count" => $costplaces_count,
    "functions" => $functions,
    "functions_count" => $functions_count,
    "professions" => $professions,
    "yes_no" => $yes_no,
    "pagination" => $pagination
);

template_parse($pi, $urlinfo, $cv);

?>
