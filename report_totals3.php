<?php

/**
 * Report totals 3
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
$pi["filename_this"] = "report_totals3.php";
$pi["filename_list"] = "report_totals.php";
$pi["filename_next"] = "report_totals4.php";

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
$sec = (!empty($_GET["sec"]) && is_numeric($_GET["sec"])) ? $_GET["sec"] : null;

$sql = ($ref && $sec) ? "
	SELECT
		-- articles.articlenumber AS 'Code',
		circulationgroups.`name` AS 'Locatie',
		articles.description AS 'Omschrijving',
		IF(ISNULL(modifications.id), sizes.name, CONCAT(sizes.name, ' ',modifications.name)) AS 'Maat',
		garmentusers.id AS 'ref__id',
		CONCAT( garmentusers.surname,
                    IF(ISNULL(garmentusers.maidenname), '', CONCAT('-',garmentusers.maidenname,', ')),
                    IF(ISNULL(garmentusers.initials), '', CONCAT(' ',garmentusers.initials)),
                    IF(ISNULL(garmentusers.intermediate), '', CONCAT(' ',garmentusers.intermediate)),
                    IF(ISNULL(garmentusers.name), '', CONCAT(', ',garmentusers.name))
		) AS " . $lang['garmentuser'] . ",
		IF(tmp3.userbound=0, 'Maatgebonden', 'Dragergebonden') AS 'Type',
		tmp3.numgarments AS " . $lang['count'] . ",
		tmp3.firstdate AS '" . $lang['longest_in_use'] . "'
	FROM
	(
        SELECT tmp2.*, tmp1.firstdate
        FROM
        (
            SELECT
            distributorlocations.circulationgroup_id,
            garments.arsimo_id,
            IF(ISNULL(garments.garmentuser_id), 0, 1) 'userbound',
            garmentusers_garments.garmentuser_id,
            MIN(garmentusers_garments.date_received) 'firstdate'
            FROM
            garmentusers_garments
            INNER JOIN garments ON garmentusers_garments.garment_id = garments.id
            INNER JOIN distributors ON garmentusers_garments.distributor_id = distributors.id
            INNER JOIN distributorlocations ON distributors.distributorlocation_id = distributorlocations.id
            GROUP BY
            garments.arsimo_id,
            garmentusers_garments.garmentuser_id
	) tmp1
	INNER JOIN
	(
            SELECT
            distributorlocations.circulationgroup_id,
            garments.arsimo_id,
            IF(ISNULL(garments.garmentuser_id), 0, 1) 'userbound',
            garmentusers_garments.garmentuser_id,
            COUNT(garmentusers_garments.garment_id) 'numgarments'
            FROM
            garmentusers_garments
            INNER JOIN garments ON garmentusers_garments.garment_id = garments.id
            INNER JOIN distributors ON garmentusers_garments.distributor_id = distributors.id
            INNER JOIN distributorlocations ON distributors.distributorlocation_id = distributorlocations.id
            GROUP BY
            garments.arsimo_id,
            garmentusers_garments.garmentuser_id
            ) tmp2 ON tmp1.circulationgroup_id  = tmp2.circulationgroup_id
                AND tmp1.arsimo_id = tmp2.arsimo_id
                AND tmp1.userbound = tmp2.userbound
                AND tmp1.garmentuser_id = tmp2.garmentuser_id
	) tmp3
	INNER JOIN circulationgroups ON tmp3.circulationgroup_id = circulationgroups.id
	INNER JOIN garmentusers ON tmp3.garmentuser_id = garmentusers.id
	INNER JOIN arsimos ON tmp3.arsimo_id = arsimos.id
	INNER JOIN articles ON arsimos.article_id = articles.id
	INNER JOIN sizes ON arsimos.size_id = sizes.id
	LEFT JOIN modifications ON arsimos.modification_id = modifications.id
	WHERE arsimos.id = " . $ref . "
          AND circulationgroups.id = " . $sec . "
" : null;

$listdata = ($ref && $sec) ? db_query($sql) : null;

//columns to show
$show = array(
    $lang["garmentuser"] => true,
    "Type" => true,
    $lang["longest_in_use"] => true,
    $lang["count"] => true
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
$columns_left = 1;

//set $totals to an array of totals (of each column).
//also set $grand_total to the total of all totals.
$grand_total = 0;
if (db_num_rows($listdata)) while ($row = db_fetch_assoc($listdata)) {
    foreach ($row as $name => $value) {
        if (is_numeric($value)) {
            if (!isset($totals[$name])){ $totals[$name] = 0; }
            $totals[$name] += $value;
        } else {
            if (!isset($totals[$name])){ $totals[$name] = null; }
        }
    }
}

if (db_num_rows($listdata)) db_data_seek($listdata, 0);

//menu string
$menu = "<a href=\"report_totals.php\">" . $lang["total"] . "</a> &raquo;
	 <a href=\"report_totals2.php\">" . $lang["article_totals"] . "</a> &raquo;
	 <strong>" . $lang["garmentusers_per_article"] . "</strong> &raquo;
	 <font color=\"grey\">" . $lang["usage_per_garmentuser"] . "</font>
	";

//caption text
if (db_num_rows($listdata)) {
    $firstrow = db_fetch_assoc($listdata);
    $caption = $lang["you_see_garmentuser_article_possession_1"] . ' <strong>' . $firstrow['Omschrijving'] . '</strong> '
                    . strtolower($lang["size"]) . ' <strong>' . $firstrow['Maat'] . '</strong> ' . $lang["you_see_garmentuser_article_possession_2"];
    db_data_seek($listdata, 0);
} else {
    $firstrow = null;
    $caption = $lang["article_not_in_use"];
}

/**
 * Show graph string when asked. We will only process values in the 'percentize' range.
 */
if (db_num_rows($listdata) && isset($_GET['graph'])) {
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

if (empty($totals))
{
    $totals = "";
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
