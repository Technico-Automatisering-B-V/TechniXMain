<?php

/**
 * Report user modifications
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
$pi["access"] = array("reports", "garmentuser_modifications");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["garmentuser_modifications"];
$pi["filename_list"] = "report_user_modifications.php";
$pi["filename_details"] = "garmentuser_details.php";
$pi["template"] = "layout/pages/report_user_modifications.tpl";
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

$urlinfo = array();

// Required for selectbox for 'yes' and 'no'
$yes_no = array(
    "yes" => $lang["yes"],
    "no" => $lang["no"]
);
/**
 * Get ids
 */

if (!empty($_GET["from_date"])){ $urlinfo["from_date"] = trim($_GET["from_date"]); }else{ $urlinfo["from_date"] = date("Y-m-d"); }
if (!empty($_GET["to_date"])){ $urlinfo["to_date"] = trim($_GET["to_date"]); }else{ $urlinfo["to_date"] = ""; }
if (!empty($_GET["function_changed"])){ $urlinfo["function_changed"] = $_GET["function_changed"]; }else{ $urlinfo["function_changed"] = "no"; }
if (!empty($_GET["clientdepartment_changed"])){ $urlinfo["clientdepartment_changed"] = $_GET["clientdepartment_changed"]; }else{ $urlinfo["clientdepartment_changed"] = "no"; }
if (!empty($_GET["date_service_on_today"])){ $urlinfo["date_service_on_today"] = $_GET["date_service_on_today"]; }else{ $urlinfo["date_service_on_today"] = "no"; }
if (!empty($_GET["date_service_off_today"])){ $urlinfo["date_service_off_today"] = $_GET["date_service_off_today"]; }else{ $urlinfo["date_service_off_today"] = "no"; }
if (!empty($_GET["lotsadays"])){ $urlinfo["lotsadays"] = $_GET["lotsadays"]; }else{ $urlinfo["lotsadays"] = ""; }

if (!empty($_GET["col-name"])){ $urlinfo["col-name"] = $_GET["col-name"]; }else{ $urlinfo["col-name"] = ""; }
if (!empty($_GET["col-surname"])){ $urlinfo["col-surname"] = $_GET["col-surname"]; }else{ $urlinfo["col-surname"] = ""; }
if (!empty($_GET["col-personnelcode"])){ $urlinfo["col-personnelcode"] = $_GET["col-personnelcode"]; }else{ $urlinfo["col-personnelcode"] = ""; }
if (!empty($_GET["col-code"])){ $urlinfo["col-code"] = $_GET["col-code"]; }else{ $urlinfo["col-code"] = ""; }
if (!empty($_GET["col-date_service_off"])){ $urlinfo["col-date_service_off"] = $_GET["col-date_service_off"]; }else{ $urlinfo["col-date_service_off"] = ""; }
if (!empty($_GET["col-date_service_on"])){ $urlinfo["col-date_service_on"] = $_GET["col-date_service_on"]; }else{ $urlinfo["col-date_service_on"] = ""; }
if (!empty($_GET["col-old_function"])){ $urlinfo["col-old_function"] = $_GET["col-old_function"]; }else{ $urlinfo["col-old_function"] = ""; }
if (!empty($_GET["col-old_clientdepartment"])){ $urlinfo["col-old_clientdepartment"] = $_GET["col-old_clientdepartment"]; }else{ $urlinfo["col-old_clientdepartment"] = ""; }
if (!empty($_GET["col-function"])){ $urlinfo["col-function"] = $_GET["col-function"]; }else{ $urlinfo["col-function"] = ""; }
if (!empty($_GET["col-clientdepartment"])){ $urlinfo["col-clientdepartment"] = $_GET["col-clientdepartment"]; }else{ $urlinfo["col-clientdepartment"] = ""; }
if (!empty($_GET["col-profession"])){ $urlinfo["col-profession"] = $_GET["col-profession"]; }else{ $urlinfo["col-profession"] = ""; }
if (!empty($_GET["col-garments_in_use"])){ $urlinfo["col-garments_in_use"] = $_GET["col-garments_in_use"]; }else{ $urlinfo["col-garments_in_use"] = ""; }
if (!empty($_GET["col-clothing"])){ $urlinfo["col-clothing"] = $_GET["col-clothing"]; }else{ $urlinfo["col-clothing"] = ""; }
if (!empty($_GET["col-date"])){ $urlinfo["col-date"] = $_GET["col-date"]; }else{ $urlinfo["col-date"] = ""; }

if (empty($pi["note"])) {
    /**
     * Collect page content
     */
       
    $table = "tmp_user_modifications";
    
    if (!empty($urlinfo["lotsadays"]) && empty($urlinfo["to_date"])){ $urlinfo["to_date"] = $urlinfo["from_date"]; }

	if (!empty($urlinfo["from_date"]) && !empty($urlinfo["to_date"])) {
		if ($urlinfo["to_date"] < $urlinfo["from_date"]) {
			$pi["note"] = html_error($lang["error_date_from_greater_then_to"]);
		}
	}
	
	if (isset($_GET["lotsadays"])) {
        $sql_where_date = "BETWEEN '" . $urlinfo["from_date"] . "' AND '" . $urlinfo["to_date"] ."'";
    } else {
        $sql_where_date = "= '" . $urlinfo["from_date"] . "'";
    }
        
    $sql_where = " h.date ". $sql_where_date;
    $sql_where_first = true;
    
    if ($urlinfo["function_changed"] == "yes"
            || $urlinfo["clientdepartment_changed"] == "yes"
            || $urlinfo["date_service_on_today"] == "yes"
            || $urlinfo["date_service_off_today"] == "yes") {
    
        $sql_where .= " AND ( ";
        
        if ($urlinfo["function_changed"] == "yes") {
            $sql_where .= " h.`function` != h2.`function`
                OR (!ISNULL(h.`function`) AND ISNULL(h2.`function`)) ";
            $sql_where_first = false;
        }   
        
        if ($urlinfo["clientdepartment_changed"] == "yes") {
            if($sql_where_first) {
                $sql_where .= " h.clientdepartment != h2.clientdepartment 
                    OR (!ISNULL(h.clientdepartment) AND ISNULL(h2.clientdepartment)) ";
                $sql_where_first = false;
            } else {
                $sql_where .= " OR h.clientdepartment != h2.clientdepartment 
                    OR (!ISNULL(h.clientdepartment) AND ISNULL(h2.clientdepartment)) ";
            }
        }  
        
        if ($urlinfo["date_service_on_today"] == "yes") {
            if($sql_where_first) {
                $sql_where .= " h.garmentusers_date_service_on = h.date ";
                $sql_where_first = false;
            } else {
                $sql_where .= " OR h.garmentusers_date_service_on = h.date ";
            }
        } 
        
        if ($urlinfo["date_service_off_today"] == "yes") {
            if($sql_where_first) {
                $sql_where .= " h.garmentusers_date_service_off = h.date ";
                $sql_where_first = false;
            } else {
                $sql_where .= " OR h.garmentusers_date_service_off = h.date ";
            }
        }
        
        $sql_where .= " )";
    } else {
        $sql_where .= " AND 0";
    }
    
    if ($urlinfo["date"] < date("Y-m-d")) {
        $sql = "
        SELECT h.garmentuser_id, h.garmentusers_name, h.garmentusers_surname,h.garmentusers_personnelcode,
            h.garmentusers_code, h.garmentusers_date_service_on, h.garmentusers_date_service_off,
            h2.function AS 'old_function', h2.clientdepartment AS 'old_clientdepartment', h.`function`, h.clientdepartment,h.profession,h.garments_in_use,h.clothing, h.date
            FROM history_garmentusers h
            INNER JOIN history_garmentusers h2 ON h2.garmentuser_id = h.garmentuser_id AND h2.date = (h.date - INTERVAL 1 DAY) 
            WHERE " . $sql_where; 
    } else {
        $sql = "
        SELECT h.* FROM (
            SELECT gu.id garmentuser_id, gu.`name` garmentusers_name, gu.surname garmentusers_surname, gu.personnelcode garmentusers_personnelcode,
            gu.`code` garmentusers_code, gu.date_service_on garmentusers_date_service_on, gu.date_service_off garmentusers_date_service_off,
            h2.function AS 'old_function', h2.clientdepartment AS 'old_clientdepartment', f.`value` function, c.`name` clientdepartment ,p.name profession, gg.garments_in_use, IF(ISNULL(g.id),'no','yes') clothing, DATE(NOW()) date
            FROM garmentusers gu
            LEFT JOIN functions f ON f.id = gu.function_id
            LEFT JOIN clientdepartments c ON c.id = gu.clientdepartment_id
            LEFT JOIN professions p ON p.id = gu.profession_id
            LEFT JOIN (SELECT garmentuser_id, COUNT(*) garments_in_use FROM garmentusers_garments GROUP BY garmentuser_id) gg ON gg.garmentuser_id = gu.id
            LEFT JOIN garments g ON g.garmentuser_id = gu.id
            WHERE ISNULL(gu.deleted_on)
            GROUP BY gu.id) h
            INNER JOIN history_garmentusers h2 ON h2.garmentuser_id = h.garmentuser_id AND h2.date = (DATE(NOW()) - INTERVAL 1 DAY)
            WHERE " . $sql_where; 
    }
    
    $query = "CREATE TABLE `". $table ."` AS (" . $sql . ")";
    
    db_query("DROP TABLE IF EXISTS `". $table ."`");
    db_query($query);

    /**
     * Collect page content
     */
    $columns = "garmentuser_id garmentusers_name garmentusers_surname garmentusers_personnelcode "
             . " garmentusers_code garmentusers_date_service_on garmentusers_date_service_off "
             . " old_function old_clientdepartment function clientdepartment profession garments_in_use clothing date";

    $urlinfo["search"] = geturl_search();
    $urlinfo["order_by"] = geturl_order_by($columns);
    $urlinfo["order_direction"] = geturl_order_direction();

    if ($hassubmit == "export") {
        $urlinfo["limit_start"] = 0;
        $urlinfo["limit_num"] = "65535";
    } else {
        $urlinfo["limit_start"] = geturl_limit_start();
        $urlinfo["limit_num"] = geturl_limit_num($config["list_rows_per_page"]);
    }

    $urlinfo["limit_total"] = db_fetch_row(db_count($table, $columns, $urlinfo));
    $urlinfo["limit_total"] = $urlinfo["limit_total"]["0"]; //array->string

    $listdata = db_read($table, $columns, $urlinfo);

    $resultinfo = result_infoline($pi, $urlinfo);

    $sortlinks["surname"] = generate_sortlink("garmentusers_surname", $lang["surname"], $pi, $urlinfo);
    $sortlinks["name"] = generate_sortlink("garmentusers_name", $lang["first_name"], $pi, $urlinfo);
    $sortlinks["personnelcode"] = generate_sortlink("garmentusers_personnelcode", $lang["personnelcode"], $pi, $urlinfo);
    $sortlinks["code"] = generate_sortlink("garmentusers_code", $lang["passcode"], $pi, $urlinfo);
    $sortlinks["date_service_on"] = generate_sortlink("garmentusers_date_service_on", $lang["service_on"], $pi, $urlinfo);
    $sortlinks["date_service_off"] = generate_sortlink("garmentusers_date_service_off", $lang["service_off"], $pi, $urlinfo);
    $sortlinks["old_function"] = generate_sortlink("old_function", $lang["old_function"], $pi, $urlinfo);
    $sortlinks["old_clientdepartment"] = generate_sortlink("old_clientdepartment", $lang["old_clientdepartment"], $pi, $urlinfo);
    $sortlinks["function"] = generate_sortlink("function", $lang["new_function"], $pi, $urlinfo);
    $sortlinks["clientdepartment"] = generate_sortlink("clientdepartment", $lang["new_clientdepartment"], $pi, $urlinfo);
    $sortlinks["profession"] = generate_sortlink("profession", $lang["profession"], $pi, $urlinfo);
    $sortlinks["garments_in_use"] = generate_sortlink("garments_in_use", $lang["in_possession"], $pi, $urlinfo);
    $sortlinks["clothing"] = generate_sortlink("clothing", $lang["clothing"], $pi, $urlinfo);
    $sortlinks["date"] = generate_sortlink("date", $lang["date"], $pi, $urlinfo);
    
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
        if(!empty($urlinfo["col-date_service_on"]))$header.=$lang["date_service_on"]."\t";
        if(!empty($urlinfo["col-date_service_off"]))$header.=$lang["date_service_off"]."\t";
        if(!empty($urlinfo["col-old_function"]))$header.=$lang["old_function"]."\t";
        if(!empty($urlinfo["col-function"]))$header.=$lang["new_function"]."\t";
        if(!empty($urlinfo["col-old_clientdepartment"]))$header.=$lang["old_clientdepartment"]."\t";
        if(!empty($urlinfo["col-clientdepartment"]))$header.=$lang["new_clientdepartment"]."\t";
        if(!empty($urlinfo["col-profession"]))$header.=$lang["profession"]."\t";
        if(!empty($urlinfo["col-garments_in_use"]))$header.=$lang["in_possession"]."\t";
        if(!empty($urlinfo["col-clothing"]))$header.=$lang["clothing"]."\t";
        if(!empty($urlinfo["col-date"]))$header.=$lang["date"]."\t";

        $data = "";    
        while(!empty($listdata) && $row = db_fetch_array($listdata)) {
            $line = "";
            $in = array();
            if(!empty($urlinfo["col-surname"]))array_push($in, $row["garmentusers_surname"]);
            if(!empty($urlinfo["col-name"]))array_push($in, $row["garmentusers_name"]);
            if(!empty($urlinfo["col-personnelcode"]))array_push($in, $row["garmentusers_personnelcode"]);
            if(!empty($urlinfo["col-code"]))array_push($in, $row["garmentusers_code"]);
            if(!empty($urlinfo["col-date_service_on"]))array_push($in, $row["garmentusers_date_service_on"]);
            if(!empty($urlinfo["col-date_service_off"]))array_push($in, $row["garmentusers_date_service_off"]);
            if(!empty($urlinfo["col-old_function"]))array_push($in, $row["old_function"]);
            if(!empty($urlinfo["col-function"]))array_push($in, $row["function"]);
            if(!empty($urlinfo["col-old_clientdepartment"]))array_push($in, $row["old_clientdepartment"]);
            if(!empty($urlinfo["col-clientdepartment"]))array_push($in, $row["clientdepartment"]);
            if(!empty($urlinfo["col-profession"]))array_push($in, $row["profession"]);
            if(!empty($urlinfo["col-garments_in_use"]))array_push($in, $row["garments_in_use"]);
            if(!empty($urlinfo["col-clothing"]))array_push($in, ($row["clothing"]=="ÿes"?$lang["yes"]:$lang["no"]));
            if(!empty($urlinfo["col-date"]))array_push($in, $row["date"]);
            
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
	"lotsadays" => ($urlinfo["lotsadays"] == true) ? "checked=\"checked\"" : "",
    "sortlinks" => $sortlinks,
    "pagination" => $pagination,
    "listdata" => $listdata,
    "hassubmit" => $hassubmit,
    "resultinfo" => $resultinfo,
    "yes_no" => $yes_no,
);
    
template_parse($pi, $urlinfo, $cv);

?>
