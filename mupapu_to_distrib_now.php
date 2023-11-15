#!/usr/bin/php5
<?php

/**
 * Write mupapu
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require_once "include/mupapu.php";

/**
 * Separate CLI from HTTP
 */
if (isset($_SERVER['argv'][0]))
{
    $circulationgroup_id = ((isset($_SERVER['argv'][1]) && is_numeric($_SERVER['argv'][1])) ? $_SERVER['argv'][1] : null);
    $numweeks = ((isset($_SERVER['argv'][2]) && is_numeric($_SERVER['argv'][2])) ? $_SERVER['argv'][2] : 4);

    //exit if missing circulationgroup (we should output the reason when using CLI)
    if (!isset($circulationgroup_id))
    {
        //MUPAPU via database date
        $sqlc = "SELECT `c`.`id` AS 'circulationgroup_id', `s`.`value` AS 'numweeks'
                   FROM `distributionperiods` `dp`
             INNER JOIN `circulationgroups` `c` ON `c`.`id` = `dp`.`circulationgroup_id`
              LEFT JOIN `settings` `s` ON `s`.`name` = 'mupapu_default_weeks_history'
                  WHERE `dp`.`from_dayofweek` = (DAYOFWEEK(NOW())-1) AND `dp`.`from_hours` = HOUR(NOW()) AND `c`.`mupapu_generate` = 'y'";
        $resultc = db_query($sqlc);

        while ($c_row = db_fetch_assoc($resultc)) {
            $circulationgroup_id = $c_row['circulationgroup_id'];
            
            
            $total_distributed_garments_sql = "SELECT ROUND(COUNT(*)*0.34)
                                    FROM log_garmentusers_garments
                                    INNER JOIN distributors ON distributors.id = log_garmentusers_garments.distributor_id
                                    INNER JOIN distributorlocations ON distributorlocations.id = distributors.distributorlocation_id
                                    WHERE starttime >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY
                                    AND starttime < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY
                                    AND distributorlocations.circulationgroup_id = " . $circulationgroup_id;
            $total_distributed_garments = db_fetch_num(db_query($total_distributed_garments_sql));
            $total_distributed_garments = $total_distributed_garments[0];

            $urlinfo["multiply_required_auto"] = 1.1;
            $position_needed = 0;

            switch (true) {
                case $total_distributed_garments < 1500:
                    $position_needed = $total_distributed_garments * 2;
                    break;
                case $total_distributed_garments <= 2000:
                    $position_needed = round($total_distributed_garments * 1.94);
                    break;
                case $total_distributed_garments <= 2500:
                    $position_needed = round($total_distributed_garments * 1.88);
                    break;
                case $total_distributed_garments <= 3000:
                    $position_needed = round($total_distributed_garments * 1.84);
                    break;
                case $total_distributed_garments <= 3500:
                    $position_needed = round($total_distributed_garments * 1.76);
                    break;
                case $total_distributed_garments <= 4000:
                    $position_needed = round($total_distributed_garments * 1.7);
                    break;
                case $total_distributed_garments <= 4500:
                    $position_needed = round($total_distributed_garments * 1.64);
                    break;
                case $total_distributed_garments <= 5000:
                    $position_needed = round($total_distributed_garments * 1.58);
                    break;
                case $total_distributed_garments > 5000:
                    $position_needed = round($total_distributed_garments * 1.52);
                    break;
                default:
                    break;
            }

            if($position_needed > 0) {
                $total_positions_sql = "SELECT SUM(`hooks`) -
                                    (
                                    SELECT IF(ISNULL(SUM(`ga`.`max_positions`)), 0, SUM(`ga`.`max_positions`)) AS 'max_positions'
                                             FROM `garmentusers_userbound_arsimos` `ga`
                                       INNER JOIN `garmentusers` `gu` ON `ga`.`garmentuser_id` = `gu`.`id`
                                       INNER JOIN `circulationgroups_garmentusers` `cg` ON `cg`.`garmentuser_id` = `gu`.`id`
                                       INNER JOIN `distributorlocations` `dl` ON `dl`.`circulationgroup_id` = `cg`.`circulationgroup_id`
                                       INNER JOIN `distributors` `d` ON (`d`.`id` = `gu`.`distributor_id`
                                               OR `d`.`id` = `gu`.`distributor_id2`
                                               OR `d`.`id` = `gu`.`distributor_id3`
                                               OR `d`.`id` = `gu`.`distributor_id4`
                                               OR `d`.`id` = `gu`.`distributor_id5`
                                               OR `d`.`id` = `gu`.`distributor_id6`
                                               OR `d`.`id` = `gu`.`distributor_id7`
                                               OR `d`.`id` = `gu`.`distributor_id8`
                                               OR `d`.`id` = `gu`.`distributor_id9`
                                               OR `d`.`id` = `gu`.`distributor_id10`) AND `d`.`distributorlocation_id` = `dl`.`id`
                                            WHERE `ga`.`enabled` = 1
                                              AND `gu`.`deleted_on` IS NULL
                                              AND `dl`.`circulationgroup_id` = `ga`.`circulationgroup_id`
                                              AND `dl`.`circulationgroup_id` = ". $circulationgroup_id ."
                                    ) AS 'hooks'
                                       FROM `distributors` `d`
                                 INNER JOIN `distributorlocations` `dl` ON `dl`.`id` = `d`.`distributorlocation_id`
                                      WHERE `dl`.`circulationgroup_id` = ". $circulationgroup_id;

                $total_positions = db_fetch_num(db_query($total_positions_sql));
                $total_positions = $total_positions[0];   

                $multiply_required_auto = round($total_positions/$position_needed, 2);

                if($multiply_required_auto > 2) {
                    $urlinfo["multiply_required_auto"] = 2;
                } else if ($multiply_required_auto < 1.1) {
                    $urlinfo["multiply_required_auto"] = 1.1;
                } else {
                    $urlinfo["multiply_required_auto"] = $multiply_required_auto;
                }
            }
            
            $periods_num_sql = "SELECT COUNT(*)
                    FROM distributionperiods dp
                    WHERE dp.circulationgroup_id = " . $circulationgroup_id;
            $periods_num = db_fetch_num(db_query($periods_num_sql));
            $periods_num = $periods_num[0];
            
            $mupapu = mupapu_generate($circulationgroup_id, $c_row['numweeks'], $calculate = true);

            $sql_del = "DELETE v.*
                    FROM distributorlocations_loadadvice v
                    INNER JOIN distributorlocations l ON v.distributorlocation_id = l.id
                    INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
                    WHERE c.id = $circulationgroup_id AND v.`type` LIKE 'auto'";
            $q_del = db_query($sql_del);

            $sql = "SELECT
                    `c`.`id` AS 'circulationgroup_id',
                    l.id AS 'distributorlocation_id',
                    COUNT(*) AS 'stations',
                    SUM(d.`hooks`) /
                    (
                        SELECT
                        SUM(d.`hooks`)
                        FROM circulationgroups c
                        INNER JOIN distributorlocations l ON l.circulationgroup_id = c.id
                        INNER JOIN distributors d ON d.distributorlocation_id = l.id
                        WHERE c.id = $circulationgroup_id
                    ) AS 'ratio'
                    FROM `circulationgroups` `c`
                    INNER JOIN `distributorlocations` `l` ON `l`.`circulationgroup_id` = `c`.`id`
                    INNER JOIN `distributors` `d` ON `d`.`distributorlocation_id` = `l`.`id`
                    WHERE `c`.`id` = $circulationgroup_id
                    GROUP BY `l`.`id`";
            $result = db_query($sql);

            while ($dist_row = db_fetch_assoc($result)) {
                $r = 0;
                $o = 0;
                $e = 0;
                
                foreach ($mupapu['mup'] as $ars => $ars_row) {
                    $demand = round(($ars_row['demand_new'] * $dist_row['ratio']),0,PHP_ROUND_HALF_UP);
                    $current_period = round(($ars_row["hitmiss_p0.h"] * $dist_row['ratio']),0,PHP_ROUND_HALF_UP);
                    $next_period = round(($ars_row["hitmiss_p1.h"] * $dist_row['ratio']),0,PHP_ROUND_HALF_UP);


                    if(!empty($ars_row['distributorlocation_id'])) {
                        if($ars_row['distributorlocation_id'] == $dist_row['distributorlocation_id']) {
                            $demand = $ars_row['demand_new'];
                            $current_period = $ars_row["hitmiss_p0.h"];
                            $next_period = $ars_row["hitmiss_p1.h"];
                        } else {
                            $demand = 0;
                            $current_period = 0;
                            $next_period = 0;
                        }
                    } else {
                        $demand = round(($ars_row['demand_new'] * $dist_row['ratio']),0,PHP_ROUND_HALF_UP);
                    }

                    $insert_arsimo['distributorlocation_id'] = $dist_row['distributorlocation_id'];
                    $insert_arsimo['arsimo_id'] = $ars_row['arsimo_id'];
                    $insert_arsimo['demand'] = $demand;
                    $insert_arsimo['critical_percentage'] = '0.33';
                    $insert_arsimo['type'] = 'auto';
                    $insert_arsimo['current_period'] = $current_period;
                    $insert_arsimo['next_period'] = $next_period;
                    db_insert("distributorlocations_loadadvice", $insert_arsimo);
                    
                    
                    $all_periods = 0;
                    $req = 0;
                    for($i=0; $i<$c_row['numweeks']; $i++) {
                        $period_max = 0;
                        for($p=0; $p < $periods_num; $p++) {
                            if($period_max < $ars_row["hitmiss_w". $i .".p". $p]){ $period_max = $ars_row["hitmiss_w". $i .".p". $p]; }
                        }
                        $all_periods = $all_periods+$period_max;
                    }
                    $all_periods_average = ceil($all_periods/$c_row['numweeks']);
                    switch (true) {
                        case $all_periods_average <= 2:
                            $req = $all_periods_average * 4;
                            break;
                        case $all_periods_average <= 5:
                            $req = $all_periods_average * 3.75;
                            break;
                        case $all_periods_average <= 10:
                            $req = $all_periods_average * 3.5;
                            break;
                        case $all_periods_average <= 20:
                            $req = $all_periods_average * 3.25;
                            break;
                        case $all_periods_average <= 40:
                            $req = $all_periods_average * 3;
                            break;
                        case $all_periods_average <= 80:
                            $req = $all_periods_average * 2.75;
                            break;
                        case $all_periods_average <= 120:
                            $req = $all_periods_average * 2.7;
                            break;
                        case $all_periods_average <= 160:
                            $req = $all_periods_average * 2.65;
                            break;
                        case $all_periods_average <= 200:
                            $req = $all_periods_average * 2.6;
                            break;
                        case $all_periods_average <= 250:
                            $req = $all_periods_average * 2.55;
                            break;
                        case $all_periods_average > 250:
                            $req = $all_periods_average * 2.5;
                            break;
                        default:
                            break;
                    }
                    $req = $req * $urlinfo["multiply_required_auto"];
                    $ad = ceil($req)-$ars_row["cir_cur"];

                    if($ad < 0) {
                        $order = 0;
                        $out = abs($ad);
                    } else {
                        $order = $ad;
                        $out = 0;
                    }
                    
                    $r += ceil($req);
                    $o += $order;
                    $e += $out;
                }
                $cg_conditions = array("mupapu_required" => $r, "mupapu_order" => $o, "mupapu_extra" => $e);
                db_update("circulationgroups", $dist_row['circulationgroup_id'], $cg_conditions);
            }
            $q = db_query("UPDATE `settings` SET `value` = NOW() WHERE `name` = 'mupapu_last_update'");
        }
        exit("- MUPAPU udated via database date -");
    }

    $http = false;
}
else
{
    $circulationgroup_id = ((isset($_GET['cid']) && is_numeric($_GET['cid'])) ? $_GET['cid'] : null);
    $numweeks = ((isset($_GET['numweeks']) && is_numeric($_GET['numweeks'])) ? $_GET['numweeks'] : 4);

    //exit if missing circulationgroup
    if (!isset($circulationgroup_id))
    {
        exit();
    }

    $http = true;
}

/**
 * Update the distributor's loadadvice NOW
 */
if ($http) echo "<pre>";

echo "Generating MUPAPU based on $numweeks weeks history...\n";
$mupapu = mupapu_generate($circulationgroup_id, $numweeks, $calculate = true);

echo "Purging any previous loadadvice data...\n";

$sql_del = "DELETE v.*
	FROM distributorlocations_loadadvice v
	INNER JOIN distributorlocations l ON v.distributorlocation_id = l.id
	INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
	WHERE c.id = $circulationgroup_id AND v.`type` LIKE 'auto'";
$q_del = db_query($sql_del);

//fixme foreach distributorlocation in circulationgroup the demand should be assigned to locations
//based on the number of positions on each location. Ex: circulationgroup=2 (gasthuisberg UZLeuven) has 2
//locations (loc 2 has 1420 pos, and loc 3 has 2130 pos.) the ratio for loc 2 = 0.4 and for loc 3 = 0.6
$sql = "SELECT
	l.id AS 'distributorlocation_id',
        COUNT(*) AS 'stations',
	SUM(d.`hooks`) /
	(
            SELECT
            SUM(d.`hooks`)
            FROM circulationgroups c
            INNER JOIN distributorlocations l ON l.circulationgroup_id = c.id
            INNER JOIN distributors d ON d.distributorlocation_id = l.id
            WHERE c.id = $circulationgroup_id
	) AS 'ratio'
	FROM `circulationgroups` `c`
	INNER JOIN `distributorlocations` `l` ON `l`.`circulationgroup_id` = `c`.`id`
	INNER JOIN `distributors` `d` ON `d`.`distributorlocation_id` = `l`.`id`
	WHERE `c`.`id` = $circulationgroup_id
	GROUP BY `l`.`id`";
$result = db_query($sql);

echo "Writing loadadvice data...\n";

while ($dist_row = db_fetch_assoc($result)) {
    foreach ($mupapu['mup'] as $ars => $ars_row) {
        $demand = round(($ars_row['demand_new'] * $dist_row['ratio']),0,PHP_ROUND_HALF_UP);
        $current_period = round(($ars_row["hitmiss_p0.h"] * $dist_row['ratio']),0,PHP_ROUND_HALF_UP);
        $next_period = round(($ars_row["hitmiss_p1.h"] * $dist_row['ratio']),0,PHP_ROUND_HALF_UP);
        
        
        if(!empty($ars_row['distributorlocation_id'])) {
            if($ars_row['distributorlocation_id'] == $dist_row['distributorlocation_id']) {
                $demand = $ars_row['demand_new'];
                $current_period = $ars_row["hitmiss_p0.h"];
                $next_period = $ars_row["hitmiss_p1.h"];
            } else {
                $demand = 0;
                $current_period = 0;
                $next_period = 0;
            }
        } else {
            $demand = round(($ars_row['demand_new'] * $dist_row['ratio']),0,PHP_ROUND_HALF_UP);
        }
        
        $insert_arsimo['distributorlocation_id'] = $dist_row['distributorlocation_id'];
        $insert_arsimo['arsimo_id'] = $ars_row['arsimo_id'];
        $insert_arsimo['demand'] = $demand;
        $insert_arsimo['critical_percentage'] = '0.33';
        $insert_arsimo['type'] = 'auto';
        $insert_arsimo['current_period'] = $current_period;
        $insert_arsimo['next_period'] = $next_period;
        db_insert("distributorlocations_loadadvice", $insert_arsimo);

        echo "Inserted new loadadvice data:"
                . " distributorlocation_id=" . $insert_arsimo['distributorlocation_id']
                . " arsimo_id=" . $insert_arsimo['arsimo_id']
                . " demand=" . $insert_arsimo['demand']
                . " critical_percentage=" . $insert_arsimo['critical_percentage']
                . "\n";
    }
}

echo "\n\n";
if ($http) echo "<font style=\"font-size: 16px;\"><b>";
echo "OK!";
if ($http) echo "</b></font>";
echo " Het belaadadvies is overschreven voor de gehele locatiegroep.\n";
echo "\n";
echo "Instellingen worden bijgewerkt...\n";
$q = db_query("UPDATE `settings` SET `value` = NOW() WHERE `name` = 'mupapu_last_update'");
config_rehash();
echo "\n";
echo "\n\n";
if ($http) echo "<font style=\"font-size: 16px;\"><b>";

if ($http) echo "<font style=\"font-size: 18px;\"><b>";
echo "Done.\n";
if ($http) echo "</b></font>";

if ($http) echo '<script type="text/javascript">
<!--
function pageScroll() {
  window.scrollBy(0,40);
  scrolldelay = setTimeout("pageScroll()",25);
  //window.scrollTo(0, document.body.scrollHeight);
}
window.onload=pageScroll();
-->
</script>';

if ($http) echo '</pre>';

?>
