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
$pi["template"] = "layout/pages/workwear_dashboard_details.tpl";
$pi["filename_list"] = "workwear_dashboard_details.php";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
}

if(isset($_GET)) {
    $urlGetinfo = xss_sanitize($_GET);
}

// if(!isset($urlGetinfo["article_id"])) {
//     redirect("workwear_dashboard.php");
// }

$urlinfo = array();

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
$article_id = $urlGetinfo['article_id'];
$warn = "";

$sData = "SELECT maxlimit, checking_percentage FROM workwearmanagement_data";
$aData = array();
$oResult = db_query($sData);

while($aRow = $oResult->fetch_assoc()) {
    $aData = $aRow;
}

if(isset($_POST) && !empty($_POST)) {
    $urlPostinfo = xss_sanitize($_POST);
    
    foreach($urlPostinfo as $sName => $vValue) {
        switch($sName) {
            case "export_red": export_red($vValue); break;
            case "export_orange": export_orange($vValue); break;
            case "raise_limits": raise_limits($article_id, $vValue, $aData['maxlimit']); break;
        }
    }
}

function export_red($export_id) {
    $redQuery = "SELECT g.tag, g.tag2, a.description AS 'article', s.name AS 'size', g.washcount FROM garments g INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN sizes s ON s.id = ar.size_id WHERE ar.article_id = ". $export_id." AND g.deleted_on IS NULL AND g.washcount > (SELECT maxlimit FROM workwearmanagement_data)";
    $redQuery_result = db_query($redQuery);
    
    $aResults = array();
    
    while($aRow = $redQuery_result->fetch_assoc()) {
        $aResults[] = $aRow;
    }
    
    $sData = "";
    
    foreach($aResults[0] as $sColumn=>$vValue) {
        $sData.= $sColumn. "\t";
    }
    
    $sData.= "\n";
    
    foreach($aResults as $iLineIndex=>$aLine) {
        foreach($aLine as $sColumn=>$vValue) {
            $sData.= ((is_numeric($vValue)) ? $vValue. "'" : $vValue). "\t";
        }
        $sData.= "\n";
    }
    
    export_data($sData, "Overschrijdingen_". str_replace(" ", "_", $aResults[0]['article']). "_". time());
    
    return;
}
function export_orange($export_id) {
    //$orangeQuery = "SELECT g.tag, g.tag2, a.description AS 'article', s.name AS 'size', g.washcount FROM garments g INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN sizes s ON s.id = ar.size_id INNER JOIN (SELECT a.id AS 'article_id', a.description as 'article', COUNT(*) AS 'amount_over_limit', totals.total, (COUNT(*) / totals.total * 100) AS 'percentage' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN (SELECT a.id AS 'article_id', COUNT(*) AS 'total' FROM garments g INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id WHERE g.deleted_on IS NULL GROUP BY a.id) totals ON totals.article_id = a.id WHERE g.deleted_on IS NULL AND g.washcount > we.extension GROUP BY a.id) percentages ON percentages.article_id = a.id WHERE g.deleted_on IS NULL AND percentages.percentage >= (SELECT checking_percentage FROM workwearmanagement_data) AND percentages.percentage <= (SELECT maxlimit FROM workwearmanagement_data) AND a.id = ". $export_id;
    $orangeQuery = "SELECT g.tag, g.tag2, a.description AS 'article', s.name AS 'size', g.washcount FROM garments g INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN sizes s ON s.id = ar.size_id INNER JOIN workwear_extensions we ON we.id = g.id WHERE g.washcount >= we.extension AND a.id = ". $export_id;
    $orangeQuery_result = db_query($orangeQuery);
    
    $aResults = array();
    
    while($aRow = $orangeQuery_result->fetch_assoc()) {
        $aResults[] = $aRow;
    }
    
    $sData = "";
    
    foreach($aResults[0] as $sColumn=>$vValue) {
        $sData.= $sColumn. "\t";
    }
    
    $sData.= "\n";
    
    foreach($aResults as $iLineIndex=>$aLine) {
        foreach($aLine as $sColumn=>$vValue) {
            $sData.= ((is_numeric($vValue)) ? $vValue. "'" : $vValue). "\t";
        }
        $sData.= "\n";
    }
    
    export_data($sData, "Steekproef_". str_replace(" ", "_", $aResults[0]['article']). "_". time());
    
    return;
}
function raise_limits($article_id, $raise_by, $max_limit) {
    global $warn;
    if($raise_by <= 0) {
        return;
    }
    if($raise_by > $max_limit) {
        $warn = "Can not raise checking limit above max limit.";
        return;
    }
    
    //$orangeQuery = "SELECT g.id, g.tag, g.tag2, a.description AS 'article', s.name AS 'size', percentages.*, we.extension FROM garments g INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN sizes s ON s.id = ar.size_id INNER JOIN workwear_extensions we ON we.id = g.id INNER JOIN (SELECT a.id AS 'article_id', a.description as 'article', COUNT(*) AS 'amount_over_limit', totals.total, (COUNT(*) / totals.total * 100) AS 'percentage' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN (SELECT a.id AS 'article_id', COUNT(*) AS 'total' FROM garments g INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id WHERE g.deleted_on IS NULL GROUP BY a.id) totals ON totals.article_id = a.id WHERE g.deleted_on IS NULL AND g.washcount >= we.extension AND g.washcount < (SELECT maxlimit FROM workwearmanagement_data) GROUP BY a.id) percentages ON percentages.article_id = a.id WHERE g.deleted_on IS NULL AND percentages.percentage >= (SELECT checking_percentage FROM workwearmanagement_data) AND g.washcount >= we.extension AND a.id = ". $article_id;
    $orangeQuery = "SELECT g.id, g.tag, g.tag2, a.description AS 'article', s.name AS 'size', g.washcount, we.extension FROM garments g INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN sizes s ON s.id = ar.size_id INNER JOIN workwear_extensions we ON we.id = g.id WHERE g.washcount >= we.extension AND a.id = ". $article_id;
    $orangeQuery_result = db_query($orangeQuery);
    
    $aResults = array();
    
    while($aRow = $orangeQuery_result->fetch_assoc()) {
        $aResults[$aRow['id']] = $aRow;
    }
    
    //Start mass insert into workwear_tmp_extensions
    $bCapped = false;
    $sValues = "";
    foreach($aResults as $iId=>$aGarment) {
        $newExtension = ($aGarment['extension'] + $raise_by);
        if($newExtension > $max_limit) {
            $newExtension = $max_limit;
            $bCapped = true;
        }
        $sValues.= "(". $aGarment['id']. ", ". $newExtension. ", ". $aGarment['extension'].")". (($iId != end(array_keys($aResults))) ? "," : "");
    }
    
    db_query("TRUNCATE workwear_tmp_extensions");
    
    $sInsertTMP = "INSERT INTO workwear_tmp_extensions (garment_id, extension, old) VALUES ". $sValues;
    
    db_query($sInsertTMP);
    
    $sUpdate = "UPDATE workwear_extensions we INNER JOIN workwear_tmp_extensions wte ON wte.garment_id = we.id SET we.extension = wte.extension";
    
    db_query($sUpdate);
    
    if($bCapped) {
        $warn = "Controlegrens is geüpdatet, echter zijn sommige kledingstukken afgekapt op de uiterste grens.";
    }
    else {
        $warn = "Controlegrens is geüpdatet";
    }
    
    return;
}
function export_data($sData, $sName) {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=".$sName. ".xls");
    header("Exires: 0");
    
    echo $sData;
    die();
}

/**
 * Get content
 */

$sArsimoData = "SELECT ar.id, ar.size_id, s.name AS 'size', ar.article_id, a.description AS 'article', COUNT(*) AS 'garments_amount', totals.total, ((COUNT(*) / totals.total) * 100) AS 'percentage', COUNT(garments_over_max.id) AS 'amount_over_maxlimit', SUM(g.washcount) AS 'total_washcount' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN sizes s ON s.id = ar.size_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN (SELECT ar.id AS 'arsimo_id', COUNT(*) AS 'total' FROM garments g INNER JOIN arsimos ar ON ar.id = g.arsimo_id WHERE g.deleted_on IS NULL GROUP BY ar.id) totals ON totals.arsimo_id = ar.id  LEFT JOIN (SELECT g.id FROM garments g WHERE g.deleted_on IS NULL AND g.washcount >= (SELECT maxlimit FROM workwearmanagement_data)) garments_over_max ON garments_over_max.id = g.id WHERE g.deleted_on IS NULL AND g.washcount > we.extension AND article_id = ". $article_id." GROUP BY ar.id";
$aArsimoData = array();
$oResult = db_query($sArsimoData);

while($aRow = $oResult->fetch_assoc()) {
    $aArsimoData[] = $aRow;
}

$aRed = array();
$aOrange = array();
$aGreen = array();

foreach($aArsimoData as $aArsimo) {
    if($aArsimo['amount_over_maxlimit'] > 0) {
        $aRed[$aArsimo['id']] = $aArsimo;
    }
    elseif($aArsimo['percentage'] > $aData['checking_percentage']) {
        $aOrange[$aArsimo['id']] = $aArsimo;
    }
    else {
        $aGreen[$aArsimo['id']] = $aArsimo;
    }
}

/**
 * Price info and counting
 */
$sRental = "SELECT ar.id, ar.article_id, ar.size_id, COUNT(*) AS 'garments_amount', SUM(wp.rental_price) AS 'rental_price' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN sizes s ON s.id = ar.size_id INNER JOIN workwearmanagement_prices wp ON wp.article_id = ar.article_id WHERE g.deleted_on IS NULL AND g.washcount < we.extension AND ar.article_id = ". $article_id." GROUP BY ar.id";
$sMaintenance = "SELECT ar.id, ar.size_id, ar.article_id, COUNT(*) AS 'garments_amount', (COUNT(*)*(SELECT maintenance_cost FROM workwearmanagement_data)) AS 'maintenance_cost' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN sizes s ON s.id = ar.size_id WHERE g.deleted_on IS NULL AND g.washcount > we.extension AND g.washcount < (SELECT maxlimit FROM workwearmanagement_data) AND ar.article_id = ". $article_id." GROUP BY ar.id";

$aRental = array();
$aMaintenance = array();

$oRental = db_query($sRental);
$oMaintenance = db_query($sMaintenance);

while($aRow = $oRental->fetch_assoc()) {
    $aRental[$aRow['id']] = $aRow;
}
while($aRow = $oMaintenance->fetch_assoc()) {
    $aMaintenance[$aRow['id']] = $aRow;
}

foreach($aRental as $iArsimo_id=>$aSingle) {
    $aRental[$iArsimo_id]['rental_price'] += $aMaintenance[$iArsimo_id]['maintenance_cost'];
}

$fTotalRental = 0;
$fTotalMaintenance = 0;

foreach($aRental as $iArsimo_id=>$aSingle) {
    $fTotalRental += $aSingle['rental_price'];
}
foreach($aMaintenance as $iArsimo_id=>$aSingle) {
    $fTotalMaintenance += $aSingle['maintenance_cost'];
}

/**
 * Get article percentage
 */
$sArticleSQL = "SELECT COUNT(*) AS 'amount' FROM garments g INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id WHERE g.deleted_on IS NULL AND a.id = ". $article_id;
$iGarments = 0;

$oArticleSQL_result = db_query($sArticleSQL);
while($aRow = $oArticleSQL_result->fetch_assoc()) {
    $iGarments = $aRow['amount'];
}

$sArticleOverSQL = "SELECT COUNT(*) AS 'amount' FROM workwear_extensions we INNER JOIN garments g ON g.id = we.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id WHERE g.deleted_on IS NULL AND g.washcount >= we.extension AND g.washcount < ". $aData['maxlimit']." AND a.id = ". $article_id;
$iGarmentsOver = 0;

$oArticleOverSQL_result = db_query($sArticleOverSQL);
while($aRow = $oArticleOverSQL_result->fetch_assoc()) {
    $iGarmentsOver = $aRow['amount'];
}

$article_percentage = $iGarmentsOver / $iGarments * 100;

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
    "warn" => $warn,
    "aRed" => $aRed,
    "aOrange" => $aOrange,
    "aGreen" => $aGreen,
    "aRental" => $aRental,
    "aMaintenance" => $aMaintenance,
    "fTotalRental" => $fTotalRental,
    "fTotalMaintenance" => $fTotalMaintenance,
    "article_id" => $article_id,
    "article_percentage" => $article_percentage,
    "checking_percentage" => $aData['checking_percentage']
);

template_parse($pi, $urlinfo, $cv);

?>