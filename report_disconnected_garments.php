<?php

/**
 * Report disconnected garments
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
$pi["access"] = array("reports", "disconnected_garments");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["disconnected_garments"];
$pi["filename_list"] = "report_disconnected_garments.php";
$pi["filename_details"] = "garmentuser_details.php";
$pi["template"] = "layout/pages/report_disconnected_garments.tpl";
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

$urlinfo = array(
    "from_date" => (!empty($_GET["from_date"])) ? trim($_GET["from_date"]) : date("Y-m-d"),
    "to_date" => (!empty($_GET["to_date"])) ? trim($_GET["to_date"]) : "",
    "lotsadays" => (!empty($_GET["lotsadays"])) ? trim($_GET["lotsadays"]) : ""
);

/**
 * Get ids
 */
if (!empty($urlinfo["lotsadays"]) && empty($urlinfo["to_date"])){ $urlinfo["to_date"] = $urlinfo["from_date"]; }
if (!empty($_GET["cid"])) {$urlinfo["cid"] = trim($_GET["cid"]);} else {$urlinfo["cid"] = null;}
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
if (!empty($_GET["col-lockernumber"])){ $urlinfo["col-lockernumber"] = $_GET["col-lockernumber"]; }else{ $urlinfo["col-lockernumber"] = ""; }
if (!empty($_GET["col-costplace"])){ $urlinfo["col-costplace"] = $_GET["col-costplace"]; }else{ $urlinfo["col-costplace"] = ""; }
if (!empty($_GET["col-function"])){ $urlinfo["col-function"] = $_GET["col-function"]; }else{ $urlinfo["col-function"] = ""; }
if (!empty($_GET["col-tag"])){ $urlinfo["col-tag"] = $_GET["col-tag"]; }else{ $urlinfo["col-tag"] = ""; }
if (!empty($_GET["col-article"])){ $urlinfo["col-article"] = $_GET["col-article"]; }else{ $urlinfo["col-article"] = ""; }
if (!empty($_GET["col-size"])){ $urlinfo["col-size"] = $_GET["col-size"]; }else{ $urlinfo["col-size"] = ""; }
if (!empty($_GET["col-modification"])){ $urlinfo["col-modification"] = $_GET["col-modification"]; }else{ $urlinfo["col-modification"] = ""; }
if (!empty($_GET["col-comments"])){ $urlinfo["col-comments"] = $_GET["col-comments"]; }else{ $urlinfo["col-comments"] = ""; }
if (!empty($_GET["col-date"])){ $urlinfo["col-date"] = $_GET["col-date"]; }else{ $urlinfo["col-date"] = ""; }
if (!empty($_GET["col-status"])){ $urlinfo["col-status"] = $_GET["col-status"]; }else{ $urlinfo["col-status"] = ""; }
if (!empty($_GET["col-next_status"])){ $urlinfo["col-next_status"] = $_GET["col-next_status"]; }else{ $urlinfo["col-next_status"] = ""; }

if (!empty($urlinfo["from_date"]) && !empty($urlinfo["to_date"])) {
    if ($urlinfo["to_date"] < $urlinfo["from_date"]) {
        $pi["note"] = html_error($lang["error_date_from_greater_then_to"]);
    }
}

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

/** Required for selectbox: Status **/
$statuses["active"] = $lang["active"];
$statuses["inactive"] = $lang["inactive"];
    
if (empty($pi["note"])) {
    /**
     * Collect page content
     */
    
    $sql_date_help = "";
    
    if (!empty($urlinfo["lotsadays"])) {
            $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
            $to_date_db = str_replace("-", "", $urlinfo["to_date"]) ."235959";

            $sql_date_help = " l.date >= '". $from_date_db ."' ";
            $sql_date_help .= " AND l.date <= '". $to_date_db ."' ";
        } else {
            $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
            $to_date_db = str_replace("-", "", $urlinfo["from_date"]) ."235959";

            $sql_date_help = " l.date >= '". $from_date_db ."' ";
            $sql_date_help .= " AND l.date <= '". $to_date_db ."' ";
        }
    
    
    $sql = db_query("SELECT `scanlocations`.`id` FROM `scanlocationstatuses` INNER JOIN `scanlocations` ON `scanlocations`.`scanlocationstatus_id` = `scanlocationstatuses`.`id` AND `scanlocationstatuses`.`name` = 'disconnected_from_garmentuser'");
    $scanresult = db_fetch_row($sql);
    $disc_scanlocation = $scanresult[0];

    $tmp_log_disc = "tmp_log_disc";
    $query1 = "CREATE VIEW `". $tmp_log_disc ."` AS (SELECT
            MAX(`l`.`id`) AS 'id', `g`.`id` AS 'garment_id' FROM `log_disconnected_garments` `l`
            INNER JOIN `garments` `g` ON `g`.`id` = `l`.`garment_id`
            WHERE `g`.`scanlocation_id` = ". $disc_scanlocation ." AND ". $sql_date_help ." 
            GROUP BY `g`.`id`)";

    db_query("DROP VIEW IF EXISTS `". $tmp_log_disc ."`");
    db_query($query1);

    $tmp_last_disc = "tmp_last_disc";
    $query2 = "CREATE VIEW `". $tmp_last_disc ."` AS (SELECT DISTINCT MAX(`l`.`id`) AS 'last_id' FROM `log_disconnected_garments` `l` WHERE ". $sql_date_help ." GROUP BY `l`.`garment_id`)";

    db_query("DROP VIEW IF EXISTS `". $tmp_last_disc ."`");
    db_query($query2);

    $tmp_next_disc = "tmp_next_disc";
    $query3 = "CREATE VIEW `". $tmp_next_disc ."` AS (SELECT `l`.`id` AS 'id',IF(ISNULL(`ss`.`id`),`s`.`translate`,`ss`.`translate`) AS 'next_status'
            FROM `tmp_last_disc` `LAST_MUT`
            INNER JOIN `log_disconnected_garments` `l` ON LAST_MUT.`last_id` = `l`.`id`
            INNER JOIN `technix_log`.`log_garments_scanlocations` `lg` ON `lg`.`garment_id` = `l`.`garment_id` AND `lg`.`date` > `l`.`date` AND `lg`.`scanlocation_id` != ". $disc_scanlocation ." 
            INNER JOIN `scanlocations` `s` ON `s`.`id` = `lg`.`scanlocation_id`
             LEFT JOIN `sub_scanlocations` `ss` ON `ss`.`id` = `lg`.`sub_scanlocation_id`
             WHERE ". $sql_date_help ."
              GROUP BY `l`.`id`)";

    db_query("DROP VIEW IF EXISTS `". $tmp_next_disc ."`");
    db_query($query3);

    $table = "log_disconnected_garments";
    $columns = "log_disconnected_garments.date garmentusers.surname garmentusers.name garmentusers.initials garmentusers.personnelcode garmentusers.id garmentusers.gender garmentusers.intermediate";
    $columns .= " garmentusers.maidenname log_disconnected_garments.comments garmentusers.title garmentusers.lockernumber articles.description sizes.name sizes.position modifications.name garments.id";
    $columns .= " circulationgroups.id professions.id professions.name clientdepartments.id clientdepartments.name costplaces.id costplaces.value functions.id functions.value garments.lastscan garments.tag garments.tag2 garments.scanlocation_id tmp_log_disc.id tmp_next_disc.next_status tmp_next_disc.id";

    $urlinfo["inner_join"]["1"] = "garments garments.id log_disconnected_garments.garment_id";
    $urlinfo["inner_join"]["2"] = "garmentusers garmentusers.id log_disconnected_garments.garmentuser_id";
    $urlinfo["inner_join"]["3"] = "arsimos arsimos.id garments.arsimo_id";
    $urlinfo["inner_join"]["4"] = "articles articles.id arsimos.article_id";
    $urlinfo["inner_join"]["5"] = "sizes sizes.id arsimos.size_id";
    $urlinfo["left_join"]["1"] = "modifications modifications.id arsimos.modification_id";
    $urlinfo["left_join"]["2"] = "circulationgroups circulationgroups.id garments.circulationgroup_id";
    $urlinfo["left_join"]["3"] = "professions professions.id garmentusers.profession_id";
    $urlinfo["left_join"]["4"] = "clientdepartments clientdepartments.id garmentusers.clientdepartment_id";
    $urlinfo["left_join"]["5"] = "costplaces costplaces.id garmentusers.costplace_id";
    $urlinfo["left_join"]["6"] = "functions functions.id garmentusers.function_id";
    $urlinfo["left_join"]["7"] = "tmp_log_disc tmp_log_disc.id log_disconnected_garments.id";
    $urlinfo["left_join"]["8"] = "tmp_next_disc tmp_next_disc.id log_disconnected_garments.id";

    $urlinfo["search"] = geturl_search();

    $urlinfo["where"]["1"] = "garmentusers.deleted_on is null";

    if (!empty($urlinfo["aid"])) {
        $urlinfo["where"]["2"] = "articles.id = ". $urlinfo["aid"];
        if (!empty($urlinfo["sid"])) {
            $urlinfo["where"]["3"] = "sizes.id = ". $urlinfo["sid"];

            if (!empty($urlinfo["mid"])) {
                $urlinfo["where"]["4"] = "modifications.id = ". $urlinfo["mid"];
            }
        } 
    }

    if (isset($urlinfo["cid"])){
        $urlinfo["where"]["5"] = "circulationgroups.id = " . $urlinfo["cid"];
    }

    if (isset($urlinfo["clientdepartment_id"]) && !empty($urlinfo["clientdepartment_id"])){
        $urlinfo["where"]["6"] = "clientdepartments.id = " . $urlinfo["clientdepartment_id"];
    }

    if (isset($urlinfo["pid"])) {
        $urlinfo["where"]["7"] = "professions.id = " . $urlinfo["pid"];
    }

    if (isset($urlinfo["costplace_id"]) && !empty($urlinfo["costplace_id"])) {
        $urlinfo["where"]["8"] = "costplaces.id = " . $urlinfo["costplace_id"];
    }

    if (isset($urlinfo["function_id"]) && !empty($urlinfo["function_id"])) {
        $urlinfo["where"]["9"] = "functions.id = " . $urlinfo["function_id"];
    }
    
    if (!isset($urlinfo["where"]["0"])) {
        $urlinfo["where"]["0"] = "log_disconnected_garments.date isnot NULL";
        if (!empty($urlinfo["lotsadays"])) {
            $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
            $to_date_db = str_replace("-", "", $urlinfo["to_date"]) ."235959";

            $urlinfo["where"]["10"] = "log_disconnected_garments.date >= ". $from_date_db;
            $urlinfo["where"]["11"] = "log_disconnected_garments.date <= ". $to_date_db;
        } else {
            $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
            $to_date_db = str_replace("-", "", $urlinfo["from_date"]) ."235959";

            $urlinfo["where"]["10"] = "log_disconnected_garments.date >= ". $from_date_db;
            $urlinfo["where"]["11"] = "log_disconnected_garments.date <= ". $to_date_db;
        }
    }

    $urlinfo["order_by"] = geturl_order_by($columns);
    $urlinfo["order_direction"] = geturl_order_direction('DESC');

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
    $sortlinks["lockernumber"] = generate_sortlink("garmentusers_lockernumber", $lang["lockernumber"], $pi, $urlinfo);
    $sortlinks["costplace"] = generate_sortlink("costplaces_value", $lang["costplace"], $pi, $urlinfo);
    $sortlinks["function"] = generate_sortlink("functions_value", $lang["function"], $pi, $urlinfo);
    $sortlinks["tag"] = generate_sortlink("garments_tag", $lang["tag"], $pi, $urlinfo);
    $sortlinks["article"] = generate_sortlink("articles_description", $lang["article"], $pi, $urlinfo);
    $sortlinks["size"] = generate_sortlink("sizes_position", $lang["size"], $pi, $urlinfo);
    $sortlinks["modification"] = generate_sortlink("modifications_name", $lang["modification"], $pi, $urlinfo);
    $sortlinks["comments"] = generate_sortlink("log_disconnected_garments_comments", $lang["comments"], $pi, $urlinfo);
    $sortlinks["date"] = generate_sortlink("log_disconnected_garments_date", $lang["date"], $pi, $urlinfo);
    $sortlinks["status"] = generate_sortlink("tmp_log_disc_id", $lang["status"], $pi, $urlinfo);
    $sortlinks["next_status"] = generate_sortlink("tmp_next_disc_next_status", $lang["next_status"], $pi, $urlinfo);


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
        if(!empty($urlinfo["col-lockernumber"]))$header.=$lang["lockernumber"]."\t";
        if(!empty($urlinfo["col-costplace"]))$header.=$lang["costplace"]."\t";
        if(!empty($urlinfo["col-function"]))$header.=$lang["function"]."\t";
        if(!empty($urlinfo["col-tag"]))$header.=$lang["tag"]."\t";
        if(!empty($urlinfo["col-article"]))$header.=$lang["article"]."\t";
        if(!empty($urlinfo["col-size"]))$header.=$lang["size"]."\t";
        if(!empty($urlinfo["col-modification"]))$header.=$lang["modification"]."\t";
        if(!empty($urlinfo["col-comments"]))$header.=$lang["comments"]."\t";
        if(!empty($urlinfo["col-date"]))$header.=$lang["date"]."\t";
        if(!empty($urlinfo["col-status"]))$header.=$lang["status"]."\t";
        if(!empty($urlinfo["col-next_status"]))$header.=$lang["next_status"]."\t";

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
            if(!empty($urlinfo["col-tag"]))array_push($in,"'".$row["garments_tag"]);
            if(!empty($urlinfo["col-article"]))array_push($in, $row["articles_description"]);
            if(!empty($urlinfo["col-size"]))array_push($in, $row["sizes_name"]);
            if(!empty($urlinfo["col-modification"]))array_push($in, $row["modifications_name"]);
            if(!empty($urlinfo["col-comments"]))array_push($in, $row["log_disconnected_garments_comments"]);
            if(!empty($urlinfo["col-date"]))array_push($in, $row["log_disconnected_garments_date"]);
            if(!empty($urlinfo["col-status"]))array_push($in, (!empty($row["tmp_log_disc_id"]))?$lang["active"]:$lang["inactive"]);
            if(!empty($urlinfo["col-next_status"]))array_push($in, (!empty($row["tmp_next_disc_next_status"]))?$lang[$row["tmp_next_disc_next_status"]]:$lang["none"]);

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
    "lotsadays" => ($urlinfo["lotsadays"] == true) ? "checked=\"checked\"" : "",
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "clientdepartments_count" => $clientdepartments_count,
    "clientdepartments" => $clientdepartments,
    "costplaces" => $costplaces,
    "costplaces_count" => $costplaces_count,
    "functions" => $functions,
    "functions_count" => $functions_count,
    "professions" => $professions,
    "disc_scanlocation" => $disc_scanlocation
);
    
template_parse($pi, $urlinfo, $cv);

?>
