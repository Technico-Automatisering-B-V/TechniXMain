<?php

/**
 * Dashboard 1
 *
 * PHP version 5
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-${date} Technico Automatisering B.V.
 * @return    json $result
 * @version   $$Id$$
 */

// Require bootstrap
require_once "include/engine.php";
$data_html = "";

$data_gu = "";
$data_de = "";
$data_la = "";
$data_re = "";

$urlinfo = array(
    "day" => (!empty($_GET["day"])) ? $_GET["day"] : 7
);

$query = "
SELECT
        c.id AS circulationgroup_id,
        COUNT(l.id) AS `count`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN scanlocationstatuses AS s ON l.scanlocationstatus_id = s.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE c.type = 'GS'
        AND g.deleted_on IS NULL
        AND g.active = 1
        AND s.name = 'distributed'
        AND g.lastscan <= DATE(NOW()) - INTERVAL ". ($urlinfo['day']-1) ." DAY
    GROUP BY
        c.id";

$tmp_gu_garment = db_query($query);

$query = "
SELECT
        c.id AS circulationgroup_id,
        COUNT(l.id) AS `count`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN scanlocationstatuses AS s ON l.scanlocationstatus_id = s.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE
        c.type = 'GS'
        AND g.deleted_on IS NULL
        AND g.active = 1
        AND s.name = 'deposited'
        AND g.lastscan <= DATE(NOW()) - INTERVAL ". ($urlinfo['day']-1) ." DAY
    GROUP BY
        c.id";

$tmp_de_garment = db_query($query);

$query = "
SELECT
        c.id AS circulationgroup_id,
        COUNT(l.id) AS `count`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN scanlocationstatuses AS s ON l.scanlocationstatus_id = s.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE
        c.type = 'GS'
        AND g.deleted_on IS NULL
       AND g.active = 1
        AND s.name = 'laundry'
        AND g.lastscan <= DATE(NOW()) - INTERVAL ". ($urlinfo['day']-1) ." DAY
    GROUP BY
        c.id";

$tmp_la_garment = db_query($query);

$query = "
SELECT
        c.id AS circulationgroup_id,
        COUNT(l.id) AS `count`
    FROM
        arsimos AS a
    INNER JOIN garments AS g ON a.id = g.arsimo_id
    INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
    INNER JOIN scanlocationstatuses AS s ON l.scanlocationstatus_id = s.id
    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
    WHERE
        c.type = 'GS'
        AND g.deleted_on IS NULL
        AND g.active = 1
        AND s.name = 'rejected'
        AND g.lastscan <= DATE(NOW()) - INTERVAL ". ($urlinfo['day']-1) ." DAY
    GROUP BY
        c.id";

$tmp_re_garment = db_query($query);

$data_gu = array();
while ($row = db_fetch_assoc($tmp_gu_garment)){
   $data_html .= "<input type=\"hidden\" id=\"data_gu_garment".$row["circulationgroup_id"]."\" value=\"". $row["count"] ."\" />";
   $data_gu[$row["circulationgroup_id"]] = $row["count"];
}

$data_de = array();
while ($row = db_fetch_assoc($tmp_de_garment)){
   $data_html .= "<input type=\"hidden\" id=\"data_de_garment".$row["circulationgroup_id"]."\" value=\"". $row["count"] ."\" />";
   $data_de[$row["circulationgroup_id"]] = $row["count"];
}

$data_la = array();
while ($row = db_fetch_assoc($tmp_la_garment)){
   $data_html .= "<input type=\"hidden\" id=\"data_la_garment".$row["circulationgroup_id"]."\" value=\"". $row["count"] ."\" />";
   $data_la[$row["circulationgroup_id"]] = $row["count"];
}

$data_re = array();
while ($row = db_fetch_assoc($tmp_re_garment)){
   $data_html .= "<input type=\"hidden\" id=\"data_re_garment".$row["circulationgroup_id"]."\" value=\"". $row["count"] ."\" />";
   $data_re[$row["circulationgroup_id"]] = $row["count"];
}

$result["data_html"] = $data_html;
$result["data_gu"]   = $data_gu;
$result["data_de"]   = $data_de;
$result["data_la"]   = $data_la;
$result["data_re"]   = $data_re;

// Return JSON object
echo json_encode($result);


/**
 * EOF
 */

?>
