<?php

/**
 * Report rejected garments
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
$pi["access"] = array("reports", "rejected_garments");
$pi["group"] = $lang["reports"];
$pi["title"] = $lang["rejected_garments"];
$pi["filename_this"] = "report_rejected_garments.php";
$pi["filename_list"] = "report_rejected_garments.php";
//$pi["filename_next"] = "garment_details.php";
$pi["template"] = "layout/pages/report_template.tpl";
$pi["page"] = "simple";

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
$sqlwhere = "";
$rejectlocation_sqlwhere = "";
$old_did = "";

if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
} else {
    $hassubmit = "";
}


if(!empty($_GET["stocksubmit"])) {
    $_SESSION["garments_selected"] = (!empty($_GET["garments_selected"])) ? $_GET["garments_selected"] : array();
    if(!empty($_SESSION["garments_selected"])) {
        $pi["note"] = html_to_stock("");
    }
} else {
   if (isset($_POST["confirmed"])) {
        foreach ($_SESSION["garments_selected"] as $name => $value)
        {
            $garment_conditions = array("scanlocation_id" => 3);
            db_update("garments", $name, $garment_conditions);
        }
   } else {
       $_SESSION["garments_selected"] = array();
   }     
}

if (!empty($_GET["did"])){
    $urlinfo["did"] = $_GET["did"];
} else {
    //we use the distributorlocation_id of the top name in our selectbox (which is alphabetically sorted).
    $selected_distributorlocation_conditions["order_by"] = "name";
    $selected_distributorlocation_conditions["limit_start"] = 0;
    $selected_distributorlocation_conditions["limit_num"] = 1;
    $urlinfo["did"] = db_fetch_row(db_read("distributorlocations", "id", $selected_distributorlocation_conditions));
    $urlinfo["did"] = $urlinfo["did"][0];
}

if (!empty($_GET["aid"])) {
    $urlinfo["aid"] = $_GET["aid"];
    $sqlwhere .= " AND article_id = ". $urlinfo["aid"];
} else {
    $urlinfo["aid"] = "";
}

if (!empty($_GET["rid"])) {
    $urlinfo["rid"] = $_GET["rid"];
    $sqlwhere .= " AND reason_id = ". $urlinfo["rid"];
} else {
    $urlinfo["rid"] = "";
}

if (!empty($_GET["scid"])){ $urlinfo["scid"] = $_GET["scid"]; }else{ $urlinfo["scid"] = ""; }

if (!empty($_GET["rlid"])) {
    $check_rejectlocation_sql = "SELECT count(*)
                            FROM rejectlocations r
                            WHERE r.distributorlocation_id = ".$urlinfo["did"]." 
                            AND r.id = ".$_GET["rlid"];
    $check_rejectlocation = db_fetch_num(db_query($check_rejectlocation_sql));
    $check_rejectlocation = $check_rejectlocation[0];

    if(isset($check_rejectlocation) && $check_rejectlocation != 0) {
        $urlinfo["rlid"] = $_GET["rlid"];
    } else {
        $selected_rejectlocation_conditions["order_by"] = "id";
        $selected_rejectlocation_conditions["where"]["1"] = "distributorlocation_id = " . $urlinfo["did"];
        $selected_rejectlocation_conditions["limit_start"] = 0;
        $selected_rejectlocation_conditions["limit_num"] = 1;

        $urlinfo["rlid"] = db_fetch_row(db_read("rejectlocations", "id name", $selected_rejectlocation_conditions));
        $urlinfo["rlid"] = $urlinfo["rlid"][0];
    }
} else {
    $selected_rejectlocation_conditions["order_by"] = "id";
    $selected_rejectlocation_conditions["where"]["1"] = "distributorlocation_id = " . $urlinfo["did"];
    $selected_rejectlocation_conditions["limit_start"] = 0;
    $selected_rejectlocation_conditions["limit_num"] = 1;

    $urlinfo["rlid"] = db_fetch_row(db_read("rejectlocations", "id name", $selected_rejectlocation_conditions));
    $urlinfo["rlid"] = $urlinfo["rlid"][0];
}

$rejectlocation_sqlwhere = "AND l.distributorlocation_id = ". $urlinfo["did"] ." AND l.rejectlocation_id = ". $urlinfo["rlid"];
$sqlwhere .= " AND rejectlocation_id = ". $urlinfo["rlid"];

// Required for selectbox: distributorlocations
$distributorlocations_conditions["order_by"] = "name";
$distributorlocations = db_read("distributorlocations", "id name", $distributorlocations_conditions);

// Required for selectbox: rejectlocations
$rejectlocations_conditions["order_by"] = "id";
$rejectlocations_conditions["where"]["1"] = "distributorlocation_id = " . $urlinfo["did"];
$rejectlocations = db_read("rejectlocations", "id translate", $rejectlocations_conditions);

// Required for selectbox: rejectionreason
$r_sql = "SELECT
            `rejectionreasons`.`id` AS 'id',
            `rejectionreasons`.`translate` AS 'translate'
       FROM `distributorlocations_rejectionreasons` `dr`
 INNER JOIN `rejectionreasons` ON `dr`.`rejectionreason_id` = `rejectionreasons`.`id`
      WHERE `dr`.`distributorlocation_id` = ". $urlinfo["did"] ."
        AND `dr`.`rejectlocation_id` = ". $urlinfo["rlid"];
$rejections = db_query($r_sql);

// Required for selectbox: articles
$articles_conditions["order_by"] = "description";
$articles = db_read("articles", "id description", $articles_conditions);

// Required for selectbox: statuses
$statuses_sql = "SELECT `scanlocationstatuses`.`id`, `scanlocationstatuses`.`name` from `scanlocationstatuses`
                  WHERE `scanlocationstatuses`.`name` NOT LIKE 'deleted'
               ORDER BY `scanlocationstatuses`.`description`";
$statuses = db_query($statuses_sql);

if (!empty($urlinfo["scid"])){
    $scanlocation_where = " AND sst.id = " . $urlinfo["scid"];
    $sqlwhere .= " AND !ISNULL(log.garment_id) ";
} else {
    $scanlocation_where = "";
}

$sql = "
SELECT
    log.location AS Location,
    log.date AS date,
    log.reason AS reason,
    log.circulationgroup AS circulationgroup,
    log.rejectlocation_id AS rejectlocation_id,
    log.tag AS tag,
    log.tag2 AS tag2,
    log.tag AS sec__id,
    log.garment_id AS ref__id,
    log.article AS article,
    IF(ISNULL(`log`.`modification`), `log`.`size`, CONCAT(`log`.`size`, ' ', `log`.`modification`)) AS size,
    IF(ISNULL(`log`.`status_name`),'',`log`.`status_name`) AS status,
    `log`.`owner` AS owner,
    IF((!ISNULL(log.garment_id) AND log.status_name != 'loaded' AND log.status_name != 'stock_hospital'),CONCAT('<input id=\"garment',log.garment_id,'\" name=\"garment_selected[',log.garment_id,']\" type=\"checkbox\" onclick=\"handleClick(this);\" value=\"',log.garment_id,'\">'),'') as 'select'
FROM
(
    /** Select properties of known tags (garment_id exists and every tag selected once) */
    SELECT
    l.distributorlocation_id AS location,
    l.date AS date,
    g.tag AS tag,
    g.tag2 AS tag2,
    l.tag AS sec__id,
    l.garment_id AS garment_id,
    r.id AS reason_id,
    c.name AS circulationgroup,
    l.rejectlocation_id AS rejectlocation_id,
    r.translate AS reason,
    a.id AS article_id,
    a.description AS article,
    s.name AS size,
    m.name AS modification,
    CONCAT(IF(ISNULL(gu.name),'',gu.name), ' ', IF(ISNULL(gu.surname),'',gu.surname)) AS owner,
    sst.description AS status,
    sst.name AS status_name
    FROM log_rejected_garments l
        INNER JOIN (
        SELECT
        l.distributorlocation_id AS location,
        l.garment_id AS garment_id,
        MAX( l.date ) AS date
        FROM log_rejected_garments l
        GROUP BY
        l.distributorlocation_id,
        l.garment_id
    ) `maxdate` ON maxdate.location = l.distributorlocation_id AND maxdate.garment_id = l.garment_id
    INNER JOIN rejectionreasons r ON l.rejectionreason_id = r.id
    INNER JOIN distributorlocations_rejectionreasons dr ON r.id = dr.rejectionreason_id " . $rejectlocation_sqlwhere . "
    INNER JOIN garments g ON l.garment_id = g.id
    INNER JOIN circulationgroups c ON c.id = g.circulationgroup_id
    INNER JOIN scanlocations scl ON g.scanlocation_id = scl.id
    INNER JOIN scanlocationstatuses sst ON scl.scanlocationstatus_id = sst.id
    INNER JOIN arsimos i ON g.arsimo_id = i.id
    INNER JOIN articles a ON i.article_id = a.id
    INNER JOIN sizes s ON i.size_id = s.id
    INNER JOIN sizegroups sg ON s.sizegroup_id = sg.id
    LEFT JOIN modifications m ON i.modification_id = m.id
    LEFT JOIN garmentusers gu ON g.garmentuser_id = gu.id
    WHERE l.date = maxdate.date ". $scanlocation_where ."
    GROUP BY
    l.distributorlocation_id,
    l.garment_id
UNION
    /** Select properties of known tags grouped by most recent date (every tag selected once) */
    SELECT
    l.distributorlocation_id AS location,
    MAX( l.date ) AS date,
    l.tag AS tag,
    NULL AS tag2,
    l.tag AS sec__id,
    NULL AS garment_id,
    r.id AS reason_id,
    NULL AS circulationgroup,
    l.rejectlocation_id AS rejectlocation_id,
    r.translate AS reason,
    NULL AS article_id,
    NULL AS article,
    NULL AS size,
    NULL AS modification,
    NULL AS owner,
    NULL AS status,
    NULL AS status_name
    FROM log_rejected_garments l
    INNER JOIN rejectionreasons r ON l.rejectionreason_id = r.id
    INNER JOIN distributorlocations_rejectionreasons dr ON r.id = dr.rejectionreason_id " . $rejectlocation_sqlwhere . "
    WHERE l.garment_id IS NULL
    AND l.tag NOT LIKE ''
    GROUP BY
    l.distributorlocation_id,
    l.tag
UNION
    /** Select properties of unkown  tags */
    SELECT
    l.distributorlocation_id AS location,
    l.date AS date,
    l.tag AS tag,
    NULL AS tag2,
    l.tag AS sec__id,
    NULL AS garment_id,
    r.id AS reason_id,
    NULL AS circulationgroup,
    l.rejectlocation_id AS rejectlocation_id,
    r.translate AS reason,
    NULL AS article_id,
    NULL AS article,
    NULL AS size,
    NULL AS modification,
    NULL AS owner,
    NULL AS status,
    NULL AS status_name
    FROM log_rejected_garments l
    INNER JOIN rejectionreasons r ON l.rejectionreason_id = r.id
    INNER JOIN distributorlocations_rejectionreasons dr ON r.id = dr.rejectionreason_id " . $rejectlocation_sqlwhere . "
    WHERE l.garment_id IS NULL
    AND l.tag LIKE ''
) `log`
WHERE log.location = ". $urlinfo["did"] . $sqlwhere . "
ORDER BY log.date DESC
LIMIT 300
";

$listdata = db_query($sql);

/**
 * Export
 */
if ($hassubmit == "export") {
    $export_filename = "excel_export_" . date("Y-m-d_His") . ".xls";

    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=". $export_filename ."");
    header("Pragma: no-cache");
    header("Expires: 0");

    $header = $lang["date"]."\t".$lang["reason"]."\t".$lang["circulationgroup"]."\t".$lang["tag"]."\t".$lang["tag"]."2\t".$lang["article"]."\t".$lang["size"]."\t".$lang["status"]."\t";
    $data = "";
    while(!empty($listdata) && $row = db_fetch_array($listdata)) {
        $line = "";
        $in = array(
            $row["date"],
            $lang[$row["reason"]],
            $row["circulationgroup"],
            $row["tag"],
            $row["tag2"],
            $row["article"],
            $row["size"],
            $lang[$row["status"]],
            $row["owner"]
        );

        foreach($in as $value) {
            if ((!isset($value)) OR ($value == "")) {
                $value = "\t";
            } else {
                $value = str_replace('"', '""', $value);
                $value = '"' . $value . '"' . "\t";
            }
            $line .= $value;
        }
        $data .= trim($line)."\n";
    }
    $data_r = str_replace("\r","",$data);

    print "$header\n$data_r";
    die();
}


//columns to show
$show = array(
    "date" => true,
    "reason" => true,
    "circulationgroup" => true,
    "tag" => true,
    "tag2" => true,
    "article" => true,
    "size" => true,
    "status" => true,
    "owner" => true,
    "select" => true
);
$translate = array(
    "reason" => true,
    "status" => true
);

$row_onclick["tag"] = " onClick=\"document.location.href='garment_details.php";

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
$columns_left = 4;

//menu string
$menu = "";

//caption text
$caption = "
<form name=\"showform\" enctype=\"multipart/form-data\" method=\"GET\" action=\"". $_SERVER["PHP_SELF"] ."\">
    <input id=\"hidden-selected\" type=\"hidden\" name=\"email\" value=\"\">
    <div class=\"filter\">
        <table>
            <tr>
                <td class=\"name\">". $lang["location"] .":</td>
                <td class=\"value\" width=\"300\">". html_selectbox_submit("did", $distributorlocations, $urlinfo["did"], $lang["make_a_choice"], "style='width:100%'") ."</td>
            </tr>
            <tr>
                <td class=\"name\">". $lang["throw_off_location"] .":</td>
                <td class=\"value\" width=\"300\">". html_selectbox_translate_submit("rlid", $rejectlocations, $urlinfo["rlid"], null, "style='width:100%'") ."</td>
            </tr>
            <tr>
                <td class=\"name\">". $lang["reason"] .":</td>
                <td class=\"value\">". html_selectbox_translate_submit("rid", $rejections, $urlinfo["rid"], $lang["(all)"], "style='width:100%'") ."</td>
            </tr>
            <tr>
                <td class=\"name\">". $lang["article"] .":</td>
                <td class=\"value\">". html_selectbox_submit("aid", $articles, $urlinfo["aid"], $lang["(all_articles)"], "style='width:100%'") ."</td>
            </tr>
            <tr>
                <td class=\"name\">". $lang["status"] .":</td>
                <td class=\"value\">". html_selectbox_translate_submit("scid", $statuses, $urlinfo["scid"], $lang["(all_statuses)"], "style='width:100%'") ."</td>
            </tr>           
        </table>
        <div class=\"buttons\">
            <input type=\"submit\" name=\"stocksubmit\" value=\"" . $lang["put_to_stock"] . "\" title=\"" . $lang["put_to_stock"] . "\" />
            <input type=\"submit\" name=\"hassubmit\" value=\"". $lang["export"]. "\" title=\"". $lang["export"]. "\" />
        </div>
    </div>
</form>
<script type=\"text/javascript\">
    function addHidden(theForm, key, value) {
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = key;
      input.value = value;
      input.id = key;
      theForm.appendChild(input);
    }
    
    function removeHidden(theForm, id) {
      var input = document.getElementById(id);
      theForm.removeChild(input);
    }
    
    function handleClick(cb) {
      var theForm = document.forms['showform'];
      if(cb.checked) {
        addHidden(theForm, 'garments_selected['+cb.value+']', 'true');
      } else {
        removeHidden(theForm, 'garments_selected['+cb.value+']');
      }
    }
</script>
<div class=\"clear\" />

" . $lang["view_last_300_items"];

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "listdata" => $listdata,
    "menu" => $menu,
    "caption" => $caption,
    "show" => $show,
    "translate" => $translate,
    "percentize" => $percentize,
    "row_onclick" => $row_onclick,
    "rounding" => $rounding,
    "suffix" => $suffix,
    "columns_left" => $columns_left,
    "background" => $background
);

template_parse($pi, $urlinfo, $cv);

?>
