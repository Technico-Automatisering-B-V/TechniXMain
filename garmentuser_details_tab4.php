<?php

$station_bound_yesno_options[0] = $lang["no"];
$station_bound_yesno_options[1] = $lang["yes"];

$station_bound_yesno = (!empty($_POST["station_bound_yesno"])) ? $_POST["station_bound_yesno"] : "0";

// Required for list: station_bound_yesno
if ($page == "details" && !empty($garmentuser_post_id)) {
    if (!isset($_POST["editsubmit"])) {
        $station_bound_yesno_columns = "garmentusers.distributor_id garmentusers.distributor_id2 garmentusers.distributor_id3 garmentusers.distributor_id4 garmentusers.distributor_id5 garmentusers.distributor_id6 garmentusers.distributor_id7 garmentusers.distributor_id8 garmentusers.distributor_id9 garmentusers.distributor_id10";
        $station_bound_yesno_conditions["where"]["1"] = "garmentusers.id " . $garmentuser_id;
        $station_bound_yesno_data = db_read("garmentusers", $station_bound_yesno_columns, $station_bound_yesno_conditions);
        if (!empty($station_bound_yesno_data)) {
            $row = db_fetch_row($station_bound_yesno_data);
            if (empty($row[0]) && empty($row[1]) && empty($row[2]) && empty($row[3]) && empty($row[4]) && empty($row[5]) && empty($row[6]) && empty($row[7]) && empty($row[8]) && empty($row[9]) && empty($row[10])) {
                    $station_bound_yesno = 0;
            } else {
                    $station_bound_yesno = 1;
            }
        } else {
            $station_bound_yesno = 0;
        }
    }

    // Aantal cariers vrij
    $station_max_positions_sql = db_query("
        SELECT SUM(`max_positions`) AS 'max_positions'
          FROM `garmentusers_arsimos`
         WHERE `garmentuser_id` = ". $garmentuser_id ."
           AND `enabled` = 1
           AND `userbound` = 1");

    $station_max_positions_data = db_fetch_row($station_max_positions_sql);
    $station_max_positions = $station_max_positions_data[0];
    if (empty($station_max_positions)){ $station_max_positions = "0"; }
    
    // Vrije carriers per station
    $distributor_id_output = "";
    $distributor_id2_output = "";
    $distributor_id3_output = "";
    $distributor_id4_output = "";
    $distributor_id5_output = "";
    $distributor_id6_output = "";
    $distributor_id7_output = "";
    $distributor_id8_output = "";
    $distributor_id9_output = "";
    $distributor_id10_output = "";
    
    $count_distributorlocations_sql = db_query("
        SELECT COUNT(*) AS 'count'
          FROM `circulationgroups`");

    $count_distributorlocations_data = db_fetch_row($count_distributorlocations_sql);
    $count_distributorlocations = $count_distributorlocations_data[0];
    if (empty($count_distributorlocations) || $count_distributorlocations > 10) {
        $free_per_station_sql = db_query("
            SELECT distributors.id,
                   circulationgroups.name,
                   distributors.doornumber,
                   distributors.`hooks` - COALESCE(`tmp`.max_positions,0) AS 'free_positions',
                   IF(ISNULL(tmp2.reserved_positions),'0',tmp2.reserved_positions)
            FROM distributors
            INNER JOIN distributorlocations ON distributors.distributorlocation_id = distributorlocations.id
            INNER JOIN circulationgroups ON distributorlocations.circulationgroup_id = circulationgroups.id
            INNER JOIN circulationgroups_garmentusers ON circulationgroups_garmentusers.circulationgroup_id = circulationgroups.id
            LEFT JOIN (
                    SELECT distributors.id AS 'distributor_id',
                          SUM(garmentusers_arsimos.max_positions) AS 'max_positions'
                    FROM garmentusers_arsimos
                    INNER JOIN garmentusers ON garmentusers_arsimos.garmentuser_id = garmentusers.id
                        AND garmentusers_arsimos.enabled = 1
                        AND garmentusers_arsimos.userbound = 1
                    INNER JOIN distributors ON distributors.id = garmentusers.distributor_id
                            OR distributors.id = garmentusers.distributor_id2
                            OR distributors.id = garmentusers.distributor_id3
                            OR distributors.id = garmentusers.distributor_id4
                            OR distributors.id = garmentusers.distributor_id5
                            OR distributors.id = garmentusers.distributor_id6
                            OR distributors.id = garmentusers.distributor_id7
                            OR distributors.id = garmentusers.distributor_id8
                            OR distributors.id = garmentusers.distributor_id9
                            OR distributors.id = garmentusers.distributor_id10
                    GROUP BY distributors.id
               ) `tmp` ON `tmp`.distributor_id = distributors.id
               LEFT JOIN (SELECT distributors.id AS 'distributor_id', SUM(ga.max_positions) as 'reserved_positions'
                    FROM garmentusers_arsimos ga
                    INNER JOIN garmentusers gu ON gu.id = ga.garmentuser_id
                    INNER JOIN distributors ON distributors.id = gu.distributor_id
                        OR distributors.id = gu.distributor_id2
                        OR distributors.id = gu.distributor_id3
                        OR distributors.id = gu.distributor_id4
                        OR distributors.id = gu.distributor_id5
                        OR distributors.id = gu.distributor_id6
                        OR distributors.id = gu.distributor_id7
                        OR distributors.id = gu.distributor_id8
                        OR distributors.id = gu.distributor_id9
                        OR distributors.id = gu.distributor_id10
                    WHERE ga.userbound = 1
                    GROUP BY distributors.id) `tmp2` ON `tmp2`.distributor_id = distributors.id
            WHERE circulationgroups_garmentusers.garmentuser_id = ". $garmentuser_post_id ."
            GROUP BY distributors.id
            ORDER BY circulationgroups.id, distributors.doornumber");

        while ($free_per_station_data = db_fetch_row($free_per_station_sql)) {
            if ($gu_data["distributor_id"] == $free_per_station_data[0]) {
                $free_per_station_checked = " checked=\"checked\"";
            } else {
                $free_per_station_checked = "";
            }

            $distributor_id_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";

        }
    } else {
        $free_per_station_sql = db_query("
            SELECT distributors.id,
                   circulationgroups.name,
                   distributors.doornumber,
                   distributors.`hooks` - COALESCE(`tmp`.max_positions,0) AS 'free_positions',
                   IF(ISNULL(tmp2.reserved_positions),'0',tmp2.reserved_positions),
                   circulationgroups.id
            FROM distributors
            INNER JOIN distributorlocations ON distributors.distributorlocation_id = distributorlocations.id
            INNER JOIN circulationgroups ON distributorlocations.circulationgroup_id = circulationgroups.id
            INNER JOIN circulationgroups_garmentusers ON circulationgroups_garmentusers.circulationgroup_id = circulationgroups.id
            LEFT JOIN (
                    SELECT distributors.id AS 'distributor_id',
                          SUM(garmentusers_userbound_arsimos.max_positions) AS 'max_positions'
                    FROM garmentusers_userbound_arsimos
                    INNER JOIN garmentusers ON garmentusers_userbound_arsimos.garmentuser_id = garmentusers.id
                        AND garmentusers_userbound_arsimos.enabled = 1
                    INNER JOIN distributors ON distributors.id = garmentusers.distributor_id
                            OR distributors.id = garmentusers.distributor_id2
                            OR distributors.id = garmentusers.distributor_id3
                            OR distributors.id = garmentusers.distributor_id4
                            OR distributors.id = garmentusers.distributor_id5
                            OR distributors.id = garmentusers.distributor_id6
                            OR distributors.id = garmentusers.distributor_id7
                            OR distributors.id = garmentusers.distributor_id8
                            OR distributors.id = garmentusers.distributor_id9
                            OR distributors.id = garmentusers.distributor_id10
                    INNER JOIN distributorlocations dl ON dl.id = distributors.distributorlocation_id AND dl.circulationgroup_id = garmentusers_userbound_arsimos.circulationgroup_id								
                    GROUP BY distributors.id
               ) `tmp` ON `tmp`.distributor_id = distributors.id
               LEFT JOIN (SELECT distributors.id AS 'distributor_id', SUM(ga.max_positions) as 'reserved_positions'
                    FROM garmentusers_userbound_arsimos ga
                    INNER JOIN garmentusers gu ON gu.id = ga.garmentuser_id
                    INNER JOIN distributors ON distributors.id = gu.distributor_id
                        OR distributors.id = gu.distributor_id2
                        OR distributors.id = gu.distributor_id3
                        OR distributors.id = gu.distributor_id4
                        OR distributors.id = gu.distributor_id5
                        OR distributors.id = gu.distributor_id6
                        OR distributors.id = gu.distributor_id7
                        OR distributors.id = gu.distributor_id8
                        OR distributors.id = gu.distributor_id9
                        OR distributors.id = gu.distributor_id10
                    INNER JOIN distributorlocations dl ON dl.id = distributors.distributorlocation_id AND dl.circulationgroup_id = ga.circulationgroup_id								
                    WHERE ga.enabled = 1
                    GROUP BY distributors.id) `tmp2` ON `tmp2`.distributor_id = distributors.id
            WHERE circulationgroups_garmentusers.garmentuser_id = ". $garmentuser_post_id ." 
            GROUP BY distributors.id
            ORDER BY circulationgroups.id, distributors.doornumber");

        while ($free_per_station_data = db_fetch_row($free_per_station_sql)) {
            if ($gu_data["distributor_id"] == $free_per_station_data[0]
                  || $gu_data["distributor_id2"] == $free_per_station_data[0]
                  || $gu_data["distributor_id3"] == $free_per_station_data[0]
                  || $gu_data["distributor_id4"] == $free_per_station_data[0]
                  || $gu_data["distributor_id5"] == $free_per_station_data[0]
                  || $gu_data["distributor_id6"] == $free_per_station_data[0]
                  || $gu_data["distributor_id7"] == $free_per_station_data[0]
                  || $gu_data["distributor_id8"] == $free_per_station_data[0]
                  || $gu_data["distributor_id9"] == $free_per_station_data[0]
                  || $gu_data["distributor_id10"] == $free_per_station_data[0] ) {
                $free_per_station_checked = " checked=\"checked\"";
            } else {
                $free_per_station_checked = "";
            }
            
            if($free_per_station_data[5] == 1) {
                $distributor_id_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";
            } elseif($free_per_station_data[5] == 2) {
                $distributor_id2_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id2\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";     
            } elseif($free_per_station_data[5] == 3) {
                $distributor_id3_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id3\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";  
            } elseif($free_per_station_data[5] == 4) {
                $distributor_id4_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id4\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";  
            } elseif($free_per_station_data[5] == 5) {
                $distributor_id5_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id5\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";  
            } elseif($free_per_station_data[5] == 6) {
                $distributor_id6_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id6\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";  
            } elseif($free_per_station_data[5] == 7) {
                $distributor_id7_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id7\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";  
            } elseif($free_per_station_data[5] == 8) {
                $distributor_id8_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id8\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";  
            } elseif($free_per_station_data[5] == 9) {
                $distributor_id9_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id9\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";  
            } elseif($free_per_station_data[5] == 10) {
                $distributor_id10_output .= "<table cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"padding-bottom:27px\"><input type=\"radio\" name=\"distributor_id10\" value=\"". $free_per_station_data[0] ."\"". $free_per_station_checked ." /></td><td>". $free_per_station_data[1] ." - Station ". $free_per_station_data[2] ."<br />".$lang["count_positions_reserved"].": ". $free_per_station_data[4] ."<br /><br /></td></tr></table>";  
            }
        }
    }
}

?>
