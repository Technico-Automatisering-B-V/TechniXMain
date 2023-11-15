<?php

/**
 * Sizes
 *
 * @author    G. I. Voros <gabor@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Include necessary files
 */
require_once "include/engine.php";
require_once "include/mupapu.php";
require_once "library/bootstrap.php";

/**
 * Collect page content
 */
//-1 a helyes
if ((date("W")-1)%4 === 0){
    if(date("W") === "1" || date("W") === "01") {
        $last_period = 13;
        $last_period_year = (date("Y", time() - 172800));
        $other_period = $last_period-1;
        $other_period_year = (date("Y", time() - 172800));
    } elseif(date("W") === "5" || date("W") === "05") {
        $last_period = 1;
        $last_period_year = date("Y");
        $other_period = 13;
        $other_period_year = (date("Y")-1);
    } else {
        $last_period = (date("W")-1)/4;
        $last_period_year = date("Y");
        $other_period = $last_period-1;
        $other_period_year = date("Y");
    }

    $circulationgroups_sql = "SELECT `id`, `name` FROM `circulationgroups`";
    $circulationgroups_sql = db_query($circulationgroups_sql);
    while ($row = db_fetch_assoc($circulationgroups_sql)){
        $circulationgroup_id = $row["id"];
        $location = $row["name"];

        $total_distributed =
        $total_misseized =
        $table_header =
        $table_data =
        $article_table_data =
        $artikel_table_header =
        $table_distributed_by_week =
        $table_misseized_by_week =
        $article_dist_by_week_sql =
        $table_misseized_by_week_in_percentage =
        $article_table_rows =
        $final_data = null;

        for($i=3; $i>=0; $i--) {
            $table_header .= '<td style="padding: 5px;font-weight:bold;">'.(($last_period*4)-$i).'</td>';
            $distributed_sql = "SELECT log_garmentusers_garments.*
                                  FROM log_garmentusers_garments
                            INNER JOIN distributors ON distributors.id = log_garmentusers_garments.distributor_id
                            INNER JOIN distributorlocations ON distributorlocations.id = distributors.distributorlocation_id
                                 WHERE WEEK(endtime, 1)  = WEEK(CURDATE() - INTERVAL ". ($i+1) ." WEEK, 1)
                                   AND YEAR(endtime) = YEAR(CURDATE() - INTERVAL ". ($i+1) ." WEEK)
                                   AND distributorlocations.circulationgroup_id = ".$circulationgroup_id;

            $distributed_by_week = db_num_rows(db_query($distributed_sql));
            $total_distributed = $total_distributed + $distributed_by_week;
            $table_distributed_by_week.='<td>'.number_format($distributed_by_week, 0, ',', '.').'</td>';

            $misseized_sql = "SELECT COALESCE(SUM(table1.count),0)
                                FROM
                                (SELECT
                                        COUNT(DISTINCT(misseized.garmentuser_id)) AS 'count'
                                        FROM (
                                            SELECT log_distributorclients.*
                                            FROM log_distributorclients
                                            WHERE log_distributorclients.numgarments = 0
                                            AND WEEK(log_distributorclients.date, 1)  = WEEK(CURDATE() - INTERVAL ". ($i+1) ." WEEK, 1)
                                                AND YEAR(log_distributorclients.date) = YEAR(CURDATE() - INTERVAL ". ($i+1) ." WEEK)
                                        ) `misseized`
                                        INNER JOIN distributorlocations ON misseized.distributorlocation_id = distributorlocations.id
                                        INNER JOIN circulationgroups ON distributorlocations.circulationgroup_id = circulationgroups.id
                                        WHERE circulationgroups.id = ".$circulationgroup_id."
                                        GROUP BY
                                        DATE(misseized.`date`),
                                        misseized.userbound,
                                        misseized.arsimo_id
                                        ORDER BY
                                        DATE(misseized.`date`) ASC,
                                        circulationgroups.id ASC,
                                        misseized.userbound,
                                        COUNT(misseized.garmentuser_id) DESC
                                        ) as table1";

            $misseized_by_week_r = db_fetch_row(db_query($misseized_sql));
            $misseized_by_week = $misseized_by_week_r[0];
            $total_misseized = $total_misseized + $misseized_by_week;
            $misseized_by_week_in_percentage = ($misseized_by_week > 0 && $distributed_by_week > 0)?(round($misseized_by_week/$distributed_by_week*100,1)):0;

            $table_misseized_by_week.='<td>'.number_format($misseized_by_week, 0, ',', '.').'</td>';
            $table_misseized_by_week_in_percentage.='<td>'.$misseized_by_week_in_percentage.'%</td>';


        }

        $total_misseized_other_period_sql = "SELECT COALESCE(SUM(table1.count),0)
                        FROM
                        (SELECT COUNT(DISTINCT(misseized.garmentuser_id)) AS 'count'
                            FROM (
                                SELECT log_distributorclients.*
                                FROM log_distributorclients
                                WHERE log_distributorclients.numgarments = 0
                                        AND WEEK(log_distributorclients.date, 1) BETWEEN WEEK(CURDATE() - INTERVAL 8 WEEK, 1)
                                        AND WEEK(CURDATE() - INTERVAL 5 WEEK, 1) AND YEAR(log_distributorclients.date) = YEAR(CURDATE() - INTERVAL 5 WEEK)
                                ) `misseized`
                            INNER JOIN distributorlocations ON misseized.distributorlocation_id = distributorlocations.id
                            INNER JOIN circulationgroups ON distributorlocations.circulationgroup_id = circulationgroups.id
                            WHERE circulationgroups.id = ".$circulationgroup_id."
                            GROUP BY
                            DATE(misseized.`date`),
                            misseized.userbound,
                            misseized.arsimo_id
                            ORDER BY
                            DATE(misseized.`date`) ASC,
                            circulationgroups.id ASC,
                            misseized.userbound,
                            COUNT(misseized.garmentuser_id) DESC
                        ) as table1";

        $total_misseized_other_period_r = db_fetch_row(db_query($total_misseized_other_period_sql));
        $total_misseized_other_period = $total_misseized_other_period_r[0];

        $total_distributed_other_period_sql = "SELECT *
                    FROM log_garmentusers_garments
                    INNER JOIN distributors ON distributors.id = log_garmentusers_garments.distributor_id
                    INNER JOIN distributorlocations ON distributorlocations.id = distributors.distributorlocation_id
                    WHERE WEEK(endtime, 1) BETWEEN WEEK(CURDATE() - INTERVAL 8 WEEK, 1)
                        AND WEEK(CURDATE() - INTERVAL 5 WEEK, 1) AND YEAR(endtime) = YEAR(CURDATE() - INTERVAL 5 WEEK)
                        AND distributorlocations.circulationgroup_id = ".$circulationgroup_id;
        $total_distributed_other_period = db_num_rows(db_query($total_distributed_other_period_sql));

        $garments_in_roulation_sql = 'SELECT count(*)
            FROM garments
            INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
            INNER JOIN scanlocations ON garments.scanlocation_id = scanlocations.id
            INNER JOIN scanlocationstatuses ON scanlocations.scanlocationstatus_id = scanlocationstatuses.id
            WHERE garments.deleted_on is null AND scanlocations.circulationgroup_id is not null
            AND scanlocationstatuses.id IN (7,8,9,10,11,12,13)
            AND garments.circulationgroup_id = '.$circulationgroup_id.'
            ORDER BY garments.lastscan DESC';

        $garments_in_roulation_r = db_fetch_row(db_query($garments_in_roulation_sql));
        $garments_in_roulation = $garments_in_roulation_r[0];

        $garments_in_storage_sql = 'SELECT count(*)
            FROM garments
            INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
            INNER JOIN scanlocations ON garments.scanlocation_id = scanlocations.id
            INNER JOIN scanlocationstatuses ON scanlocations.scanlocationstatus_id = scanlocationstatuses.id
            WHERE garments.deleted_on is null AND garments.circulationgroup_id = '.$circulationgroup_id.'
            AND scanlocationstatuses.id = 3
            ORDER BY garments.lastscan DESC';

        $garments_in_storage_r = db_fetch_row(db_query($garments_in_storage_sql));
        $garments_in_storage = $garments_in_storage_r[0];

        $deleted_garments_sql = 'SELECT count(*)
            FROM garments
            WHERE !ISNULL(garments.deleted_on) AND garments.circulationgroup_id = '.$circulationgroup_id.'
            ORDER BY garments.lastscan DESC';


        $deleted_garments_r = db_fetch_row(db_query($deleted_garments_sql));
        $deleted_garments = $deleted_garments_r[0];


        $garments_seven_days_distributed_sql = 'SELECT count(*)
            FROM garments
            INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
            INNER JOIN scanlocations ON garments.scanlocation_id = scanlocations.id
            INNER JOIN scanlocationstatuses ON scanlocations.scanlocationstatus_id = scanlocationstatuses.id
            WHERE garments.deleted_on is null AND scanlocations.circulationgroup_id is not null
            AND scanlocationstatuses.id = 10
            AND garments.circulationgroup_id = '.$circulationgroup_id.'
            AND garments.lastscan <= (SELECT DATE_ADD(DATE(NOW()),INTERVAL -6 DAY))
            order by garments.lastscan DESC';

        $garments_seven_days_distributed_r = db_fetch_row(db_query($garments_seven_days_distributed_sql));
        $garments_seven_days_distributed = $garments_seven_days_distributed_r[0];


        $garments_seven_days_laundry_sql = 'SELECT count(*)
            FROM garments
            INNER JOIN arsimos ON garments.arsimo_id = arsimos.id
            INNER JOIN scanlocations ON garments.scanlocation_id = scanlocations.id
            INNER JOIN scanlocationstatuses ON scanlocations.scanlocationstatus_id = scanlocationstatuses.id
            WHERE garments.deleted_on is null AND scanlocations.circulationgroup_id is not null
            AND scanlocationstatuses.id = 13
            AND garments.circulationgroup_id = '.$circulationgroup_id.'
            AND garments.lastscan <= (SELECT DATE_ADD(DATE(NOW()),INTERVAL -6 DAY))
            order by garments.lastscan DESC';

        $garments_seven_days_laundry_r = db_fetch_row(db_query($garments_seven_days_laundry_sql));
        $garments_seven_days_laundry = $garments_seven_days_laundry_r[0];


        $garmentusers_total_sql = 'SELECT count(*) from garmentusers
            INNER JOIN circulationgroups_garmentusers ON circulationgroups_garmentusers.garmentuser_id = garmentusers.id
            WHERE ISNULL(garmentusers.deleted_on)
            AND circulationgroups_garmentusers.circulationgroup_id = '.$circulationgroup_id;

        $garmentusers_total_r = db_fetch_row(db_query($garmentusers_total_sql));
        $garmentusers_total = $garmentusers_total_r[0];


        $garmentusers_total_taken_out_sql = 'SELECT count(*)
            FROM
            (SELECT DISTINCT log_distributorclients.garmentuser_id
            FROM log_distributorclients
            INNER JOIN distributorlocations ON distributorlocations.id = log_distributorclients.distributorlocation_id
            INNER JOIN garmentusers ON garmentusers.id = log_distributorclients.garmentuser_id
            where log_distributorclients.buttonevent = "proceed"
            AND log_distributorclients.date BETWEEN DATE_ADD(CURDATE() - INTERVAL 4 WEEK, INTERVAL - WEEKDAY(CURDATE() - INTERVAL 4 WEEK) DAY) AND CURDATE()
            AND garmentusers.deleted_on is null
            AND distributorlocations.circulationgroup_id = '.$circulationgroup_id.'
            ORDER BY log_distributorclients.garmentuser_id) as garmentusers_taken_out';

        $garmentusers_total_taken_out_r = db_fetch_row(db_query($garmentusers_total_taken_out_sql));
        $garmentusers_total_taken_out = $garmentusers_total_taken_out_r[0];


        //Last period data from management_info table
        $management_info_sql = "
            SELECT * FROM `management_info`
             WHERE `year` = '". $other_period_year. "'
               AND `period` = ". $other_period ."
               AND `circulationgroup_id` = ". $circulationgroup_id;

        $management_info = db_fetch_assoc(db_query($management_info_sql));

        //INSERT current period to database
        $current_management_info_sql = "
            SELECT * FROM `management_info`
             WHERE `year` = '". $last_period_year ."'
               AND `period` = ". $last_period ."
               AND `circulationgroup_id` = ". $circulationgroup_id;

        $current_management_info = db_num_rows(db_query($current_management_info_sql));

        if(isset($current_management_info) && $current_management_info === 0)
        {
            $insert_management_info_sql =
                'INSERT INTO management_info
                    (circulationgroup_id, year, period,
                    garments_in_roulation, garments_in_storage, deleted_garments, garments_seven_days_distributed,
                    garments_seven_days_laundry, garmentusers_total, garmentusers_total_taken_out)
                VALUES ('. $circulationgroup_id .', "'. $last_period_year .'", '.$last_period.', '.
                $garments_in_roulation.', '.$garments_in_storage.', '.$deleted_garments.', '.$garments_seven_days_distributed.', '.
                $garments_seven_days_laundry.', '.$garmentusers_total.', '.$garmentusers_total_taken_out.')';

            db_query($insert_management_info_sql) or die("ERROR LINE ". __LINE__ .": ". db_error());
        } else {
            $update_management_info_sql =
                'UPDATE management_info
                 SET
                   garments_in_roulation = '. $garments_in_roulation .',
                   garments_in_storage = '. $garments_in_storage .',
                   deleted_garments = '. $deleted_garments .',
                   garments_seven_days_distributed = '. $garments_seven_days_distributed .',
                   garments_seven_days_laundry = '. $garments_seven_days_laundry .',
                   garmentusers_total = '. $garmentusers_total .',
                   garmentusers_total_taken_out = '. $garmentusers_total_taken_out .'
                 WHERE `year` = "'. $last_period_year .'"
                   AND `period` = '. $last_period .'
                   AND `circulationgroup_id` = '. $circulationgroup_id;
            
            db_query($update_management_info_sql) or die("ERROR LINE ". __LINE__ .": ". db_error());
        }
        
        //part2
        $article_sql = 'SELECT arsimos.id as id,
                        articles.description as article,
                        sizes.name as size,
                        modifications.`name` as modification,
                        miss_total.miss_total_count
                        FROM
                            arsimos
                            INNER JOIN articles ON arsimos.article_id = articles.id
                            INNER JOIN sizes ON arsimos.size_id = sizes.id
                            LEFT JOIN modifications ON arsimos.modification_id = modifications.id

                        LEFT JOIN (select sum(table1.count) as miss_total_count, table1.arsimo_id
                                FROM
                                (SELECT
                                    DATE(misseized.`date`) AS date,
                                    circulationgroups.name AS circulationgroup_name,
                                    misseized.userbound,
                                    misseized.arsimo_id AS arsimo_id,
                                    COUNT(DISTINCT(misseized.garmentuser_id)) AS count
                                    FROM (
                                        SELECT log_distributorclients.*
                                        FROM log_distributorclients
                                        WHERE log_distributorclients.numgarments = 0
                                        AND log_distributorclients.date BETWEEN DATE_ADD(CURDATE() - INTERVAL 4 WEEK, INTERVAL - WEEKDAY(CURDATE() - INTERVAL 4 WEEK) DAY) AND CURDATE()
                                    ) `misseized`
                                    INNER JOIN distributorlocations ON misseized.distributorlocation_id = distributorlocations.id
                                    INNER JOIN circulationgroups ON distributorlocations.circulationgroup_id = circulationgroups.id
                                    INNER JOIN arsimos ON misseized.arsimo_id = arsimos.id
                                    WHERE circulationgroups.id = '.$circulationgroup_id.'
                                    GROUP BY
                                    DATE(misseized.`date`),
                                    misseized.arsimo_id
                                    ORDER BY
                                    circulationgroups.id ASC,
                                    misseized.userbound,
                                    COUNT(misseized.garmentuser_id) DESC
                                    ) as table1
                                GROUP BY
                                table1.arsimo_id
                                ORDER BY
                                table1.arsimo_id) miss_total ON miss_total.arsimo_id = arsimos.id
                        WHERE miss_total.miss_total_count is not null
                        ORDER BY articles.description, sizes.position';

        $article_sql = db_query($article_sql);
        $mupapu = mupapu_generate($circulationgroup_id, $GLOBALS['config']['mupapu_default_weeks_history'], $calculate = true);
        
        $periods_num_sql = "SELECT COUNT(*)
                FROM distributionperiods dp
                WHERE dp.circulationgroup_id = " . $circulationgroup_id;
        $periods_num = db_fetch_num(db_query($periods_num_sql));
        $periods_num = $periods_num[0];
        
        
        $total_distributed_garments_sql = "SELECT ROUND(COUNT(*)*0.34)
                                FROM log_garmentusers_garments
                                INNER JOIN distributors ON distributors.id = log_garmentusers_garments.distributor_id
                                INNER JOIN distributorlocations ON distributorlocations.id = distributors.distributorlocation_id
                                WHERE starttime >= curdate() - INTERVAL DAYOFWEEK(curdate())+5 DAY
                                AND starttime < curdate() - INTERVAL DAYOFWEEK(curdate())-2 DAY
                                AND distributorlocations.circulationgroup_id = " . $circulationgroup_id;
        $total_distributed_garments = db_fetch_num(db_query($total_distributed_garments_sql));
        $total_distributed_garments = $total_distributed_garments[0];

        $multiply_required_auto = 1.1;
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
                $multiply_required_auto = 2;
            } else if ($multiply_required_auto < 1.1) {
                $multiply_required_auto = 1.1;
            }             
        }
        
        
        
        while ($row = db_fetch_assoc($article_sql)){
            $all_periods = 0;
            for($i=0; $i<$GLOBALS['config']['mupapu_default_weeks_history']; $i++) {
                $period_max  = 0;
                for($p=0; $p < $periods_num; $p++) {
                    if($period_max < $mupapu["mup"][$row['id']]["hitmiss_w". $i .".p". $p]){
                        $period_max  = $mupapu["mup"][$row['id']]["hitmiss_w". $i .".p". $p];
                    }
                }
                $all_periods = $all_periods+$period_max;
            }
            $all_periods_average = ceil($all_periods/$GLOBALS['config']['mupapu_default_weeks_history']);
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
            $req = $req * $multiply_required_auto;
    
            $ad = ceil($req)-$mupapu["mup"][$row['id']]["cir_cur"];

            if($ad < 0) {
                $cur_bestellen = '0';
            } else {
                $cur_bestellen = $ad;
            }                  
 
            $article_table_rows .= '<tr style="background-color: #F4F4F5;"><td style="text-align:left;">'. $row['article'] .'</td><td>'. $row['size'] .'</td><td>'. $row['modification'] .'</td><td>'. (isset($row['miss_total_count'])?$row['miss_total_count']:'0') .'</td><td>'. $cur_bestellen .'</td></tr>';
        }

        $email_addresses_sql = "SELECT `email_address`, `name`, `locale_id`
                                  FROM `emailaddresses`
                                 WHERE `group` = 'MANAGEMENT_INFO'";
        $email_addresses = db_query($email_addresses_sql);

        while ($row = db_fetch_assoc($email_addresses)) {
            if (isset($row['locale_id']))
            {
                $localedata = db_fetch_assoc(db_read_row_by_id("locales", $row['locale_id']));
                if (!empty($localedata))
                {
                    $_SESSION['locale_map'] = $localedata['locale_map'];
                    require_once('locale/' . $localedata['locale_map'] . '/language.php');
                } else {
                    echo "Locale data could not be read. Please inform the Technico helpdesk.";
                    require_once('locale/english/language.php');
                }
            } else {
                $l_sql = "SELECT
                            locales.locale_map AS 'locale_map'
                            FROM
                            settings
                            INNER JOIN locales ON settings.`value` = locales.id
                            WHERE settings.`name` = 'default_locale_id'";
                $localedata = db_fetch_assoc(db_query($l_sql));

                $_SESSION['locale_map'] = $localedata['locale_map'];
                require_once('locale/' . $localedata['locale_map'] . '/language.php');
            }
            
            //table data
            $table_data ='<table cellpadding="7px" style="text-align:center;width:100%;border: 2px solid #98AAB1;"><tr style="color: #FFFFFF;background-color: #25814E;"><td style="padding: 5px;">&nbsp;</td><td style="padding: 5px;"><strong>'. $lang["period"] .' '. $other_period .'</strong></td><td style="padding: 5px;"><strong>'. $lang["period"] .' '. $last_period .'</strong></td></tr>';

            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Totaal aantal uitgiftes</strong></td><td style="background-color: #F4F4F5;">'.number_format($total_distributed_other_period, 0, ',', '.').'</td><td style="background-color: #F4F4F5;">'.number_format($total_distributed, 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Uitgiftes gemiddeld per week</strong></td><td style="background-color: #F4F4F5;">'.number_format(ceil($total_distributed_other_period/4), 0, ',', '.').'</td><td style="background-color: #F4F4F5;">'.number_format(ceil($total_distributed/4), 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Totaal misgegrepen kleding</strong></td><td style="background-color: #F4F4F5;">'.number_format($total_misseized_other_period, 0, ',', '.').'</td><td style="background-color: #F4F4F5;">'.number_format($total_misseized, 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Misgegrepen kleding gemiddeld per week</strong></td><td style="background-color: #F4F4F5;">'.number_format(ceil($total_misseized_other_period/4), 0, ',', '.').'</td><td style="background-color: #F4F4F5;">'.number_format(ceil($total_misseized/4), 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Misgegrepen kleding in percentage van de totale uitgifte</strong></td><td style="background-color: #F4F4F5;">'.round(($total_misseized_other_period/$total_distributed_other_period*100),1).'%</td><td style="background-color: #F4F4F5;">'.round(($total_misseized/$total_distributed*100),1).'%</td></tr>';


            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Aantal kledingstukken in roulatie</strong></td><td style="background-color: #F4F4F5;">'.(isset($management_info['garments_in_roulation'])?number_format($management_info['garments_in_roulation'], 0, ',', '.'):'0').'</td><td style="background-color: #F4F4F5;">'.number_format($garments_in_roulation, 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Aantal kledingstukken in magazijn</strong></td><td style="background-color: #F4F4F5;">'.(isset($management_info['garments_in_storage'])?number_format($management_info['garments_in_storage'], 0, ',', '.'):'0').'</td><td style="background-color: #F4F4F5;">'.number_format($garments_in_storage, 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Aantal kledingstukken verwijderd</strong></td><td style="background-color: #F4F4F5;">'.(isset($management_info['deleted_garments'])?number_format($management_info['deleted_garments'], 0, ',', '.'):'0').'</td><td style="background-color: #F4F4F5;">'.number_format($deleted_garments, 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Kledingstukken langer dan 7 dagen bij de drager</strong></td><td style="background-color: #F4F4F5;">'.(isset($management_info['garments_seven_days_distributed'])?number_format($management_info['garments_seven_days_distributed'], 0, ',', '.'):'0').'</td><td style="background-color: #F4F4F5;">'.number_format($garments_seven_days_distributed, 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Kledingstukken langer dan 7 dagen bij de wasserij</strong></td><td style="background-color: #F4F4F5;">'.(isset($management_info['garments_seven_days_laundry'])?number_format($management_info['garments_seven_days_laundry'], 0, ',', '.'):'0').'</td><td style="background-color: #F4F4F5;">'.number_format($garments_seven_days_laundry, 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Totaal aantal dragers</strong></td><td style="background-color: #F4F4F5;">'.(isset($management_info['garmentusers_total'])?number_format($management_info['garmentusers_total'], 0, ',', '.'):'0').'</td><td style="background-color: #F4F4F5;">'.number_format($garmentusers_total, 0, ',', '.').'</td></tr>';
            $table_data .='<tr><td style="text-align:left;background-color: #F4F4F5;"><strong>Aantal dragers die zich hebben aangemeld</strong></td><td style="background-color: #F4F4F5;">'.(isset($management_info['garmentusers_total_taken_out'])?number_format($management_info['garmentusers_total_taken_out'], 0, ',', '.'):'0').'</td><td style="background-color: #F4F4F5;">'.number_format($garmentusers_total_taken_out, 0, ',', '.').'</td></tr>';
            $table_data .='</table><br />';

            $table_data .='<table cellpadding="7px" style="text-align:center;width:100%;border: 2px solid #98AAB1;"><tr style="color: #FFFFFF;background-color: #25814E;"><td style="text-align:left;padding: 5px;"><strong>'. $lang["week"] .'</strong></td>'. $table_header .'<td style="padding: 5px;font-weight:bold;">'. $lang["total"] .'</td><td style="padding: 5px;font-weight:bold;">'. $lang["average"] .'</td></tr>';
            $table_data .='<tr style="background-color: #F4F4F5;"><td style="text-align:left;"><strong>Uitgiftes in aantal</strong></td>'.$table_distributed_by_week.'<td>'.number_format($total_distributed, 0, ',', '.').'</td><td>'.number_format(ceil($total_distributed/4), 0, ',', '.').'</td></tr>';
            $table_data .='<tr style="background-color: #F4F4F5;"><td style="text-align:left;"><strong>Misgegrepen kleding in aantal</strong></td>'.$table_misseized_by_week.'<td>'.number_format($total_misseized, 0, ',', '.').'</td><td>'.number_format(ceil($total_misseized/4), 0, ',', '.').'</td></tr>';
            $table_data .='<tr style="background-color: #F4F4F5;"><td style="text-align:left;"><strong>Misgegrepen kleding in percentage</strong></td>'.$table_misseized_by_week_in_percentage.'<td>'.round(($total_misseized/$total_distributed*100),1).'%</td><td>'.round((($total_misseized/4)/($total_distributed/4)*100),1).'%</td></tr>';
            $table_data .='</table><br />';


            $article_table_data ='<table cellpadding="7px" style="text-align:center;width:100%;border: 2px solid #98AAB1;">';
            $article_table_data .='<tr style="color: #FFFFFF;background-color: #25814E;"><td style="text-align:left;padding: 5px;"><strong>'. $lang["article"] .'</strong></td><td style="padding: 5px;"><strong>'. $lang["size"] .'</strong></td><td style="padding: 5px;"><strong>'. $lang["garmentmodification"] .'</strong></td><td style="padding: 5px;"><strong>'. $lang["misseized"] .'</strong></td><td style="padding: 5px;"><strong>'. $lang["order"] .'</strong></td></tr>';
            $article_table_data .= $article_table_rows;
            $article_table_data .='</table>';

            $final_data = '<strong>Techni<span style="color:#1C5A39;">X</span> GS</strong> - Beheer informatie
                        Locatie: '.$location.'
                        Date: '.date("Y-m-d").'<br /><br />'. $table_data . $article_table_data;

        
            $current_emailaddress = $row["email_address"];
            $current_name = $row["name"];

            try {
                $m = new Email();
                $m->setRecepients(array($current_emailaddress => $current_name));
                $m->setSubject("Beheer infomatie - ". $location);
                $m->addBody($final_data, "text/html");
                $m->send();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}

?>
