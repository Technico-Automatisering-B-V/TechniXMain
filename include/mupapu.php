<?php

/**
 * MUPAPU
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * MUPAPU generator
 */

//change next to last for the original mupapu
//mupapu_get_periods: generate periods array with timestamps 'from' and 'to'
function mup_gp($circulationgroup_id, $now, $periods, $numweeks)
{
    /* order periods, first periods is last complete period */
    foreach($periods as $key => $period) {
        if($period['is_current']) {
            $current_period = $period;
            break;
        } else {
            array_push($periods, array_shift($periods));
        }
    }

    /* calculate unixtimestamps for periods */
    $new_periods = array();
    for ($i=0; $i<$numweeks; $i++)
    {
         /* check if starting with broken period in after sunday */
        $mod = ((mup_is_week_broken($periods[0], $now)) ? 1 : 0);

        $broken = false;

        foreach($periods as $key => $period)
        {
            $timestamps = array();

            $week = $numweeks  - $i + $mod;
            $days = $period['from_dayofweek'];
            $hours = $period['from_hours'];
            $minutes = $period['from_minutes'];
            $timestamps['from'] = strtotime("last sunday +$days days $hours hours $minutes minutes -$week week", $now);

            /* check if period is broken (ex. fri-mon) */
            if(mup_is_broken($period, $now)) $mod--;

            $week = $numweeks  - $i + $mod;
            $days = $period['to_dayofweek'];
            $hours = $period['to_hours'];
            $minutes = $period['to_minutes'];
            $timestamps['to'] = strtotime("last sunday +$days days $hours hours $minutes minutes -$week week", $now);

            #var_dump(date ("Y-m-d H:i:s", $timestamps['from']), date ("Y-m-d H:i:s", $timestamps['to']));
            $new_periods[$i][] = $timestamps;
        }
    }

    /*
    $format = "%a. %R";
    foreach($new_periods[0] as $weeks) {
            echo strftime($format, $weeks['from']) , " . " , strftime($format, $weeks['to']) , "\n";
    }
    */
    return $new_periods;
}

//change next to last for the original mupapu
/* Test if period is broken (from > to) */
function mup_is_broken($period, $now)
{
        $days = $period['from_dayofweek'];
        $hours = $period['from_hours'];
        $minutes = $period['from_minutes'];
        $timestamps['from'] = strtotime("last sunday +$days days $hours hours $minutes minutes", $now);

        $days = $period['to_dayofweek'];
        $hours = $period['to_hours'];
        $minutes = $period['to_minutes'];
        $timestamps['to'] = strtotime("last sunday +$days days $hours hours $minutes minutes", $now);

        return $timestamps['from'] > $timestamps['to'];
}

//change next to last for the original mupapu
/* Test if period is broken and after sunday */
function mup_is_week_broken($period, $now)
{
        $days = $period['from_dayofweek'];
        $hours = $period['from_hours'];
        $minutes = $period['from_minutes'];
        $timestamps['from'] = strtotime("last sunday +$days days $hours hours $minutes minutes", $now);

        $days = $period['to_dayofweek'];
        $hours = $period['to_hours'];
        $minutes = $period['to_minutes'];
        $timestamps['to'] = strtotime("last sunday +$days days $hours hours $minutes minutes", $now);

        return $timestamps['from'] > $timestamps['to'] && strtotime("last monday",$now) < $now && $now <  $timestamps['to'];
}

//create_period_query1
function mup_cpq1($circulationgroup_id, $now, $periods, $from_date = null)
{
    $sql = "
        SELECT
            a.id AS `arsimo_id`,
            a.article_id AS `aarticle_id`,
            a.size_id AS `asize_id`,
            r.articlenumber AS `articlecode`,
            r.description AS `description`,
            r.distributorlocation_id AS `distributorlocation_id`,
            s.name AS `size`,
            m.name AS `modification`,
            COALESCE(`loadadvice`.`demand`,0) AS `demand`,
            COALESCE(`circulation`.`cir_cur`,0) AS `cir_cur`,
            COALESCE(`stock_laundry`.`sto_l_cur`,0) AS `sto_l_cur`,
            COALESCE(`stock_hospital`.`sto_h_cur`,0) AS `sto_h_cur`, " . "\n";

    foreach($periods as $weeknr => $weeks) {
        foreach($weeks as $periodnr => $period) {
            $sql .= "COALESCE(`dist_available`.`w$weeknr.p$periodnr`,0) AS `hit_w$weeknr.p$periodnr`," . "\n";
            $sql .= "COALESCE(`dist_unavailable`.`w$weeknr.p$periodnr`,0) AS `miss_w$weeknr.p$periodnr`," . "\n";
        }
    }
    $sql = substr($sql, 0, -2) . "\n"; //remove last ','

    $sql .= "
        FROM
            arsimos a

        LEFT JOIN (
            " . mup_cpq2($circulationgroup_id, $now, $periods) . "
        ) `dist_available` ON  a.id = `dist_available`.arsimo_id


        LEFT JOIN (
            " . mup_cpq3($circulationgroup_id, $now, $periods) . "
        ) `dist_unavailable` ON a.id = `dist_unavailable`.arsimo_id

        LEFT JOIN (
            SELECT
                v.arsimo_id,
                SUM(v.demand) AS `demand`
            FROM
                distributorlocations_loadadvice v
            INNER JOIN arsimos a ON v.arsimo_id = a.id
            INNER JOIN distributorlocations l ON v.distributorlocation_id = l.id
            INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
            WHERE
                v.type =  'auto'
                AND c.id = $circulationgroup_id
            GROUP BY
                v.arsimo_id
        ) `loadadvice` ON a.id = `loadadvice`.arsimo_id

        LEFT JOIN (
            SELECT
                a.id AS `arsimo_id`,
                COUNT(l.id) AS `cir_cur`
            FROM
                arsimos AS a
            INNER JOIN garments AS g ON a.id = g.arsimo_id
            INNER JOIN scanlocations AS l ON g.scanlocation_id = l.id
            WHERE
                g.deleted_on IS NULL
                AND g.garmentuser_id IS NULL
                AND g.active = 1
                AND l.circulationgroup_id IS NOT NULL ";
                if(!empty($from_date)) {
                    $sql .= " AND g.lastscan IS NOT NULL ";
                    $sql .= " AND g.lastscan >= '".$from_date."' ";
                }
                $sql .= " AND g.circulationgroup_id = $circulationgroup_id
            GROUP BY
                a.id
        ) `circulation` ON a.id = `circulation`.arsimo_id

        LEFT JOIN (
            SELECT
                a.id AS `arsimo_id`,
                COUNT(g.id) AS `sto_h_cur`
            FROM
                arsimos a
            INNER JOIN garments g ON a.id = g.arsimo_id
            INNER JOIN scanlocations l ON g.scanlocation_id = l.id
            INNER JOIN scanlocationstatuses s ON l.scanlocationstatus_id = s.id
            WHERE
                s.name LIKE 'stock_hospital' 
            GROUP BY
                a.id
        ) `stock_hospital` ON a.id = `stock_hospital`.arsimo_id
        
        LEFT JOIN (
            SELECT
                a.id AS `arsimo_id`,
                COUNT(g.id) AS `sto_l_cur`
            FROM
                arsimos a
            INNER JOIN garments g ON a.id = g.arsimo_id
            INNER JOIN scanlocations l ON g.scanlocation_id = l.id
            INNER JOIN scanlocationstatuses s ON l.scanlocationstatus_id = s.id
            WHERE
                s.name LIKE 'stock_laundry' 
            GROUP BY
                a.id
        ) `stock_laundry` ON a.id = `stock_laundry`.arsimo_id

        INNER JOIN articles r ON a.article_id = r.id
        INNER JOIN sizes s ON a.size_id = s.id
        INNER JOIN sizegroups g ON s.sizegroup_id = g.id
        LEFT JOIN modifications m ON a.modification_id = m.id
        WHERE
            a.deleted_on IS NULL
        GROUP BY
            a.id
        ORDER BY
            r.description,
            s.position,
            m.name" . "\n";

    return $sql;
}

//create_period_query2: count distributions per period per garmentuser
function mup_cpq2($circulationgroup_id, $now, $periods)
{
    $sql = "
        SELECT
            d.arsimo_id," . "\n";

    foreach($periods as $weeknr => $weeks) {
        foreach($weeks as $periodnr => $period) {
            $from = $period['from'];
            $to = $period['to'];
            $sql .= "
                SUM(IF( (UNIX_TIMESTAMP(d.`date`) BETWEEN $from AND $to),1,0) ) AS 'w$weeknr.p$periodnr'," . "\n";
        }
    }

    $sql = substr($sql, 0, -2) . "\n"; // remove last ','
    $first_period = $periods[0][0]['from'];
    $last_period = $to;

    $sql .= "
        FROM
            log_distributorclients d
        INNER JOIN distributorlocations l ON d.distributorlocation_id = l.id
        INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
        LEFT JOIN settings s ON s.name = 'mupapu_use_superuser'
        WHERE
            UNIX_TIMESTAMP(d.date) BETWEEN  $first_period AND $last_period
            AND d.userbound = 0
            AND d.numgarments > 0
            AND d.buttonevent LIKE  'proceed'
            AND !ISNULL(d.superuser_id) AND IF(s.`value` = 0,d.superuser_id = 0,d.superuser_id >= 0)
            AND c.id = $circulationgroup_id
        GROUP BY
            d.arsimo_id" . "\n";

    return $sql;
}

//create_period_query3: count unavailable distributions per period per day per garmentuser
function mup_cpq3($circulationgroup_id, $now, $periods)
{
    $sql = "
        SELECT
            d.arsimo_id," . "\n";

    foreach($periods as $weeknr => $weeks) {
        foreach($weeks as $periodnr => $period) {
            $from = $period['from'];
            $to = $period['to'];
            $sql .= "
                COUNT(
                    DISTINCT IF(
                        (UNIX_TIMESTAMP(d.`date`) BETWEEN $from AND $to),
                        CONCAT(d.garmentuser_id,DATE(d.date)),NULL)
                    ) AS 'w$weeknr.p$periodnr'," . "\n";
        }
    }

    $sql = substr($sql, 0, -2) . "\n"; // remove last ','

    $first_period = $periods[0][0]['from'];
    $last_period = $to;

    $sql .= "
        FROM
            log_distributorclients d
        INNER JOIN distributorlocations l ON d.distributorlocation_id = l.id
        INNER JOIN circulationgroups c ON l.circulationgroup_id = c.id
        LEFT JOIN settings s ON s.name = 'mupapu_use_superuser'
        WHERE
            UNIX_TIMESTAMP(d.`date`) BETWEEN $first_period AND $last_period
            AND d.userbound = 0
            AND d.numgarments = 0
            AND !ISNULL(d.superuser_id) AND IF(s.`value` = 0,d.superuser_id = 0,d.superuser_id >= 0)
            AND c.id = $circulationgroup_id
        GROUP BY
            d.arsimo_id" . "\n";

    return $sql;
}


/*original mupapu
 IF(
        p.from_dayofweek < p.to_dayofweek,
        p.from_dayofweek * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
        AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60  < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60,
        IF(
            WEEKDAY(NOW())+1 >= p.from_dayofweek,
                p.from_dayofweek  * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < 691200,
                    86400 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
            AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60
        )
    ) AS 'original_is_current'
 */
 //generate mupapu
function mupapu_generate($circulationgroup_id, $numweeks, $calculate = null)
{
    //maximum number of weeks to calculate
    $max_numweeks = 52;

    //get the given number of weeks, or set default
    $numweeks = (isset($numweeks) && is_numeric($numweeks) && $numweeks > 0 && $numweeks <= $max_numweeks) ? $numweeks : $GLOBALS['config']['mupapu_default_weeks_history'];

    //read the total number of available hooks for this distributorlocation
    $th_sql = "
        SELECT SUM(`d`.`hooks`) -
       (
            SELECT IF(ISNULL(SUM(`ga`.`max_positions`)), 0, SUM(`ga`.`max_positions`)) AS 'max_positions'
              FROM `garmentusers_userbound_arsimos` `ga`
        INNER JOIN `garmentusers` `gu` ON `ga`.`garmentuser_id` = `gu`.`id` AND ";
    
    if($circulationgroup_id == 1) $th_sql .= " !ISNULL(gu.distributor_id)";
    else $th_sql .= " !ISNULL(gu.distributor_id$circulationgroup_id)";
    
    $th_sql .= "    INNER JOIN `circulationgroups_garmentusers` `cg` ON `cg`.`garmentuser_id` = `gu`.`id`
             WHERE `ga`.`enabled` = 1
               AND `gu`.`deleted_on` IS NULL
               AND `cg`.`circulationgroup_id` = $circulationgroup_id
               AND `ga`.`circulationgroup_id` = $circulationgroup_id
       ) AS totalhooks
          FROM `circulationgroups` `c`
    INNER JOIN `distributorlocations` `l` ON `l`.`circulationgroup_id` = `c`.`id`
    INNER JOIN `distributors` `d` ON `d`.`distributorlocation_id` = `l`.`id`
         WHERE `c`.`id` = $circulationgroup_id" . "\n";

    $th_res = db_query($th_sql);
    $th_row = db_fetch_row($th_res);
    $th = $th_row[0];

    //get the table of periods
    $sql = "
        SELECT
            p.from_dayofweek,
            p.from_hours,
            p.from_minutes,
            p.to_dayofweek,
            p.to_hours,
            p.to_minutes,
            -- WEEKDAY(NOW())+1 AS 'weekday',
            -- ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)
            -- HOUR(NOW()) AS 'hours',
            -- MINUTE(NOW()) AS 'minutes',
            IF(
                p.from_dayofweek < p.to_dayofweek,
                p.from_dayofweek * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60  < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60,
                IF(
                    WEEKDAY(NOW())+1 >= p.from_dayofweek,
                        p.from_dayofweek  * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                        AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < 691200,
                            86400 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                    AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60
                )
            ) AS 'is_current'
        FROM
            distributionperiods p
        WHERE
            p.circulationgroup_id = $circulationgroup_id" . "\n";

    $db_periods_res = db_query($sql);

    $db_periods = array();
    while ($row = db_fetch_assoc($db_periods_res)) {
        $db_periods[] = $row;
    }

    //get time only once
    $now = time();
    
    #$now = strtotime('yesterday');

    //collect periods in from-to format and ordered according to first complete period first.
    $periods = mup_gp($circulationgroup_id, $now, $db_periods, $numweeks);

    //initialize variables
    $mup = array();
    $t = null;
    $date_info = null;


    if ($calculate)
    {
        $sql = mup_cpq1($circulationgroup_id, $now, $periods);
        $mup_res = db_query($sql);

        $mup = array();

        $t['ars'] = 0;
        $t['demand_small'] = 0;
        $t['demand'] = 0;
        $t['demand_new'] = 0;
        $t['cir_cur'] = 0;
        $t['cir_new'] = 0;
        $t['cir_diff'] = 0;
        $t['cir_diff_pos'] = 0;
        $t['cir_diff_neg'] = 0;
        $t['sto_cur'] = 0;
        $t['sto_new'] = 0;
        $t['sto_diff'] = 0;
        $t['sto_diff_pos'] = 0;
        $t['sto_diff_neg'] = 0;
        $t['sto_h_cur'] = 0;
        $t['sto_l_cur'] = 0;        
        $t['order'] = 0;
        $t['hit.h'] = 0;
        $t['miss.h'] = 0;
        $t['hitmiss.h'] = 0;
        $t['hit.t'] = 0;
        $t['miss.t'] = 0;
        $t['hitmiss.t'] = 0;
        $t['hit.ht'] = 0;
        $t['miss.ht'] = 0;
        $t['hitmiss.ht'] = 0;

        while ($row = db_fetch_array($mup_res))
        {
            //fetch basic data
            $ars = $row['arsimo_id'];
            $mup[$ars] = $row;

            //increment arsimos total
            $t['ars']++;

            //init highest period
            $mup[$ars]['hit.h'] = 0;
            $mup[$ars]['miss.h'] = 0;
            $mup[$ars]['hitmiss.h'] = 0;
            $mup[$ars]['demand_new'] = 0;
            $mup[$ars]['sto_cur'] = $mup[$ars]['sto_h_cur'] + $mup[$ars]['sto_l_cur'];

            //handle each week
            for ($w = 0; isset($row['hit_w'.$w.'.p0']); $w++)
            {
                //handle each period for this week within this arsimo
                for ($p = 0; isset($row['hit_w'.$w.'.p'.$p]); $p++)
                {
                    //presetting hit+miss makes our shit easier to read
                    $mup[$ars]['hitmiss_w'.$w.'.p'.$p] = ($row['hit_w'.$w.'.p'.$p] + $row['miss_w'.$w.'.p'.$p]);

                    //init highest period
                    if (!isset($mup[$ars]['hit_p'.$p.'.h'])) $mup[$ars]['hit_p'.$p.'.h'] = 0;
                    if (!isset($mup[$ars]['miss_p'.$p.'.h'])) $mup[$ars]['miss_p'.$p.'.h'] = 0;
                    if (!isset($mup[$ars]['hitmiss_p'.$p.'.h'])) $mup[$ars]['hitmiss_p'.$p.'.h'] = 0;

                    //get highest week for this period within this arsimo
                    if ($mup[$ars]['hitmiss_w'.$w.'.p'.$p] > $mup[$ars]['hitmiss_p'.$p.'.h'])
                    {
                        $mup[$ars]['hit_p'.$p.'.h'] = $mup[$ars]['hit_w'.$w.'.p'.$p];
                        $mup[$ars]['miss_p'.$p.'.h'] = $mup[$ars]['miss_w'.$w.'.p'.$p];
                        $mup[$ars]['hitmiss_p'.$p.'.h'] = $mup[$ars]['hitmiss_w'.$w.'.p'.$p];

                        //highest over all weeks
                        if ($mup[$ars]['hitmiss_w'.$w.'.p'.$p] > $mup[$ars]['hitmiss.h'])
                        {
                            $mup[$ars]['hit.h'] = $mup[$ars]['hit_w'.$w.'.p'.$p];
                            $mup[$ars]['miss.h'] = $mup[$ars]['miss_w'.$w.'.p'.$p];
                            $mup[$ars]['hitmiss.h'] = $mup[$ars]['hitmiss_w'.$w.'.p'.$p];
                        }
                    }

                    if (!isset($mup[$ars]['weeks_info.p'.$p]))
                    {
                        $mup[$ars]['weeks_info.p'.$p] =  ($w+1) . "e week: " . $mup[$ars]['hitmiss_w'.$w.'.p'.$p] . "<br />";
                    } else {
                        $mup[$ars]['weeks_info.p'.$p] .= ($w+1) . "e week: " . $mup[$ars]['hitmiss_w'.$w.'.p'.$p] . "<br />";
                    }
                }
            }

            //calculate the percentage over the total hitmiss per arsimo (or 0) for each period
            for ($p = 0; isset($mup[$ars]['hitmiss_p'.$p.'.h']); $p++)
            {
                if (isset($mup[$ars]['hitmiss_p'.$p]) && isset($mup[$ars]['hitmiss.ht']) && isset($mup[$ars]['hitmiss_p'.$p]))
                {
                    $mup[$ars]['hitmiss_p'.$p.'.perc'] = (($mup[$ars]['hitmiss_p'.$p] > 0) ? round((100 / $mup[$ars]['hitmiss.ht']) * $mup[$ars]['hitmiss_p'.$p]) : 0);
                }
            }

            //calculate the new required load
            if ($mup[$ars]['hitmiss.h'] > 0)
            {
                if ($mup[$ars]['hitmiss.h'] < 16)
                {
                    $mup[$ars]['demand_new'] = $mup[$ars]['hitmiss.h'] + 1;
                    $t['demand_small'] += $mup[$ars]['demand_new'];
                } else {
                    $mup[$ars]['demand_new'] = round($mup[$ars]['hitmiss.h']);
                }
            } else {
                $mup[$ars]['demand_new'] = 0;
            }

            //calculate the new circulation
            $mup[$ars]['cir_new'] = ceil($mup[$ars]['hitmiss.h'] * $GLOBALS['config']['mupapu_circulation_multiplier']);

            //calculate the new stock
            $mup[$ars]['sto_new'] = ceil($mup[$ars]['cir_new'] * $GLOBALS['config']['mupapu_stock_multiplier']);

            if ($mup[$ars]['cir_new'] > 0 && $mup[$ars]['sto_new'] < $GLOBALS['config']['mupapu_stock_minimal'])
                $mup[$ars]['sto_new'] = $GLOBALS['config']['mupapu_stock_minimal'];

            if ($mup[$ars]['sto_new'] > $GLOBALS['config']['mupapu_stock_maximal'])
                $mup[$ars]['sto_new'] = $GLOBALS['config']['mupapu_stock_maximal'];

            //cir_new - cir_cur = cir_diff
            $mup[$ars]['cir_diff'] = $mup[$ars]['cir_new'] - $mup[$ars]['cir_cur'];

            //sto_new - sto_cur = sto_diff
            $mup[$ars]['sto_diff'] = ($mup[$ars]['sto_new'] - $mup[$ars]['sto_cur']);

            //calculate the amount to order
            $mup[$ars]['order'] = (($mup[$ars]['cir_diff'] + $mup[$ars]['sto_diff']));

            //if the amount to order is negative, set 0
            if ($mup[$ars]['order'] < 0) $mup[$ars]['order'] = 0;

            //final totals of highest periods
            $t['hit.ht'] += ((isset($mup[$ars]['hit.h'])) ? $mup[$ars]['hit.h'] : 0);
            $t['miss.ht'] += ((isset($mup[$ars]['miss.h'])) ? $mup[$ars]['miss.h'] : 0);
            $t['hitmiss.ht'] += ((isset($mup[$ars]['hitmiss.h'])) ? $mup[$ars]['hitmiss.h'] : 0);

            //final totals
            $t['hit.t'] += ((isset($mup[$ars]['hit.ht'])) ? $mup[$ars]['hit.ht'] : 0);
            $t['miss.t'] += ((isset($mup[$ars]['miss.ht'])) ? $mup[$ars]['miss.ht'] : 0);
            $t['hitmiss.t'] += ((isset($mup[$ars]['hitmiss.ht'])) ? $mup[$ars]['hitmiss.ht'] : 0);

            //total of current demand
            $t['demand'] += $mup[$ars]['demand'];

            //total of new demand
            $t['demand_new'] += $mup[$ars]['demand_new'];

            //total of current circulation
            $t['cir_cur'] += $mup[$ars]['cir_cur'];

            //total of new circulation
            $t['cir_new'] += $mup[$ars]['cir_new'];

            //total cir_diff (positive and negative)
            if ($mup[$ars]['cir_diff'] > 0) {
                $t['cir_diff_pos'] += $mup[$ars]['cir_diff'];
            } elseif ($mup[$ars]['cir_diff'] < 0) {
                $t['cir_diff_neg'] -= $mup[$ars]['cir_diff'];
            }

            //total of current stock
            $t['sto_cur'] += $mup[$ars]['sto_cur'];

            //total of new stock
            $t['sto_new'] += $mup[$ars]['sto_new'];

            //total sto_diff (positive and negative)
            if ($mup[$ars]['sto_diff'] > 0) {
                $t['sto_diff_pos'] += $mup[$ars]['sto_diff'];
            } elseif ($mup[$ars]['sto_diff'] < 0) {
                $t['sto_diff_neg'] -= $mup[$ars]['sto_diff'];
            }

            //total to order
            $t['order'] += $mup[$ars]['order'];

            if ( $mup[$ars]['demand'] == 0 && $mup[$ars]['cir_cur'] == 0 && $mup[$ars]['sto_cur'] == 0 &&
                $mup[$ars]['demand_new'] == 0 && $mup[$ars]['cir_new'] == 0 && $mup[$ars]['sto_new'] == 0) {
                unset($mup[$ars]);
            }

        }

        //db_free_result($mup_res);
        unset($mup_res);

        //init final totals of highest periods
        $t['hit.ht'] = 0;
        $t['miss.ht'] = 0;
        $t['hitmiss.ht'] = 0;

        //init final totals
        $t['hit.t'] = 0;
        $t['miss.t'] = 0;
        $t['hitmiss.t'] = 0;

        $t['demand_new2'] = $t['demand_new'];
        $t['demand_new'] = 0;


        //things we do better in a separate loop
        foreach ($mup as $ars => $row)
        {
            //init totals for this arsimo
            $mup[$ars]['hit.t'] = 0;
            $mup[$ars]['miss.t'] = 0;
            $mup[$ars]['hitmiss.t'] = 0;

            //init highest-total for this arsimo
            $mup[$ars]['hit.ht'] = 0;
            $mup[$ars]['miss.ht'] = 0;
            $mup[$ars]['hitmiss.ht'] = 0;

            //correct the number of lde_new based on the number of available hooks
            
            if($mup[$ars]['hitmiss.h'] >= 16) {
                $mup[$ars]['demand_new'] = (($t['demand_new2'] > 0) ? round(($mup[$ars]['demand_new'] / ($t['demand_new2'] - $t['demand_small'])) * ($th - $t['demand_small'])) : 0);
            }
            
            //recalculate total of new demand
            $t['demand_new'] += $mup[$ars]['demand_new'];

            //handle each highest period for this arsimo
            for ($p = 0; isset($row['hit_p'.$p.'.h']); $p++)
            {
                //totals of highest periods per arsimo
                $mup[$ars]['hit.ht'] += $mup[$ars]['hit_p'.$p.'.h'];
                $mup[$ars]['miss.ht'] += $mup[$ars]['miss_p'.$p.'.h'];
                $mup[$ars]['hitmiss.ht'] += $mup[$ars]['hitmiss_p'.$p.'.h'];
            }
        }

        //total number of weeks/periods
        $t['w'] = $w;
        $t['p'] = $p;
    }

    //return MUPAPU array
    return array(
        'periods' => $periods,
        'mup' => $mup,
        't' => $t,
        'numweeks' => $numweeks,
        'max_numweeks' => $max_numweeks
    );
}

//generate mupapu
function mupapu_generate_with_lastscan_date($circulationgroup_id, $numweeks, $calculate, $from_date = null)
{
    //maximum number of weeks to calculate
    $max_numweeks = 10;

    //get the given number of weeks, or set default
    $numweeks = (isset($numweeks) && is_numeric($numweeks) && $numweeks > 0 && $numweeks <= $max_numweeks) ? $numweeks : $GLOBALS['config']['mupapu_default_weeks_history'];

    //read the total number of available hooks for this distributorlocation
    $th_sql = "
        SELECT SUM(`d`.`hooks`) -
       (
            SELECT IF(ISNULL(SUM(`ga`.`max_positions`)), 0, SUM(`ga`.`max_positions`)) AS 'max_positions'
              FROM `garmentusers_userbound_arsimos` `ga`
        INNER JOIN `garmentusers` `gu` ON `ga`.`garmentuser_id` = `gu`.`id` AND ";
    
    if($circulationgroup_id == 1) $th_sql .= " !ISNULL(gu.distributor_id)";
    else $th_sql .= " !ISNULL(gu.distributor_id$circulationgroup_id)";
    
    $th_sql .= "    INNER JOIN `circulationgroups_garmentusers` `cg` ON `cg`.`garmentuser_id` = `gu`.`id`
             WHERE `ga`.`enabled` = 1
               AND `gu`.`deleted_on` IS NULL
               AND `cg`.`circulationgroup_id` = $circulationgroup_id
               AND `ga`.`circulationgroup_id` = $circulationgroup_id
       ) AS totalhooks
          FROM `circulationgroups` `c`
    INNER JOIN `distributorlocations` `l` ON `l`.`circulationgroup_id` = `c`.`id`
    INNER JOIN `distributors` `d` ON `d`.`distributorlocation_id` = `l`.`id`
         WHERE `c`.`id` = $circulationgroup_id" . "\n";

    $th_res = db_query($th_sql);
    $th_row = db_fetch_row($th_res);
    $th = $th_row[0];

    //get the table of periods
    $sql = "
        SELECT
            p.from_dayofweek,
            p.from_hours,
            p.from_minutes,
            p.to_dayofweek,
            p.to_hours,
            p.to_minutes,
            -- WEEKDAY(NOW())+1 AS 'weekday',
            -- ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)
            -- HOUR(NOW()) AS 'hours',
            -- MINUTE(NOW()) AS 'minutes',
            IF(
                p.from_dayofweek < p.to_dayofweek,
                p.from_dayofweek * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60  < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60,
                IF(
                    WEEKDAY(NOW())+1 >= p.from_dayofweek,
                        p.from_dayofweek  * 86400 + p.from_hours * 3600 + p.from_minutes * 60 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                        AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < 691200,
                            86400 <= (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60
                    AND (WEEKDAY(NOW())+1) * 86400 + HOUR(NOW()) * 3600 + MINUTE(NOW()) * 60 < p.to_dayofweek * 86400 + p.to_hours * 3600 + p.to_minutes * 60
                )
            ) AS 'is_current'
        FROM
            distributionperiods p
        WHERE
            p.circulationgroup_id = $circulationgroup_id" . "\n";

    $db_periods_res = db_query($sql);

    $db_periods = array();
    while ($row = db_fetch_assoc($db_periods_res)) {
        $db_periods[] = $row;
    }

    //get time only once
    $now = time();
    
    #$now = strtotime('yesterday');

    //collect periods in from-to format and ordered according to first complete period first.
    $periods = mup_gp($circulationgroup_id, $now, $db_periods, $numweeks);

    //initialize variables
    $mup = array();
    $t = null;
    $date_info = null;


    if ($calculate)
    {
        $sql = mup_cpq1($circulationgroup_id, $now, $periods, $from_date);
        $mup_res = db_query($sql);

        $mup = array();

        $t['ars'] = 0;
        $t['demand_small'] = 0;
        $t['demand'] = 0;
        $t['demand_new'] = 0;
        $t['cir_cur'] = 0;
        $t['cir_new'] = 0;
        $t['cir_diff'] = 0;
        $t['cir_diff_pos'] = 0;
        $t['cir_diff_neg'] = 0;
        $t['sto_cur'] = 0;
        $t['sto_new'] = 0;
        $t['sto_diff'] = 0;
        $t['sto_diff_pos'] = 0;
        $t['sto_diff_neg'] = 0;
        $t['order'] = 0;
        $t['hit.h'] = 0;
        $t['miss.h'] = 0;
        $t['hitmiss.h'] = 0;
        $t['hit.t'] = 0;
        $t['miss.t'] = 0;
        $t['hitmiss.t'] = 0;
        $t['hit.ht'] = 0;
        $t['miss.ht'] = 0;
        $t['hitmiss.ht'] = 0;

        while ($row = db_fetch_array($mup_res))
        {
            //fetch basic data
            $ars = $row['arsimo_id'];
            $mup[$ars] = $row;

            //increment arsimos total
            $t['ars']++;

            //init highest period
            $mup[$ars]['hit.h'] = 0;
            $mup[$ars]['miss.h'] = 0;
            $mup[$ars]['hitmiss.h'] = 0;
            $mup[$ars]['demand_new'] = 0;
            $mup[$ars]['sto_cur'] = $mup[$ars]['sto_h_cur'] + $mup[$ars]['sto_l_cur'];

            //handle each week
            for ($w = 0; isset($row['hit_w'.$w.'.p0']); $w++)
            {
                //handle each period for this week within this arsimo
                for ($p = 0; isset($row['hit_w'.$w.'.p'.$p]); $p++)
                {
                    //presetting hit+miss makes our shit easier to read
                    $mup[$ars]['hitmiss_w'.$w.'.p'.$p] = ($row['hit_w'.$w.'.p'.$p] + $row['miss_w'.$w.'.p'.$p]);

                    //init highest period
                    if (!isset($mup[$ars]['hit_p'.$p.'.h'])) $mup[$ars]['hit_p'.$p.'.h'] = 0;
                    if (!isset($mup[$ars]['miss_p'.$p.'.h'])) $mup[$ars]['miss_p'.$p.'.h'] = 0;
                    if (!isset($mup[$ars]['hitmiss_p'.$p.'.h'])) $mup[$ars]['hitmiss_p'.$p.'.h'] = 0;

                    //get highest week for this period within this arsimo
                    if ($mup[$ars]['hitmiss_w'.$w.'.p'.$p] > $mup[$ars]['hitmiss_p'.$p.'.h'])
                    {
                        $mup[$ars]['hit_p'.$p.'.h'] = $mup[$ars]['hit_w'.$w.'.p'.$p];
                        $mup[$ars]['miss_p'.$p.'.h'] = $mup[$ars]['miss_w'.$w.'.p'.$p];
                        $mup[$ars]['hitmiss_p'.$p.'.h'] = $mup[$ars]['hitmiss_w'.$w.'.p'.$p];

                        //highest over all weeks
                        if ($mup[$ars]['hitmiss_w'.$w.'.p'.$p] > $mup[$ars]['hitmiss.h'])
                        {
                            $mup[$ars]['hit.h'] = $mup[$ars]['hit_w'.$w.'.p'.$p];
                            $mup[$ars]['miss.h'] = $mup[$ars]['miss_w'.$w.'.p'.$p];
                            $mup[$ars]['hitmiss.h'] = $mup[$ars]['hitmiss_w'.$w.'.p'.$p];
                        }
                    }

                    if (!isset($mup[$ars]['weeks_info.p'.$p]))
                    {
                        $mup[$ars]['weeks_info.p'.$p] =  ($w+1) . "e week: " . $mup[$ars]['hitmiss_w'.$w.'.p'.$p] . "<br />";
                    } else {
                        $mup[$ars]['weeks_info.p'.$p] .= ($w+1) . "e week: " . $mup[$ars]['hitmiss_w'.$w.'.p'.$p] . "<br />";
                    }
                }
            }

            //calculate the percentage over the total hitmiss per arsimo (or 0) for each period
            for ($p = 0; isset($mup[$ars]['hitmiss_p'.$p.'.h']); $p++)
            {
                if (isset($mup[$ars]['hitmiss_p'.$p]) && isset($mup[$ars]['hitmiss.ht']) && isset($mup[$ars]['hitmiss_p'.$p]))
                {
                    $mup[$ars]['hitmiss_p'.$p.'.perc'] = (($mup[$ars]['hitmiss_p'.$p] > 0) ? round((100 / $mup[$ars]['hitmiss.ht']) * $mup[$ars]['hitmiss_p'.$p]) : 0);
                }
            }

            //calculate the new required load
            if ($mup[$ars]['hitmiss.h'] > 0)
            {
                if ($mup[$ars]['hitmiss.h'] < 16)
                {
                    $mup[$ars]['demand_new'] = $mup[$ars]['hitmiss.h'] + 1;
                    $t['demand_small'] += $mup[$ars]['demand_new'];
                } else {
                    $mup[$ars]['demand_new'] = round($mup[$ars]['hitmiss.h']);
                }
            } else {
                $mup[$ars]['demand_new'] = 0;
            }

            //calculate the new circulation
            $mup[$ars]['cir_new'] = ceil($mup[$ars]['hitmiss.h'] * $GLOBALS['config']['mupapu_circulation_multiplier']);

            //calculate the new stock
            $mup[$ars]['sto_new'] = ceil($mup[$ars]['cir_new'] * $GLOBALS['config']['mupapu_stock_multiplier']);

            if ($mup[$ars]['cir_new'] > 0 && $mup[$ars]['sto_new'] < $GLOBALS['config']['mupapu_stock_minimal'])
                $mup[$ars]['sto_new'] = $GLOBALS['config']['mupapu_stock_minimal'];

            if ($mup[$ars]['sto_new'] > $GLOBALS['config']['mupapu_stock_maximal'])
                $mup[$ars]['sto_new'] = $GLOBALS['config']['mupapu_stock_maximal'];

            //cir_new - cir_cur = cir_diff
            $mup[$ars]['cir_diff'] = $mup[$ars]['cir_new'] - $mup[$ars]['cir_cur'];

            //sto_new - sto_cur = sto_diff
            $mup[$ars]['sto_diff'] = ($mup[$ars]['sto_new'] - $mup[$ars]['sto_cur']);

            //calculate the amount to order
            $mup[$ars]['order'] = (($mup[$ars]['cir_diff'] + $mup[$ars]['sto_diff']));

            //if the amount to order is negative, set 0
            if ($mup[$ars]['order'] < 0) $mup[$ars]['order'] = 0;

            //final totals of highest periods
            $t['hit.ht'] += ((isset($mup[$ars]['hit.h'])) ? $mup[$ars]['hit.h'] : 0);
            $t['miss.ht'] += ((isset($mup[$ars]['miss.h'])) ? $mup[$ars]['miss.h'] : 0);
            $t['hitmiss.ht'] += ((isset($mup[$ars]['hitmiss.h'])) ? $mup[$ars]['hitmiss.h'] : 0);

            //final totals
            $t['hit.t'] += ((isset($mup[$ars]['hit.ht'])) ? $mup[$ars]['hit.ht'] : 0);
            $t['miss.t'] += ((isset($mup[$ars]['miss.ht'])) ? $mup[$ars]['miss.ht'] : 0);
            $t['hitmiss.t'] += ((isset($mup[$ars]['hitmiss.ht'])) ? $mup[$ars]['hitmiss.ht'] : 0);

            //total of current demand
            $t['demand'] += $mup[$ars]['demand'];

            //total of new demand
            $t['demand_new'] += $mup[$ars]['demand_new'];

            //total of current circulation
            $t['cir_cur'] += $mup[$ars]['cir_cur'];

            //total of new circulation
            $t['cir_new'] += $mup[$ars]['cir_new'];

            //total cir_diff (positive and negative)
            if ($mup[$ars]['cir_diff'] > 0) {
                $t['cir_diff_pos'] += $mup[$ars]['cir_diff'];
            } elseif ($mup[$ars]['cir_diff'] < 0) {
                $t['cir_diff_neg'] -= $mup[$ars]['cir_diff'];
            }

            //total of current stock
            $t['sto_cur'] += $mup[$ars]['sto_cur'];

            //total of new stock
            $t['sto_new'] += $mup[$ars]['sto_new'];

            //total sto_diff (positive and negative)
            if ($mup[$ars]['sto_diff'] > 0) {
                $t['sto_diff_pos'] += $mup[$ars]['sto_diff'];
            } elseif ($mup[$ars]['sto_diff'] < 0) {
                $t['sto_diff_neg'] -= $mup[$ars]['sto_diff'];
            }

            //total to order
            $t['order'] += $mup[$ars]['order'];

            if ( $mup[$ars]['demand'] == 0 && $mup[$ars]['cir_cur'] == 0 && $mup[$ars]['sto_cur'] == 0 &&
                $mup[$ars]['demand_new'] == 0 && $mup[$ars]['cir_new'] == 0 && $mup[$ars]['sto_new'] == 0) {
                unset($mup[$ars]);
            }

        }

        //db_free_result($mup_res);
        unset($mup_res);

        //init final totals of highest periods
        $t['hit.ht'] = 0;
        $t['miss.ht'] = 0;
        $t['hitmiss.ht'] = 0;

        //init final totals
        $t['hit.t'] = 0;
        $t['miss.t'] = 0;
        $t['hitmiss.t'] = 0;

        $t['demand_new2'] = $t['demand_new'];
        $t['demand_new'] = 0;


        //things we do better in a separate loop
        foreach ($mup as $ars => $row)
        {
            //init totals for this arsimo
            $mup[$ars]['hit.t'] = 0;
            $mup[$ars]['miss.t'] = 0;
            $mup[$ars]['hitmiss.t'] = 0;

            //init highest-total for this arsimo
            $mup[$ars]['hit.ht'] = 0;
            $mup[$ars]['miss.ht'] = 0;
            $mup[$ars]['hitmiss.ht'] = 0;

            //correct the number of lde_new based on the number of available hooks
            if($mup[$ars]['hitmiss.h'] >= 16) {
                $mup[$ars]['demand_new'] = (($t['demand_new2'] > 0) ? round(($mup[$ars]['demand_new'] / ($t['demand_new2'] - $t['demand_small'])) * ($th - $t['demand_small'])) : 0);
            }
            
            //recalculate total of new demand
            $t['demand_new'] += $mup[$ars]['demand_new'];

            //handle each highest period for this arsimo
            for ($p = 0; isset($row['hit_p'.$p.'.h']); $p++)
            {
                //totals of highest periods per arsimo
                $mup[$ars]['hit.ht'] += $mup[$ars]['hit_p'.$p.'.h'];
                $mup[$ars]['miss.ht'] += $mup[$ars]['miss_p'.$p.'.h'];
                $mup[$ars]['hitmiss.ht'] += $mup[$ars]['hitmiss_p'.$p.'.h'];
            }
        }

        //total number of weeks/periods
        $t['w'] = $w;
        $t['p'] = $p;
    }

    //return MUPAPU array
    return array(
        'periods' => $periods,
        'mup' => $mup,
        't' => $t,
        'numweeks' => $numweeks,
        'max_numweeks' => $max_numweeks
    );
}

?>
