<?php

/**
 * Include necessary files
 */
require_once "include/engine.php";

/**
 * Page settings
 */
$pi = array();
$pi["group"] = $lang["workwearmanagement"];
$pi["title"] = $lang["garmentageanalysis"];
$pi["template"] = "layout/pages/workwear_ageanalysis.tpl";
$pi["filename_list"] = "workwear_ageanalysis.php";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}
$urlinfo = array();

function debug_echo($s) {
    echo("<pre>");
    print_r($s);
    echo("</pre>");
}

/**
 * Recursive xss prevention
 */
function xss_sanitize($x)
{
    if(is_array($x)) {
        foreach($x as $k=>$v) {
            $x[$k] = xss_sanitize($v);
        }
    }
    elseif(is_object($x)) {
        foreach($x as $k=>$v) {
            $x->$k = xss_sanitize($v);
        }
    }
    else {
        $x = htmlspecialchars($x, ENT_QUOTES);
    }
    return $x;
}

$urlinfo = array();

if(isset($_GET)) {
    $urlGetinfo = xss_sanitize($_GET);
}

if(isset($_POST)) {
    $urlPostinfo = xss_sanitize($_POST);
}

/**
 * Handel filters requests
 */
$iArticleFilter = 0;
$sArticleFilter = "";
if(array_key_exists("article_id", $urlGetinfo))
{
    if($urlGetinfo["article_id"] > 0) {
        $iArticleFilter = $urlGetinfo["article_id"];
        $sArticleFilter = "AND ar.article_id = ". $iArticleFilter;
    }
}

/**
 * Handel POST requests
 */
if(!empty($urlPostinfo)) {
    $iSelectedArsimo = key($urlPostinfo);
    $iIncreaseAmount = (int)$urlPostinfo[$iSelectedArsimo]; 
    $sIncreaseMaxWashCount = "UPDATE garments SET maxwashcount = maxwashcount + {$iIncreaseAmount} WHERE arsimo_id = {$iSelectedArsimo} AND ISNULL(deleted_on)";
    db_query($sIncreaseMaxWashCount);
}

/**
 * Query for loading page content
 */
//$sAgeAnalysisSql = "SELECT * FROM workwear_garment_age;";
//$sAgeOverviewGarments = "SELECT * FROM workwear_garment_age_overview_all;";
$sArticleNamesSql= "SELECT DISTINCT id, article_name FROM workwear_garment_age;";

/**
 * Correction queries
 */
$sAverageWeeksPerArticle = "SELECT ar.article_id, a.description AS 'article', COUNT(*) AS 'garments', AVG(weeksremaining) AS 'average_weeksremaining' FROM workwear_garment_replacement_dates rd INNER JOIN garments g ON g.id = rd.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id WHERE g.deleted_on IS NULL ". $sArticleFilter." GROUP BY ar.article_id";
$sAveragesPerArsimo = "SELECT ar.id, ar.size_id, s.name AS 'size', ar.article_id, a.description AS 'article', COUNT(*) AS 'garments', AVG(g.washcount) AS 'average_washcount', AVG(weeksremaining) AS 'average_weeksremaining', over.amount AS 'garments_over_max', toreplace.full_replacement_date FROM workwear_garment_replacement_dates rd INNER JOIN garments g ON g.id = rd.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN sizes s ON s.id = ar.size_id LEFT JOIN (SELECT ar.id AS 'arsimo_id', COUNT(*) AS 'amount' FROM workwear_garment_prices wp INNER JOIN garments g ON g.id = wp.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id WHERE g.washcount > wp.truemaxwashcount GROUP BY ar.id) over ON over.arsimo_id = ar.id LEFT JOIN (SELECT g.arsimo_id, MAX(rd.replacement_date) AS 'full_replacement_date' FROM workwear_garment_replacement_dates rd INNER JOIN garments g ON g.id = rd.id WHERE g.deleted_on IS NULL GROUP BY g.arsimo_id) toreplace ON toreplace.arsimo_id = ar.id WHERE g.deleted_on IS NULL ". $sArticleFilter." GROUP BY ar.id";
$sTotals = "SELECT COUNT(*) AS 'garments', MAX(wp.truemaxwashcount) AS 'highest_max', MIN(wp.truemaxwashcount) AS 'lowest_max', AVG(wp.washcount) AS 'average_washcount', SUM(wp.average_washcount) AS 'average_weekly_washcount' FROM workwear_garment_prices wp";
$sRemaining = "SELECT MAX(weeksremaining) AS 'weeksremaining' FROM workwear_garment_replacement_dates";
$sAverageWashesPerArsimo = "SELECT g.arsimo_id, SUM(wd.average_washcount) 'average_weekly_washes' FROM workwear_garment_replacement_dates wd INNER JOIN garments g ON g.id = wd.id WHERE g.deleted_on IS NULL GROUP BY g.arsimo_id";

/**
 * Extended cycle
 */
$sAverageExtendedWeeks = "SELECT ar.id, ar.size_id, s.name, ar.article_id, a.description AS 'article', we.extension, AVG(IF(we.extension > g.washcount, ((we.extension - g.washcount) / g.average_washcount), 0)) AS 'extended_life_in_weeks' FROM garments g INNER JOIN workwear_extensions we ON we.id = g.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN sizes s ON s.id = ar.size_id INNER JOIN articles a ON a.id = ar.article_id GROUP BY ar.id";

/**
 * Normal
 */

$aAverageWeeksPerArticle = array();
$aAveragesPerArsimo = array();
$aTotals = array();
$aRemaining = array();
$aAverageWashesPerArsimo = array();

/**
 * Extended cycle
 */

$aAverageExtendedWeeks = array();

/**
 * Normal
 */

$oAverageWeeksPerArticle_result = db_query($sAverageWeeksPerArticle);
$oAveragesPerArsimo_result = db_query($sAveragesPerArsimo);
$oTotals_result = db_query($sTotals);
$oRemaining_result = db_query($sRemaining);
$oAverageWashesPerArsimo_result = db_query($sAverageWashesPerArsimo);

while($aRow = $oAverageWeeksPerArticle_result->fetch_assoc()) {
    $aAverageWeeksPerArticle[] = $aRow;
}
while($aRow = $oAveragesPerArsimo_result->fetch_assoc()) {
    $aAveragesPerArsimo[$aRow['article_id']][$aRow['id']] = $aRow;
}
while($aRow = $oTotals_result->fetch_assoc()) {
    $aTotals[] = $aRow;
}
while($aRow = $oRemaining_result->fetch_assoc()) {
    $aRemaining[] = $aRow;
}
while($aRow = $oAverageWashesPerArsimo_result->fetch_assoc()) {
    $aAverageWashesPerArsimo[$aRow['arsimo_id']] = $aRow;
}

$aTotals[0]["weeksremaining"] = $aRemaining[0]['weeksremaining'];

/**
 * Extended cycle
 */
$oAverageExtendedWeeks_result = db_query($sAverageExtendedWeeks);

while($aRow = $oAverageExtendedWeeks_result->fetch_assoc()) {
    $aAverageExtendedWeeks[] = $aRow;
}

/**
 * Mix Extended into Normal values
 */
foreach($aAverageExtendedWeeks as $aAverage) {
    $aAveragesPerArsimo[$aAverage['article_id']][$aAverage['id']]['extended_weeksremaining'] = $aAverage['extended_life_in_weeks'];
}

/**
 * Executing queries and queries with filters
 */
//if (isset($iArticleFilter) && $iArticleFilter != 0) {
//    $sAgeAnalysisWithFilterSql = "SELECT * FROM workwear_garment_age WHERE id = {$iArticleFilter}";
//    $aAgeAnalysisResults = db_query($sAgeAnalysisWithFilterSql);
//} else {
//    $aAgeAnalysisResults = db_query($sAgeAnalysisSql);
//}

$aArticleNamesResults = db_query($sArticleNamesSql);
//$aAgeOverviewGarmentsResults = db_query($sAgeOverviewGarments);

/**
 * Fetch result
 */
//$aAgeArsimo = array();
//while($aRow = $aAgeAnalysisResults->fetch_assoc()) {
//    $aAgeArsimo[] = $aRow; 
//}

$aArticleNames = array();
while($aRow = $aArticleNamesResults ->fetch_assoc()) {
    $aArticleNames[] = $aRow;
}

//$aOverviewAgeGarments = array();
//while($aRow = $aAgeOverviewGarmentsResults ->fetch_assoc()) {
//    $aOverviewAgeGarments = $aRow;
//}

/**
 * Organize data if needed
 */
//$aGroupedArsimoPerArticle = array();
//foreach($aAgeArsimo as $aArsimoAgeInfo) {
//    $aGroupedArsimoPerArticle[$aArsimoAgeInfo["id"]][] = $aArsimoAgeInfo;
//}

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "sortlinks" => $sortlinks,
    "resultinfo" => $resultinfo,
    "listdata" => $listdata,
    "pagination" => $pagination,
    "articleNames" => $aArticleNames,
    "selectedArticle" => $iArticleFilter,
    //"ageAnalysis" => $aGroupedArsimoPerArticle,
    //"overviewAgeGarments" => $aOverviewAgeGarments,
    "aAverageWeeksPerArticle" => $aAverageWeeksPerArticle,
    "aAveragesPerArsimo" => $aAveragesPerArsimo,
    "aAverageWashesPerArsimo" => $aAverageWashesPerArsimo,
    "aTotals" => $aTotals
);

template_parse($pi, $urlinfo, $cv);

?>