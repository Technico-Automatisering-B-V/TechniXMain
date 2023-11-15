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
$pi["title"] = $lang["wwm_dashboard"];
$pi["template"] = "layout/pages/workwear_dashboard.tpl";
$pi["filename_list"] = "workwear_dashboard.php";
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

if(isset($urlPostinfo['undoLast']) && !empty($urlPostinfo['undoLast']) && $urlPostinfo['undoLast'] == 'yes') {
    $sUndoSQL = "UPDATE workwear_extensions we INNER JOIN workwear_tmp_extensions wte ON wte.garment_id = we.id SET we.extension = wte.old";
    db_query($sUndoSQL);
    $sTruncateSQL = "TRUNCATE workwear_tmp_extensions";
    db_query($sTruncateSQL);
}

/**
 * DO THE GRIDDY
 */

$sData = "SELECT maintenance_cost, maxlimit, checking_percentage FROM workwearmanagement_data";
$aData = array();
$oResult = db_query($sData);

while($aRow = $oResult->fetch_assoc()) {
    $aData[] = $aRow;
}

if(isset($_GET['getArticlePercentages'])) {
    //Select all garments per article
    $sArticlesQuery = "SELECT ar.article_id, a.description AS 'article', a.workwear_category_id AS 'category', COUNT(*) AS 'total' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id GROUP BY ar.article_id";
    
    $aArticles = array();
    
    $oArticlesQuery_result = db_query($sArticlesQuery);
    
    while($aRow = $oArticlesQuery_result->fetch_assoc()) {
        $aArticles[$aRow['article_id']] = $aRow;
        $aArticles[$aRow['article_id']]['amount_over_maxlimit'] = 0;
        $aArticles[$aRow['article_id']]['garments_amount'] = 0;
        $aArticles[$aRow['article_id']]['percentage'] = 0;
    }
    
    //Select all garments over check per article
    $sArticlesOverCheck = "SELECT ar.article_id, a.description AS 'article', COUNT(*) AS 'garments_amount' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id WHERE g.washcount >= we.extension GROUP BY ar.article_id";
    
    $aArticlesOverCheck = array();
    
    $oArticlesOverCheck_result = db_query($sArticlesOverCheck);
    
    while($aRow = $oArticlesOverCheck_result->fetch_assoc()) {
        if(array_key_exists($aRow['article_id'], $aArticles)) {
            $aArticles[$aRow['article_id']]['garments_amount'] = $aRow['garments_amount'];
            $aArticles[$aRow['article_id']]['percentage'] = number_format(($aRow['garments_amount'] / $aArticles[$aRow['article_id']]['total'] * 100), 0, ".", ",");
        }
    }
    
    //Select all garments over maxlimit per article
    $sArticlesOverLimit = "SELECT ar.article_id, a.description AS 'article', COUNT(*) AS 'garments_amount' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id WHERE g.washcount >= ". $aData[0]['maxlimit']." GROUP BY ar.article_id";
    
    $aArticlesOverLimit = array();
    
    $oArticlesOverLimit_result = db_query($sArticlesOverLimit);
    
    while($aRow = $oArticlesOverLimit_result->fetch_assoc()) {
        if(array_key_exists($aRow['article_id'], $aArticles)) {
            $aArticles[$aRow['article_id']]['amount_over_maxlimit'] = $aRow['garments_amount'];
        }
    }
    
    //Select article categories
    $sCategories = "SELECT * FROM workwear_categories";
    $aCategories = array();
    $oCategories_result = db_query($sCategories);
    
    while($aRow = $oCategories_result->fetch_assoc()) {
        $aCategories[$aRow['id']] = $aRow['name'];
    }
    
    //Sort articles so indexes match up for JS
    $aSorted = array();
    
    $iTrack = 0;
    foreach($aArticles as $aArticle) {
        if(array_key_exists($aArticle['category'], $aCategories)) {
            $aSorted[$aArticle['category']][$iTrack]['article'] = $aArticle;
            $aSorted[$aArticle['category']][$iTrack]['category'] = $aCategories[$aArticle['category']];
        }
        else {
            $aSorted[0][$iTrack]['article'] = $aArticle;
            $aSorted[0][$iTrack]['category'] = 'Geen categorie';
        }
        $iTrack++;
    }
    
    $sResponse = json_encode($aSorted);
    
    echo $sResponse;
    return;
}

$sRental = "SELECT SUM(wp.rental_price) AS 'rental_price' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN workwearmanagement_prices wp ON wp.article_id = ar.article_id WHERE g.deleted_on IS NULL AND g.washcount < we.extension";
$aRental = array();
$oResult = db_query($sRental);

while($aRow = $oResult->fetch_assoc()) {
    $aRental[] = $aRow;
}

$sMaintenance = "SELECT (COUNT(*)*(SELECT maintenance_cost FROM workwearmanagement_data)) AS 'maintenance_cost' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id WHERE g.deleted_on IS NULL AND g.washcount > we.extension AND g.washcount < ". $aData[0]['maxlimit'];
$aMaintenance = array();
$oResult = db_query($sMaintenance);

while($aRow = $oResult->fetch_assoc()) {
    $aMaintenance[] = $aRow;
}

$sTotalMaintenance = "SELECT (COUNT(*)*(SELECT maintenance_cost FROM workwearmanagement_data)) AS 'maintenance_cost' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id WHERE g.deleted_on IS NULL";
$aTotalMaintenance = array();
$oResult = db_query($sTotalMaintenance);

while($aRow = $oResult->fetch_assoc()) {
    $aTotalMaintenance = $aRow;
}

$aRental[0]['rental_price'] += $aTotalMaintenance['maintenance_cost'];

/**
 * For the undo button
 */
$tmpSQL = "SELECT * FROM workwear_tmp_extensions";
$aTMPResult = array();
$oTMPResult = db_query($tmpSQL);

while($aRow = $oTMPResult->fetch_assoc()) {
    $aTMPResult[] = $aRow;
}
$bHasTMP = ((count($aTMPResult) > 0) ? true : false);

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
    "aData" => $aData,
    "aRental" => $aRental,
    "aMaintenance" => $aMaintenance,
    "bHasTMP" => $bHasTMP
);

template_parse($pi, $urlinfo, $cv);

?>