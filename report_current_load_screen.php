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
$pi["template"] = "layout/pages/report_template_screen.tpl";
$pi["page"] = "simple";
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

$loading_screen_sort_sql = "
    SELECT sort FROM loading_screens
       WHERE `circulationgroup_id` = ". $urlinfo["cid"];

$loading_screen_sort_r = db_fetch_row(db_query($loading_screen_sort_sql));
$loading_screen_sort = $loading_screen_sort_r[0];

if(empty($loading_screen_sort)) $loading_screen_sort = "article";

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
ORDER BY ";

if($loading_screen_sort === "loadable") {
    $sql .= " COALESCE(SUM(tmp3.demand),0)-COALESCE(SUM(tmp3.sizebound_count),0) DESC, articles.description,
    sizes.position,
    modifications.name";
} else {
    $sql .= " articles.description,
    sizes.position,
    modifications.name";
}

$listdata = db_query($sql);

//columns to calculate the percentage of (relative to each percentized column)
$percentize = array();

//columns to colorize (this only works on percentized columns)
$background = array();

//columns which values must be rounded to X digits
$rounding = array();

//columns requiring a suffix, like a percent character
$suffix = array();

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

$data = "";
$num_rows = 1;

$tdata[1] = "<table class='list' style=\"display: inline-block;vertical-align:top;margin-right: 8px;\">";
$tdata[1] .= "<thead><tr class='listtitle'><th class='list'>".$lang["description"]."</th>";
$tdata[1] .= "<th class='list'>".$lang["size"]."</th>";
$tdata[1] .= "<th class='list'>Maximale be.</th>";
$tdata[1] .= "<th class='list'>Huidige be.</th>";
$tdata[1] .= "<th class='list'>".$lang["loadable"]."</th></thead>";


$tdata[2] = $tdata[3] = $tdata[4]= $tdata[5]= $tdata[1];

while ($row = db_fetch_assoc($listdata)) {
         $data = "<tr class=\"list\"";

	if(!empty($row["ref__rowcolor"])){
		$data .=" style=\"background-color:". $row["ref__rowcolor"] ."; color:#000;\"";
	}

        $data .= "><td class=\"list\">". $row["description"] ."</td>"
                 . "<td class=\"list\">". $row["size"] ."</td>"
                 . "<td class=\"list\">". $row["max_load"] ."</td>"
                 . "<td class=\"list\">". $row["current_load"] ."</td>"
                 . "<td class=\"list\">". $row["loadable"] ."</td>"
                 . "</tr>";   

if($num_rows > 120) {
	$tdata[5] .= $data;
} else if ($num_rows > 90) {
	$tdata[4] .= $data;
} else if ($num_rows > 60) {
	$tdata[3] .= $data;
} else if ($num_rows > 30) {
	$tdata[2] .= $data;
} else {
	$tdata[1] .= $data;
}

$num_rows++;
}
$tdata[1] .= "</table>";
$tdata[2] .= "</table>";
$tdata[3] .= "</table>";
$tdata[4] .= "</table>";
$tdata[5] .= "</table>";

$data = $tdata[1] . $tdata[2] . $tdata[3]. $tdata[4]. $tdata[5];

//caption text
$caption = "<div id=\"headerDiv\">";
$total_current_sizebound_load_without_others = $total_current_sizebound_load - ($total_yellow_garments + $deleted_garments);             

$caption .= "<div id=\"headerRightDiv\" class=\"filter\" >";
$caption .= "<table>"
        . "<tr>"
        . "<td>".  lang("__Report_current_load__automatically_required").":</td>"
        . "<td style=\"text-align: right;\"><strong>". $total_auto_required_load ."</strong></td>"
        . "<td>&nbsp;</td>"
        . "<td>".  lang("__Report_current_load__garments_which_are_deleted").":</td>"
        . "<td style=\"text-align: right;\"><strong>".$deleted_garments."</strong></td>"
        . "<td>&nbsp;</td>"
        . "<td><span style=\"background-color:#FFCCCC;margin-right:5px;\">&nbsp;&nbsp;&nbsp;</span>".  lang("not_loaded").":</td>"
        . "<td style=\"text-align: right;\"><strong>".$total_red_rows."</strong></td>
            </tr>
             <tr>
             <td>".  lang("__Report_current_load__manually_required").":</td>"
        . "<td style=\"text-align: right;\"><strong>". $total_manual_required_load ."</strong></td>"
        . "<td>&nbsp;</td>"
        . "<td>".  lang("__Report_current_load__loaded_sizebound_garments").":</td>"
        . "<td style=\"text-align: right;\"><strong>". $total_current_sizebound_load ."</strong></td>"
        . "<td>&nbsp;</td>"
        . "<td><span style=\"background-color:#C4D1DA;margin-right:5px;\">&nbsp;&nbsp;&nbsp;</span>".  lang("lower_then_critical_limit").":</td>"
        . "<td style=\"text-align: right;\"><strong>".$total_blue_rows."</strong></td>
            </tr>
             <tr>
             <td>".  lang("__Report_current_load__total_required_garments").":</td>"
        . "<td style=\"text-align: right;\"><strong>". $total_required_load ."</strong></td>"
        . "<td>&nbsp;</td>"
        . "<td>".  lang("__Report_current_load__available_sizebound_positions").":</td>"
        . "<td style=\"text-align: right;\"><strong>". $available_hooks_sizebound ."</strong></td>"
        . "<td>&nbsp;</td>"
        . "<td><span style=\"background-color:#FFEEBB;margin-right:5px;\">&nbsp;&nbsp;&nbsp;</span>".  lang("__Report_current_load__not_wanted_garments").":</td>"
        . "<td style=\"text-align: right;\"><strong>".$total_yellow_garments."</strong></td>
            </tr>
             <tr>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td>".  lang("__Report_current_load__loaded_userbound_garments").":</td>"
        . "<td style=\"text-align: right;\"><strong>". $total_current_userbound_load ."</strong></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            </tr>
             <tr>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td>".  lang("__Report_current_load__available_userbound_positions").":</td>"
        . "<td style=\"text-align: right;\"><strong>". $available_hooks_userbound ."</strong></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            </tr>
              </table>";
$caption .= "</div></div>
<br />";

$caption .= "<div style=\"display: table;margin-right: auto;margin-left: auto;\" class=\"clear\" />".$data;


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
