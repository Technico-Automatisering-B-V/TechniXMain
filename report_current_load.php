<?php

/**
 * Report current load
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require_once "include/mupapu.php";

/**
 * Page settings
 */
$pi["access"] = array("load", "current_sizebound_load");
$pi["group"] = $lang["load"];
$pi["title"] = $lang["current_sizebound_load"];
$pi["filename_this"] = "report_current_load.php";
$pi["filename_list"] = "report_current_load.php";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

if (isset($_GET["print"])) {
    $pi["template"] = "layout/pages/report_template.print.tpl";
    $pi["page"] = "report";
} else {
    $pi["template"] = "layout/pages/report_template.tpl";
    $pi["page"] = "simple";
}

/**
 * Collect page content
 */
$urlinfo = array();
$where     = "";
$ref_where = "";

if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; $only_loadable = false;}
    $evalue = $lang["export"]. " ". $lang["loadable"];
    if ($_GET["hassubmit"] == $evalue){ $hassubmit = "export"; $only_loadable = true;}
} else {
    $hassubmit = "";
}

if (!empty($_GET["cid"])) {
    $urlinfo["cid"] = trim($_GET["cid"]);
} else {
    //we use the circulationgroup_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_circulationgroup_conditions["order_by"] = "name";
    $selected_circulationgroup_conditions["limit_start"] = 0;
    $selected_circulationgroup_conditions["limit_num"] = 1;
    $urlinfo["cid"] = db_fetch_row(db_read("circulationgroups", "id", $selected_circulationgroup_conditions));
    $urlinfo["cid"] = $urlinfo["cid"][0];
}

if (!empty($_GET["did"])) {
    $urlinfo["did"] = trim($_GET["did"]);
    $where .= " AND tmp3.distributorlocation_id = ". $urlinfo["did"] ." ";
    $hwhere = " AND dl.id = ". $urlinfo["did"];
} else {
    $urlinfo["did"] = null;
    $hwhere = "";
}

if (!empty($_GET["aid"])) {
    $urlinfo["aid"] = trim($_GET["aid"]);
    $where .= " AND articles.id = ". $urlinfo["aid"] ." ";
} else {
    $urlinfo["aid"] = null;
}

if (!empty($_GET["sid"])) {
    $urlinfo["sid"] = trim($_GET["sid"]);
    $where .= " AND sizes.id = ". $urlinfo["sid"] ." ";
} else {
    $urlinfo["sid"] = null;
}

if (!empty($_GET["status"])) {
    $urlinfo["status"] = trim($_GET["status"]);
    switch ($urlinfo["status"]) {
        case 'red':
            $ref_where = " AND ref__rowcolor = '#FFCCCC' ";
            break;
        case 'yellow':
            $ref_where = " AND ref__rowcolor = '#FFEEBB' ";
            break;
        case 'blue':
            $ref_where = " AND ref__rowcolor = '#C4D1DA' ";
            break;
        default:
            break;
    }
} else {
    $urlinfo["status"] = null;
}

if (!empty($_GET["edid"])) {
    $urlinfo["edid"] = trim($_GET["edid"]);
} else {
    $urlinfo["edid"] = null;
}
if (!empty($_GET["eaid"])) {
    $urlinfo["eaid"] = trim($_GET["eaid"]);
} else {
    $urlinfo["eaid"] = null;
}
if (!empty($_GET["esid"])) {
    $urlinfo["esid"] = trim($_GET["esid"]);
} else {
    $urlinfo["esid"] = null;
}
if (!empty($_GET["edemand"])) {
    $urlinfo["edemand"] = trim($_GET["edemand"]);
} else {
    $urlinfo["edemand"] = null;
}

if (!empty($_GET["eadd_submit"])) {
    if(!empty($urlinfo["edid"]) && !empty($urlinfo["eaid"]) && !empty($urlinfo["esid"]) && !empty($urlinfo["edemand"])) { 
        //retrieve the arsimo_id from bindingdata
        $arsimo_conditions["where"]["1"] = "article_id = " . $urlinfo["eaid"];
        $arsimo_conditions["where"]["2"] = "size_id = " . $urlinfo["esid"];
        $arsimo_conditions["where"]["3"] = "modification_id is NULL";
        $arsimo_data = db_read("arsimos", "id", $arsimo_conditions);
        $arsimo_id = db_fetch_num($arsimo_data);
        $detailsdata["arsimo_id"] = $arsimo_id[0];
        $detailsdata["demand"] = $urlinfo["edemand"];
        $detailsdata["distributorlocation_id"] = $urlinfo["edid"];
        $detailsdata["critical_percentage"] = "0.33";
        $detailsdata["type"] = "manual";

        $sql    = "DELETE FROM `distributorlocations_loadadvice` WHERE `arsimo_id` = ". $detailsdata["arsimo_id"] ." AND `distributorlocation_id` = ". $detailsdata["distributorlocation_id"] ." AND `type` = 'manual'";
        $q      = db_query($sql);
        $table  = "distributorlocations_loadadvice";
        
        db_insert($table, $detailsdata);
        
        $urlinfo["edid"] = null;
        $urlinfo["eaid"] = null;
        $urlinfo["esid"] = null;
        $urlinfo["edemand"] = null;
    }
}

if (!empty($_GET["edel_submit"])) {
    if(!empty($urlinfo["edid"]) && !empty($urlinfo["eaid"]) && !empty($urlinfo["esid"])){ 
        //retrieve the arsimo_id from bindingdata
        $arsimo_conditions["where"]["1"] = "article_id = " . $urlinfo["eaid"];
        $arsimo_conditions["where"]["2"] = "size_id = " . $urlinfo["esid"];
        $arsimo_conditions["where"]["3"] = "modification_id is NULL";
        $arsimo_data = db_read("arsimos", "id", $arsimo_conditions);
        $arsimo_id = db_fetch_num($arsimo_data);
        $detailsdata["arsimo_id"] = $arsimo_id[0];
        $detailsdata["distributorlocation_id"] = $urlinfo["edid"];
        
        $sql    = "DELETE FROM `distributorlocations_loadadvice` WHERE `arsimo_id` = ". $detailsdata["arsimo_id"] ." AND `distributorlocation_id` = ". $detailsdata["distributorlocation_id"] ." AND `type` = 'manual'";
        $q      = db_query($sql);

        $urlinfo["edid"] = null;
        $urlinfo["eaid"] = null;
        $urlinfo["esid"] = null;
        $urlinfo["edemand"] = null;
    }
}

// Required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);

// Required for selectbox: distributorlocations
$distributorlocations_conditions["order_by"] = "name";
$distributorlocations_conditions["where"]["1"] = "circulationgroup_id = " . $urlinfo["cid"];
$distributorlocations = db_read("distributorlocations", "id name", $distributorlocations_conditions);
$distributorlocation_count = db_num_rows($distributorlocations);

// Required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles = db_read("articles", "id description", $articles_conditions);

// Required for selectbox: sizes
$sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
$sizes_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["aid"];
$sizes_conditions["where"]["2"] = "arsimos.deleted_on is null";
$sizes_conditions["order_by"] = "sizes.position";
$sizes_conditions["group_by"] = "arsimos.size_id";
$sizes = db_read("arsimos", "arsimos.size_id sizes.name", $sizes_conditions);

/** Required for selectbox: Status **/
$statuses["red"]    = $lang["not_loaded"];
$statuses["blue"]   = $lang["lower_then_critical_limit"];
$statuses["yellow"] = $lang["__Report_current_load__not_wanted_garments"];

$available_hooks_sizebound_sql = "
    SELECT SUM(`hooks`) -
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
               ". $hwhere ."
     ) AS 'hooks'
        FROM `distributors` `d`
  INNER JOIN `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
       WHERE `dl`.`circulationgroup_id` = ". $urlinfo["cid"] . $hwhere;

$available_hooks_sizebound_r = db_fetch_row(db_query($available_hooks_sizebound_sql));
$available_hooks_sizebound = $available_hooks_sizebound_r[0];

if(empty($available_hooks_sizebound)) $available_hooks_sizebound = 0;

$available_hooks_userbound_sql = "
    SELECT SUM(`hooks`) - (SUM(`hooks`) -
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
               ". $hwhere ."
     ))
        FROM `distributors` `d`
  INNER JOIN `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
       WHERE `dl`.`circulationgroup_id` = ". $urlinfo["cid"] . $hwhere;

$available_hooks_userbound_r = db_fetch_row(db_query($available_hooks_userbound_sql));
$available_hooks_userbound = $available_hooks_userbound_r[0];

if(empty($available_hooks_userbound)) $available_hooks_userbound = 0;

$total_current_userbound_load = 0;

$current_userbound_load_sql = "
    SELECT SUM(count)
    FROM (
    SELECT 
        distributors.distributorlocation_id,
        garments.arsimo_id,
        COUNT(distributors_load.garment_id) 'count'
    FROM
        distributors_load
        INNER JOIN distributors ON distributors_load.distributor_id = distributors.id
        INNER JOIN distributorlocations dl ON dl.id = distributors.distributorlocation_id
        INNER JOIN garments ON distributors_load.garment_id = garments.id
    WHERE
        garments.garmentuser_id IS NOT NULL AND dl.circulationgroup_id = ". $urlinfo["cid"] . $hwhere ."
    GROUP BY
        distributors.distributorlocation_id,
        garments.arsimo_id) t1";

$current_userbound_load_r = db_fetch_row(db_query($current_userbound_load_sql));
$total_current_userbound_load = $current_userbound_load_r[0];

if(empty($total_current_userbound_load)) $total_current_userbound_load = 0;

$sql = "
SELECT
    arsimos.id AS 'ref__id',
    CASE    WHEN (SUM(tmp3.sizebound_count) = 0) THEN '#FFCCCC' -- red
            WHEN (SUM(tmp3.demand) < SUM(tmp3.sizebound_count) ) THEN '#FFEEBB' -- yellow
            WHEN (SUM(tmp3.sizebound_count) < COALESCE(ROUND(SUM(tmp3.demand) * tmp3.critical_percentage),0)) THEN '#C4D1DA' -- blue
            ELSE ''
    END AS 'ref__rowcolor',
    articles.articlenumber AS 'article',
    articles.description AS 'description',
    IF( ISNULL(modifications.id),
        sizes.name,
        CONCAT(sizes.name, ' ', modifications.name)
    ) AS 'size',
    COALESCE(SUM(tmp3.demand),0) AS 'max_load',

    COALESCE(SUM(tmp3.autodemand),0) AS 'automatically_required',
    COALESCE(SUM(tmp3.manualdemand),0) AS 'manually_added',

    COALESCE(SUM(tmp3.sizebound_count),0) AS 'current_load',
    COALESCE(SUM(tmp3.demand),0)-COALESCE(SUM(tmp3.sizebound_count),0) AS 'loadable',
    COALESCE(ROUND(SUM(tmp3.demand) * tmp3.critical_percentage),0) AS 'critical_limit',
    /* Also handles tmp3.demand = 0 */
    COALESCE(AVG(tmp3.current_load_percentage),0) AS 'current_load_percentage',
    COALESCE(tmp3.critical_percentage * 100,0) AS 'critical_limit_percentage',
    SUM(tmp3.current_period) AS 'min_load_current_period',
    SUM(tmp3.next_period) AS 'min_load_next_period'
FROM
arsimos
INNER JOIN articles ON arsimos.article_id = articles.id
INNER JOIN sizes ON arsimos.size_id = sizes.id
INNER JOIN sizegroups ON sizes.sizegroup_id = sizegroups.id
LEFT JOIN modifications ON arsimos.modification_id = modifications.id
LEFT JOIN (
    SELECT
        tmp.distributorlocation_id,
        tmp.arsimo_id,
        IF(ISNULL(tmp1.demand), 0, tmp1.demand) 'demand',
        IF(ISNULL(tmp1a.autodemand), 0, tmp1a.autodemand) 'autodemand',
        IF(ISNULL(tmp1m.manualdemand), 0, tmp1m.manualdemand) 'manualdemand',
        IF(ISNULL(tmp2.count), 0,tmp2.count) 'sizebound_count',
        IF(IF(ISNULL(tmp1.demand), 0, tmp1.demand)<=IF(ISNULL(tmp2.count), 0,tmp2.count),1,IF(ISNULL(tmp2.count), 0,tmp2.count) / IF(ISNULL(tmp1.demand), 0, tmp1.demand)) as current_load_percentage,
        tmp1.critical_percentage,
        IF(ISNULL(tmp1.demand), 0, tmp1.demand) - IF(ISNULL(tmp2.count),0,tmp2.count) 'diff',
        IF(ISNULL(tmp1a.current_period), 0, tmp1a.current_period) 'current_period', IF(ISNULL(tmp1a.next_period), 0, tmp1a.next_period) 'next_period' 
    FROM
    (
            SELECT DISTINCT
                distributors.distributorlocation_id,
                garments.arsimo_id
            FROM
                distributors_load
                INNER JOIN distributors ON distributors_load.distributor_id = distributors.id
                INNER JOIN garments ON distributors_load.garment_id = garments.id
            WHERE ISNULL(garments.garmentuser_id)
        UNION
        SELECT DISTINCT
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id
        FROM
            distributorlocations_loadadvice
    ) tmp
    LEFT JOIN
    (
        SELECT
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id,
            SUM(distributorlocations_loadadvice.demand) AS 'demand',
            distributorlocations_loadadvice.critical_percentage
        FROM
            distributorlocations_loadadvice
        GROUP BY
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id
    ) tmp1 ON tmp.distributorlocation_id = tmp1.distributorlocation_id
        AND tmp.arsimo_id = tmp1.arsimo_id

    LEFT JOIN
    (
        SELECT
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id,
            SUM(distributorlocations_loadadvice.demand) AS 'autodemand',
            distributorlocations_loadadvice.critical_percentage,
            SUM(distributorlocations_loadadvice.current_period) AS 'current_period',
            SUM(distributorlocations_loadadvice.next_period) AS 'next_period'
        FROM
            distributorlocations_loadadvice
        WHERE
            distributorlocations_loadadvice.type = 'auto'
        GROUP BY
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id
    ) tmp1a ON tmp.distributorlocation_id = tmp1a.distributorlocation_id
        AND tmp.arsimo_id = tmp1a.arsimo_id
    LEFT JOIN
    (
        SELECT
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id,
            SUM(distributorlocations_loadadvice.demand) AS 'manualdemand',
            distributorlocations_loadadvice.critical_percentage
        FROM
            distributorlocations_loadadvice
        WHERE
            distributorlocations_loadadvice.type = 'manual'
        GROUP BY
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id
    ) tmp1m ON tmp.distributorlocation_id = tmp1m.distributorlocation_id
        AND tmp.arsimo_id = tmp1m.arsimo_id

    LEFT JOIN (
        SELECT
            distributors.distributorlocation_id,
            garments.arsimo_id,
            COUNT(distributors_load.garment_id) 'count'
        FROM
            distributors_load
            INNER JOIN distributors ON distributors_load.distributor_id = distributors.id
            INNER JOIN garments ON distributors_load.garment_id = garments.id
        WHERE
            ISNULL(garments.garmentuser_id)
        GROUP BY
            distributors.distributorlocation_id,
            garments.arsimo_id
    ) tmp2 ON tmp.distributorlocation_id = tmp2.distributorlocation_id
        AND tmp.arsimo_id = tmp2.arsimo_id
) tmp3 ON tmp3.arsimo_id = arsimos.id
INNER JOIN `distributorlocations` `d` ON `d`.`id` = `tmp3`.`distributorlocation_id`
WHERE arsimos.deleted_on IS NULL AND `d`.`circulationgroup_id` = ". $urlinfo["cid"] . $where ."
GROUP BY `tmp3`.`arsimo_id`
HAVING (
`max_load` > 0 OR
`automatically_required` > 0 OR
`manually_added` > 0 OR
`current_load` > 0 ) 
". $ref_where ." 
ORDER BY
    articles.description,
    sizes.position,
    modifications.name
";


$listdata = db_query($sql);

/**
 * Export
 */
if ($hassubmit == "export") {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename ."");
    header("Pragma: no-cache");
    header("Expires: 0");

    if($only_loadable) {
        $header = $lang["article"]."\t".$lang["size"]."\t".$lang["max_load"]."\t".$lang["loadable"]."\t";
    } else {
        $header = $lang["article"]."\t".$lang["size"]."\t".$lang["max_load"]."\t".$lang["current_load"]."\t".$lang["loadable"]."\t".$lang["min_load_current_period"]."\t".$lang["min_load_next_period"]."\t".$lang["critical_limit"]."\t";
    }
    $data = "";
    while (!empty($listdata) && $row = db_fetch_array($listdata)) {
        if(!$only_loadable || ($only_loadable && $row["loadable"] > 0)) {
            $line = "";
            if($only_loadable) {
                $in = array(
                    $row["description"],
                    $row["size"],
                    $row["max_load"],
                    $row["loadable"]
                );
            } else {
                $in = array(
                    $row["description"],
                    $row["size"],
                    $row["max_load"],
                    $row["current_load"],
                    $row["loadable"],
                    $row["min_load_current_period"],
                    $row["min_load_next_period"],
                    $row["critical_limit"]
                );
            }
            foreach ($in as $value) {
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
    $data_p = str_replace("\r","",$data);

    print "$header\n$data_p";
    die();
}


// Columns to show
$show = array(
    "description" => true,
    "size" => true,
    "max_load" => true,
    "current_load" => true,
    "loadable" => true,
    "min_load_current_period" => true,
    "min_load_next_period" => true
);

//columns to calculate the percentage of (relative to each percentized column)
$percentize = array();

//columns to colorize (this only works on percentized columns)
$background = array();

//columns which values must be rounded to X digits
$rounding = array();

//columns requiring a suffix, like a percent character
$suffix = array();

//mouseover info: one column can contain multiple values. give a column name
//a subarray of other column names with values that are only either true or false.
//when set true, these 'sub column names' will be shown on mouseover. when set false, they
//will only show if there is actually a value to show.
if ($GLOBALS["config"]["mupapu_enabled"]) { //this is obviously report-specific
    $mouseover_columns = array(
        "max_load" => array (
            "automatically_required" => true,
            "manually_added" => false
        )
    );

    //the title that will appear at the top of each mouseover content
    $mouseover_title = $lang["there_is_a_manually_added_extra_required_load"];

    //the notices that will appear inside a column when a value is present
    $mouseover_notices = array(
        "max_load" => array (
            "automatically_required" => false,
            "manually_added" => "<font style=\"font-weight: bold; color: #880000\">!</font>"
        )
    );
}

//the number of columns (starting left) which content must be aligned
//to the left. any other content will be aligned to the center.
$columns_left = 1;


db_data_seek($listdata, 0);

$deleted_garments_sql = "
    SELECT count(*)
    FROM distributors_load
    WHERE distributor_id IN (select d.id from distributors d INNER JOIN distributorlocations dl ON dl.id = d.distributorlocation_id where dl.circulationgroup_id = " . $urlinfo["cid"] . ")
          and garment_id in (select id from garments where deleted_on is not null)
";

$deleted_garments_r = db_fetch_row(db_query($deleted_garments_sql));
$deleted_garments = $deleted_garments_r[0];

//get the number of yellow/red rows
$total_required_load = 0;
$total_auto_required_load = 0;
$total_manual_required_load = 0;
$total_current_sizebound_load = 0;
$total_yellow_rows = 0;
$total_yellow_garments = 0;
$total_red_rows = 0;
$total_blue_rows = 0;

while ($row = db_fetch_assoc($listdata)) {
    $total_required_load += $row["max_load"];
    $total_auto_required_load += $row["automatically_required"];
    $total_manual_required_load += $row["manually_added"];
    $total_current_sizebound_load += $row["current_load"];
    if (!empty($row["ref__rowcolor"])) {
        if ($row["ref__rowcolor"] == "#FFEEBB") {
            $total_yellow_rows++;
            $total_yellow_garments += $row["current_load"] - $row["max_load"];
        }
        if ($row["ref__rowcolor"] == "#FFCCCC"){ $total_red_rows++; }
        if ($row["ref__rowcolor"] == "#C4D1DA"){ $total_blue_rows++; }
    }
}
db_data_seek($listdata, 0);

//menu string
$menu = "";

$cir_id = $urlinfo["cid"];

 //get the table of periods
$sql = "
    SELECT
        p.from_dayofweek,
        p.from_hours,
        p.from_minutes,
        p.to_dayofweek,
        p.to_hours,
        p.to_minutes,
        -- WEEKDAY(NOW())+1 AS 'weekday',
        -- ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)
        -- HOUR(NOW()) AS 'hours',
        -- MINUTE(NOW()) AS 'minutes',
        IF(
            p.from_dayofweek < p.to_dayofweek,
            p.from_dayofweek * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
            AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60  < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60,
            IF(
                WEEKDAY(NOW())+1 >= p.from_dayofweek,
                    p.from_dayofweek  * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                    AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < 691200,
                        86400 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60
            )
        ) AS 'is_current'
    FROM
        distributionperiods p
    WHERE
        p.circulationgroup_id = $cir_id" . "\n";

    $db_periods_res = db_query($sql);

    $db_periods = array();
    while ($row = db_fetch_assoc($db_periods_res)) {
        $db_periods[] = $row;
    }

    //get time only once
    $now = time();
    //collect periods in from-to format and ordered according to first complete period first.
    $periods = mup_gp($cir_id, $now, $db_periods, 1);


//caption text
$caption = "<div id=\"headerDiv\"><div id=\"headerLeftDiv\" style=\"float: left;\"><div id=\"headerLeftTopDiv\" style=\"float: left;clear:both;\"><div>
<form name=\"showform\" enctype=\"multipart/form-data\" method=\"GET\" action=\"" . $_SERVER["PHP_SELF"] . "\">
    <div class=\"filter\">
        <table>
            <tr>
                <td class=\"name\">". $lang["circulationgroup"] .":</td>
                <td class=\"value\">". html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["make_a_choice"]) ."</td>
            </tr>";
if ($distributorlocation_count > 1) {
    $caption .= "         
        <tr>
            <td class=\"name\">". $lang["location"] .":</td>
            <td class=\"value\">". html_selectbox_submit("did", $distributorlocations, $urlinfo["did"], $lang["(all_locations)"], "style='width:100%'") ."</td>
        </tr>";
}

$caption .= "
            <tr>
                <td class=\"name\">". $lang["article"] .":</td>
                <td class=\"value\">". html_selectbox_submit("aid", $articles, $urlinfo["aid"], $lang["(all_articles)"], "style='width:100%'") ."</td>
            </tr>
            <tr>
                <td class=\"name\">". $lang["size"] .":</td>
                <td class=\"value\">". html_selectbox_submit("sid", $sizes, $urlinfo["sid"], $lang["(all_sizes)"], "style='width:100%'") ."</td>
            </tr>
            <tr>
                <td class=\"name\">". $lang["status"] .":</td>
                <td class=\"value\">". html_selectbox_array_out_submit("status", $statuses, $urlinfo["status"], $lang["(all_statuses)"], "style='width:100%'") ."</td>
            </tr>
        </table>
        <div class=\"buttons\">
            <input type=\"submit\" name=\"hassubmit\" value=\"". $lang["export"]. "\" title=\"". $lang["export"]. "\" />
            <input type=\"submit\" name=\"hassubmit\" value=\"". $lang["export"]. " ". $lang["loadable"]. "\" title=\"". $lang["export"]." ". $lang["loadable"]. "\" />
        </div>
    </div>
</form></div></div>
<div id=\"headerLeftBottomDiv\" class=\"filter\" style=\"float: left;clear:both;\">
<table>";
for($i=0;$i<=1;$i++) {
    if($i==0) $string = "<tr><td>".$lang["current_period"].": </td><td style=\"text-align: right;\">";
    else $string = "<tr><td>".$lang["next_period"].": </td><td style=\"text-align: right;\">";
    
    $string.=$lang[strtolower(lang(date("l", $periods[0][$i]["from"])))]." ";
    $string.=lang(date("d", strtotime("+1 week", $periods[0][$i]["from"])))." "; 
    $string.=$lang[strtolower(lang(date("F", $periods[0][$i]["from"])))]." "; 
    $string.=lang(date("H", $periods[0][$i]["from"]));
    $string.=":";
    $string.=lang(date("i", $periods[0][$i]["from"]))." ";
    $string.="t/m"." ";
    $string.=$lang[strtolower(lang(date("l", $periods[0][$i]["to"])))]." "; 
    $string.=lang(date("d", strtotime("+1 week", $periods[0][$i]["to"])))." "; 
    $string.=$lang[strtolower(lang(date("F", $periods[0][$i]["to"])))]." "; 
    $string.=lang(date("H", $periods[0][$i]["to"]));
    $string.=":";
    $string.=lang(date("i", $periods[0][$i]["to"]));
    $string.="</td></tr>";
    $caption .= $string;
}

$caption .= "</table></div></div>";
$total_current_sizebound_load_without_others = $total_current_sizebound_load - ($total_yellow_garments + $deleted_garments);

$caption .= "<div id=\"headerRightDiv\" class=\"filter\" style=\"margin-left: 15px;float: right;\">";
$caption .= "<table><tr><td>".  lang("__Report_current_load__automatically_required").":</td><td style=\"text-align: right;\"><strong>". $total_auto_required_load ."</strong></td></tr>
             <tr><td>".  lang("__Report_current_load__manually_required").":</td><td style=\"text-align: right;\"><strong>". $total_manual_required_load ."</strong></td></tr>
             <tr><td>".  lang("__Report_current_load__total_required_garments").":</td><td style=\"text-align: right;\"><strong>". $total_required_load ."</strong></td></tr>
             <tr><td>".  lang("__Report_current_load__available_sizebound_positions").":</td><td style=\"text-align: right;\"><strong>". $available_hooks_sizebound ."</strong></td></tr>
             <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
             <tr><td>".  lang("__Report_current_load__garments_which_are_deleted").":</td><td style=\"text-align: right;\"><strong>".$deleted_garments."</strong></td></tr>
             <tr><td>".  lang("__Report_current_load__loaded_sizebound_garments").":</td><td style=\"text-align: right;\"><strong>". $total_current_sizebound_load ."</strong></td></tr>
             <tr><td>".  lang("__Report_current_load__available_sizebound_positions").":</td><td style=\"text-align: right;\"><strong>". $available_hooks_sizebound ."</strong></td></tr>
             <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
             <tr><td>".  lang("__Report_current_load__loaded_userbound_garments").":</td><td style=\"text-align: right;\"><strong>". $total_current_userbound_load ."</strong></td></tr>
             <tr><td>".  lang("__Report_current_load__available_userbound_positions").":</td><td style=\"text-align: right;\"><strong>". $available_hooks_userbound ."</strong></td></tr>
             <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
             <tr><td><span style=\"background-color:#FFCCCC;margin-right:5px;\">&nbsp;&nbsp;&nbsp;</span>".  lang("not_loaded").":</td><td style=\"text-align: right;\"><strong>".$total_red_rows."</strong></td></tr>
             <tr><td><span style=\"background-color:#C4D1DA;margin-right:5px;\">&nbsp;&nbsp;&nbsp;</span>".  lang("lower_then_critical_limit").":</td><td style=\"text-align: right;\"><strong>".$total_blue_rows."</strong></td></tr>
             <tr><td><span style=\"background-color:#FFEEBB;margin-right:5px;\">&nbsp;&nbsp;&nbsp;</span>".  lang("__Report_current_load__not_wanted_garments").":</td><td style=\"text-align: right;\"><strong>".$total_yellow_garments."</strong></td></tr>
              </table>";
$caption .= "</div></div>
<br />";

// Required for selectbox: extra load - distributorlocations
$ext_distributorlocations_conditions["order_by"] = "distributorlocations.name";
$ext_distributorlocations_conditions["inner_join"]["1"] = "circulationgroups circulationgroups.id distributorlocations.circulationgroup_id";
$ext_distributorlocations = db_read("distributorlocations", "distributorlocations.id distributorlocations.name", $ext_distributorlocations_conditions);
$ext_distributorlocation_count = db_num_rows($ext_distributorlocations);

// Required for selectbox: extra load - articles
$ext_articles_conditions["order_by"] = "description";
$ext_articles = db_read("articles", "id description", $ext_articles_conditions);

// Required for selectbox: extra load - sizes
$ext_sizes_conditions["order_by"] = "position";
$ext_sizes = db_read("sizes", "id name", $ext_sizes_conditions);

// Required for selectbox: sizes
$ext_sizes_conditions["left_join"]["1"] = "sizes sizes.id arsimos.size_id";
$ext_sizes_conditions["where"]["1"] = "arsimos.article_id = " . $urlinfo["eaid"];
$ext_sizes_conditions["where"]["2"] = "arsimos.deleted_on is null";
$ext_sizes_conditions["order_by"] = "sizes.position";
$ext_sizes_conditions["group_by"] = "arsimos.size_id";
$ext_sizes = db_read("arsimos", "arsimos.size_id sizes.name", $ext_sizes_conditions);

$caption .= "<div class=\"clear\" />";

/**
 * Show graph string when asked. We will only process values in the "percentize" range.
 */
if (isset($_GET["graph"])) {
    $lines = array();
    $graphrow = 1;

    while ($row = db_fetch_assoc($listdata)) {
        $graphcol = 1;
        foreach ($row as $name => $value) {
            if (isset($percentize[$name])) {
                $lines[] =  "data" . $graphrow . "series" . $graphcol . ": " . $value;
                $graphcol++;
            }
        }
        $graphrow++;
    }

    for ($i = 0; $i < count($lines); $i++) {
        print $lines[$i] . "\n";
    }

    exit(0);
}

/**
 * Generate the page
 */
if (isset($_GET["print"])) {
    include($pi["template"]);
} else {
    $cv = array(
        "pi" => $pi,
        "urlinfo" => $urlinfo,
        "listdata" => $listdata,
        "menu" => $menu,
        "caption" => $caption,
        "show" => $show,
        "percentize" => $percentize,
        "rounding" => $rounding,
        "suffix" => $suffix,
        "background" => $background,
        "mouseover_title" => $mouseover_title,
        "mouseover_columns" => $mouseover_columns,
        "mouseover_notices" => $mouseover_notices,
        "columns_left" => $columns_left
    );

    template_parse($pi, $urlinfo, $cv);
}

?>
