<?php

/**
 * Report totals 4
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
$pi["filename_this"] = "report_totals4.php";
$pi["filename_list"] = "report_totals.php";
$pi["filename_next"] = "garment_details.php";

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

$ref = (!empty($_GET["ref"]) && is_numeric($_GET["ref"])) ? $_GET["ref"] : null;

$sql = ($ref) ? "
    SELECT
        `arsimos`.`id` AS 'ref_back__id',
        `garmentusers_garments`.`garmentuser_id` AS 'ref__id',
        `garmentusers`.`gender` AS 'gender',
        `garmentusers`.`initials` AS 'initials',
        `garmentusers`.`intermediate` AS 'intermediate',
        `garmentusers`.`surname` AS 'surname',
        `garmentusers`.`maidenname` AS 'maidenname',
        `garmentusers`.`title` AS 'title',
        `garmentusers`.`personnelcode` AS 'personnelcode',
        `articles`.`description` AS 'description',
        IF(
            ISNULL(modifications.id), sizes.name, CONCAT(sizes.name, ' ',modifications.name)
        ) AS 'size',
        `garments`.`id` AS 'ref__id',
        `garments`.`tag` AS 'tag',
        `garmentusers_garments`.`date_received` AS 'in_use_since',
        DATEDIFF(NOW(), `garmentusers_garments`.`date_received`) 'days'
    FROM
        `garmentusers_garments`
    INNER JOIN distributors ON garmentusers_garments.distributor_id = distributors.id
    INNER JOIN distributorlocations ON distributors.distributorlocation_id = distributorlocations.id
    INNER JOIN garmentusers ON garmentusers_garments.garmentuser_id = garmentusers.id
    INNER JOIN garments ON garmentusers_garments.garment_id = garments.id
    INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
    INNER JOIN articles ON arsimos.article_id = articles.id
    INNER JOIN sizes ON arsimos.size_id = sizes.id
    LEFT JOIN modifications ON arsimos.modification_id = modifications.id
    WHERE garmentusers_garments.garmentuser_id = " . $ref . "
    ORDER BY garmentusers_garments.date_received, articles.description
" : null;

$listdata = ($ref) ? db_query($sql) : null;

//columns to show
$show = array(
    "description" => true,
    "size" => true,
    "tag" => true,
    "in_use_since" => true,
    "days" => true
);

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
$columns_left = 2;

//set $totals to an array of totals (of each column).
//also set $grand_total to the total of all totals.
$grand_total = 0;
while ($ref && $row = db_fetch_assoc($listdata)) {
    foreach ($row as $name => $value) {
        if (is_numeric($value)) {
            if (!isset($totals[$name])){ $totals[$name] = 0; }
            $totals[$name] += $value;
        } else {
            if (!isset($totals[$name])){ $totals[$name] = null; }
        }
    }
}

if ($ref){ db_data_seek($listdata, 0); }

//menu string
$menu = "<a href=\"report_totals.php\">" . $lang["total"] . "</a> &raquo;
    <a href=\"report_totals2.php\">" . $lang["article_totals"] . "</a> &raquo;
    <font color=\"grey\">Dragers per artikel</font> &raquo;
    <strong>Gebruik per drager</strong>
";

//caption text
if ($ref) {
    $firstrow = db_fetch_assoc($listdata);
    $caption = "U ziet de kleding op dit moment in bezit door drager: <strong>" . generate_garmentuser_label($firstrow["title"], $firstrow["gender"], $firstrow["initials"], $firstrow["intermediate"], $firstrow["surname"], $firstrow["maidenname"], $firstrow["personnelcode"]) . "</strong>";
} else {
    $firstrow = null;
    $caption = "Dit artikel is niet in gebruik door dragers.";
}

if ($ref){ db_data_seek($listdata, 0); }

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
    "background" => $background,
    "columns_left" => $columns_left,
    "grand_total" => $grand_total
);

template_parse($pi, $urlinfo, $cv);

?>
