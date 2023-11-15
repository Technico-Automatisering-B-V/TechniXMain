<?php

/**
 * Report circulationadvice
 *
 * @author    G. I. Voros <gabor@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once 'include/engine.php';
require_once 'include/mupapu.php';

/**
 * Page settings
 */
$urlinfo = array();

$pi["access"] = array("circulation_management", "circulationadvice");
$pi["group"] = $lang["circulation_management"];
$pi["title"] = $lang["circulationadvice"];
$pi["filename_list"] = "report_circulationadvice.php";
$pi["filename_this"] = "report_circulationadvice.php";
$pi["filename_details"] = "report_circulationadvice.php";
$pi["template"] = "layout/pages/report_circulationadvice.tpl";
$pi["page"] = "simple";


$urlinfo = array(
    "from_date" => (!empty($_GET["from_date"])) ? trim($_GET["from_date"]) : date("Y-m-d"),
    "filter_last_scanned" => (!empty($_GET["filter_last_scanned"])) ? trim($_GET["filter_last_scanned"]) : "",
    "w" => (!empty($_GET["w"])) ? trim($_GET["w"]) : $GLOBALS['config']['mupapu_default_weeks_history'],
    "col-weeks" => (!empty($_GET["col-weeks"])) ? $_GET["col-weeks"] : "",
    "col-stock" => (!empty($_GET["col-stock"])) ? $_GET["col-stock"] : "",
    "gu_day" => (!empty($_GET["gu_day"])) ? $_GET["gu_day"] : 7,
    "de_day" => (!empty($_GET["de_day"])) ? $_GET["de_day"] : 7,
    "co_day" => (!empty($_GET["co_day"])) ? $_GET["co_day"] : 7,
    "la_day" => (!empty($_GET["la_day"])) ? $_GET["la_day"] : 7,
    "re_day" => (!empty($_GET["re_day"])) ? $_GET["re_day"] : 7,
    "multiply_required" => (!empty($_GET["multiply_required"])) ? $_GET["multiply_required"] : 1
);

if (!isset($urlinfo["gu_day"]) || (isset($urlinfo["gu_day"]) && $urlinfo["gu_day"] < 0)) $urlinfo["gu_day"] = 7;
if (!isset($urlinfo["de_day"]) || (isset($urlinfo["de_day"]) && $urlinfo["de_day"] < 0)) $urlinfo["de_day"] = 7;
if (!isset($urlinfo["co_day"]) || (isset($urlinfo["co_day"]) && $urlinfo["co_day"] < 0)) $urlinfo["co_day"] = 7;
if (!isset($urlinfo["la_day"]) || (isset($urlinfo["la_day"]) && $urlinfo["la_day"] < 0)) $urlinfo["la_day"] = 7;
if (!isset($urlinfo["re_day"]) || (isset($urlinfo["re_day"]) && $urlinfo["re_day"] < 0)) $urlinfo["re_day"] = 7;

if (!isset($urlinfo["w"]) || (isset($urlinfo["w"]) && $urlinfo["w"] < 1)) $urlinfo["w"] = $GLOBALS['config']['mupapu_default_weeks_history'];
if (!isset($urlinfo["multiply_required"]) || (isset($urlinfo["multiply_required"]) && ($urlinfo["multiply_required"] <= 0 || $urlinfo["multiply_required"] >= 10))) $urlinfo["multiply_required"] = 1;
/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}
/**
 * Generate MUPAPU
 */
if (!empty($_GET['cid']) && is_numeric($_GET['cid'])) $urlinfo['cid'] = trim($_GET['cid']);
else {
    // We use the circulationgroup_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_circulationgroup_conditions['order_by'] = 'name';
    $selected_circulationgroup_conditions['limit_start'] = 0;
    $selected_circulationgroup_conditions['limit_num'] = 1;
    $urlinfo['cid'] = db_fetch_row(db_read('circulationgroups', 'id', $selected_circulationgroup_conditions));
    $urlinfo['cid'] = $urlinfo['cid'][0];
}

// Required for selectbox: circulationgroups
$circulationgroups_conditions['order_by'] = 'name';
$circulationgroups = db_read('circulationgroups', 'id name', $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

if (!empty($circulationgroups)) {
    while ($row = db_fetch_num($circulationgroups)) {
        $circulationgroups_name[$row[0]] = $row[1];
    }
}
db_data_seek($circulationgroups, 0);

$periods_num_sql = "SELECT COUNT(*)
        FROM distributionperiods dp
        WHERE dp.circulationgroup_id = " . $urlinfo["cid"];
$periods_num = db_fetch_num(db_query($periods_num_sql));
$periods_num = $periods_num[0];

if (!empty($urlinfo["filter_last_scanned"])) {
    $from_date = str_replace("-", "", $urlinfo["from_date"]) ."000000";
    $mupapu = mupapu_generate_with_lastscan_date($urlinfo['cid'], $urlinfo["w"], $calculate = true, $from_date);
} else {
    $mupapu = mupapu_generate_with_lastscan_date($urlinfo['cid'], $urlinfo["w"], $calculate = true);
}

$query = "
SELECT
        a.id AS `arsimo_id`,
        COUNT(l.id) AS `count`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN scanlocationstatuses AS s ON l.scanlocationstatus_id = s.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE
        g.deleted_on IS NULL
        AND g.garmentuser_id IS NULL
        AND g.active = 1
        AND s.name = 'distributed'
        AND g.lastscan <= DATE(NOW()) - INTERVAL ". ($urlinfo['gu_day']-1) ." DAY
        AND c.id = " . $urlinfo['cid']  ."
    GROUP BY
        a.id";

$tmp_gu_garment = db_query($query);

$query = "
SELECT
        a.id AS `arsimo_id`,
        COUNT(l.id) AS `count`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN scanlocationstatuses AS s ON l.scanlocationstatus_id = s.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE
        g.deleted_on IS NULL
        AND g.garmentuser_id IS NULL
        AND g.active = 1
        AND s.name = 'deposited'
        AND g.lastscan <= DATE(NOW()) - INTERVAL ". ($urlinfo['de_day']-1) ." DAY
        AND c.id = " . $urlinfo['cid']  ."
    GROUP BY
        a.id";

$tmp_de_garment = db_query($query);

$query = "
SELECT
        a.id AS `arsimo_id`,
        COUNT(l.id) AS `count`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN scanlocationstatuses AS s ON l.scanlocationstatus_id = s.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE
        g.deleted_on IS NULL
        AND g.garmentuser_id IS NULL
        AND g.active = 1
        AND s.name = 'container'
        AND g.lastscan <= DATE(NOW()) - INTERVAL ". ($urlinfo['co_day']-1) ." DAY
        AND c.id = " . $urlinfo['cid']  ."
    GROUP BY
        a.id";

$tmp_co_garment = db_query($query);

$query = "
SELECT
        a.id AS `arsimo_id`,
        COUNT(l.id) AS `count`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN scanlocationstatuses AS s ON l.scanlocationstatus_id = s.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE
        g.deleted_on IS NULL
        AND g.garmentuser_id IS NULL
        AND g.active = 1
        AND s.name = 'laundry'
        AND g.lastscan <= DATE(NOW()) - INTERVAL ". ($urlinfo['la_day']-1) ." DAY
        AND c.id = " . $urlinfo['cid']  ."
    GROUP BY
        a.id";

$tmp_la_garment = db_query($query);

$query = "
SELECT
        a.id AS `arsimo_id`,
        COUNT(l.id) AS `count`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN scanlocationstatuses AS s ON l.scanlocationstatus_id = s.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE
        g.deleted_on IS NULL
        AND g.garmentuser_id IS NULL
        AND g.active = 1
        AND s.name = 'rejected'
        AND g.lastscan <= DATE(NOW()) - INTERVAL ". ($urlinfo['re_day']-1) ." DAY
        AND c.id = " . $urlinfo['cid']  ."
    GROUP BY
        a.id";

$tmp_re_garment = db_query($query);

$gu_garment = array();
while ($row = db_fetch_assoc($tmp_gu_garment)){
   $gu_garment[$row["arsimo_id"]] = $row["count"];
}

$de_garment = array();
while ($row = db_fetch_assoc($tmp_de_garment)){
   $de_garment[$row["arsimo_id"]] = $row["count"];
}

$co_garment = array();
while ($row = db_fetch_assoc($tmp_co_garment)){
   $co_garment[$row["arsimo_id"]] = $row["count"];
}

$la_garment = array();
while ($row = db_fetch_assoc($tmp_la_garment)){
   $la_garment[$row["arsimo_id"]] = $row["count"];
}

$re_garment = array();
while ($row = db_fetch_assoc($tmp_re_garment)){
   $re_garment[$row["arsimo_id"]] = $row["count"];
}

$total_distributed_garments_sql = "SELECT ROUND(COUNT(*)*0.34)
                        FROM log_garmentusers_garments
                        INNER JOIN distributors ON distributors.id = log_garmentusers_garments.distributor_id
                        INNER JOIN distributorlocations ON distributorlocations.id = distributors.distributorlocation_id
                        WHERE starttime >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY
                        AND starttime < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY
                        AND distributorlocations.circulationgroup_id = " . $urlinfo['cid'];
$total_distributed_garments = db_fetch_num(db_query($total_distributed_garments_sql));
$total_distributed_garments = $total_distributed_garments[0];

if(!isset($urlinfo["multiply_required_auto"])) $urlinfo["multiply_required_auto"] = 1.1;

$position_needed = 0;

switch (true) {
    case $total_distributed_garments < 1500:
        $position_needed = $total_distributed_garments * 2;
        break;
    case $total_distributed_garments <= 2000:
        $position_needed = round($total_distributed_garments * 1.94);
        break;
    case $total_distributed_garments <= 2500:
        $position_needed = round($total_distributed_garments * 1.88);
        break;
    case $total_distributed_garments <= 3000:
        $position_needed = round($total_distributed_garments * 1.84);
        break;
    case $total_distributed_garments <= 3500:
        $position_needed = round($total_distributed_garments * 1.76);
        break;
    case $total_distributed_garments <= 4000:
        $position_needed = round($total_distributed_garments * 1.7);
        break;
    case $total_distributed_garments <= 4500:
        $position_needed = round($total_distributed_garments * 1.64);
        break;
    case $total_distributed_garments <= 5000:
        $position_needed = round($total_distributed_garments * 1.58);
        break;
    case $total_distributed_garments > 5000:
        $position_needed = round($total_distributed_garments * 1.52);
        break;
    default:
        break;
}

if($position_needed > 0) {
    
    $total_positions_sql = "SELECT SUM(`hooks`) -
                        (
                        SELECT IF(ISNULL(SUM(`ga`.`max_positions`)), 0, SUM(`ga`.`max_positions`)) AS 'max_positions'
                                 FROM `garmentusers_userbound_arsimos` `ga`
                           INNER JOIN `garmentusers` `gu` ON `ga`.`garmentuser_id` = `gu`.`id`
                           INNER JOIN `circulationgroups_garmentusers` `cg` ON `cg`.`garmentuser_id` = `gu`.`id`
                           INNER JOIN `distributorlocations` `dl` ON `dl`.`circulationgroup_id` = `cg`.`circulationgroup_id`
                           INNER JOIN `distributors` `d` ON (`d`.`id` = `gu`.`distributor_id`
                                   OR `d`.`id` = `gu`.`distributor_id2`
                                   OR `d`.`id` = `gu`.`distributor_id3`
                                   OR `d`.`id` = `gu`.`distributor_id4`
                                   OR `d`.`id` = `gu`.`distributor_id5`
                                   OR `d`.`id` = `gu`.`distributor_id6`
                                   OR `d`.`id` = `gu`.`distributor_id7`
                                   OR `d`.`id` = `gu`.`distributor_id8`
                                   OR `d`.`id` = `gu`.`distributor_id9`
                                   OR `d`.`id` = `gu`.`distributor_id10`) AND `d`.`distributorlocation_id` = `dl`.`id`
                                WHERE `ga`.`enabled` = 1
                                  AND `gu`.`deleted_on` IS NULL
                                  AND `dl`.`circulationgroup_id` = `ga`.`circulationgroup_id`
                                  AND `dl`.`circulationgroup_id` = ". $urlinfo["cid"] ."
                        ) AS 'hooks'
                           FROM `distributors` `d`
                     INNER JOIN `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
                          WHERE `dl`.`circulationgroup_id` = ". $urlinfo["cid"] ;
    
    $total_positions = db_fetch_num(db_query($total_positions_sql));
    $total_positions = $total_positions[0];   
    
    $multiply_required_auto = round($total_positions/$position_needed, 2);
    
    if($multiply_required_auto > 2) {
        $urlinfo["multiply_required_auto"] = 2;
    } else if ($multiply_required_auto < 1.1) {
        $urlinfo["multiply_required_auto"] = 1.1;
    } else {
        $urlinfo["multiply_required_auto"] = $multiply_required_auto;
    }
}

/**
* Export
*/
if (isset($_GET["export"]) || isset($_GET["export_order"]) || isset($_GET["export_order_stock"]) || isset($_GET["export_too_much_in_circulation"])) {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename);
    header("Pragma: no-cache");
    header("Expires: 0");
    if (isset($_GET["export"])) {
        $header = $lang["article"]."\t"." \t"." \t";
        
        if(!empty($urlinfo["col-weeks"])) {
            $header .= $lang["highest_distribution_period_per_week"]."\t";
            for($i=1;$i<$urlinfo["w"];$i++) {
                $header .= " \t";
            }
        }
        
        $header .= $lang["circulationadvice"]."\t"." \t"." \t"." \t"." \t"." \t";
        $header .= $lang["to_long_in_circulation"]."\t"." \t"." \t";
        
        if(!empty($urlinfo["col-stock"])) {
            $header .= $lang["stock"]."\t";
        }
        
        $header .= "\n".$lang["articlenumber"]."\t".$lang["description"]."\t".$lang["size"]."\t";
        
        if(!empty($urlinfo["col-weeks"])) {
            for($i=0;$i<$urlinfo["w"];$i++) {
                $header .= $lang["week"] ." ". ($i+1) ."\t";
            }
        }
        
        $header .= $lang["total"]."\t".$lang["average"]."\t".$lang["required"]."\t".$lang["now_circulating"]."\t".$lang["order"]."\t".$lang["too_much"]."\t";
        $header .= $lang["__circulation_advice_longer_garmentuser"]."\t".$lang["__circulation_advice_longer_deposited"]."\t"
                .$lang["__circulation_advice_longer_container"]."\t".$lang["__circulation_advice_longer_laundry"]."\t".$lang["__circulation_advice_longer_chaoot"]."\t";
        
        if(!empty($urlinfo["col-stock"])) {
            $header .= $lang["required"]."\t".$lang["measured"]."\t".$lang["complement"]."\t".$lang["order"]."\t".$lang["too_much"]."\t";
        }
    } elseif(isset($_GET["export_too_much_in_circulation"])) {
        $header.=$lang["articlenumber"]."\t";
        $header.=$lang["description"]."\t";
        $header.=$lang["size"]."\t";
        $header.=$lang["too_much"]."\t";
    } else {
        $header.=$lang["articlenumber"]."\t";
        $header.=$lang["description"]."\t";
        $header.=$lang["size"]."\t";
        $header.=$lang["order"]."\t";
    }
    $data = "";
        
    foreach ($mupapu["mup"] as $ars => $row) {
        $line = "";
        $in = array();
        $inw = array();
        
        $all_periods = 0;
        for($i=0; $i<$urlinfo['w']; $i++) {
            $period_max = 0;
            for($p=0; $p < $periods_num; $p++) {
                if($period_max < $row["hitmiss_w". $i .".p". $p]){ $period_max = $row["hitmiss_w". $i .".p". $p]; }
            }
            $all_periods = $all_periods+$period_max;
            array_push($inw, $period_max);
        }
        $all_periods_average = ceil($all_periods/$urlinfo['w']);
        switch (true) {
            case $all_periods_average <= 2:
                $req = $all_periods_average * 4;
                break;
            case $all_periods_average <= 5:
                $req = $all_periods_average * 3.75;
                break;
            case $all_periods_average <= 10:
                $req = $all_periods_average * 3.5;
                break;
            case $all_periods_average <= 20:
                $req = $all_periods_average * 3.25;
                break;
            case $all_periods_average <= 40:
                $req = $all_periods_average * 3;
                break;
            case $all_periods_average <= 80:
                $req = $all_periods_average * 2.75;
                break;
            case $all_periods_average <= 120:
                $req = $all_periods_average * 2.7;
                break;
            case $all_periods_average <= 160:
                $req = $all_periods_average * 2.65;
                break;
            case $all_periods_average <= 200:
                $req = $all_periods_average * 2.6;
                break;
            case $all_periods_average <= 250:
                $req = $all_periods_average * 2.55;
                break;
            case $all_periods_average > 250:
                $req = $all_periods_average * 2.5;
                break;
            default:
                break;
        }
        $req = $req * $urlinfo["multiply_required_auto"] * $urlinfo["multiply_required"];
        $ad = ceil($req)-$row["cir_cur"];
        $sto_ad = $ad + $row["sto_diff"];
        
        if($ad < 0) {
            $order = 0;
            $out = abs($ad);
            $order_color = null;
        } else {
            $order = $ad;
            $out = 0;
            $out_color = null;
        }
        
        if($sto_ad < 0) {
            $sto_order = 0;
            $sto_out = abs($sto_ad);
        } else {
            $sto_order = $sto_ad;
            $sto_out = 0;
        } 
        
        if (isset($_GET["export"])) {
            
            array_push($in, $row["articlecode"]);      
            array_push($in, $row["description"]);      
            if(!empty($row["modification"])) { array_push($in, $row["size"]. " " . $row["modification"]); } else { array_push($in, $row["size"]); }
            
            foreach ($inw as $value) {
                if(!empty($urlinfo["col-weeks"])) { if(!empty($value)){ array_push($in, $value);} else { array_push($in, "0"); } }
            }
            if($all_periods != 0){ array_push($in, $all_periods);} else { array_push($in, "0"); }
            if($all_periods_average != 0){ array_push($in, $all_periods_average);} else { array_push($in, "0"); }
            
            if(ceil($req) != 0){ array_push($in, ceil($req)); } else {array_push($in, "0");}   
            if($row["cir_cur"] != 0){ array_push($in, $row["cir_cur"]);} else { array_push($in, "0"); }
            if($order != 0){ array_push($in, $order);} else { array_push($in, "0"); }
            if($out != 0){ array_push($in, $out);} else { array_push($in, "0"); }
            if(isset($gu_garment[$ars])){ array_push($in, $gu_garment[$ars]);} else { array_push($in, "0"); }
            if(isset($de_garment[$ars])){ array_push($in, $de_garment[$ars]);} else { array_push($in, "0"); }
            if(isset($co_garment[$ars])){ array_push($in, $co_garment[$ars]);} else { array_push($in, "0"); }
            if(isset($la_garment[$ars])){ array_push($in, $la_garment[$ars]);} else { array_push($in, "0"); }
            if(isset($re_garment[$ars])){ array_push($in, $re_garment[$ars]);} else { array_push($in, "0"); }
            
            if(!empty($urlinfo["col-stock"])) {
                if($row["sto_new"] != 0){ array_push($in, $row["sto_new"]); } else { array_push($in, "0");}
                if($row["sto_cur"] != 0){ array_push($in, $row["sto_cur"]); } else { array_push($in, "0");}
                if($row["sto_diff"] != 0){ array_push($in, $row["sto_diff"]); } else { array_push($in, "0");}
                if($sto_order != 0){ array_push($in, $sto_order); } else { array_push($in, "0");}
                if($sto_out != 0){ array_push($in, $sto_out); } else { array_push($in, "0");}
            }
            
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
        } elseif(isset($_GET["export_too_much_in_circulation"])) {
            if($out > 0) {
                array_push($in, $row["articlecode"]);      
                array_push($in, $row["description"]);      
                if(!empty($row["modification"])) { array_push($in, $row["size"]. " " . $row["modification"]); } else { array_push($in, $row["size"]); }
                array_push($in, $out); 
                
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
        } else {
            if(isset($_GET["export_order_stock"])) {  
                $advice = $sto_order;
            } else {
                $advice = $order;
            }
            
            if($advice > 0) {
                $order_sum += $advice;
                array_push($in, $row["articlecode"]);      
                array_push($in, $row["description"]);      
                if(!empty($row["modification"])) { array_push($in, $row["size"]. " " . $row["modification"]); } else { array_push($in, $row["size"]); }
                array_push($in, $advice); 
                
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
        }      
    }

    $data_r = str_replace("\r","",$data);
    
    if(isset($_GET["export_order"]) || isset($_GET["export_order_stock"])) {
        $header =
            $lang["order_list"]." - ".$circulationgroups_name[$urlinfo["cid"]]."\t\n".
            $lang["total"].": ".$order_sum." ".strtolower($lang["garments"])."\t\n\n".
            $header;
    }
    
    print "$header\n$data_r";
    die();
    
 }

/**
 * Generate the page
 */
$cv = array(
    'pageinfo' => $pi,
    'filter_last_scanned' => ($urlinfo["filter_last_scanned"] == true) ? "checked=\"checked\"" : "",
    'urlinfo' => $urlinfo,
    'circulationgroup_count' => $circulationgroup_count,
    'circulationgroups' => $circulationgroups,
    'circulationgroups_name' => $circulationgroups_name,
    'mupapu' => $mupapu,
    "periods_num" => $periods_num,
    "gu_garment" => $gu_garment,
    "de_garment" => $de_garment,
    "co_garment" => $co_garment,
    "la_garment" => $la_garment,
    "re_garment" => $re_garment  
);

template_parse($pi, $urlinfo = array(), $cv);


?>
