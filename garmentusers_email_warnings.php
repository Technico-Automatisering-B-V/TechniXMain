<?php

/**
 * Garmentusers email warnings
 *
 * @author    G. I. Voros <gabor@technico.nl>
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
$pi["access"] = array("reports", "email_warning");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["email_warning"];
$pi["filename_list"] = "garmentusers_email_warnings.php";
$pi["filename_details"] = "garmentuser_details.php";
$pi["template"] = "layout/pages/garmentusers_email_warnings.tpl";
$pi["page"] = "list";
$pi["toolbar"]["no_new"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
} else {
    $hassubmit = "";
}

$table = "tmp_garmentusers_email_warnings";

//required for selectbox: error types
$statuses["ALL"]     = $lang["(all)"];
$statuses["warning"] = $lang["warning"];
$statuses["mail_1"]  = $lang["mail_1"];
$statuses["mail_2"]  = $lang["mail_2"];
$statuses["blocked"] = $lang["blocked"];

if (!empty($_GET["status"])) {
    $urlinfo["status"] = $_GET["status"];
} else {
    $urlinfo["status"] = "ALL";
}
/**
 * Get ids
 */

if (!empty($_GET["cid"])){
    $urlinfo["cid"] = trim($_GET["cid"]);
    $where = " AND c.id = ". trim($_GET["cid"]);
} else {
    $urlinfo["cid"] = null;
    $where = " ";
}

if (!empty($_GET["pid"])) {$urlinfo["pid"] = trim($_GET["pid"]);} else {$urlinfo["pid"] = null;}
if (!empty($_GET["clientdepartment_id"])){$urlinfo["clientdepartment_id"] = trim($_GET["clientdepartment_id"]);} else {$urlinfo["clientdepartment_id"] = null;}
if (!empty($_GET["costplace_id"])) {$urlinfo["costplace_id"] = trim($_GET["costplace_id"]);} else {$urlinfo["costplace_id"] = null;}
if (!empty($_GET["function_id"])) {$urlinfo["function_id"] = trim($_GET["function_id"]);} else {$urlinfo["function_id"] = null;}

if (!empty($_GET["aid"])) {$urlinfo["aid"] = $_GET["aid"];} else {$urlinfo["aid"] = "";}
if (!empty($_GET["sid"])) {$urlinfo["sid"] = $_GET["sid"];} else {$urlinfo["sid"] = "";}
if (!empty($_GET["mid"])) {$urlinfo["mid"] = $_GET["mid"];} else {$urlinfo["mid"] = "";}

if (!empty($_GET["col-surname"])){ $urlinfo["col-surname"] = $_GET["col-surname"]; }else{ $urlinfo["col-surname"] = ""; }
if (!empty($_GET["col-name"])){ $urlinfo["col-name"] = $_GET["col-name"]; }else{ $urlinfo["col-name"] = ""; }
if (!empty($_GET["col-personnelcode"])){ $urlinfo["col-personnelcode"] = $_GET["col-personnelcode"]; }else{ $urlinfo["col-personnelcode"] = ""; }
if (!empty($_GET["col-clientdepartment"])){ $urlinfo["col-clientdepartment"] = $_GET["col-clientdepartment"]; }else{ $urlinfo["col-clientdepartment"] = ""; }
if (!empty($_GET["col-profession"])){ $urlinfo["col-profession"] = $_GET["col-profession"]; }else{ $urlinfo["col-profession"] = ""; }
if (!empty($_GET["col-costplace"])){ $urlinfo["col-costplace"] = $_GET["col-costplace"]; }else{ $urlinfo["col-costplace"] = ""; }
if (!empty($_GET["col-function"])){ $urlinfo["col-function"] = $_GET["col-function"]; }else{ $urlinfo["col-function"] = ""; }
if (!empty($_GET["col-service_off"])){ $urlinfo["col-service_off"] = $_GET["col-service_off"]; }else{ $urlinfo["col-service_off"] = ""; }
if (!empty($_GET["col-article"])){ $urlinfo["col-article"] = $_GET["col-article"]; }else{ $urlinfo["col-article"] = ""; }
if (!empty($_GET["col-size"])){ $urlinfo["col-size"] = $_GET["col-size"]; }else{ $urlinfo["col-size"] = ""; }
if (!empty($_GET["col-modification"])){ $urlinfo["col-modification"] = $_GET["col-modification"]; }else{ $urlinfo["col-modification"] = ""; }
if (!empty($_GET["col-max_days_before_warning"])){ $urlinfo["col-max_days_before_warning"] = $_GET["col-max_days_before_warning"]; }else{ $urlinfo["col-max_days_before_warning"] = ""; }
if (!empty($_GET["col-max_days_before_lock"])){ $urlinfo["col-max_days_before_lock"] = $_GET["col-max_days_before_lock"]; }else{ $urlinfo["col-max_days_before_lock"] = ""; }
if (!empty($_GET["col-days"])){ $urlinfo["col-days"] = $_GET["col-days"]; }else{ $urlinfo["col-days"] = ""; }
if (!empty($_GET["col-warning"])){ $urlinfo["col-warning"] = $_GET["col-warning"]; }else{ $urlinfo["col-warning"] = ""; }
if (!empty($_GET["col-mail_1"])){ $urlinfo["col-mail_1"] = $_GET["col-mail_1"]; }else{ $urlinfo["col-mail_1"] = ""; }
if (!empty($_GET["col-mail_2"])){ $urlinfo["col-mail_2"] = $_GET["col-mail_2"]; }else{ $urlinfo["col-mail_2"] = ""; }
if (!empty($_GET["col-blocked"])){ $urlinfo["col-blocked"] = $_GET["col-blocked"]; }else{ $urlinfo["col-blocked"] = ""; }

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

// Required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles = db_read("articles", "id description", $articles_conditions);

// Required for selectbox: sizes
if (!empty($urlinfo["aid"])) {
    $sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
    $sizes_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["aid"];
    $sizes_conditions["where"]["2"] = "arsimos.deleted_on is null";
    $sizes_conditions["order_by"] = "sizes.position";
    $sizes_conditions["group_by"] = "arsimos.size_id";
    $sizes_data = db_read("arsimos", "arsimos.size_id sizes.name", $sizes_conditions);
    if (!empty($sizes_data)) {
        while ($row = db_fetch_num($sizes_data)) {
            $sizes[$row[0]] = $row[1];
        }   
    } else {
        $sizes = null;
    }
    
    if (!empty($urlinfo["sid"])) {
        $modifications_conditions["inner_join"]["1"] = "modifications modifications.id arsimos.modification_id";
        $modifications_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["aid"];
        $modifications_conditions["where"]["2"] = "arsimos.size_id = " . $urlinfo["sid"];
        $modifications_conditions["where"]["3"] = "arsimos.deleted_on is null";
        $modifications_conditions["order_by"] = "modifications.id";
        $modifications_conditions["group_by"] = "arsimos.modification_id";
        $modifications_data = db_read("arsimos", "arsimos.modification_id modifications.name", $modifications_conditions);
        if (!empty($modifications_data)) {
            while ($row = db_fetch_num($modifications_data)) {
                $modifications[$row[0]] = $row[1];
            }
        } else {
            $modifications = null;
        }
    } else {
        $modifications = null;
    } 
} else {
    $sizes = null;
    $modifications = null;
}

/**
 * Create view
 */

$query = "CREATE VIEW `". $table ."` AS (SELECT
               `gu`.`surname` AS 'garmentusers_surname',
               `gu`.`name` AS 'garmentusers_name',
               `gu`.`initials` AS 'garmentusers_initials',
               `gu`.`personnelcode` AS 'garmentusers_personnelcode',
               `gu`.`id` AS 'garmentusers_id',
               `gu`.`gender` AS 'garmentusers_gender',
               `gu`.`intermediate` AS 'garmentusers_intermediate',
               `gu`.`maidenname` AS 'garmentusers_maidenname',
               `gu`.`title` AS 'garmentusers_title',
               `gu`.`date_service_off` AS 'garmentusers_date_service_off',
               `ar`.`id` AS 'articles_id',
               `ar`.`description` AS 'articles_description',
               `s`.`id` AS 'sizes_id',
               `s`.`name` AS 'sizes_name',
               `s`.`position` AS 'sizes_position',
               `m`.`id` AS 'modifications_id',
               `m`.`name` AS 'modifications_name',
               `c`.`id` AS 'circulationgroups_id',
               `p`.`id` AS 'professions_id',
               `p`.`name` AS 'professions_name',
               `cl`.`id` AS 'clientdepartments_id',
               `cl`.`name` AS 'clientdepartments_name',
               `cp`.`id` AS 'costplaces_id',
               `cp`.`value` AS 'costplaces_value',
               `f`.`id` AS 'functions_id',
               `f`.`value` AS 'functions_value',
      COALESCE(`gu`.`daysbeforewarning`, `p`.`daysbeforewarning`, 0) AS 'max_days_before_warning',
      COALESCE(`gu`.`daysbeforelock`, `p`.`daysbeforelock`, 0) AS 'max_days_before_lock',
      TIMESTAMPDIFF(DAY, DATE(gg.date_received), DATE_SUB(NOW(), INTERVAL COALESCE(`gu`.`daysbeforewarning`, `p`.`daysbeforewarning`, 0) DAY))
            AS 'days_warning_active',
      TIMESTAMPDIFF(DAY, DATE(gg.date_received), NOW()) AS 'days',
                'y' AS 'warning',
   IF(TIMESTAMPDIFF(DAY, DATE(gg.date_received), NOW()) >= (SELECT value FROM settings WHERE name = 'mail_1_days'), 'y', 'n') AS 'mail_1',
   IF(TIMESTAMPDIFF(DAY, DATE(gg.date_received), NOW()) >= (SELECT value FROM settings WHERE name = 'mail_2_days'), 'y', 'n') AS 'mail_2',
   IF(TIMESTAMPDIFF(DAY, DATE(gg.date_received), NOW()) >= COALESCE(`gu`.`daysbeforewarning`, `p`.`daysbeforewarning`, 0) + COALESCE(`gu`.`daysbeforelock`, `p`.`daysbeforelock`, 0), 'y', 'n') AS 'blocked'
        FROM `garmentusers_garments` `gg`
    INNER JOIN `garmentusers` `gu` ON `gg`.`garmentuser_id` = `gu`.`id` AND ISNULL(`gu`.`deleted_on`)
    INNER JOIN `garments` `g` ON `g`.`id` = `gg`.`garment_id`
    INNER JOIN `arsimos` `a` ON `a`.`id` = `g`.`arsimo_id`
    INNER JOIN `articles` `ar` ON `ar`.`id` = `a`.`article_id`
    INNER JOIN `sizes` `s` ON `s`.`id` = `a`.`size_id`
     LEFT JOIN `modifications` `m` ON `m`.`id` = `a`.`modification_id`
     LEFT JOIN `circulationgroups_garmentusers` `cg` ON `cg`.`garmentuser_id` = `gu`.`id`
     LEFT JOIN `circulationgroups` `c` ON `c`.`id` = `cg`.`circulationgroup_id`
    INNER JOIN `professions` `p` ON `p`.`id` = `gu`.`profession_id`
     LEFT JOIN `clientdepartments` `cl` ON `cl`.`id` = `gu`.`clientdepartment_id`
     LEFT JOIN `costplaces` `cp` ON `cp`.`id` = `gu`.`costplace_id`
     LEFT JOIN `functions` `f` ON `f`.`id` = `gu`.`function_id`
         WHERE `gg`.`date_received` < DATE_SUB(NOW(), INTERVAL COALESCE(`gu`.`daysbeforewarning`, `p`.`daysbeforewarning`, 0) DAY)  AND `gg`.`superuser_id` = 0".
    $where
   ." GROUP BY `gu`.`id`
      ORDER BY `gu`.`surname`, `gu`.`name`)";

db_query("DROP VIEW IF EXISTS `". $table ."`");
db_query($query);

/**
 * Collect page content
 */
$columns = "garmentusers_surname garmentusers_name garmentusers_initials garmentusers_personnelcode garmentusers_id garmentusers_gender garmentusers_intermediate";
$columns .= " garmentusers_maidenname garmentusers_title garmentusers_date_service_off articles_id articles_description sizes_id sizes_name sizes_position modifications_id ";
$columns .= " modifications_name circulationgroups_id professions_id professions_name clientdepartments_id clientdepartments_name costplaces_id costplaces_value";
$columns .= " functions_id functions_value max_days_before_warning max_days_before_lock days_warning_active days warning mail_1 mail_2 blocked";

$urlinfo["search"] = geturl_search();
$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

if (!empty($urlinfo["aid"])) {
    $urlinfo["where"]["1"] = "articles_id = ". $urlinfo["aid"];
    if (!empty($urlinfo["sid"])) {
        $urlinfo["where"]["2"] = "sizes_id = ". $urlinfo["sid"];
        
        if (!empty($urlinfo["mid"])) {
            $urlinfo["where"]["3"] = "modifications_id = ". $urlinfo["mid"];
        }
    } 
}

if (isset($urlinfo["clientdepartment_id"]) && !empty($urlinfo["clientdepartment_id"])){
    $urlinfo["where"]["4"] = "clientdepartments_id = " . $urlinfo["clientdepartment_id"];
}

if (isset($urlinfo["pid"])) {
    $urlinfo["where"]["5"] = "professions_id = " . $urlinfo["pid"];
}

if (isset($urlinfo["costplace_id"]) && !empty($urlinfo["costplace_id"])) {
    $urlinfo["where"]["6"] = "costplaces_id = " . $urlinfo["costplace_id"];
}

if (isset($urlinfo["function_id"]) && !empty($urlinfo["function_id"])) {
    $urlinfo["where"]["7"] = "functions_id = " . $urlinfo["function_id"];
}

if (isset($urlinfo["status"]) && !empty($urlinfo["status"])) {
    if($urlinfo["status"] === "warning") {
        $urlinfo["where"]["8"] = "warning = y";
        $urlinfo["where"]["9"] = "mail_1 = n";
        $urlinfo["where"]["10"] = "mail_2 = n";
        $urlinfo["where"]["11"] = "blocked = n";
    } elseif($urlinfo["status"] === "mail_1") {
        $urlinfo["where"]["8"] = "warning = y";
        $urlinfo["where"]["9"] = "mail_1 = y";
        $urlinfo["where"]["10"] = "mail_2 = n";
        $urlinfo["where"]["11"] = "blocked = n";
    } elseif($urlinfo["status"] === "mail_2") {
        $urlinfo["where"]["8"] = "warning = y";
        $urlinfo["where"]["9"] = "mail_1 = y";
        $urlinfo["where"]["10"] = "mail_2 = y";
        $urlinfo["where"]["11"] = "blocked = n";
    } elseif($urlinfo["status"] === "blocked") {
        $urlinfo["where"]["8"] = "warning = y";
        $urlinfo["where"]["9"] = "mail_1 = y";
        $urlinfo["where"]["10"] = "mail_2 = y";
        $urlinfo["where"]["11"] = "blocked = y";
    }
}

if ($hassubmit == "export") {
    $urlinfo["limit_start"] = 0;
    $urlinfo["limit_num"] = "65535";
} else {
    $urlinfo["limit_start"] = geturl_limit_start();
    $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
}

$urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
$urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string

$listdata = db_read($table, $columns, $urlinfo);

$resultinfo = result_infoline($pi, $urlinfo);

$sortlinks["surname"] = generate_sortlink("garmentusers_surname", $lang["surname"], $pi, $urlinfo);
$sortlinks["name"] = generate_sortlink("garmentusers_name", $lang["first_name"], $pi, $urlinfo);
$sortlinks["personnelcode"] = generate_sortlink("garmentusers_personnelcode", $lang["personnelcode"], $pi, $urlinfo);
$sortlinks["clientdepartment"] = generate_sortlink("clientdepartments_name", $lang["clientdepartment"], $pi, $urlinfo);
$sortlinks["profession"] = generate_sortlink("professions_name", $lang["profession"], $pi, $urlinfo);
$sortlinks["costplace"] = generate_sortlink("costplaces_value", $lang["costplace"], $pi, $urlinfo);
$sortlinks["function"] = generate_sortlink("functions_value", $lang["function"], $pi, $urlinfo);
$sortlinks["service_off"] = generate_sortlink("garmentusers_date_service_off", $lang["service_off"], $pi, $urlinfo);
$sortlinks["article"] = generate_sortlink("articles_description", $lang["article"], $pi, $urlinfo);
$sortlinks["size"] = generate_sortlink("sizes_position", $lang["size"], $pi, $urlinfo);
$sortlinks["modification"] = generate_sortlink("modifications_name", $lang["modification"], $pi, $urlinfo);
$sortlinks["days"] = generate_sortlink("days", $lang["taken_out"], $pi, $urlinfo);
$sortlinks["warning"] = generate_sortlink("warning", $lang["warning"], $pi, $urlinfo);
$sortlinks["mail_1"] = generate_sortlink("mail_1", $lang["mail_1"], $pi, $urlinfo);
$sortlinks["mail_2"] = generate_sortlink("mail_2", $lang["mail_2"], $pi, $urlinfo);
$sortlinks["blocked"] = generate_sortlink("blocked", $lang["blocked"], $pi, $urlinfo);

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
    if(!empty($urlinfo["col-clientdepartment"]))$header.=$lang["clientdepartment"]."\t";
    if(!empty($urlinfo["col-profession"]))$header.=$lang["profession"]."\t";
    if(!empty($urlinfo["col-costplace"]))$header.=$lang["costplace"]."\t";
    if(!empty($urlinfo["col-function"]))$header.=$lang["function"]."\t";
    if(!empty($urlinfo["col-service_off"]))$header.=$lang["service_off"]."\t";
    if(!empty($urlinfo["col-article"]))$header.=$lang["article"]."\t";
    if(!empty($urlinfo["col-size"]))$header.=$lang["size"]."\t";
    if(!empty($urlinfo["col-modification"]))$header.=$lang["modification"]."\t";
    if(!empty($urlinfo["col-days"]))$header.=$lang["taken_out"]."\t";
    if(!empty($urlinfo["col-warning"]))$header.=$lang["warning"]."\t";
    if(!empty($urlinfo["col-mail_1"]))$header.=$lang["mail_1"]."\t";
    if(!empty($urlinfo["col-mail_2"]))$header.=$lang["mail_2"]."\t";
    if(!empty($urlinfo["col-blocked"]))$header.=$lang["blocked"]."\t";
    
    $data = "";    
    while(!empty($listdata) && $row = db_fetch_array($listdata)) {
        $line = "";
        $in = array();
        if(!empty($urlinfo["col-surname"]))array_push($in, generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"], $row["garmentusers_personnelcode"]));
        if(!empty($urlinfo["col-name"]))array_push($in, $row["garmentusers_name"]);
        if(!empty($urlinfo["col-personnelcode"]))array_push($in, $row["garmentusers_personnelcode"]);
        if(!empty($urlinfo["col-clientdepartment"]))array_push($in, $row["clientdepartments_name"]);
        if(!empty($urlinfo["col-profession"]))array_push($in, $row["professions_name"]);
        if(!empty($urlinfo["col-lockernumber"]))array_push($in, $row["garmentusers_lockernumber"]);
        if(!empty($urlinfo["col-costplace"]))array_push($in, $row["costplaces_value"]);
        if(!empty($urlinfo["col-function"]))array_push($in, $row["functions_value"]);
        if(!empty($urlinfo["col-service_off"]))array_push($in, $row["garmentusers_date_service_off"]);
        if(!empty($urlinfo["col-article"]))array_push($in, $row["articles_description"]);
        if(!empty($urlinfo["col-size"]))array_push($in, $row["sizes_name"]);
        if(!empty($urlinfo["col-modification"]))array_push($in, $row["modifications_name"]);
        if(!empty($urlinfo["col-days"]))array_push($in, $row["days"]);
        if(!empty($urlinfo["col-warning"]))array_push($in, $row["warning"]);
        if(!empty($urlinfo["col-mail_1"]))array_push($in, $row["mail_1"]);
        if(!empty($urlinfo["col-mail_2"]))array_push($in, $row["mail_2"]);
        if(!empty($urlinfo["col-blocked"]))array_push($in, $row["blocked"]);

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
    "sortlinks" => $sortlinks,
    "pagination" => $pagination,
    "listdata" => $listdata,
    "hassubmit" => $hassubmit,
    "resultinfo" => $resultinfo,
    "sizes" => $sizes,
    "modifications" => $modifications,
    "articles" => $articles,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "clientdepartments_count" => $clientdepartments_count,
    "clientdepartments" => $clientdepartments,
    "costplaces" => $costplaces,
    "costplaces_count" => $costplaces_count,
    "functions" => $functions,
    "functions_count" => $functions_count,
    "professions" => $professions,
    "statuses" => $statuses
);
    
template_parse($pi, $urlinfo, $cv);

?>
