<?php

/**
 * Report MUPAPU to distributor
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
$pi["title"] = $lang["maintenance"] . ": " . $lang["export_loadadvice_to_distributor"];
$pi["filename_this"] = "report_mupapu_to_distrib.php";
$pi["filename_list"] = "report_mupapu.php";
#$pi["filename_next"] = "report_mupapu_to_distrib.php";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico"){
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

$mupapu = (!empty($_POST["mup"])) ? $_POST["mup"] : null;
$continue = (!empty($_POST["continue"])) ? $_POST["continue"] : null;

if ($mupapu && !$continue) {
    $sql = "DELETE FROM `tmp_distributorlocations_loadadvice` WHERE `distributorlocation_id` = 1";
    $q = db_query($sql);

    foreach ($mupapu as $mup => $ars) {
        $insert_arsimo["distributorlocation_id"] = 1;
        $insert_arsimo["arsimo_id"] = $ars["arsimo_id"];
        $insert_arsimo["demand"] = (!empty($ars["demand_new"])) ? $ars["demand_new"] : 0;
        db_insert("tmp_distributorlocations_loadadvice", $insert_arsimo);
    }
} elseif (!$continue) {
    redirect($pi["filename_list"]);
}

$sql = "SELECT
		`tmp_distributorlocations_loadadvice`.`distributorlocation_id` AS `distributorlocation_id`,
		`tmp_distributorlocations_loadadvice`.`arsimo_id` AS 'arsimo_id',

		`articles`.`articlenumber` AS `Code`,
		`articles`.`description` AS `Description`,

		IF(     ISNULL(`modifications`.`id`),
				`sizes`.`name`,
				CONCAT(`sizes`.`name`, ' ', `modifications`.`name`)
		) AS 'Size',

		`tmp_distributorlocations_loadadvice`.`demand` AS 'Count'
	FROM
		`tmp_distributorlocations_loadadvice`
		INNER JOIN arsimos ON tmp_distributorlocations_loadadvice.arsimo_id = arsimos.id
		INNER JOIN articles ON arsimos.article_id = articles.id
		INNER JOIN sizes ON arsimos.size_id = sizes.id
		INNER JOIN sizegroups ON sizes.sizegroup_id = sizegroups.id
		LEFT JOIN modifications ON arsimos.modification_id = modifications.id
	WHERE
	`distributorlocation_id` = 1
";

$listdata = db_query($sql);

if ($continue) {
    $sql = "DELETE FROM `distributorlocations_loadadvice` WHERE `distributorlocation_id` = 1 AND type LIKE 'auto'";
    $q = db_query($sql);

    while ($row = db_fetch_assoc($listdata)) {
        $insert_arsimo["distributorlocation_id"] = 1;
        $insert_arsimo["arsimo_id"] = $row["arsimo_id"];
        $insert_arsimo["demand"] = (!empty($row["Count"])) ? $row["Count"] : 0;
        $insert_arsimo["critical_percentage"] = "0.33";
        $insert_arsimo["type"] = "auto";
        db_insert("distributorlocations_loadadvice", $insert_arsimo);
    }
}

//columns to show
$show = array(
    "Articlegroup" => true,
    "Description" => true,
    "Size" => true,
    "Count" => true,
    //"Debug" => true
);

//columns to calculate the percentage of (relative to each percentized column)
$percentize = array();

//columns to colorize (this only works on percentized columns)
$background = array();

//columns which values must be rounded to X digits
$rounding = array();

//columns requiring a suffix
$prefix = array("Count" => "<strong>");

//columns requiring a suffix, like a percent character
$suffix = array("Count" => "</strong>");

//the number of columns (starting left) which content must be aligned
//to the left. any other content will be aligned to the center.
$columns_left = 1;

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
$menu = "";

//caption text
if ($continue) {
    $caption = "De beladingsreservering is aangepast.";
} else {
    $caption = 'Als u doorgaat worden er <b>' . $totals['Count'] . '</b> haken gereserveerd voor onderstaande dragergebonden kleding.
            <form id="loadadvice_to_distributor" enctype="multipart/form-data" method="POST" action="' . $pi['filename_details'] . '">
            <input type="submit" name="cancel" value="' . $lang["back"] . '" title="' . $lang["back"] . '" onclick="this.form.action=\'report_mupapu.php\'; this.form.target=\'_self\';">
            <input type="submit" name="continue" value="' . $lang["continue"] . '" title="' . $lang["continue"] . '">
            </form>';
}

/**
 * Show graph string when asked. We will only process values in the 'percentize' range.
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
if(isset($_GET["print"])) {
    include($pi["template"]);
} else {
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
        "prefix" => $prefix,
        "suffix" => $suffix,
        "background" => $background,
        "columns_left" => $columns_left,
        "grand_total" => $grand_total
    );

    template_parse($pi, $urlinfo, $cv);
}

?>
