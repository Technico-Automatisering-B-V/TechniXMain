<?php

/**
 * Report totals
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
$pi["access"] = array("reports", "total_report");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["circulation"];
$pi["template"] = "layout/pages/report_template.tpl";
$pi["page"] = "simple";
$pi["filename_this"] = "report_totals.php";
$pi["filename_list"] = "report_totals.php";
$pi["filename_next"] = "report_totals2.php";
$pi["filename_next2"] = "report_totals_out2.php";

/**
 * Check authorization to view the page
 */
if (!user_has_access_to($pi["access"])) {
    redirect("login.php");
}

/**
 * Collect page content
 */
$urlinfo = array();

$sql_v = "DROP VIEW IF EXISTS report_totals";
$q_v = db_query($sql_v);

$sql_c = "
CREATE VIEW report_totals AS
    SELECT
    circulationgroups.name AS circulationgroup_name,
    circulationgroups.id AS circulationgroup_id,
    SUM( scanlocationstatuses.name = 'conveyor' ) AS 'sum_conveyor',
    SUM( scanlocationstatuses.name = 'loaded' ) AS 'sum_stock',
    SUM( scanlocationstatuses.name = 'rejected' ) AS 'sum_rejected',
    SUM( scanlocationstatuses.name = 'distributed') AS 'sum_distributed',
    SUM( scanlocationstatuses.name = 'deposited' ) AS 'sum_deposited',
    SUM( scanlocationstatuses.name = 'container' ) AS 'sum_container',
    SUM( scanlocationstatuses.name = 'transport_to_laundry' ) AS 'sum_transport_to_laundry',
    SUM( scanlocationstatuses.name = 'laundry') AS 'sum_laundry',
    SUM( scanlocationstatuses.name = 'laundry' AND ss.`value` = 'INSCAN') AS 'sum_laundry_inscan',
    SUM( scanlocationstatuses.name = 'laundry' AND ss.`value` = 'OUTSCAN' ) AS 'sum_laundry_outscan',
    SUM( scanlocationstatuses.name = 'laundry' AND ss.`value` = 'REPAIR' ) AS 'sum_laundry_repair',
    SUM( scanlocationstatuses.name = 'laundry' AND ss.`value` = 'REWASH' ) AS 'sum_laundry_rewash',
    SUM( scanlocations.circulationgroup_id IS NOT NULL ) AS 'total'
    FROM
    arsimos
    INNER JOIN garments ON garments.arsimo_id = arsimos.id
    INNER JOIN scanlocations ON garments.scanlocation_id = scanlocations.id
    INNER JOIN scanlocationstatuses ON scanlocations.scanlocationstatus_id = scanlocationstatuses.id
    INNER JOIN circulationgroups ON circulationgroups.id = garments.circulationgroup_id
    LEFT JOIN sub_scanlocations ss ON ss.id = garments.sub_scanlocation_id
    WHERE arsimos.deleted_on IS NULL AND garments.deleted_on IS NULL
    GROUP BY circulationgroups.id";

$q_c = db_query($sql_c);

$sql = "
    SELECT
    circulationgroup_name AS 'location',
    circulationgroup_id  AS 'sec__id',
    sum_conveyor AS 'conveyor',
    sum_stock AS 'loaded',
    sum_rejected AS 'rejected',
    sum_distributed AS 'garmentuser',
    sum_deposited AS 'deposit',
    sum_container AS 'transport',
    sum_transport_to_laundry AS 'transport_to_laundry',
    sum_laundry AS 'laundry',
    sum_laundry_inscan AS 'laundry_inscan',
    sum_laundry_outscan AS 'laundry_outscan',
    sum_laundry_repair AS 'laundry_repair',
    sum_laundry_rewash AS 'laundry_rewash',
    total AS 'total'
    FROM report_totals;
";

$listdata = db_query($sql);

$sql_v = "DROP VIEW IF EXISTS report_totals";
$q_v = db_query($sql_v);

$sql_c = "
CREATE VIEW report_totals AS
        SELECT
    circulationgroups.name AS circulationgroup_name,
    circulationgroups.id AS circulationgroup_id,
    SUM( scanlocationstatuses.name = 'never_scanned') AS 'sum_unknown',
    SUM( scanlocationstatuses.name = 'missing') AS 'sum_missing',
    SUM( scanlocationstatuses.name = 'stock_hospital') AS 'sum_stock_hospital',
    SUM( scanlocationstatuses.name = 'stock_laundry') AS 'sum_stock_laundry',
    SUM( scanlocationstatuses.name = 'homewash') AS 'sum_selfcleaning',
    SUM( scanlocationstatuses.name = 'repair') AS 'sum_repair',
    SUM( scanlocationstatuses.name = 'despeckle') AS 'sum_despeckle',
    SUM( scanlocationstatuses.name = 'disconnected_from_garmentuser') AS 'sum_disconnected',
    SUM( scanlocations.circulationgroup_id IS NULL ) AS 'total'
    FROM
    arsimos
    INNER JOIN garments ON garments.arsimo_id = arsimos.id
    INNER JOIN scanlocations ON garments.scanlocation_id = scanlocations.id
    INNER JOIN scanlocationstatuses ON scanlocations.scanlocationstatus_id = scanlocationstatuses.id
    INNER JOIN circulationgroups ON circulationgroups.id = garments.circulationgroup_id
    WHERE arsimos.deleted_on IS NULL AND garments.deleted_on IS NULL
    GROUP BY circulationgroups.id";

$q_c = db_query($sql_c);

$sql = "
    SELECT
    circulationgroup_name AS 'location',
    circulationgroup_id  AS 'sec__id',
    sum_unknown AS 'never_scanned',
    sum_missing AS 'missing',
    sum_stock_hospital AS 'stock_hospital',
    sum_stock_laundry AS 'stock_laundry',
    sum_selfcleaning AS 'homewash',
    sum_repair AS 'repair',
    sum_despeckle AS 'despeckle',
    sum_disconnected AS 'disconnected_from_garmentuser',
    total AS 'total'
    FROM report_totals;
";

$listdata2 = db_query($sql);

$sql_v = "DROP VIEW IF EXISTS report_totals";
$q_v = db_query($sql_v);

//columns to show
$show = array(
    //"_" => true,
    "location" => true,
    "garmentuser" => true,
    "conveyor" => true,
    "deposit" => true,
    "transport" => true,
    "transport_to_laundry" => true,
    "laundry" => true,
    "rejected" => true,
    "loaded" => true,
    "total" => true
);

//columns to show
$show2 = array(
    //"_" => true,
    "location" => true,
    "never_scanned" => true,
    "missing" => true,
    "stock_hospital" => true,
    "stock_laundry" => true,
    "homewash" => true,
    "repair" => true,
    "despeckle" => true,
    "disconnected_from_garmentuser" => true,
    "total" => true
);

//columns to calculate the percentage of (relative to each percentized column)
$percentize = array(
    "garmentuser" => true,
    "conveyor" => true,
    "deposit" => true,
    "transport" => true,
    "transport_to_laundry" => true,
    "laundry" => true,
    "rejected" => true,
    "loaded" => true
);

//columns to calculate the percentage of (relative to each percentized column)
$percentize2 = array(
    "never_scanned" => true,
    "missing" => true,
    "stock_hospital" => true,
    "stock_laundry" => true,
    "homewash" => true,
    "repair" => true,
    "despeckle" => true,
    "disconnected_from_garmentuser" => true
);

//columns to colorize (this only works on percentized columns)
$background = array(
    "garmentuser" => "blue",
    "conveyor" => "blue",
    "deposit" => "blue",
    "transport" => "blue",
    "transport_to_laundry" => "blue",
    "laundry" => "blue",
    "rejected" => "blue",
    "loaded" => "blue"
);

//columns to colorize (this only works on percentized columns)
$background2 = array(
    "never_scanned" => "blue",
    "missing" => "blue",
    "stock_hospital" => "blue",
    "stock_laundry" => "blue",
    "homewash" => "blue",
    "repair" => "blue",
    "despeckle" => "blue",
    "disconnected_from_garmentuser" => "blue"
);

//columns which values must be rounded to X digits
$rounding = array(
    "garmentuser" => 0,
    "conveyor" => 0,
    "deposit" => 0,
    "transport" => 0,
    "transport_to_laundry" => 0,
    "loaded" => 0,
    "rejected" => 0,
    "laundry" => 0
);

//columns which values must be rounded to X digits
$rounding2 = array(
    "never_scanned" => 0,
    "missing" => 0,
    "stock_hospital" => 0,
    "stock_laundry" => 0,
    "homewash" => 0,
    "repair" => 0,
    "despeckle" => 0,
    "disconnected_from_garmentuser" => 0
);

//columns requiring a suffix, like a percent character
$suffix = array(
    "garmentuser" => "%",
    "conveyor" => "%",
    "deposit" => "%",
    "transport" => "%",
    "transport_to_laundry" => "%",
    "laundry" => "%",
    "rejected" => "%",
    "loaded" => "%"
);

//columns requiring a suffix, like a percent character
$suffix2 = array(
    "never_scanned" => "%",
    "missing" => "%",
    "stock_hospital" => "%",
    "stock_laundry" => "%",
    "homewash" => "%",
    "repair" => "%",
    "despeckle" => "%",
    "disconnected_from_garmentuser" => "%"
);

//only rows with this column name being true are shown (fook-2.0-ultra-hack)
$only_true = "sec__id";

//mouseover info: one column can contain multiple values. give a column name
//a subarray of other column names with values that are only either true or false.
//when set true, these 'sub column names' will be shown on mouseover. when set false, they
//will only show if there is actually a value to show.
$mouseover_columns = array(
    "garmentuser" => array("garmentuser" => true),
    "conveyor" => array("conveyor" => true),
    "deposit" => array("deposit" => true),
    "transport" => array("transport" => true),
    "transport_to_laundry" => array("transport_to_laundry" => true),
    "laundry" => array("laundry_inscan" => true, "laundry_outscan" => true, "laundry_repair" => true, "laundry_rewash" => true),
    "rejected" => array("rejected" => true),
    "loaded" => array("loaded" => true)
);

$mouseover_columns2 = array(
    "never_scanned" => array("never_scanned" => true),
    "missing" => array("missing" => true),
    "stock_hospital" => array("stock_hospital" => true),
    "stock_laundry" => array("stock_laundry" => true),
    "homewash" => array("homewash" => true),
    "repair" => array("repair" => true),
    "despeckle" => array("despeckle" => true),
    "disconnected_from_garmentuser" => array("disconnected_from_garmentuser" => true)
);

//the title that will appear at the top of each mouseover content
$mouseover_title = "";

//the notices that will appear inside a column when a value is present
$mouseover_notices = array();

//the title that will appear at the top of each mouseover content
$mouseover_title2 = "";

//the notices that will appear inside a column when a value is present
$mouseover_notices2 = array();

//the number of columns (starting left) which content must be aligned
//to the left. any other content will be aligned to the center.
$columns_left = 1;
$columns_left2 = 1;

//the height of each row (uses default if not set)
$row_height = 30;

//set $totals to an array of totals (of each column).
//also set $grand_total to the total of all totals.
$grand_total = 0;

while ($row = db_fetch_assoc($listdata)) {
    foreach ($row as $name => $value) {
        if (is_numeric($value)) {
            if (!isset($totals[$name])){ $totals[$name] = 0; }
            $totals[$name] += $value;
        } else {
            if (!isset($totals[$name])){ $totals[$name] = null; }
        }
    }
}

db_data_seek($listdata, 0);

//menu string
$menu = "<strong>". $lang["total"] ."</strong> &raquo;
     <a href=\"report_totals2.php\">". $lang["article_totals"] ."</a> &raquo;
     <font color=\"grey\">" . $lang["garmentusers_per_article"] ."</font> &raquo;
     <font color=\"grey\">" . $lang["usage_per_garmentuser"] . "</font>
    ";

//caption text
$caption = $lang["you_see_circulationtotals"] . " " . $lang["click_location_details"];

/**
 * Show graph string when asked. We will only process values in the 'percentize' range.
 */

if (isset($_GET['graph'])) {
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

$tableheader = "circulating";
$tableheader2 = "out_circulation";

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "listdata2" => $listdata2,
    "menu" => $menu,
    "caption" => $caption,
    "totals" => $totals,
    "show" => $show,
    "show2" => $show2,
    "percentize" => $percentize,
    "percentize2" => $percentize2,
    "rounding" => $rounding,
    "rounding2" => $rounding2,
    "suffix" => $suffix,
    "suffix2" => $suffix2,
    "background" => $background,
    "background2" => $background2,
    "columns_left" => $columns_left,
    "columns_left2" => $columns_left2,
    "row_height" => $row_height,
    "only_true" => $only_true,
    "grand_total" => $grand_total,
    "mouseover_title" => $mouseover_title,
    "mouseover_title2" => $mouseover_title2,
    "mouseover_columns" => $mouseover_columns,
    "mouseover_columns2" => $mouseover_columns2,
    "mouseover_notices" => $mouseover_notices,
    "mouseover_notices2" => $mouseover_notices2,
    "tableheader" => $tableheader,
    "tableheader2" => $tableheader2
);

template_parse($pi, $urlinfo, $cv);

?>
