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
$pi["title"] = $lang["ordermanagement"];
$pi["template"] = "layout/pages/workwear_order.tpl";
$pi["filename_list"] = "workwear_order.php";
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
 * Automatic projection based on set interval if not null
 */
// Filter stock
$stock_filter = 'rd.scanlocationstatus_id != "3" AND rd.scanlocationstatus_id != "16"';
$in_stock_filter = 'rd.scanlocationstatus_id = "3" OR rd.scanlocationstatus_id = "16"';
// Determine whether to show interval
$bShowInterval = false;
// Generate list of dates
$workwearmanagement_data_sql = "SELECT * FROM workwearmanagement_data";
$workwearmanagement_data = db_query($workwearmanagement_data_sql);
$aData = array();
while($row = $workwearmanagement_data->fetch_assoc()) {
    $aData = xss_sanitize($row);
}
$aIntervalData = array();
$sNext = '';
$sPrev = '';
$interval_order_date = '0000-00-00';
$interval_delivery_date = '0000-00-00';
$bIntervalOrderIsLate = false;
$new_interval_delivery_date = '0000-00-00';
if(isset($aData['checks_interval']) && !empty($aData['checks_interval'])) {
    //echo "Interval has been set";
    $bShowInterval = true;
    $aIntervalDates = array();
    $iIntervalDates = $aData['checks_interval'];
    $iToDates = floor(52 / $aData['checks_interval']);
    $sLastDate = date('Y-01-01'); 
   for($i = 0; $i < $iIntervalDates; $i++) {
       $sDate = strtotime("+". $iToDates." weeks", strtotime($sLastDate));
       $sLastDate = date('Y-m-d', $sDate);
       $aIntervalDates[] = date('Y-m-d', $sDate);
    }
    //print_r($aIntervalDates);

    //Determine next date based on calculated dates for projection
    $sNext = '';
    $sPrev = '';
    foreach($aIntervalDates as $i=>$sDate) {
        $bPast = false;
        if(date('Y-m-d') > $sDate) {
            $bPast = true;
            //print_r($sDate. ", in the past: ". $bPast. "<br/>");
        }
        if(!$bPast) {
            $sNext = $sDate;
            $sPrev = (($i > 0) ? $aIntervalDates[$i - 1] : date('Y-01-01'));
            break;
        }
    }
    //print_r("Previous date: ". $sPrev. "<br/>");
    //print_r("Next date: ". $sNext);
    
    //Use determined period to predict orders for specified interval
    $interval_order_sql = "SELECT ar.id, a.description, s.name, COUNT(*) AS 'garmentsperarsimo', fullprice, SUM(fullprice) AS 'total',"
            . " ais.arsimos_in_stock AS 'arsimos_in_stock', IF(arsimos_in_stock IS NOT NULL, SUM(fullprice) - arsimos_in_stock * fullprice, SUM(fullprice)) AS 'actual_total'"
            . " FROM workwear_garment_replacement_dates rd"
            . " INNER JOIN garments g ON g.id = rd.id"
            . " INNER JOIN arsimos ar ON ar.id = g.arsimo_id"
            . " INNER JOIN articles a ON a.id = ar.article_id"
            . " INNER JOIN sizes s ON s.id = ar.size_id"
            . " INNER JOIN workwear_garment_prices_from_article gar ON gar.id = rd.id"
            . " LEFT JOIN ("
            . "SELECT ar.id, COUNT(*) AS arsimos_in_stock FROM workwear_garment_replacement_dates rd"
            . " INNER JOIN garments g ON g.id = rd.id"
            . " INNER JOIN arsimos ar ON ar.id = g.arsimo_id"
            . " WHERE ". $in_stock_filter." GROUP BY ar.id)"
            . " ais ON ais.id = ar.id"
            . " WHERE rd.id NOT IN ("
            . "SELECT id FROM workwear_garment_prices_from_arsimo"
            . ") AND rd.replacement_date BETWEEN '". $sPrev."' AND '". $sNext."'"
            . " GROUP BY ar.id"
            . " UNION SELECT ar.id, a.description, s.name, COUNT(*) AS 'garmentsperarsimo', fullprice, SUM(fullprice) AS 'total',"
            . " ais.arsimos_in_stock AS 'arsimos_in_stock', IF(arsimos_in_stock IS NOT NULL, SUM(fullprice) - arsimos_in_stock * fullprice, SUM(fullprice)) AS 'actual_total'"
            . " FROM workwear_garment_replacement_dates rd"
            . " INNER JOIN garments g ON g.id = rd.id"
            . " INNER JOIN arsimos ar ON ar.id = g.arsimo_id"
            . " INNER JOIN articles a ON a.id = ar.article_id"
            . " INNER JOIN sizes s ON s.id = ar.size_id"
            . " INNER JOIN workwear_garment_prices_from_arsimo gar ON gar.id = rd.id"
            . " LEFT JOIN ("
            . "SELECT ar.id, COUNT(*) AS arsimos_in_stock FROM workwear_garment_replacement_dates rd"
            . " INNER JOIN garments g ON g.id = rd.id"
            . " INNER JOIN arsimos ar ON ar.id = g.arsimo_id"
            . " WHERE ". $in_stock_filter." GROUP BY ar.id)"
            . " ais ON ais.id = ar.id"
            . " WHERE rd.replacement_date BETWEEN '". $sPrev."' AND '". $sNext."'"
            . " GROUP BY ar.id"
            . " HAVING actual_total > 0";
    
    $interval_order_result = db_query($interval_order_sql);
    while($row = $interval_order_result->fetch_assoc()) {
        $aIntervalData[] = xss_sanitize($row);
    }
    
    //Determine order and delivery dates based on delivery and circulation times
    $iWeeks = $aData['delivery_time'] + $aData['in_circulation_time'];
    $interval_order_date = date('Y-m-d', strtotime('-'. $iWeeks. ' weeks', strtotime($sNext)));
    $interval_delivery_date = date('Y-m-d', strtotime('+'. $iWeeks. 'weeks', strtotime($interval_order_date)));
    
    if(date('Y-m-d') > $interval_order_date) {
        $bIntervalOrderIsLate = true;
        $new_interval_delivery_date = date('Y-m-d', strtotime('+'. $iWeeks. 'weeks', strtotime(date('Y-m-d'))));
    }
}

/**
 * Order information
 */
$aOrderData = array();
if(isset($urlinfo['dateSelector']) && !empty($urlinfo['dateSelector'])) {
    $order_sql = "SELECT ar.id, a.description, s.name, COUNT(*) AS 'garmentsperarsimo', fullprice, SUM(fullprice) AS 'total',"
            . " ais.arsimos_in_stock AS 'arsimos_in_stock', IF(arsimos_in_stock IS NOT NULL, SUM(fullprice) - arsimos_in_stock * fullprice, SUM(fullprice)) AS 'actual_total'"
            . " FROM workwear_garment_replacement_dates rd"
            . " INNER JOIN garments g ON g.id = rd.id"
            . " INNER JOIN arsimos ar ON ar.id = g.arsimo_id"
            . " INNER JOIN articles a ON a.id = ar.article_id"
            . " INNER JOIN sizes s ON s.id = ar.size_id"
            . " INNER JOIN workwear_garment_prices_from_article gar ON gar.id = rd.id"
            . " LEFT JOIN ("
            . "SELECT ar.id, COUNT(*) AS arsimos_in_stock FROM workwear_garment_replacement_dates rd"
            . " INNER JOIN garments g ON g.id = rd.id"
            . " INNER JOIN arsimos ar ON ar.id = g.arsimo_id"
            . " WHERE ". $in_stock_filter." GROUP BY ar.id)"
            . " ais ON ais.id = ar.id"
            . " WHERE rd.id NOT IN ("
            . "SELECT id FROM workwear_garment_prices_from_arsimo"
            . ") AND rd.replacement_date BETWEEN NOW() AND '". $urlinfo['dateSelector']."'"
            . " GROUP BY ar.id"
            . " UNION SELECT ar.id, a.description, s.name, COUNT(*) AS 'garmentsperarsimo', fullprice, SUM(fullprice) AS 'total',"
            . " ais.arsimos_in_stock AS 'arsimos_in_stock', IF(arsimos_in_stock IS NOT NULL, SUM(fullprice) - arsimos_in_stock * fullprice, SUM(fullprice)) AS 'actual_total'"
            . " FROM workwear_garment_replacement_dates rd"
            . " INNER JOIN garments g ON g.id = rd.id"
            . " INNER JOIN arsimos ar ON ar.id = g.arsimo_id"
            . " INNER JOIN articles a ON a.id = ar.article_id"
            . " INNER JOIN sizes s ON s.id = ar.size_id"
            . " INNER JOIN workwear_garment_prices_from_arsimo gar ON gar.id = rd.id"
            . " LEFT JOIN ("
            . "SELECT ar.id, COUNT(*) AS arsimos_in_stock FROM workwear_garment_replacement_dates rd"
            . " INNER JOIN garments g ON g.id = rd.id"
            . " INNER JOIN arsimos ar ON ar.id = g.arsimo_id"
            . " WHERE ". $in_stock_filter." GROUP BY ar.id)"
            . " ais ON ais.id = ar.id"
            . " WHERE rd.replacement_date BETWEEN NOW() AND '". $urlinfo['dateSelector']."'"
            . " GROUP BY ar.id"
            . " HAVING actual_total > 0";
    $order_result = db_query($order_sql);
    $aData = array();
    while($row = $order_result->fetch_assoc()) {
        $aData[] = xss_sanitize($row);
    }
    $aOrderData = $aData;
//    echo "<pre>";
//    print_r($aOrderData);
//    echo "</pre>";
}

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
    "OrderData" => $aOrderData,
    "bShowInterval" => $bShowInterval,
    "aIntervalData" => $aIntervalData,
    "sNext" => $sNext,
    "sPrev" => $sPrev,
    "interval_order_date" => $interval_order_date,
    "interval_delivery_date" => $interval_delivery_date,
    "bIntervalOrderIsLate" => $bIntervalOrderIsLate,
    "new_interval_delivery_date" => $new_interval_delivery_date
);

template_parse($pi, $urlinfo, $cv);

?>