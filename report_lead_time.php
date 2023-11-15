<?php

/**
 * Report lead time
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
$pi["access"] = array("circulation_management", "lead_time");
$pi["group"] = $lang["circulation_management"];
$pi["title"] = $lang["lead_time"];
$pi["template"] = "layout/pages/report_lead_time.tpl";
$pi["page"] = "list";
$pi["filename_this"] = "report_lead_time.php";
$pi["filename_list"] = "report_lead_time.php";
$pi["toolbar"]["no_new"] = "yes";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["view"]){ $hassubmit = "show"; }
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
    if ($_GET["hassubmit"] == $lang["print"]){ $hassubmit = "print"; }
} else {
    $hassubmit = "";
}

$urlinfo = array(
    "cid" => null,
    "did" => null,
    "from_date" => (!empty($_GET["from_date"])) ? trim($_GET["from_date"]) : date("Y-m-d"),
    "to_date" => (!empty($_GET["to_date"])) ? trim($_GET["to_date"]) : "",
    "lotsadays" => (!empty($_GET["lotsadays"])) ? trim($_GET["lotsadays"]) : "",
    "hassubmit" => (!empty($_GET["hassubmit"])) ? trim($_GET["hassubmit"]) : "",
    "based_on" => (!empty($_GET["based_on"])) ? trim($_GET["based_on"]) : ""
);

$table = "tmp_lead_time";

if (!empty($urlinfo["lotsadays"]) && empty($urlinfo["to_date"])){ $urlinfo["to_date"] = $urlinfo["from_date"]; }
if (!empty($_GET["cid"])){ $urlinfo["cid"] = trim($_GET["cid"]); }
if (!empty($_GET["did"])){ $urlinfo["did"] = trim($_GET["did"]); }


if (!empty($_GET["col-tag"])){ $urlinfo["col-tag"] = $_GET["col-tag"]; }else{ $urlinfo["col-tag"] = ""; }
if (!empty($_GET["col-article"])){ $urlinfo["col-article"] = $_GET["col-article"]; }else{ $urlinfo["col-article"] = ""; }
if (!empty($_GET["col-size"])){ $urlinfo["col-size"] = $_GET["col-size"]; }else{ $urlinfo["col-size"] = ""; }
if (!empty($_GET["col-deposited"])){ $urlinfo["col-deposited"] = $_GET["col-deposited"]; }else{ $urlinfo["col-deposited"] = ""; }
if (!empty($_GET["col-deposited_laundry_in"])){ $urlinfo["col-deposited_laundry_in"] = $_GET["col-deposited_laundry_in"]; }else{ $urlinfo["col-deposited_laundry_in"] = ""; }
if (!empty($_GET["col-laundry_in_laundry_out"])){ $urlinfo["col-laundry_in_laundry_out"] = $_GET["col-laundry_in_laundry_out"]; }else{ $urlinfo["col-laundry_in_laundry_out"] = ""; }
if (!empty($_GET["col-laundry_out_loaded"])){ $urlinfo["col-laundry_out_loaded"] = $_GET["col-laundry_out_loaded"]; }else{ $urlinfo["col-laundry_out_loaded"] = ""; }
if (!empty($_GET["col-deposited_loaded"])){ $urlinfo["col-deposited_loaded"] = $_GET["col-deposited_loaded"]; }else{ $urlinfo["col-deposited_loaded"] = ""; }
if (!empty($_GET["col-loaded_distributed"])){ $urlinfo["col-loaded_distributed"] = $_GET["col-loaded_distributed"]; }else{ $urlinfo["col-loaded_distributed"] = ""; }
if (!empty($_GET["col-depositlocation"])){ $urlinfo["col-depositlocation"] = $_GET["col-depositlocation"]; }else{ $urlinfo["col-depositlocation"] = ""; }


$from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
$wd = " AND l.date >= '". $from_date_db . "' ";

if (!empty($urlinfo["from_date"]) && !empty($urlinfo["to_date"])) {
    if ($urlinfo["to_date"] < $urlinfo["from_date"]) {
        $pi["note"] = html_error($lang["error_date_from_greater_then_to"]);
    }
}

$query1 = "INSERT INTO technix_log.tmp_scanlocations 
        SELECT l.* FROM technix_log.log_garments_scanlocations l
        INNER JOIN scanlocations s ON s.id = l.scanlocation_id
        INNER JOIN scanlocationstatuses ss ON ss.id = s.scanlocationstatus_id
        WHERE ss.name = 'deposited' " . $wd . " ORDER BY date;";

db_query("TRUNCATE TABLE technix_log.tmp_scanlocations;");
db_query($query1);

$query2 = "INSERT INTO technix_log.tmp_scanlocations2 
        SELECT l.* FROM technix_log.log_garments_scanlocations l
        INNER JOIN scanlocations s ON s.id = l.scanlocation_id
        INNER JOIN scanlocationstatuses ss ON ss.id = s.scanlocationstatus_id
        WHERE ss.name = 'laundry' " . $wd . " ORDER BY date;";

db_query("TRUNCATE TABLE technix_log.tmp_scanlocations2;");
db_query($query2);

$query3 = "INSERT INTO technix_log.tmp_scanlocations3 
        SELECT l.* FROM technix_log.log_garments_scanlocations l
        INNER JOIN scanlocations s ON s.id = l.scanlocation_id
        INNER JOIN scanlocationstatuses ss ON ss.id = s.scanlocationstatus_id
        WHERE ss.name = 'conveyor' " . $wd . " ORDER BY date;";

db_query("TRUNCATE TABLE technix_log.tmp_scanlocations3;");
db_query($query3);

$query4 = "INSERT INTO technix_log.tmp_scanlocations4 
        SELECT l.* FROM technix_log.log_garments_scanlocations l
        INNER JOIN scanlocations s ON s.id = l.scanlocation_id
        INNER JOIN scanlocationstatuses ss ON ss.id = s.scanlocationstatus_id
        WHERE ss.name = 'distributed' " . $wd . " ORDER BY date;";

db_query("TRUNCATE TABLE technix_log.tmp_scanlocations4;");
db_query($query4);


// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

// Required for selectbox: depositlocations
$depositlocations_conditions["order_by"] = "name";
$depositlocations = db_read("depositlocations", "id name", $depositlocations_conditions);
$depositlocation_count = db_num_rows($depositlocations);

$where = " ";
if (empty($pi["note"])) {
    if (!empty($_GET["cid"])){
	$urlinfo["cid"] = trim($_GET["cid"]);
    $where .= " AND c.id = ". trim($_GET["cid"]);
} else {
	$urlinfo["cid"] = null;
}

if (!empty($urlinfo["lotsadays"])) {
    $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
    $to_date_db = str_replace("-", "", $urlinfo["to_date"]) ."235959";
    
    $where .= " AND l.date >= '". $from_date_db . "' ";
    $where .= " AND l.date <= '". $to_date_db . "' ";
} else {
    $from_date_db = str_replace("-", "", $urlinfo["from_date"]) ."000000";
    $to_date_db = str_replace("-", "", $urlinfo["from_date"]) ."235959";

    $where .= " AND l.date >= '". $from_date_db . "' ";
    $where .= " AND l.date <= '". $to_date_db . "' ";
}

if (!empty($urlinfo["did"])) {
    $where .= " AND dl.id = " . $urlinfo["did"];
}

$query = "CREATE VIEW `". $table ."` AS (SELECT 
        c.id AS c_id,
        g.id AS garment_id,
        c.name AS location,
        dl.name AS 'depositlocation',
        g.tag AS tag,
        ar.articlenumber AS articlenumber,
        ar.description AS article,
        s.name AS size,
        l.date AS 'deposited',
        t22.date AS 'laundry_in',
        t21.date AS 'laundry_out',
        t3.date AS 'loaded',
        t4.date AS 'distributed',
        ROUND(time_to_sec(timediff(t22.date, l.date )) / 3600) AS 'deposited_laundry_in',
        ROUND(time_to_sec(timediff(t21.date, t22.date )) / 3600) AS 'laundry_in_laundry_out',
        ROUND(time_to_sec(timediff(t3.date, t21.date )) / 3600)AS 'laundry_out_loaded',
        ROUND(time_to_sec(timediff(t4.date, t3.date )) / 3600)AS 'loaded_distributed',
        ROUND(time_to_sec(timediff(t3.date, l.date )) / 3600)AS 'deposited_loaded'
    FROM technix_log.`log_garments_scanlocations` l
    INNER JOIN scanlocations sl ON sl.id = l.scanlocation_id
    INNER JOIN scanlocationstatuses ss ON ss.id = sl.scanlocationstatus_id
    LEFT JOIN technix_log.tmp_scanlocations t1 ON t1.garment_id = l.garment_id AND t1.id > l.id
    LEFT JOIN technix_log.tmp_scanlocations3 t3 ON t3.garment_id = l.garment_id AND t3.id > l.id AND (ISNULL(t1.id) OR t3.id < t1.id)
    LEFT JOIN technix_log.tmp_scanlocations2 t21 ON t21.garment_id = l.garment_id AND (ISNULL(t21.sub_scanlocation_id) OR t21.sub_scanlocation_id = 2) AND t21.id > l.id AND (ISNULL(t1.id) OR t21.id < t1.id) AND t21.id < t3.id
    LEFT JOIN technix_log.tmp_scanlocations2 t22 ON t22.garment_id = l.garment_id AND t22.sub_scanlocation_id = 1 AND t22.id > l.id AND t22.id < t21.id AND (ISNULL(t1.id) OR t22.id < t1.id)
    LEFT JOIN technix_log.tmp_scanlocations4 t4 ON t4.garment_id = l.garment_id AND t4.id > t3.id AND (ISNULL(t1.id) OR t4.id < t1.id)
    LEFT JOIN garments g ON g.id = l.garment_id
    LEFT JOIN arsimos a ON a.id = g.arsimo_id
    LEFT JOIN articles ar ON ar.id = a.article_id
    LEFT JOIN sizes s ON s.id = a.size_id
    LEFT JOIN circulationgroups c ON c.id = g.circulationgroup_id
    LEFT JOIN depositlocations dl ON dl.scanlocation_id = l.scanlocation_id
    WHERE ss.name = 'deposited' ".
        $where
        ."
    GROUP BY l.id
    ORDER BY l.date)";

    db_query("DROP VIEW IF EXISTS `". $table ."`");
    db_query($query);

    $columns = "location depositlocation garment_id c_id tag articlenumber article size deposited laundry_in laundry_out loaded deposited_loaded distributed deposited_laundry_in laundry_in_laundry_out laundry_out_loaded loaded_distributed";
    
    $urlinfo["order_by"] = geturl_order_by($columns);
    $urlinfo["order_direction"] = geturl_order_direction("DESC");

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
    
    $sortlinks["tag"] = generate_sortlink("tag", $lang["tag"], $pi, $urlinfo);
    $sortlinks["name"] = generate_sortlink("article", $lang["article"], $pi, $urlinfo);
    $sortlinks["size"] = generate_sortlink("size", $lang["size"], $pi, $urlinfo);
    $sortlinks["deposited"] = generate_sortlink("deposited", $lang["deposited"], $pi, $urlinfo);
    $sortlinks["deposited_laundry_in"] = generate_sortlink("deposited_laundry_in", $lang["deposited"]."->".$lang["laundry_inscan"], $pi, $urlinfo);
    $sortlinks["laundry_in_laundry_out"] = generate_sortlink("laundry_in_laundry_out", $lang["laundry_inscan"]."->".$lang["laundry_outscan"], $pi, $urlinfo);
    $sortlinks["laundry_out_loaded"] = generate_sortlink("laundry_out_loaded", $lang["laundry_outscan"]."->".$lang["loaded"], $pi, $urlinfo);
    $sortlinks["deposited_loaded"] = generate_sortlink("deposited_loaded", $lang["deposited"]."->".$lang["loaded"], $pi, $urlinfo);
    $sortlinks["loaded_distributed"] = generate_sortlink("loaded_distributed", $lang["loaded"]."->".$lang["distributed"], $pi, $urlinfo);
    $sortlinks["depositlocation"] = generate_sortlink("depositlocation", $lang["depositlocation"], $pi, $urlinfo);

    $pagination = generate_pagination($pi, $urlinfo);

    if ($hassubmit == "export") {
        $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=". $export_filename);
        header("Pragma: no-cache");
        header("Expires: 0");
				
		$header = "";
		if(!empty($urlinfo["col-tag"]))$header.=$lang["tag"]."\t";
		if(!empty($urlinfo["col-article"]))$header.=$lang["article"]."\t";
		if(!empty($urlinfo["col-size"]))$header.=$lang["size"]."\t";
		if(!empty($urlinfo["col-deposited"]))$header.=$lang["deposited"]."\t";
		if(!empty($urlinfo["col-deposited_laundry_in"]))$header.=$lang["deposited"]."->".$lang["laundry_inscan"]."\t";
		if(!empty($urlinfo["col-laundry_in_laundry_out"]))$header.=$lang["laundry_inscan"]."->".$lang["laundry_outscan"]."\t";
		if(!empty($urlinfo["col-laundry_out_loaded"]))$header.=$lang["laundry_outscan"]."->".$lang["loaded"]."\t";
		if(!empty($urlinfo["col-deposited_loaded"]))$header.=$lang["deposited"]."->".$lang["loaded"]."\t";
		if(!empty($urlinfo["col-loaded_distributed"]))$header.=$lang["loaded"]."->".$lang["distributed"]."\t";
		if(!empty($urlinfo["col-depositlocation"]))$header.=$lang["depositlocation"]."\t";
		
        $data = "";
        while ($row = db_fetch_array($listdata)) {
            $line = "";
            $last_used_unknown = "<span class=\"empty\">" . $lang["unknown"] . "</span>";
           
			$in = array();
			if(!empty($urlinfo["col-tag"]))array_push($in, "'".$row["tag"]);
			if(!empty($urlinfo["col-article"]))array_push($in, $row["article"]);
			if(!empty($urlinfo["col-size"]))array_push($in, $row["size"]);
			if(!empty($urlinfo["col-deposited"]))array_push($in, $row["deposited"]);
			if(!empty($urlinfo["col-deposited_laundry_in"]))array_push($in, $row["deposited_laundry_in"]);
			if(!empty($urlinfo["col-laundry_in_laundry_out"]))array_push($in, $row["laundry_in_laundry_out"]);
			if(!empty($urlinfo["col-laundry_out_loaded"]))array_push($in, $row["laundry_out_loaded"]);
			if(!empty($urlinfo["col-deposited_loaded"]))array_push($in, $row["deposited_loaded"]);
			if(!empty($urlinfo["col-loaded_distributed"]))array_push($in, $row["loaded_distributed"]);
			if(!empty($urlinfo["col-depositlocation"]))array_push($in, $row["depositlocation"]);

            foreach ($in as $value) {
                if (!isset($value) || $value == "") {
                    $value = "\t";
                } else {
                    $value = str_replace('"', '""', $value);
                    $value = '"' . $value . '"' . "\t";
                }
                $line .= $value;
            }
            $data .= trim($line)."\n";
        }
        $data_r = str_replace("\r","",$data);

        print "$header\n$data_r";
        die();
    }
};

/** Required for selectbox: Based on **/
$based_on["deposit"] = $lang["deposit"];
$based_on["laundry"] = $lang["laundry"];
$based_on["loaded"]  = $lang["loaded"];
$based_on["distributed"] = $lang["distributed"];

/**
 * Generate the page
 */
$cv = array(
    "hassubmit" => $hassubmit,
    "history" => $history,
    "pi" => $pi,
    "lotsadays" => ($urlinfo["lotsadays"] == true) ? "checked=\"checked\"" : "",
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "resultinfo" => $resultinfo,
    "pagination" => $pagination,
    "sortlinks" => $sortlinks,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups" => $circulationgroups,
    "depositlocation_count" => $depositlocation_count,
    "depositlocations" => $depositlocations,
    "garmentusers" => $garmentusers,
    "based_on" => $based_on
);

template_parse($pi, $urlinfo, $cv);

?>
