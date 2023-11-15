<?php

/**
 * Dashboard
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
$pi["access"] = array("common", "dashboard");
$pi["title"] = $lang["dashboard_tv"];
$pi["filename_list"] = "dashboard_tv.php";
$pi["template"] = "layout/pages/dashboard_tv.tpl";
$pi["page"] = "tv";

/**
 * Check authorization to view the page
 */

$urlinfo = array();

if (!empty($_GET["hassubmit"])) {
    if ($_GET["hassubmit"] == $lang["export"]){ $hassubmit = "export"; }
} else {
    $hassubmit = "";
}


/**
 * Create view
 */
$sql_cg = "
    SELECT
    circulationgroups.name AS circulationgroup_name,
    circulationgroups.id AS circulationgroup_id,
	circulationgroups.mupapu_required AS circulationgroup_required,
	circulationgroups.mupapu_order AS circulationgroup_order,
	circulationgroups.mupapu_extra AS circulationgroup_extra
    FROM circulationgroups
    WHERE type = 'GS'";

$c_groups = db_query($sql_cg);

$sql_ir = "
    SELECT
    circulationgroups.name AS circulationgroup_name,
    circulationgroups.id AS circulationgroup_id,
    SUM( scanlocationstatuses.name = 'conveyor' ) AS 'sum_conveyor',
    SUM( scanlocationstatuses.name = 'loaded' ) AS 'sum_stock',
    SUM( scanlocationstatuses.name = 'rejected' ) AS 'sum_rejected',
    SUM( scanlocationstatuses.name = 'distributed') AS 'sum_distributed',
    SUM( scanlocationstatuses.name = 'deposited' ) AS 'sum_deposited',
    SUM( scanlocationstatuses.name = 'container' ) AS 'sum_container',
    SUM( scanlocationstatuses.name = 'transport_to_laundry' ) AS 'sum_transport_to_laundry',
    SUM( scanlocationstatuses.name = 'laundry') AS 'sum_laundry',
    SUM( scanlocationstatuses.name = 'laundry' AND ss.`value` = 'INSCAN') AS 'sum_laundry_inscan',
    SUM( scanlocationstatuses.name = 'laundry' AND ss.`value` = 'OUTSCAN' ) AS 'sum_laundry_outscan',
    SUM( scanlocationstatuses.name = 'laundry' AND ss.`value` = 'REPAIR' ) AS 'sum_laundry_repair',
    SUM( scanlocationstatuses.name = 'laundry' AND ss.`value` = 'REWASH' ) AS 'sum_laundry_rewash',
    SUM( scanlocations.circulationgroup_id IS NOT NULL ) AS 'total'
    FROM
    arsimos
    INNER JOIN garments ON garments.arsimo_id = arsimos.id
    INNER JOIN scanlocations ON garments.scanlocation_id = scanlocations.id
    INNER JOIN scanlocationstatuses ON scanlocations.scanlocationstatus_id = scanlocationstatuses.id
    INNER JOIN circulationgroups ON circulationgroups.id = garments.circulationgroup_id
    LEFT JOIN sub_scanlocations ss ON ss.id = garments.sub_scanlocation_id
    WHERE arsimos.deleted_on IS NULL AND garments.deleted_on IS NULL
    GROUP BY circulationgroups.id";

$in_roulation = db_query($sql_ir);

$sql_load = "SELECT t2.circulationgroup_id,t2.free,(t2.loaded-COALESCE(SUM(t5.overload),0)) AS loaded,t2.hooks,IF(ISNULL(SUM(t5.overload)),0,SUM(t5.overload)) AS overloaded FROM (SELECT dl.circulationgroup_id,SUM(t1.hooks) AS hooks, SUM(t1.loaded) AS loaded, SUM(t1.hooks)-SUM(t1.loaded) AS 'free'
        FROM (
        select d.distributorlocation_id, d.hooks, COUNT(dsl.garment_id) loaded FROM distributors d
        LEFT JOIN distributors_load dsl ON dsl.distributor_id = d.id
        GROUP BY d.id) t1
        INNER JOIN distributorlocations dl ON dl.id = t1.distributorlocation_id
        GROUP BY dl.circulationgroup_id) t2
LEFT JOIN (
SELECT d.circulationgroup_id,
    COALESCE(SUM(tmp3.demand),0) AS 'max_load',
    COALESCE(SUM(tmp3.sizebound_count),0) AS 'current_load',
    COALESCE(SUM(tmp3.sizebound_count),0)-COALESCE(SUM(tmp3.demand),0) AS 'overload'
FROM
arsimos
LEFT JOIN (
    SELECT
        tmp.distributorlocation_id,
        tmp.arsimo_id,
        IF(ISNULL(tmp1.demand), 0, tmp1.demand) 'demand',
        IF(ISNULL(tmp1a.autodemand), 0, tmp1a.autodemand) 'autodemand',
        IF(ISNULL(tmp1m.manualdemand), 0, tmp1m.manualdemand) 'manualdemand',
        IF(ISNULL(tmp2.count), 0,tmp2.count) 'sizebound_count',
        IF(IF(ISNULL(tmp1.demand), 0, tmp1.demand)<=IF(ISNULL(tmp2.count), 0,tmp2.count),1,IF(ISNULL(tmp2.count), 0,tmp2.count) / IF(ISNULL(tmp1.demand), 0, tmp1.demand)) as current_load_percentage,
        tmp1.critical_percentage,
        IF(ISNULL(tmp1.demand), 0, tmp1.demand) - IF(ISNULL(tmp2.count),0,tmp2.count) 'diff',
        IF(ISNULL(tmp1a.current_period), 0, tmp1a.current_period) 'current_period', IF(ISNULL(tmp1a.next_period), 0, tmp1a.next_period) 'next_period' 
    FROM
    (
            SELECT DISTINCT
                distributors.distributorlocation_id,
                garments.arsimo_id
            FROM
                distributors_load
                INNER JOIN distributors ON distributors_load.distributor_id = distributors.id
                INNER JOIN garments ON distributors_load.garment_id = garments.id
            WHERE ISNULL(garments.garmentuser_id)
        UNION
        SELECT DISTINCT
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id
        FROM
            distributorlocations_loadadvice
    ) tmp
    LEFT JOIN
    (
        SELECT
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id,
            SUM(distributorlocations_loadadvice.demand) AS 'demand',
            distributorlocations_loadadvice.critical_percentage
        FROM
            distributorlocations_loadadvice
        GROUP BY
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id
    ) tmp1 ON tmp.distributorlocation_id = tmp1.distributorlocation_id
        AND tmp.arsimo_id = tmp1.arsimo_id

    LEFT JOIN
    (
        SELECT
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id,
            SUM(distributorlocations_loadadvice.demand) AS 'autodemand',
            distributorlocations_loadadvice.critical_percentage,
            SUM(distributorlocations_loadadvice.current_period) AS 'current_period',
            SUM(distributorlocations_loadadvice.next_period) AS 'next_period'
        FROM
            distributorlocations_loadadvice
        WHERE
            distributorlocations_loadadvice.type = 'auto'
        GROUP BY
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id
    ) tmp1a ON tmp.distributorlocation_id = tmp1a.distributorlocation_id
        AND tmp.arsimo_id = tmp1a.arsimo_id
    LEFT JOIN
    (
        SELECT
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id,
            SUM(distributorlocations_loadadvice.demand) AS 'manualdemand',
            distributorlocations_loadadvice.critical_percentage
        FROM
            distributorlocations_loadadvice
        WHERE
            distributorlocations_loadadvice.type = 'manual'
        GROUP BY
            distributorlocations_loadadvice.distributorlocation_id,
            distributorlocations_loadadvice.arsimo_id
    ) tmp1m ON tmp.distributorlocation_id = tmp1m.distributorlocation_id
        AND tmp.arsimo_id = tmp1m.arsimo_id

    LEFT JOIN (
        SELECT
            distributors.distributorlocation_id,
            garments.arsimo_id,
            COUNT(distributors_load.garment_id) 'count'
        FROM
            distributors_load
            INNER JOIN distributors ON distributors_load.distributor_id = distributors.id
            INNER JOIN garments ON distributors_load.garment_id = garments.id
        WHERE
            ISNULL(garments.garmentuser_id)
        GROUP BY
            distributors.distributorlocation_id,
            garments.arsimo_id
    ) tmp2 ON tmp.distributorlocation_id = tmp2.distributorlocation_id
        AND tmp.arsimo_id = tmp2.arsimo_id
) tmp3 ON tmp3.arsimo_id = arsimos.id
INNER JOIN `distributorlocations` `d` ON `d`.`id` = `tmp3`.`distributorlocation_id`
WHERE arsimos.deleted_on IS NULL
GROUP BY d.circulationgroup_id, `tmp3`.`arsimo_id`
HAVING (
`current_load` > max_load))t5 ON t5.circulationgroup_id = t2.circulationgroup_id
GROUP BY t2.circulationgroup_id";

$load = db_query($sql_load);

/**
 * Generate the page
 */
$cv = array(
    "pi" => $pi,
    "urlinfo" => $urlinfo,
    "in_roulation" => $in_roulation,
    "load" => $load,
    "c_groups" => $c_groups
);

template_parse($pi, $urlinfo, $cv);

?>
