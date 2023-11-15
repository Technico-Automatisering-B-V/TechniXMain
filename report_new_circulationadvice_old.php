<?php

/**
 * Garmentusers
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
$pi["access"] = array("circulation_management", "circulationadvice");
$pi["group"] = $lang["circulation_management"];
$pi["title"] = $lang["circulationadvice_2"];
$pi["filename_list"] = "report_new_circulationadvice.php";
$pi['filename_this'] = 'report_new_circulationadvice.php';
$pi["filename_details"] = "";
$pi['template'] = 'layout/pages/report_new_circulationadvice.tpl';
$pi['page'] = 'simple';


/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

$table = "tmp_circulationadvice";
$urlinfo = array();

/**
 * Create view
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

if (!empty($_GET["cid"])){
    $urlinfo["cid"] = trim($_GET["cid"]);
    $where = " AND c.id = ". trim($_GET["cid"]);
    $where2 = " AND dl.circulationgroup_id = ". trim($_GET["cid"]);
} else {
    $selected_circulationgroup_conditions['order_by'] = 'name';
    $selected_circulationgroup_conditions['limit_start'] = 0;
    $selected_circulationgroup_conditions['limit_num'] = 1;
    $urlinfo['cid'] = db_fetch_row(db_read('circulationgroups', 'id', $selected_circulationgroup_conditions));
    $urlinfo['cid'] = $urlinfo['cid'][0];
    
    $where = " AND c.id = " . $urlinfo["cid"];
    $where2 = " AND dl.circulationgroup_id = " . $urlinfo["cid"];
}

$urlinfo["gu_day"] = (!empty($_GET["gu_day"])) ? trim($_GET["gu_day"]) : 7;
$urlinfo["la_day"] = (!empty($_GET["la_day"])) ? trim($_GET["la_day"]) : 7;
$urlinfo["re_day"] = (!empty($_GET["re_day"])) ? trim($_GET["re_day"]) : 7;
$urlinfo["w"] = (!empty($_GET["w"])) ? trim($_GET["w"]) : 4;
$urlinfo["from_date"] = (!empty($_GET["from_date"])) ? trim($_GET["from_date"]) : date("Y-m-d");
$urlinfo["filter_last_scanned"] = (!empty($_GET["filter_last_scanned"])) ? trim($_GET["filter_last_scanned"]) : "";

if (!isset($urlinfo["gu_day"]) || (isset($urlinfo["gu_day"]) && $urlinfo["gu_day"] < 0)) $urlinfo["gu_day"] = 7;
if (!isset($urlinfo["la_day"]) || (isset($urlinfo["la_day"]) && $urlinfo["la_day"] < 0)) $urlinfo["la_day"] = 7;
if (!isset($urlinfo["re_day"]) || (isset($urlinfo["re_day"]) && $urlinfo["re_day"] < 0)) $urlinfo["re_day"] = 7;

if (!isset($urlinfo["w"]) || (isset($urlinfo["w"]) && $urlinfo["w"] < 0)) $urlinfo["w"] = 4;

db_query("DROP VIEW IF EXISTS tmp_circulation");
db_query("DROP VIEW IF EXISTS tmp_gu_garment");
db_query("DROP VIEW IF EXISTS tmp_la_garment");
db_query("DROP VIEW IF EXISTS tmp_re_garment");

for($i=0; $i<26; $i++) {
    db_query("DROP VIEW IF EXISTS tmp_w". ($i+1));
}

for($i=0; $i<$urlinfo['w']; $i++) {
    $query = "
    CREATE VIEW tmp_w". ($i+1) . "
    AS 
    SELECT
        arsimo_id,
        COUNT(*) AS 'count'
    FROM
        log_distributorclients
    INNER JOIN distributorlocations dl ON dl.id = log_distributorclients.distributorlocation_id 
    WHERE
        date >= DATE(NOW()) - INTERVAL ". ($urlinfo['w']-$i) ." WEEK AND date <= DATE(NOW()) - INTERVAL ". ($urlinfo['w']-$i-1) ." WEEK
        AND ((buttonevent = 'proceed' AND numgarments > 0) OR numgarments = 0)
        ". $where2 ."
    GROUP BY
        arsimo_id";

    db_query($query);
}

$query = "
CREATE VIEW tmp_circulation
AS
SELECT
        a.id AS `arsimo_id`,
        COUNT(l.id) AS `cir_cur`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE
        g.deleted_on IS NULL
        AND g.garmentuser_id IS NULL
        AND g.active = 1 ";
        if(!empty($urlinfo["filter_last_scanned"])) {
            $from_date = str_replace("-", "", $urlinfo["from_date"]) ."000000";
            $query .= " AND g.lastscan IS NOT NULL ";
            $query .= " AND g.lastscan >= '".$from_date."' ";
        }
        $query .= $where ."
    GROUP BY
        a.id";

db_query($query);

$query = "
CREATE VIEW tmp_gu_garment
AS
SELECT
        a.id AS `arsimo_id`,
        COUNT(l.id) AS `cir_cur`
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
        ". $where ."
    GROUP BY
        a.id";

db_query($query);

$query = "
CREATE VIEW tmp_la_garment
AS
SELECT
        a.id AS `arsimo_id`,
        COUNT(l.id) AS `cir_cur`
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
        ". $where ."
    GROUP BY
        a.id";

db_query($query);

$query = "
CREATE VIEW tmp_re_garment
AS
SELECT
        a.id AS `arsimo_id`,
        COUNT(l.id) AS `cir_cur`
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
        ". $where ."
    GROUP BY
        a.id";

db_query($query);

$query = "CREATE VIEW `". $table ."` AS (SELECT
    `ar`.`description` AS 'article',
    `ar`.`articlenumber` AS 'articlenumber',
    `s`.`name` AS 'size',";
    for($i=0; $i<$urlinfo['w']; $i++) {
        $query .= " IF(ISNULL(`w". ($i+1) . "`.`count`), 0,`w". ($i+1) . "`.`count`) AS 'w". ($i+1) . "',";
        if($i != ($urlinfo['w']-1)) {$q_total .= "IF(ISNULL(`w". ($i+1) . "`.`count`), 0,`w". ($i+1) . "`.`count`)+";}
        else {$q_total .= "IF(ISNULL(`w". ($i+1) . "`.`count`), 0,`w". ($i+1) . "`.`count`)";}
    }

    $query .= $q_total ." AS 'total',
    ROUND((". $q_total .")/4) AS 'average',
    ROUND((". $q_total .")/4*1.34) AS 'upset',
    IF(ISNULL(`circulation`.`cir_cur`), 0,`circulation`.`cir_cur`) AS 'measured',
    ROUND((". $q_total .")/4*1.34) - IF(ISNULL(`circulation`.`cir_cur`), 0,`circulation`.`cir_cur`) AS 'advice',
    IF(ISNULL(`gu_garment`.`cir_cur`), 0,`gu_garment`.`cir_cur`) AS 'gu_garment',
    IF(ISNULL(`la_garment`.`cir_cur`), 0,`la_garment`.`cir_cur`) AS 'la_garment',
    IF(ISNULL(`re_garment`.`cir_cur`), 0,`re_garment`.`cir_cur`) AS 're_garment'
FROM
    `arsimos` `a`
    INNER JOIN `articles` `ar` ON `ar`.`id` = `a`.`article_id`
    INNER JOIN `sizes` `s` ON `s`.`id` = `a`.`size_id`
    LEFT JOIN `modifications` `m` ON `m`.`id` = `a`.`modification_id` "; 
    
    for($i=0; $i<$urlinfo['w']; $i++) {
        $query .= " LEFT JOIN tmp_w". ($i+1) . " w". ($i+1) . " ON w". ($i+1) . ".arsimo_id = a.id ";
    }
        
    $query .= " LEFT JOIN tmp_circulation circulation ON a.id = circulation.arsimo_id
    LEFT JOIN tmp_gu_garment gu_garment ON gu_garment.arsimo_id = a.id
    LEFT JOIN tmp_la_garment la_garment ON la_garment.arsimo_id = a.id
    LEFT JOIN tmp_re_garment re_garment ON re_garment.arsimo_id = a.id
WHERE a.deleted_on IS NULL
    
ORDER BY ar.description, s.position)";

db_query("DROP VIEW IF EXISTS `". $table ."`");
db_query($query);

$columns = "articlenumber article size";
for($i=0; $i<$urlinfo['w']; $i++) {
    $columns .= " w". ($i+1);
}
$columns .= " total average upset measured advice gu_garment la_garment re_garment";
    
/**
 * Collect page content
 */

if (!empty($_GET["col-weeks"])){ $urlinfo["col-weeks"] = $_GET["col-weeks"]; }else{ $urlinfo["col-weeks"] = ""; }
// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);
$circulationgroup_count = db_num_rows($circulationgroups);

if (!empty($circulationgroups)) {
    while ($row = db_fetch_num($circulationgroups)) {
        $circulationgroups_name[$row[0]] = $row[1];
    }
}
db_data_seek($circulationgroups, 0);

$urlinfo["search"] = geturl_search();

$urlinfo["order_by"] = geturl_order_by($columns);
$urlinfo["order_direction"] = geturl_order_direction();

$listdata = db_read($table, $columns, $urlinfo);

//fixme! exclude GROUP BY in the remaining COUNT queries
unset($urlinfo["group_by"]);

$limit_total_res = db_count($table, $columns, $urlinfo);
if ($limit_total_res) {
    $urlinfo["limit_total"] = db_fetch_row($limit_total_res);
    $urlinfo["limit_total"] = $urlinfo["limit_total"][0]; //array->string
}
          
$sortlinks["article"] = generate_sortlink("article", $lang["article"], $pi, $urlinfo);
$sortlinks["size"] = generate_sortlink("size", $lang["size"], $pi, $urlinfo);
for($i=0; $i<$urlinfo['w']; $i++) {
    $sortlinks["w".($i+1)] = generate_sortlink("w".($i+1), $lang["week"] ." ". ($i+1), $pi, $urlinfo);
}
$sortlinks["total"] = generate_sortlink("total", $lang["total"], $pi, $urlinfo);
$sortlinks["average"] = generate_sortlink("average", $lang["average"], $pi, $urlinfo);
$sortlinks["upset"] = generate_sortlink("upset", $lang["upset"], $pi, $urlinfo);
$sortlinks["measured"] = generate_sortlink("measured", $lang["measured"], $pi, $urlinfo);
$sortlinks["advice"] = generate_sortlink("advice", $lang["advice"], $pi, $urlinfo);
$sortlinks["gu_garment"] = generate_sortlink("gu_garment", $lang["__circulation_advice_longer_garmentuser"] ." ". $urlinfo['gu_day'] ." ". $lang["days2"], $pi, $urlinfo);
$sortlinks["la_garment"] = generate_sortlink("la_garment", $lang["__circulation_advice_longer_laundry"] ." ". $urlinfo['la_day'] ." ". $lang["days2"], $pi, $urlinfo);
$sortlinks["re_garment"] = generate_sortlink("re_garment", $lang["__circulation_advice_longer_chaoot"] ." ". $urlinfo['re_day'] ." ". $lang["days2"], $pi, $urlinfo);

/**
 * Export
 */
if (isset($_GET["export"]) || isset($_GET["export_order"])) {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename ."");
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = "";
    if (isset($_GET["export"])) {
        $header.=$lang["article"]."\t";
        $header.=$lang["size"]."\t";
        for($i=0; $i<$urlinfo['w']; $i++) {
            if(!empty($urlinfo["col-weeks"]))$header.=$lang["week"] ." ". ($i+1) ."\t";
        }
        $header.=$lang["total"]."\t";
        $header.=$lang["average"]."\t";
        $header.=$lang["upset"]."\t";
        $header.=$lang["measured"]."\t";
        $header.=$lang["advice"]."\t";
        $header.=$lang["__circulation_advice_longer_garmentuser"] ." ". $urlinfo['gu_day'] ." ". $lang["days2"]."\t";
        $header.=$lang["__circulation_advice_longer_laundry"] ." ". $urlinfo['la_day'] ." ". $lang["days2"]."\t";
        $header.=$lang["__circulation_advice_longer_chaoot"] ." ". $urlinfo['re_day'] ." ". $lang["days2"]."\t";
    } else {
        $header.=$lang["articlenumber"]."\t";
        $header.=$lang["description"]."\t";
        $header.=$lang["size"]."\t";
        $header.=$lang["advice"]."\t";
    }
    
    $data = "";
    $order_sum;
    while(!empty($listdata) && $row = db_fetch_array($listdata)) {
        $line = "";
        $in = array();
        
        if (isset($_GET["export"])) {
            array_push($in, $row["article"]);
            array_push($in, $row["size"]);
            for($i=0; $i<$urlinfo['w']; $i++) {
                if(!empty($urlinfo["col-weeks"]))array_push($in, $row["w".($i+1)]);
            }
            array_push($in, $row["total"]);
            array_push($in, $row["average"]);
            array_push($in, $row["upset"]);
            array_push($in, $row["measured"]);
            array_push($in, $row["advice"]);
            array_push($in, $row["gu_garment"]);
            array_push($in, $row["la_garment"]);
            array_push($in, $row["re_garment"]);
            
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
        
        } else {
            if($row["advice"] > 0) {
                $order_sum += $row["advice"];
                array_push($in, $row["articlenumber"]);
                array_push($in, $row["article"]);
                array_push($in, $row["size"]);
                array_push($in, $row["advice"]);
                
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
    $data = str_replace("\r", "", $data);
    
    if(isset($_GET["export_order"])) {
        $header =
            $lang["order_list"]." - ".$circulationgroups_name[$urlinfo["cid"]]."\t\n".
            $lang["total"].": ".$order_sum." ".strtolower($lang["garments"])."\t\n\n".
            $header;
    }
    
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
    "listdata" => $listdata,
    "circulationgroup_count" => $circulationgroup_count,
    "circulationgroups_name" => $circulationgroups_name,
    "circulationgroups" => $circulationgroups,
    "filter_last_scanned" => ($urlinfo["filter_last_scanned"] == true) ? "checked=\"checked\"" : ""
);

template_parse($pi, $urlinfo, $cv);

?>
