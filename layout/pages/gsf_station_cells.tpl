
<?php
if (isset($distributorlocations) && $distributorlocations != "") {
    $tab_li_all = "";
    $tab_div_all = "";

    while ($row = db_fetch_assoc($distributorlocations)){
	$tab_li = "<li><a href=\"#tab-". $row["id"] ."\">". $row["name"] ."</a></li>";
        $tab_div = "<div style=\"padding: 5px;background-color: white;\" id=\"tab-". $row["id"] ."\">";
        $tmp_table[3] = $tmp_table[2] = $tmp_table[1] = "";  
            $station_cells_query = "SELECT position, arsimo_id, max_loaded, loaded, d.id AS distributor_id, d.rows, d.`columns`
                        FROM gsf_distributors_load gdl
                        INNER JOIN distributors d on d.id = gdl.distributor_id
                        WHERE d.id = ". $row["id"] ."
                        ORDER BY `gdl`.`position` ASC";

                $station_cells_sql = db_query($station_cells_query) or die("ERROR LINE ". __LINE__);
                
                if (db_num_rows($station_cells_sql) > 0){
                        $tab_div .= "<form action=\"". $_SERVER["PHP_SELF"] ."#". $row["id"] ."\" name=\"". $row["id"] ."\" method=\"POST\">";
                        $tab_div .= "<input type=\"hidden\" name=\"distributor_id\" value=\"".$row["id"]."\" />";    
                       $tab_div .= "<table>";
                        $rows = 0;
                        
                            while ($station_cells_result = db_fetch_assoc($station_cells_sql)) {
                                
                                if(strlen($station_cells_result["position"]) === 2 && substr($station_cells_result["position"],0,1) === 1) {
                                    $tmp_table[substr($station_cells_result["position"],1,1)] .= "<tr>";
                                }
                                    $rows = strlen($station_cells_result["position"]) === 2?substr($station_cells_result["position"],1,1):substr($station_cells_result["position"],2,1);
                                    $position = $station_cells_result["position"];
                                    $tmp_table[$rows] .= "<td style=\"padding: 1px 1px 0px 1px;text-align: center;\">";
                                    $tmp_table[$rows] .= "<div style=\"border: 2px solid; border-color: gray;border-radius: 8px; padding: 4px; background: #f2f2f3 url(images/ui-bg_flat_100_f2f2f3_40x100.png) 50% 50% repeat-x;\">";
                                    $tmp_table[$rows] .= "<div style=\"font-size: 19px;\">".$position ."</div>";
                                    
                                    $available_station_cells_sql = "SELECT a.id AS 'arsimos_id', CONCAT(ar.description, ' - ', s.name) AS 'description'
                                        FROM arsimos a
                                        INNER JOIN articles ar ON ar.id = a.article_id
                                        INNER JOIN sizes s ON s.id = a.size_id
                                        INNER JOIN garments g ON g.arsimo_id = a.id AND g.circulationgroup_id = ". $row["cid"] ." 
                                        WHERE a.modification_id IS NULL
                                        AND a.deleted_on IS NULL
                                        AND g.deleted_on IS NULL
                                        GROUP BY a.id
                                        ORDER BY ar.description, s.position";
                                  
                                    $available_station_cells = db_query($available_station_cells_sql);
                                    
                                        if ($station_cells_result["arsimo_id"] === NULL) {
                                            $tmp_table[$rows] .= html_selectbox_style("position[". $station_cells_result["position"] ."]", $available_station_cells, null, $lang['make_a_choice'], null, "width: 100%;");
                                        } else {
                                           $tmp_table[$rows] .= html_selectbox_style("position[". $station_cells_result["position"] ."]", $available_station_cells, $station_cells_result["arsimo_id"], $lang['make_a_choice'], null, "width: 100%;"); 
                                        }
                                        db_free_result($available_station_cells);
                                    
                               
                                $tmp_table[$rows] .= "<div style=\"padding-top: 10px;\">Max items: <input type=\"text\" id=\"max_loaded[". $station_cells_result["position"] ."]\" name=\"max_loaded[". $station_cells_result["position"] ."]\" value=\"".$station_cells_result["max_loaded"]."\" size=\"1\" maxlength=\"2\" style=\"text-align: center;\"></div></div></td>";
                            
                                if(($station_cells_result["columns"] >= 10 && substr($station_cells_result["position"],0,2) === $station_cells_result["columns"]) 
                                    || ($station_cells_result["columns"] < 10 && substr($station_cells_result["position"],0,1) === $station_cells_result["columns"])) {
                                    $tmp_table[$rows] .= "</tr>";
                                } 
                            }
                        
                        $tab_div .= $tmp_table[3].$tmp_table[2].$tmp_table[1];
			$tab_div .= "</table>";
                        
                            $tab_div .= "<br /><div class=\"buttons\">";
                                $tab_div .= "<input type=\"reset\" name=\"reset\" value=\"". $lang["restore"] ."\" title=\"". $lang["restore"] ."\" />";
                                $tab_div .= "<input type=\"submit\" name=\"submit\" value=\"". $lang["save"] ."\" title=\"". $lang["save"] ."\" />";
                            $tab_div .= "</div>";
                       
                        $tab_div .= "</form>";
                }else{
                    $tab_div .= $lang["no_items_found"];
                }
                
            
            
            
        $tab_div .= "</div>";
	$tab_li_all .= $tab_li;
    	$tab_div_all .= $tab_div;

    }
    ?>

    <div id="tabs">
        <ul>
            <?=$tab_li_all?>
        </ul>
        <?=$tab_div_all?>
    </div>
	<p id="demo"></p>

<? } ?>
