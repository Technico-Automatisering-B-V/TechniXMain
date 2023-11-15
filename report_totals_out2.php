<?php

/**
 * Report totals 2
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
$pi["filename_this"] = "report_totals_out2.php";
$pi["filename_list"] = "report_totals.php";
$pi["filename_next"] = "";

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

if (!empty($_GET["sec"]) && is_numeric($_GET["sec"])) $sec = $_GET["sec"];
else {
	//we use the circulationgroup_id of the top name in our selectbox (which is alphabetically sorted).
	$selected_circulationgroup_conditions["order_by"] = "name";
	$selected_circulationgroup_conditions["limit_start"] = 0;
	$selected_circulationgroup_conditions["limit_num"] = 1;
	$sec = db_fetch_row(db_read("circulationgroups", "id", $selected_circulationgroup_conditions));
	$sec = $sec[0];
}

//required for selectbox: circulationgroups
$circulationgroups_conditions["order_by"] = "name";
$circulationgroups = db_read("circulationgroups", "id name", $circulationgroups_conditions);

//fetch the selected circulationgroup's name
$circulationgroup_name = db_fetch_row(db_read_where("circulationgroups", "id", $sec));
$circulationgroup_name = $circulationgroup_name[1];

$sql_v = "DROP VIEW IF EXISTS report";
$q_v = db_query($sql_v);

$sql_c = "
CREATE VIEW report AS
	SELECT
	arsimos.id AS 'arsimo_id',
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
	INNER JOIN circulationgroups ON garments.circulationgroup_id = circulationgroups.id
	WHERE arsimos.deleted_on IS NULL AND garments.deleted_on IS NULL
	AND circulationgroups.id = " . $sec . "
	GROUP BY arsimos.id;
";

$q_c = db_query($sql_c);

$sql = "
	SELECT
	" . $sec . " AS 'sec__id',
	arsimos.id AS 'ref__id',
	articles.articlenumber AS 'Article',
	articles.description AS 'description',
	IF(ISNULL(modifications.id), sizes.name, CONCAT(sizes.name, ' ', modifications.name)) AS 'size',
	COALESCE(report.sum_unknown,0) AS 'never_scanned',
	COALESCE(report.sum_missing,0) AS 'missing',
	COALESCE(report.sum_stock_hospital,0) AS 'stock_hospital',
	COALESCE(report.sum_stock_laundry,0) AS 'stock_laundry',
	COALESCE(report.sum_selfcleaning,0) AS 'homewash',
	COALESCE(report.sum_repair,0)  AS 'repair',
	COALESCE(report.sum_despeckle,0) AS 'despeckle',
	COALESCE(report.sum_disconnected,0) AS 'disconnected_from_garmentuser',
        COALESCE(report.total,0)  AS 'total'
	FROM
	arsimos
	INNER JOIN articles ON arsimos.article_id = articles.id
	INNER JOIN sizes ON arsimos.size_id = sizes.id
	LEFT JOIN modifications ON arsimos.modification_id = modifications.id
	LEFT JOIN report ON report.arsimo_id = arsimos.id
	WHERE arsimos.deleted_on IS NULL
	HAVING `total` > 0
	ORDER BY articles.description, sizes.position;
";

$listdata = ($sec) ? db_query($sql) : null;

$sql_v = "DROP VIEW IF EXISTS report";
$q_v = db_query($sql_v);

//columns to show
$show = array(
    "description" => true,
    "size" => true,
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
#if ($ua_li) $show["Uit"] = true;

//columns to calculate the percentage of (relative to each percentized column)
$percentize = array(
    "never_scanned" => true,
    "missing" => true,
    "stock_hospital" => true,
    "stock_laundry" => true,
    "homewash" => true,
    "repair" => true,
    "despeckle" => true,
    "disconnected_from_garmentuser" => true
);
#if ($ua_li) $percentize["Uit"] = true;

//columns to colorize (this only works on percentized columns)
$background = array(
    "never_scanned" => "blue",
    "missing" => "blue",
    "stock_hospital" => "blue",
    "stock_laundry" => "blue",
    "homewash" => "blue",
    "repair" => "blue",
    "despeckle" => "blue",
    "disconnected_from_garmentuser" => "blue"
);
#if ($ua_li) $background["Uit"] = "yellow";

//columns which values must be rounded to X digits
$rounding = array(
    "never_scanned" => 0,
    "missing" => 0,
    "stock_hospital" => 0,
    "stock_laundry" => 0,
    "despeckle" => 0,
    "repair" => 0,
    "homewash" => 0,
    "disconnected_from_garmentuser" => 0
);
#if ($ua_li) $rounding["Uit"] = 0;

//columns requiring a suffix, like a percent character
$suffix = array(
    "never_scanned" => "%",
    "missing" => "%",
    "stock_hospital" => "%",
    "stock_laundry" => "%",
    "homewash" => "%",
    "repair" => "%",
    "despeckle" => "%",
    "disconnected_from_garmentuser" => "%"
);
#if ($ua_li) $suffix["Uit"] = "%";

//mouseover info: one column can contain multiple values. give a column name
//a subarray of other column names with values that are only either true or false.
//when set true, these 'sub column names' will be shown on mouseover. when set false, they
//will only show if there is actually a value to show.
$mouseover_columns = array(
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
$mouseover_notices = array(
);

//the number of columns (starting left) which content must be aligned
//to the left. any other content will be aligned to the center.
$columns_left = 2;

//set $totals to an array of totals (of each column).
//also set $grand_total to the total of all totals.
$grand_total = 0;

while ($row = db_fetch_assoc($listdata)) {
    foreach ($row as $col => $value) {
        if (is_numeric($value)) {
            if (!isset($totals[$col])){ $totals[$col] = 0; }
            $totals[$col] += $value;
        } else {
            if (!isset($totals[$col])){ $totals[$col] = null; }
        }
    }
}

db_data_seek($listdata, 0);

//menu string
$menu = "<a href=\"report_totals.php\">". $lang["total"] ."</a> &raquo;
     <strong>". $lang["article_totals"] ."</strong>";

//caption text
$caption = $lang["you_see_circulationtotal_article_location"] . ' <strong>' . $circulationgroup_name . '</strong>. ';

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
                $lines[] = "data" . $graphrow . "series" . $graphcol . ": " . $value;
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
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "menu" => $menu,
    "caption" => $caption,
    "totals" => $totals,
    "show" => $show,
    "percentize" => $percentize,
    "rounding" => $rounding,
    "suffix" => $suffix,
    "mouseover_title" => $mouseover_title,
    "mouseover_columns" => $mouseover_columns,
    "mouseover_notices" => $mouseover_notices,
    "background" => $background,
    "columns_left" => $columns_left,
    "grand_total" => $grand_total
);

template_parse($pi, $urlinfo, $cv);

?>
