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
$pi["title"] = $lang["valueanalysis"];
$pi["template"] = "layout/pages/workwear_valueanalysis.tpl";
$pi["filename_list"] = "workwear_valueanalysis.php";
$pi["page"] = "simple";

/**
 * Check authorization to view the page
 */
if ($_SESSION["username"] !== "Technico") {
    redirect("login.php");
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
    $urlinfo = xss_sanitize($_GET);
}

/**
 * Calculate necessary investment + cost over a certain period of time based on the items that need to be replaced within that timeframe
 */
$IntervalInvestment = 0;
$InvestmentDate = 0;
if(isset($urlinfo['periode']) && !empty($urlinfo['periode'])) {
    $IntervalType = null;
    switch($urlinfo['periode']) {
        case 'maanden': $IntervalType = 'MONTH'; break;
        case 'weken': $IntervalType = 'WEEK'; break;
        case 'date_now': $IntervalType = 'todate'; break;
    }
    if(isset($urlinfo['numberSelector']) && !empty($urlinfo['numberSelector'])) {
        $IntervalQuery = "SELECT SUM(wp.fullprice) AS 'stockless_investment' FROM workwear_garment_replacement_dates rd INNER JOIN workwear_garment_prices wp ON wp.id = rd.id WHERE rd.replacement_date BETWEEN NOW() AND (NOW() + INTERVAL ". $urlinfo['numberSelector']." ". $IntervalType.")";
        $IntervalResult = db_query($IntervalQuery);
        while($row = $IntervalResult->fetch_assoc()) {
            $IntervalInvestment = $row['stockless_investment'];
        }
//        echo "<pre>";
//        echo "Total Investment: &euro;". number_format($IntervalInvestment, 2, ",", ".");
//        echo "</pre>";
    }
    if(isset($urlinfo['dateSelector']) && !empty($urlinfo['dateSelector'])) {
        $InvestmentQuery = "SELECT SUM(wp.fullprice) AS 'stockless_investment' FROM workwear_garment_replacement_dates rd INNER JOIN workwear_garment_prices wp ON wp.id = rd.id WHERE rd.replacement_date BETWEEN NOW() AND '". $urlinfo['dateSelector']."'";
        $InvestmentResult = db_query($InvestmentQuery);
        while($row = $InvestmentResult->fetch_assoc()) {
            $InvestmentDate = $row['stockless_investment'];
        }
//        echo "<pre>";
//        echo "Total Investment: &euro;". number_format($InvestmentDate, 2, ",", ".");
//        echo "</pre>";
    }
}

/**
 * Need to move this to the article or arsimo price assignment once Johan creates this
 */
$update_fullprice = false;
if($update_fullprice) {
    $sql = "UPDATE workwearmanagement_prices wp, workwearmanagement_data wd SET wp.fullprice = ((wp.price + wd.insert_price) * (1 + (wd.increment / 100)));";
    db_query($sql);
}

/**
 * Setting up calculation queries
 */
$stock_filter = 'scanlocationstatus_id != "3" AND scanlocationstatus_id != "16"';
$in_stock_filter = 'scanlocationstatus_id = "3" OR scanlocationstatus_id = "16"';

$totaloriginalvalue_sql = "SELECT SUM(fullprice) AS originalvalue FROM workwear_garment_prices";
$totalcurrentvalue_sql = "SELECT SUM(CASE WHEN (currentprice >= 0) THEN currentprice ELSE 0 END) AS currentvalue FROM workwear_garment_prices";

$totalcurrentvalue_inroulation_sql = "SELECT SUM(CASE WHEN (currentprice >= 0) THEN currentprice ELSE 0 END) AS currentvalue FROM workwear_garment_prices WHERE ". $stock_filter;
$totalcurrentvalue_instock_sql = "SELECT SUM(CASE WHEN (currentprice >= 0) THEN currentprice ELSE 0 END) AS currentvalue FROM workwear_garment_prices WHERE ". $in_stock_filter;

$pricesperarticle_sql = "SELECT a.id, a.description, SUM(fullprice) AS 'totalarticlevalue', SUM(CASE WHEN (currentprice >= 0) THEN currentprice ELSE 0 END) AS 'currentarticlevalue', COUNT(*) AS 'garmentsperarticle' FROM workwear_garment_prices wp INNER JOIN garments g ON g.id = wp.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id WHERE ar.deleted_on IS NULL AND ". $stock_filter." GROUP BY a.id";
$pricesperarticle_instock_sql = "SELECT a.id, a.description, SUM(fullprice) AS 'totalarticlevalue', SUM(CASE WHEN (currentprice >= 0) THEN currentprice ELSE 0 END) AS 'currentarticlevalue', COUNT(*) AS 'garmentsperarticle' FROM workwear_garment_prices wp INNER JOIN garments g ON g.id = wp.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id WHERE ar.deleted_on IS NULL AND ". $in_stock_filter." GROUP BY a.id";
$pricesperarsimo_sql = "SELECT ar.id, a.id AS 'article_id', a.description, s.name, SUM(fullprice) AS 'totalarsimovalue', SUM(CASE WHEN (currentprice >= 0) THEN currentprice ELSE 0 END) AS 'currentarsimovalue', COUNT(*) AS 'garmentsperarsimo' FROM workwear_garment_prices wp INNER JOIN garments g ON g.id = wp.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN sizes s ON s.id = ar.size_id WHERE ar.deleted_on IS NULL AND ". $stock_filter." GROUP BY ar.id";
$pricesperarsimo_instock_sql = "SELECT ar.id, a.id AS 'article_id', a.description, s.name, SUM(fullprice) AS 'totalarsimovalue', SUM(CASE WHEN (currentprice >= 0) THEN currentprice ELSE 0 END) AS 'currentarsimovalue', COUNT(*) AS 'garmentsperarsimo' FROM workwear_garment_prices wp INNER JOIN garments g ON g.id = wp.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id = ar.article_id INNER JOIN sizes s ON s.id = ar.size_id WHERE ar.deleted_on IS NULL AND ". $in_stock_filter." GROUP BY ar.id";

$replacementcostperarsimo_sql = "SELECT ar.id AS 'arsimo_id', a.id AS 'article_id', s.id AS 'size_id', a.description AS 'article', s.name AS 'size', COUNT(*) AS 'amount', SUM(wp.fullprice) AS 'total', ais.amount_in_stock, ais.price_per_piece, IF(ais.amount_in_stock IS NOT NULL, (SUM(wp.fullprice) - ais.price_per_piece * ais.amount_in_stock), SUM(wp.fullprice)) AS 'replacement_cost' FROM workwear_garment_prices wp INNER JOIN garments g ON g.id = wp.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id INNER JOIN articles a ON a.id =  ar.article_id INNER JOIN sizes s ON s.id = ar.size_id LEFT JOIN (SELECT ar.id AS 'arsimo_id', COUNT(*) AS 'amount_in_stock', wp.fullprice AS 'price_per_piece' FROM workwear_garment_prices wp INNER JOIN garments g ON g.id = wp.id INNER JOIN arsimos ar ON ar.id = g.arsimo_id WHERE (". $in_stock_filter.") AND wp.washcount < truemaxwashcount GROUP BY ar.id) ais ON ais.arsimo_id = ar.id WHERE wp.washcount >= wp.truemaxwashcount AND ". $stock_filter." GROUP BY ar.id";

/**
 * Executing calculation queries
 */
$totaloriginalvalue_result = db_query($totaloriginalvalue_sql);
$totalcurrentvalue_result = db_query($totalcurrentvalue_sql);
$totalcurrentvalue_inroulation_result = db_query($totalcurrentvalue_inroulation_sql);
$totalcurrentvalue_instock_result = db_query($totalcurrentvalue_instock_sql);
$pricesperarticle_result = db_query($pricesperarticle_sql);
$pricesperarticle_instock_result = db_query($pricesperarticle_instock_sql);
$pricesperarsimo_result = db_query($pricesperarsimo_sql);
$pricesperarsimo_instock_result = db_query($pricesperarsimo_instock_sql);
$replacementcostperarsimo_result = db_query($replacementcostperarsimo_sql);

/**
 * Gathering results
 */

//Getting totals
$TotalOriginalValue = 0;
while($aRow = $totaloriginalvalue_result->fetch_assoc()) {
    $TotalOriginalValue = $aRow['originalvalue'];
}
$TotalCurrentValue = 0;
while($aRow = $totalcurrentvalue_result->fetch_assoc()) {
    $TotalCurrentValue = $aRow['currentvalue'];
}
$TotalDepreciation = $TotalOriginalValue - $TotalCurrentValue;

//Getting totals in stock
$TotalCurrentValueInRoulation = 0;
while($aRow = $totalcurrentvalue_inroulation_result->fetch_assoc()) {
    $TotalCurrentValueInRoulation = $aRow['currentvalue'];
}
$TotalCurrentValueInStock = 0;
while($aRow = $totalcurrentvalue_instock_result->fetch_assoc()) {
    $TotalCurrentValueInStock = $aRow['currentvalue'];
}

//Getting grouped prices
$PricesPerArticle = array();
while($aRow = $pricesperarticle_result->fetch_assoc()) {
    $PricesPerArticle[] = $aRow;
}
$PricesPerArsimo = array();
while($aRow = $pricesperarsimo_result->fetch_assoc()) {
    $PricesPerArsimo[] = $aRow;
}
$PricesPerArticleInStock = array();
while($aRow = $pricesperarticle_instock_result->fetch_assoc()) {
    $PricesPerArticleInStock[] = $aRow;
}
$PricesPerArsimoInStock = array();
while($aRow = $pricesperarsimo_instock_result->fetch_assoc()) {
    $PricesPerArsimoInStock[] = $aRow;
}

//Merging arrays for easy access and algorithmic looping
$PriceInfo = array();
$StockPriceInfo = array();
foreach($PricesPerArticle as $Article) {
    $PriceInfo[$Article['id']] = $Article;
}
foreach($PricesPerArsimo as $Arsimo) {
    $PriceInfo[$Arsimo['article_id']]['arsimos'][] = $Arsimo;
}
foreach($PricesPerArticleInStock as $Article) {
    $StockPriceInfo[$Article['id']] = $Article;
}
foreach($PricesPerArsimoInStock as $Arsimo) {
    $StockPriceInfo[$Arsimo['article_id']]['arsimos'][$Arsimo['id']] = $Arsimo;
}

if(isset($urlinfo['artikel_id']) && !empty($urlinfo['artikel_id'])) {
    $Article = $PriceInfo[$urlinfo['artikel_id']];
    $PriceInfo = array();
    $PriceInfo[$Article['id']] = $Article;
}

$garmentsinroulation_result = db_query("SELECT COUNT(*) AS 'inroulation' FROM workwear_garment_prices WHERE ". $stock_filter);
$iGarmentsinroulation = 0;
while($row = $garmentsinroulation_result->fetch_assoc()) {
    $iGarmentsinroulation = $row['inroulation'];
}

$garmentsinstock_result = db_query("SELECT COUNT(*) AS 'instock' FROM workwear_garment_prices WHERE ". $in_stock_filter);
$iGarmentsinstock = 0;
while($row = $garmentsinstock_result->fetch_assoc()) {
    $iGarmentsinstock = $row['instock'];
}

//Getting replacement cost results
$ArsimoReplacementInfo = array();
while($row = $replacementcostperarsimo_result->fetch_assoc()) {
    $ArsimoReplacementInfo[$row['article_id']]['arsimos'][$row['arsimo_id']] = $row;
}

//Only for debugging
//echo "<pre>";
//print_r($PriceInfo);
//echo "</pre>";

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
    "TotalOriginalValue" => $TotalOriginalValue,
    "TotalCurrentValue" => $TotalCurrentValue,
    "TotalCurrentValueInRoulation" => $TotalCurrentValueInRoulation,
    "TotalCurrentValueInStock" => $TotalCurrentValueInStock,
    "TotalDepreciation" => $TotalDepreciation,
    "PriceInfo" => $PriceInfo,
    "StockPriceInfo" => $StockPriceInfo,
    "PricesPerArticle" => $PricesPerArticle,
    "GarmentsInRoulation" => $iGarmentsinroulation,
    "GarmentsInStock" => $iGarmentsinstock,
    "IntervalInvestment" => $IntervalInvestment,
    "InvestmentDate" => $InvestmentDate,
    "ArsimoReplacementInfo" => $ArsimoReplacementInfo
);

template_parse($pi, $urlinfo, $cv);

?>