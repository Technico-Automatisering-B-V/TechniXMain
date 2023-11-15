<? $data_ir = array();
   $data_load = array();
   $data_html = "";

    while ($row = db_fetch_assoc($in_roulation)){ 
        $data_ir[$row["circulationgroup_id"]] = 
                 $row["sum_stock"] . "," .
                 $row["sum_rejected"] . "," .
                 $row["sum_distributed"] . "," .
                 $row["sum_deposited"] . "," .
                 $row["sum_container"] . "," .
                 $row["sum_transport_to_laundry"] . "," .
                 $row["sum_laundry"];
        $data_html .= "<input type=\"hidden\" id=\"data_ir".$row["circulationgroup_id"]."\" value=\"". $data_ir[$row["circulationgroup_id"]] ."\" />";         
    }
    
    while ($row = db_fetch_assoc($load)){ 
        $data_load[$row["circulationgroup_id"]] = $row["loaded"] . "," . 
                 $row["free"]. "," . 
                 $row["overloaded"];
        $data_html .= "<input type=\"hidden\" id=\"data_load".$row["circulationgroup_id"]."\" value=\"". $data_load[$row["circulationgroup_id"]] ."\" />"; 
    }
?>

<? $uls = "";$tabs="";$cgs=array();  ?>
<div id="tabs">    
    <?
        while ($row = db_fetch_assoc($c_groups)){ 
            $uls .= "<li><a href=\"#tab".$row["circulationgroup_id"]."\">". $row["circulationgroup_name"] ."</a></li>";
            $tabs .="<div id=\"tab". $row["circulationgroup_id"] ."\">";
            $data_html .= "<input type=\"hidden\" id=\"data_roulationadvice".$row["circulationgroup_id"]."\" value=\"". $row["circulationgroup_required"] .",". $row["circulationgroup_order"] .",". $row["circulationgroup_extra"] ."\" />"; 
			$tabs .="<div style=\"display: table;width: 100%;\">";
            $tabs .="<div id=\"div-top-row-1\" style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartInRoulation". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
			$tabs .="<div id=\"div-top-row-2\" style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartOutRoulation". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="<div id=\"div-top-row-3\" style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartLoad". $row["circulationgroup_id"] ."\"></canvas></div>";
			$tabs .="</div>";
            $tabs .="<div id=\"div-top-row-4\" style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
			$tabs .="<div style=\"height:200px;\"><canvas id=\"chartRoulationAdvice". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;vertical-align: top;\">";
			$tabs .="<div style=\"vertical-align: baseline;text-align: center;font-size: 13px;font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;color: black;font-weight: bold;padding: 10px;line-height: 1.2;\">";
			$tabs .="Statistiek</div>";
			$tabs .="<div style=\"margin-left: 10px;margin-top:25px;margin-right: 10px;\">";
			$tabs .= "<table style=\"width: 100%;line-height: 1.5;\">";
			$tabs .= "<tr><td>".$lang["__circulation_advice_longer_garmentuser"].":</td><td style=\"text-align: right;\"><strong><span id=\"data_gu_garment_value".$row["circulationgroup_id"]."\"></span></strong></td></tr>";
            $tabs .= "<tr><td>".$lang["__circulation_advice_longer_deposited"].":</td><td style=\"text-align: right;\"><strong><span id=\"data_de_garment_value".$row["circulationgroup_id"]."\"></span></strong></td></tr>";
            $tabs .= "<tr><td>".$lang["__circulation_advice_longer_laundry"].":</td><td style=\"text-align: right;\"><strong><span id=\"data_la_garment_value".$row["circulationgroup_id"]."\"></span></strong></td></tr>";
            $tabs .= "<tr><td>".$lang["__circulation_advice_longer_chaoot"].":</td><td style=\"text-align: right;\"><strong><span id=\"data_re_garment_value".$row["circulationgroup_id"]."\"></span></strong></td></tr>";			
			$tabs .= "</table></div>";
			$tabs .="<div style=\"margin-top:34px;text-align:left;\"><table style=\"width: 100%;\"><tr><td>";
            $tabs .="&nbsp;</td><td style=\"text-align:right;\"><span class=\"radioset\">";
            $tabs .="<input name=\"GarmentAtRadio". $row["circulationgroup_id"] ."\" id=\"GarmentAtRadio1". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"1\"><label name=\"radiolabel\" for=\"GarmentAtRadio1". $row["circulationgroup_id"] ."\" onClick=\"updateGarmentAtData(". $row["circulationgroup_id"] .",'1')\">1 Dag</label>";
            $tabs .="<input name=\"GarmentAtRadio". $row["circulationgroup_id"] ."\" id=\"GarmentAtRadio2". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"7\" checked=\"checked\"><label name=\"radiolabel\" for=\"GarmentAtRadio2". $row["circulationgroup_id"] ."\" onClick=\"updateGarmentAtData(". $row["circulationgroup_id"] .",'7')\">7 Dagen</label>";
            $tabs .="<input name=\"GarmentAtRadio". $row["circulationgroup_id"] ."\" id=\"GarmentAtRadio3". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"14\"><label name=\"radiolabel\" for=\"GarmentAtRadio3". $row["circulationgroup_id"] ."\" onClick=\"updateGarmentAtData(". $row["circulationgroup_id"] .",'14')\">14 Dagen</label>";
            $tabs .="</span></td></tr></table></div>";
			$tabs .="</div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table;width: 100%\">";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;vertical-align:top\">";
			$tabs .="<div style=\"height:200px;\"><canvas id=\"chartDistributionTime". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="<div style=\"margin-top:10px;text-align:left;\"><table style=\"width: 100%;\"><tr><td><span class=\"radioset\">";
            $tabs .="<input name=\"DistributionTimeRadio". $row["circulationgroup_id"] ."\" id=\"DistributionTimeRadioMin". $row["circulationgroup_id"] ."\" type=\"radio\" value=\"min\"><label name=\"radiolabel\" for=\"DistributionTimeRadioMin". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionTime(". $row["circulationgroup_id"] .",'min')\">Min</label>";
            $tabs .="<input name=\"DistributionTimeRadio". $row["circulationgroup_id"] ."\" id=\"DistributionTimeRadioAverage". $row["circulationgroup_id"] ."\" type=\"radio\" value=\"average\" checked=\"checked\"><label name=\"radiolabel\" for=\"DistributionTimeRadioAverage". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionTime(". $row["circulationgroup_id"] .",'average')\">Gem</label>";
            $tabs .="<input name=\"DistributionTimeRadio". $row["circulationgroup_id"] ."\" id=\"DistributionTimeRadioMax". $row["circulationgroup_id"] ."\" type=\"radio\" value=\"max\"><label name=\"radiolabel\" for=\"DistributionTimeRadioMax". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionTime(". $row["circulationgroup_id"] .",'max')\">Max</label>";
            $tabs .="</span></td><td style=\"text-align:right;\"><span class=\"radioset\">";
            $tabs .="<input name=\"DistributionTimeRadio2". $row["circulationgroup_id"] ."\" id=\"DistributionTimeRadioWeek". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"week\" checked=\"checked\"><label name=\"radiolabel\" for=\"DistributionTimeRadioWeek". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionChart(". $row["circulationgroup_id"] .",'week')\">Week</label>";
            $tabs .="<input name=\"DistributionTimeRadio2". $row["circulationgroup_id"] ."\" id=\"DistributionTimeRadioPeriod". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"period\"><label name=\"radiolabel\" for=\"DistributionTimeRadioPeriod". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionChart(". $row["circulationgroup_id"] .",'period')\">Periode</label>";
            $tabs .="<input name=\"DistributionTimeRadio2". $row["circulationgroup_id"] ."\" id=\"DistributionTimeRadioYear". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"year\"><label name=\"radiolabel\" for=\"DistributionTimeRadioYear". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionChart(". $row["circulationgroup_id"] .",'year')\">Jaar</label>";
            $tabs .="</span></td></tr></table></div></div>";
			$tabs .="<div style=\"display: table-cell;width: 60%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartDistributionChart". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"position: relative;display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartLoadTime". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="<div style=\"position: absolute;right: 10px;top: 10px;\"><input type=\"checkbox\" id=\"loadtimeChaootCheckbox". $row["circulationgroup_id"] ."\" onClick=\"updateLoadTimeCheckbox(". $row["circulationgroup_id"] .")\"><label for=\"loadtimeChaootCheckbox". $row["circulationgroup_id"] ."\">Chaoot</label></div>";
            $tabs .="<div style=\"margin-top:10px;text-align:left;\"><table style=\"width: 100%;\"><tr><td><span class=\"radioset\">";
			$tabs .="<input name=\"LoadTimeRadio". $row["circulationgroup_id"] ."\" id=\"LoadTimeRadioMin". $row["circulationgroup_id"] ."\" type=\"radio\" value=\"min\"><label name=\"radiolabel\" for=\"LoadTimeRadioMin". $row["circulationgroup_id"] ."\" onClick=\"updateLoadTime(". $row["circulationgroup_id"] .",'min')\">Min</label>";
            $tabs .="<input name=\"LoadTimeRadio". $row["circulationgroup_id"] ."\" id=\"LoadTimeRadioAverage". $row["circulationgroup_id"] ."\" type=\"radio\" value=\"average\" checked=\"checked\"><label name=\"radiolabel\" for=\"LoadTimeRadioAverage". $row["circulationgroup_id"] ."\" onClick=\"updateLoadTime(". $row["circulationgroup_id"] .",'average')\">Gem</label>";
            $tabs .="<input name=\"LoadTimeRadio". $row["circulationgroup_id"] ."\" id=\"LoadTimeRadioMax". $row["circulationgroup_id"] ."\" type=\"radio\" value=\"max\"><label name=\"radiolabel\" for=\"LoadTimeRadioMax". $row["circulationgroup_id"] ."\" onClick=\"updateLoadTime(". $row["circulationgroup_id"] .",'max')\">Max</label>";
            $tabs .="</span></td><td style=\"text-align:right;\"><span class=\"radioset\">";
            $tabs .="<input name=\"LoadTimeRadio2". $row["circulationgroup_id"] ."\" id=\"LoadTimeRadioWeek". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"week\"><label name=\"radiolabel\" for=\"LoadTimeRadioWeek". $row["circulationgroup_id"] ."\" onClick=\"updateLoadChart(". $row["circulationgroup_id"] .",'week')\">Week</label>";
            $tabs .="<input name=\"LoadTimeRadio2". $row["circulationgroup_id"] ."\" id=\"LoadTimeRadioPeriod". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"period\"><label name=\"radiolabel\" for=\"LoadTimeRadioPeriod". $row["circulationgroup_id"] ."\" onClick=\"updateLoadChart(". $row["circulationgroup_id"] .",'period')\">Periode</label>";
            $tabs .="<input name=\"LoadTimeRadio2". $row["circulationgroup_id"] ."\" id=\"LoadTimeRadioYear". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"year\"><label name=\"radiolabel\" for=\"LoadTimeRadioYear". $row["circulationgroup_id"] ."\" onClick=\"updateLoadChart(". $row["circulationgroup_id"] .",'year')\">Jaar</label>";
            $tabs .="</span></td></tr></table></div></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table;width: 100%\">";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartMisseized". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="<div style=\"margin-top:10px;text-align:left;\"><table style=\"width: 100%;\"><tr><td>";
            $tabs .="&nbsp;</td><td style=\"text-align:right;\"><span class=\"radioset\">";
            $tabs .="<input name=\"MisseizedRadio2". $row["circulationgroup_id"] ."\" id=\"MisseizedRadioWeek". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"week\"><label name=\"radiolabel\" for=\"MisseizedRadioWeek". $row["circulationgroup_id"] ."\" onClick=\"updateMisseizedChart(". $row["circulationgroup_id"] .",'week')\">Week</label>";
            $tabs .="<input name=\"MisseizedRadio2". $row["circulationgroup_id"] ."\" id=\"MisseizedRadioPeriod". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"period\"><label name=\"radiolabel\" for=\"MisseizedRadioPeriod". $row["circulationgroup_id"] ."\" onClick=\"updateMisseizedChart(". $row["circulationgroup_id"] .",'period')\">Periode</label>";
            $tabs .="<input name=\"MisseizedRadio2". $row["circulationgroup_id"] ."\" id=\"MisseizedRadioYear". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"year\"><label name=\"radiolabel\" for=\"MisseizedRadioYear". $row["circulationgroup_id"] ."\" onClick=\"updateMisseizedChart(". $row["circulationgroup_id"] .",'year')\">Jaar</label>";
            $tabs .="</span></td></tr></table></div></div>";
			$tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartLogin". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="<div style=\"margin-top:10px;text-align:left;\"><table style=\"width: 100%;\"><tr><td>";
            $tabs .="&nbsp;</td><td style=\"text-align:right;\"><span class=\"radioset\">";
            $tabs .="<input name=\"LoginRadio2". $row["circulationgroup_id"] ."\" id=\"LoginRadioWeek". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"week\"><label name=\"radiolabel\" for=\"LoginRadioWeek". $row["circulationgroup_id"] ."\" onClick=\"updateLoginChart(". $row["circulationgroup_id"] .",'week')\">Week</label>";
            $tabs .="<input name=\"LoginRadio2". $row["circulationgroup_id"] ."\" id=\"LoginRadioPeriod". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"period\"><label name=\"radiolabel\" for=\"LoginRadioPeriod". $row["circulationgroup_id"] ."\" onClick=\"updateLoginChart(". $row["circulationgroup_id"] .",'period')\">Periode</label>";
            $tabs .="<input name=\"LoginRadio2". $row["circulationgroup_id"] ."\" id=\"LoginRadioYear". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"year\"><label name=\"radiolabel\" for=\"LoginRadioYear". $row["circulationgroup_id"] ."\" onClick=\"updateLoginChart(". $row["circulationgroup_id"] .",'year')\">Jaar</label>";
            $tabs .="</span></td></tr></table></div></div>";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartDistributioncount". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="<div style=\"margin-top:10px;text-align:left;\"><table style=\"width: 100%;\"><tr><td>";
            $tabs .="&nbsp;</td><td style=\"text-align:right;\"><span class=\"radioset\">";
            $tabs .="<input name=\"DistributioncountRadio2". $row["circulationgroup_id"] ."\" id=\"DistributioncountRadioWeek". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"week\"><label name=\"radiolabel\" for=\"DistributioncountRadioWeek". $row["circulationgroup_id"] ."\" onClick=\"updateDistributioncountChart(". $row["circulationgroup_id"] .",'week')\">Week</label>";
            $tabs .="<input name=\"DistributioncountRadio2". $row["circulationgroup_id"] ."\" id=\"DistributioncountRadioPeriod". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"period\"><label name=\"radiolabel\" for=\"DistributioncountRadioPeriod". $row["circulationgroup_id"] ."\" onClick=\"updateDistributioncountChart(". $row["circulationgroup_id"] .",'period')\">Periode</label>";
            $tabs .="<input name=\"DistributioncountRadio2". $row["circulationgroup_id"] ."\" id=\"DistributioncountRadioYear". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"year\"><label name=\"radiolabel\" for=\"DistributioncountRadioYear". $row["circulationgroup_id"] ."\" onClick=\"updateDistributioncountChart(". $row["circulationgroup_id"] .",'year')\">Jaar</label>";
            $tabs .="</span></td></tr></table></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartDistributionuser". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="<div style=\"margin-top:10px;text-align:left;\"><table style=\"width: 100%;\"><tr><td>";
            $tabs .="&nbsp;</td><td style=\"text-align:right;\"><span class=\"radioset\">";
            $tabs .="<input name=\"DistributionuserRadio2". $row["circulationgroup_id"] ."\" id=\"DistributionuserRadioPeriod". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"period\"><label name=\"radiolabel\" for=\"DistributionuserRadioPeriod". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionuserChart(". $row["circulationgroup_id"] .",'period')\">Periode</label>";
            $tabs .="<input name=\"DistributionuserRadio2". $row["circulationgroup_id"] ."\" id=\"DistributionuserRadioYear". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"year\"><label name=\"radiolabel\" for=\"DistributionuserRadioYear". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionuserChart(". $row["circulationgroup_id"] .",'year')\">Jaar</label>";
            $tabs .="</span></td></tr></table></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:200px;\"><canvas id=\"chartDistributionstation". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="<div style=\"margin-top:10px;text-align:left;\"><table style=\"width: 100%;\"><tr><td>";
            $tabs .="&nbsp;</td><td style=\"text-align:right;\"><span class=\"radioset\">";
            $tabs .="<input name=\"DistributionstationRadio2". $row["circulationgroup_id"] ."\" id=\"DistributionstationRadioWeek". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"week\"><label name=\"radiolabel\" for=\"DistributionstationRadioWeek". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionstationChart(". $row["circulationgroup_id"] .",'week')\">Week</label>";
            $tabs .="<input name=\"DistributionstationRadio2". $row["circulationgroup_id"] ."\" id=\"DistributionstationRadioPeriod". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"period\"><label name=\"radiolabel\" for=\"DistributionstationRadioPeriod". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionstationChart(". $row["circulationgroup_id"] .",'period')\">Periode</label>";
            $tabs .="<input name=\"DistributionstationRadio2". $row["circulationgroup_id"] ."\" id=\"DistributionstationRadioYear". $row["circulationgroup_id"] ."\" type=\"radio\" disabled=\"disabled\" value=\"year\"><label name=\"radiolabel\" for=\"DistributionstationRadioYear". $row["circulationgroup_id"] ."\" onClick=\"updateDistributionstationChart(". $row["circulationgroup_id"] .",'year')\">Jaar</label>";
            $tabs .="</span></td></tr></table></div>";
            $tabs .="</div></div>";
            $tabs .="</div>";
            array_push($cgs, $row["circulationgroup_id"]);
            
        }
        echo "<ul>".$uls."</ul>";
        echo $tabs;

        echo $data_html;
        
    ?>
    
</div>
    
<script type="text/javascript">

	function updateLoadTimeCheckbox(circulationgroup_id) {
		document.getElementById('LoadTimeRadioAverage'+circulationgroup_id).click();
		updateLoadTime(circulationgroup_id, 'average');
	}
	
    function updateDistributionTime(circulationgroup_id, option) {
        
        if (option == 'min') { var value = document.getElementById('data_distributiontime_min'+circulationgroup_id).value;} else
        if (option == 'max') { var value = document.getElementById('data_distributiontime_max'+circulationgroup_id).value;}
        else { var value = document.getElementById('data_distributiontime_average'+circulationgroup_id).value;}
        
        if (value <= 10) { var color = 'rgba(15, 220, 99, 0.6)';} else
        if (value <= 20 && value > 10) { var color = 'rgba(253, 151, 4, 0.6)';}
        else { var color = 'rgba(255, 113, 67, 0.4)';}
        
        var chartId = circulationgroup_id-1;
        
		for (var i = 0, len = chartDistributionTime[chartId].data.datasets.length; i < len; i++) {
		  chartDistributionTime[chartId].data.datasets[i].gaugeData.value = value;
		  chartDistributionTime[chartId].data.datasets[i].gaugeData.valueColor = color;
		}
		
        chartDistributionTime[chartId].options.animation.duration = 5;
        chartDistributionTime[chartId].update();
    }
    
    function updateLoadTime(circulationgroup_id, option) {
		var max = 0;
		if(document.getElementById('loadtimeChaootCheckbox'+circulationgroup_id).checked) { 
			if (option == 'min') { var value = document.getElementById('data_loadtime_reject_min'+circulationgroup_id).value;} else
			if (option == 'max') { var value = document.getElementById('data_loadtime_reject_max'+circulationgroup_id).value;}
			else { var value = document.getElementById('data_loadtime_reject_average'+circulationgroup_id).value;}
			max = document.getElementById('data_loadtime_reject_max'+circulationgroup_id).value;
		} else {
			if (option == 'min') { var value = document.getElementById('data_loadtime_min'+circulationgroup_id).value;} else
			if (option == 'max') { var value = document.getElementById('data_loadtime_max'+circulationgroup_id).value;}
			else { var value = document.getElementById('data_loadtime_average'+circulationgroup_id).value;}
			max = document.getElementById('data_loadtime_max'+circulationgroup_id).value;
		}
        
        var chartId = circulationgroup_id-1;
		
		for (var i = 0, len = chartLoadTime[chartId].data.datasets.length; i < len; i++) {
		  chartLoadTime[chartId].data.datasets[i].gaugeData.value = value;
		  chartLoadTime[chartId].data.datasets[i].gaugeLimits[2] = max;
		}
		
        chartLoadTime[chartId].options.animation.duration = 5;
        chartLoadTime[chartId].update();
    }	
	
	function updateGarmentAtData(circulationgroup_id, option) {
    
		$.get( "dashboard_6.php?day="+option, function( data ) {
			if (data.data_de[circulationgroup_id]) {
				var d6_data_de = data.data_de[circulationgroup_id];
			} else {
				var d6_data_de = 0;
			}
						
			if (data.data_gu[circulationgroup_id]) {
				var d6_data_gu = data.data_gu[circulationgroup_id];
			} else {
				var d6_data_gu = 0;
			}
			
			if (data.data_la[circulationgroup_id]) {
				var d6_data_la = data.data_la[circulationgroup_id];
			} else {
				var d6_data_la = 0;
			}
			
			if (data.data_re[circulationgroup_id]) {
				var d6_data_re = data.data_re[circulationgroup_id];
			} else {
				var d6_data_re = 0;
			}
			
			
			document.getElementById('data_gu_garment_value'+circulationgroup_id).textContent = d6_data_gu;
			document.getElementById('data_de_garment_value'+circulationgroup_id).textContent = d6_data_de;
			document.getElementById('data_la_garment_value'+circulationgroup_id).textContent = d6_data_la;
			document.getElementById('data_re_garment_value'+circulationgroup_id).textContent = d6_data_re;
		
		}, "json" );
    
    }
	
    function updateDistributionChart(circulationgroup_id, option) {
        if (option == 'period') {
            var date = document.getElementById('data_distributiontimemonth_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributiontimemonth_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftesnelheid laatste periode";
        } else if (option == 'year') {
            var date = document.getElementById('data_distributiontimeyear_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributiontimeyear_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftesnelheid laatste jaar";
        } else { 
            var date = document.getElementById('data_distributiontimeweek_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributiontimeweek_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftesnelheid laatste week";
            
        }
        
        var chartId = circulationgroup_id-1;       
		
		for (var i = 0, len = chartDistributionChart[chartId].data.datasets.length; i < len; i++) {
		  chartDistributionChart[chartId].data.datasets[i].data = average;          
		}
		
        chartDistributionChart[chartId].data.labels = date;
        chartDistributionChart[chartId].options.title.text = titleText;
		for (var i = 0, len = chartDistributionChart[chartId].options.scales.yAxes.length; i < len; i++) {
		  chartDistributionChart[chartId].options.scales.yAxes[i].ticks.suggestedMax = 20;       
		}
        chartDistributionChart[chartId].update();
    }
    
    function updateLoadChart(circulationgroup_id, option) {
		if(document.getElementById('loadtimeChaootCheckbox'+circulationgroup_id).checked) { 
			if (option == 'period') {
				var date = document.getElementById('data_loadtime_reject_month_date'+circulationgroup_id).value;
				date = date.split(",");
				var average = document.getElementById('data_loadtime_reject_month_average'+circulationgroup_id).value;
				average = average.split(",");
				var titleText = "Beladensnelheid laatste periode";
			} else if (option == 'year') {
				var date = document.getElementById('data_loadtime_reject_year_date'+circulationgroup_id).value;
				date = date.split(",");
				var average = document.getElementById('data_loadtime_reject_year_average'+circulationgroup_id).value;
				average = average.split(",");
				var titleText = "Beladensnelheid laatste jaar";
			} else { 
				var date = document.getElementById('data_loadtime_reject_week_date'+circulationgroup_id).value;
				date = date.split(",");
				var average = document.getElementById('data_loadtime_reject_week_average'+circulationgroup_id).value;
				average = average.split(",");
				var titleText = "Beladensnelheid laatste week";
			}
		} else {
			if (option == 'period') {
				var date = document.getElementById('data_loadtimemonth_date'+circulationgroup_id).value;
				date = date.split(",");
				var average = document.getElementById('data_loadtimemonth_average'+circulationgroup_id).value;
				average = average.split(",");
				var titleText = "Beladensnelheid laatste periode";
			} else if (option == 'year') {
				var date = document.getElementById('data_loadtimeyear_date'+circulationgroup_id).value;
				date = date.split(",");
				var average = document.getElementById('data_loadtimeyear_average'+circulationgroup_id).value;
				average = average.split(",");
				var titleText = "Beladensnelheid laatste jaar";
			} else { 
				var date = document.getElementById('data_loadtimeweek_date'+circulationgroup_id).value;
				date = date.split(",");
				var average = document.getElementById('data_loadtimeweek_average'+circulationgroup_id).value;
				average = average.split(",");
				var titleText = "Beladensnelheid laatste week";
			}
		}
        
        var chartId = circulationgroup_id-1; 
        		
		for (var i = 0, len = chartDistributionChart[chartId].data.datasets.length; i < len; i++) {
		  chartDistributionChart[chartId].data.datasets[i].data = average;          
		}
        chartDistributionChart[chartId].data.labels = date;
        chartDistributionChart[chartId].options.title.text = titleText;
        chartDistributionChart[chartId].update();
    }
    
    function updateMisseizedChart(circulationgroup_id, option) {
        if (option == 'period') {
            var date = document.getElementById('data_misseizedmonth_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_misseizedmonth_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Misgrijpen laatste periode";
        } else if (option == 'year') {
            var date = document.getElementById('data_misseizedyear_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_misseizedyear_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Misgrijpen laatste jaar";
        } else { 
            var date = document.getElementById('data_misseizedweek_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_misseizedweek_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Misgrijpen laatste week";
            
        }
        
        var chartId = circulationgroup_id-1;
        
        for (var i = 0, len = chartDistributionChart[chartId].data.datasets.length; i < len; i++) {
		  chartDistributionChart[chartId].data.datasets[i].data = average;           
		}
        chartDistributionChart[chartId].data.labels = date;
        chartDistributionChart[chartId].options.title.text = titleText;
        chartDistributionChart[chartId].options.scales.text = titleText;
	
		for (var i = 0, len = chartDistributionChart[chartId].options.scales.yAxes.length; i < len; i++) {
		  chartDistributionChart[chartId].options.scales.yAxes[i].ticks.suggestedMax = 5;     
		}
        chartDistributionChart[chartId].update();
    }
    
    function updateLoginChart(circulationgroup_id, option) {
        if (option == 'period') {
            var date = document.getElementById('data_loginmonth_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_loginmonth_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Aantal unieke aangemelde dragers laatste periode";
        } else if (option == 'year') {
            var date = document.getElementById('data_loginyear_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_loginyear_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Aantal unieke aangemelde dragers laatste jaar";
        } else { 
            var date = document.getElementById('data_loginweek_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_loginweek_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Aantal unieke aangemelde dragers laatste week";        
        }
        
        var chartId = circulationgroup_id-1;
        
        for (var i = 0, len = chartDistributionChart[chartId].data.datasets.length; i < len; i++) {
		  chartDistributionChart[chartId].data.datasets[i].data = average;          
		}
        chartDistributionChart[chartId].data.labels = date;
        chartDistributionChart[chartId].options.title.text = titleText;
        chartDistributionChart[chartId].options.scales.text = titleText;
        chartDistributionChart[chartId].update();
    }
	
    function updateDistributioncountChart(circulationgroup_id, option) {
        if (option == 'period') {
            var date = document.getElementById('data_distributioncountmonth_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributioncountmonth_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftes laatste periode";
        } else if (option == 'year') {
            var date = document.getElementById('data_distributioncountyear_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributioncountyear_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftes laatste jaar";
        } else { 
            var date = document.getElementById('data_distributioncountweek_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributioncountweek_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftes laatste week";
            
        }
        
        var chartId = circulationgroup_id-1;      
        
        for (var i = 0, len = chartDistributionChart[chartId].data.datasets.length; i < len; i++) {
		  chartDistributionChart[chartId].data.datasets[i].data = average;         
		}
        chartDistributionChart[chartId].data.labels = date;
        chartDistributionChart[chartId].options.title.text = titleText;
        chartDistributionChart[chartId].options.scales.text = titleText;
		
		for (var i = 0, len = chartDistributionChart[chartId].options.scales.yAxes.length; i < len; i++) {
		  chartDistributionChart[chartId].options.scales.yAxes[i].ticks.suggestedMax = 5;     
		}
		
        chartDistributionChart[chartId].update();
    }
    
    function updateDistributionuserChart(circulationgroup_id, option) {
        if (option == 'period') {
            var date = document.getElementById('data_distributionusermonth_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributionusermonth_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftes per drager laatste periode";
        } else if (option == 'year') {
            var date = document.getElementById('data_distributionuseryear_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributionuseryear_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftes per drager laatste jaar";
        } else { 
            var date = document.getElementById('data_distributionuserweek_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributionuserweek_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftes per drager laatste week";
            
        }
        
        var chartId = circulationgroup_id-1;
                
        for (var i = 0, len = chartDistributionChart[chartId].data.datasets.length; i < len; i++) {
		  chartDistributionChart[chartId].data.datasets[i].data = average;          
		}
        chartDistributionChart[chartId].data.labels = date;
        chartDistributionChart[chartId].options.title.text = titleText;
        chartDistributionChart[chartId].options.scales.text = titleText;
		
		for (var i = 0, len = chartDistributionChart[chartId].options.scales.yAxes.length; i < len; i++) {
		  chartDistributionChart[chartId].options.scales.yAxes[i].ticks.suggestedMax = 5;     
		}
		
        chartDistributionChart[chartId].update();
    }
    
    function updateDistributionstationChart(circulationgroup_id, option) {
        if (option == 'period') {
            var date = document.getElementById('data_distributionstationmonth_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributionstationmonth_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftes per station laatste periode";
        } else if (option == 'year') {
            var date = document.getElementById('data_distributionstationyear_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributionstationyear_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftes per station laatste jaar";
        } else { 
            var date = document.getElementById('data_distributionstationweek_date'+circulationgroup_id).value;
            date = date.split(",");
            var average = document.getElementById('data_distributionstationweek_average'+circulationgroup_id).value;
            average = average.split(",");
            var titleText = "Uitgiftes per station laatste week";
            
        }
        
        var chartId = circulationgroup_id-1;
        
        for (var i = 0, len = chartDistributionChart[chartId].data.datasets.length; i < len; i++) {
		  chartDistributionChart[chartId].data.datasets[i].data = average;         
		}
        chartDistributionChart[chartId].data.labels = date;
        chartDistributionChart[chartId].options.title.text = titleText;
        chartDistributionChart[chartId].options.scales.text = titleText;
		
		for (var i = 0, len = chartDistributionChart[chartId].options.scales.yAxes.length; i < len; i++) {
		  chartDistributionChart[chartId].options.scales.yAxes[i].ticks.suggestedMax = 5;     
		}
		
        chartDistributionChart[chartId].update();
    }
	

    var cgs = <?php echo json_encode($cgs) ?>;
    var chartInRoulationElement = [];
    var chartOutRoulationElement = [];
    var chartLoadElement = [];
    var chartRoulationAdviceElement = [];
    var chartDistributionTimeElement = [];
    var chartDistributionChartElement = [];
    var charts = [];
    var chartDistributionTime = [];
    var chartDistributionChart = [];
    
    var chartLoadTimeElement = [];
    var chartLoadChartElement = [];
    var chartLoadTime = [];
    var chartLoadChart = [];
    
    var chartMisseizedElement = [];
    var chartMisseized = [];
    
    var chartDistributioncountElement = [];
    var chartDistributioncount = [];
    
    var chartDistributionuserElement = [];
    var chartDistributionuser = [];
    
    var chartDistributionstationElement = [];
    var chartDistributionstation = [];
    
    var chartLoginElement = [];
    var chartLogin = [];

    
    $.each(cgs, function (i, elem) {

        chartInRoulationElement.push(document.getElementById('chartInRoulation'+elem));
        var data_ir = document.getElementById('data_ir'+elem).value;
        data_ir = data_ir.split(",");
		var totalInRoulation = 0;
		$.each(data_ir, function(key, value) {
			totalInRoulation += parseInt(value);
		});

        chartInRoulationElement[elem-1].height = 220;
        		var currentChart = new Chart(chartInRoulationElement[elem-1], {
            type: 'pie',
            data: {
                labels: ["<?php echo $lang['loaded']; ?>", "<?php echo $lang['rejected']; ?>", "<?php echo $lang['garmentuser']; ?>", "<?php echo $lang['deposit']; ?>", "<?php echo $lang['transport']; ?>", "<?php echo $lang['transport_to_laundry']; ?>", "<?php echo $lang['laundry']; ?>"],
                datasets: [{
                    label: '# of Votes',
                    data: data_ir,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(0, 0, 117, 0.2)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(0, 0, 117, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: true,
                    position: "right",
					labels: {
					  /* filter */
					  filter: function(legendItem, data) {
						/* filter already loops throw legendItem & data (console.log) to see this idea */
						var index = legendItem.index;
						var currentDataValue =  data.datasets[0].data[index];
						if (currentDataValue > 0)
						{
						  return true; //only show when the label is cash
						}
					  }
					}
      
                },
                title: {
                    display: true,
                    text: ["<?php echo $lang['circulating']; ?>","<?php echo $lang['total']; ?>"+': '+totalInRoulation],
                    fontStyle: 'bold',
                    fontColor: 'black',
                    fontSize: 13
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
        charts.push(currentChart);
        
        chartLoadElement.push(document.getElementById('chartLoad'+elem));
        var data_load = document.getElementById('data_load'+elem).value;
        data_load = data_load.split(",");
		
		var totalPosition = 0;
		$.each(data_load, function(key, value) {
			totalPosition += parseInt(value);
		}); 
		

        chartLoadElement[elem-1].height = 220;
        var currentChart = new Chart(chartLoadElement[elem-1], {
            type: 'pie',
            data: {
                labels: ["<?php echo $lang['loaded']; ?>", "<?php echo $lang['free']; ?>", "<?php echo $lang['too_much']; ?>"],
                datasets: [{
                    label: '# of Votes',
                    data: data_load,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
						'rgba(255, 99, 132, 0.2)',
						'rgba(255, 206, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
						'rgba(255, 99, 132, 1)',
						'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: true,
                    position: "right"
                },
                title: {
                    display: true,
                    text: ["<?php echo $lang['current_load']; ?>", "<?php echo $lang['total']; ?>"+': '+totalPosition],
                    fontStyle: 'bold',
                    fontColor: 'black',
                    fontSize: 13
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
        charts.push(currentChart);
		
		chartRoulationAdviceElement.push(document.getElementById('chartRoulationAdvice'+elem));
        var data_roulationadvice = document.getElementById('data_roulationadvice'+elem).value;
        data_roulationadvice = data_roulationadvice.split(",");
		
		chartRoulationAdviceElement[elem-1].height = 220;
        var currentChart = new Chart(chartRoulationAdviceElement[elem-1], {
            type: 'pie',
            data: {
                labels: ["<?php echo $lang['required']; ?>", "<?php echo $lang['order']; ?>", "<?php echo $lang['too_much']; ?>"],
                datasets: [{
					label: '# of Votes',
                    data: data_roulationadvice,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(253, 151, 4, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(253, 151, 4, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                legend: {
                    display: true,
                    position: "right"
                },
                title: {
                    display: true,
                    text: ["<?php echo $lang['circulationadvice']; ?>", ''],
                    fontStyle: 'bold',
                    fontColor: 'black',
                    fontSize: 13
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
        charts.push(currentChart);
    });
    
    $(document).ready(function() {
 		 

        $("#search").focus();
		
		$.get( "dashboard_1.php", function( data ) {
                var d1_data_html = data.data_html;
                
                $("#tabs").append( d1_data_html );
                $.each(cgs, function (i, elem) {
                    chartOutRoulationElement.push(document.getElementById('chartOutRoulation'+elem));
                    var data_or = document.getElementById('data_or'+elem).value;
                    data_or = data_or.split(",");
					
					var totalOutRoulation = 0;
					$.each(data_or, function(key, value) {
						totalOutRoulation += parseInt(value);
					});
					
                    chartOutRoulationElement[elem-1].height = 220;
                    var currentChart = new Chart(chartOutRoulationElement[elem-1], {
                        type: 'pie',
                        data: {
                            labels: ["<?php echo $lang['never_scanned']; ?>", "<?php echo $lang['missing']; ?>", "<?php echo $lang['stock_hospital']; ?>", "<?php echo $lang['stock_laundry']; ?>", "<?php echo $lang['homewash']; ?>",  "<?php echo $lang['repair']; ?>", "<?php echo $lang['despeckle']; ?>", "<?php echo $lang['disconnected_garments']; ?>"],
                            datasets: [{
                                label: '# of Votes',
                                data: data_or,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(0, 0, 0, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(255, 159, 64, 0.2)',
                                    'rgba(0, 0, 117, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(0, 0, 0, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                    'rgba(0, 0, 117, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            legend: {
                                display: true,
                                position: "right",
								labels: {
								  /* filter */
								  filter: function(legendItem, data) {
									/* filter already loops throw legendItem & data (console.log) to see this idea */
									var index = legendItem.index;
									var currentDataValue =  data.datasets[0].data[index];
									if (currentDataValue > 0)
									{
									  return true; //only show when the label is cash
									}
								  }
								}
                            },
                            title: {
                                display: true,
                                text: ["<?php echo $lang['out_circulation']; ?>","<?php echo $lang['total']; ?>"+': '+totalOutRoulation],
                                fontStyle: 'bold',
                                fontColor: 'black',
                                fontSize: 13
                            },
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                    charts.push(currentChart); 
                });  
				$.get( "dashboard_6.php?day=7", function( data ) {
					var d6_data_html = data.data_html;
					
					$("#tabs").append( d6_data_html );
					$.each(cgs, function (i, elem) {
						
						if (document.getElementById('data_gu_garment'+elem)) {
							var data_gu_garment = document.getElementById('data_gu_garment'+elem).value;
						} else {
							var data_gu_garment = 0;
						}
						
						if (document.getElementById('data_la_garment'+elem)) {
							var data_la_garment = document.getElementById('data_la_garment'+elem).value;
						} else {
							var data_la_garment = 0;
						}
						
						if (document.getElementById('data_re_garment'+elem)) {
							var data_re_garment = document.getElementById('data_re_garment'+elem).value;
						} else {
							var data_re_garment = 0;
						}
						
						if (document.getElementById('data_de_garment'+elem)) {
							var data_de_garment = document.getElementById('data_de_garment'+elem).value;
						} else {
							var data_de_garment = 0;
						}
						
						document.getElementById('data_gu_garment_value'+elem).textContent = data_gu_garment;
						document.getElementById('data_de_garment_value'+elem).textContent = data_de_garment;
						document.getElementById('data_la_garment_value'+elem).textContent = data_la_garment;
						document.getElementById('data_re_garment_value'+elem).textContent = data_re_garment;
					});    
					
				
				
				$.get( "dashboard_2.php", function( data ) {
            var d2_data_html = data.data_html;

            $("#tabs").append( d2_data_html );
            $.each(cgs, function (i, elem) {
                chartDistributionTimeElement.push(document.getElementById('chartDistributionTime'+elem));
                var data_distributiontime_average = document.getElementById('data_distributiontime_average'+elem).value;
                var data_distributiontime_max = document.getElementById('data_distributiontime_max'+elem).value;

                chartDistributionTimeElement[elem-1].height = 178;
                if (data_distributiontime_average <= 10) { var color = 'rgba(15, 220, 99, 0.6)';} else
                if (data_distributiontime_average <= 20 && data_distributiontime_average > 10) { var color = 'rgba(253, 151, 4, 0.6)';}
                else { var color = 'rgba(255, 30, 14, 0.3)';}

                var currentChart = new Chart(chartDistributionTimeElement[elem-1], {
                        type: "tsgauge",
                        data: {
                                datasets: [{
                                        backgroundColor: [
                                        'rgba(15, 220, 99, 0.4)',
                                        'rgba(253, 151, 4, 0.4)',
                                        'rgba(255, 30, 14, 0.3)'
                                        ],
                                        borderColor: [
                                            'rgba(15, 220, 99, 1)',
                                            'rgba(253, 151, 4, 1)',
                                            'rgba(255, 30, 14, 1)'
                                        ],
                                        borderWidth: 1,
                                        gaugeData: {
                                                value: data_distributiontime_average,
                                                valueColor: color
                                        },
                                        gaugeLimits: [0, 10, 20, data_distributiontime_max]
                                }]
                        },
                        options: {
                            events: [],
                            showMarkers: true,
                            title: {
                                display: true,
                                text: ["<?php echo $lang['distribution_speed']; ?>","<?php echo $lang['today']; ?>"],
                                fontStyle: 'bold',
                                fontColor: 'black',
                                fontSize: 13
                            },
                            responsive: true,
                            maintainAspectRatio: false
                        }
                });
                chartDistributionTime.push(currentChart); 
                
                chartDistributionChartElement.push(document.getElementById('chartDistributionChart'+elem));

			chartDistributionChartElement[elem-1].height = 220;
            var data_distributiontimeweek_date = document.getElementById('data_distributiontimeweek_date'+elem).value;
            data_distributiontimeweek_date = data_distributiontimeweek_date.split(",");
            var data_distributiontimeweek_average = document.getElementById('data_distributiontimeweek_average'+elem).value;
            data_distributiontimeweek_average = data_distributiontimeweek_average.split(",");

            var colors = palette('tol-rainbow', data_distributiontimeweek_average.length).map(function(hex) {return '#' + hex;});
            var finalColors = [];
            for ( var i = 0, l = colors.length; i < l; i++ ) {
                var rgbaCol = 'rgba(' + parseInt(colors[ i ].slice(-6,-4),16)
                        + ',' + parseInt(colors[ i ].slice(-4,-2),16)
                        + ',' + parseInt(colors[ i ].slice(-2),16)
                        +',0.3)';
                finalColors.push(rgbaCol);
            }

            var currentChart = new Chart(chartDistributionChartElement[elem-1], {
                type: 'bar',
                data: {
                    labels: data_distributiontimeweek_date, 
                    datasets: [{
                        data: data_distributiontimeweek_average,
                        borderWidth: 1,
                        backgroundColor: 'rgba(15, 220, 99, 0.4)',
                        borderColor: 'rgba(15, 220, 99, 1)'
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: "<?php echo $lang['distribution_speed_last_week']; ?>",
                        fontStyle: 'bold',
                        fontColor: 'black',
                        fontSize: 13
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                suggestedMax: 20
                            }
                        }]
                    }
                }
            });

            chartDistributionChart.push(currentChart);
                
            });
			$.get( "dashboard_3.php", function( data ) {
                var d3_data_html = data.data_html;
                
                $("#tabs").append( d3_data_html );
                $.each(cgs, function (i, elem) {
                    chartLoadTimeElement.push(document.getElementById('chartLoadTime'+elem));
					
					var element = document.getElementById('data_loadtime_average'+elem);
					if (element && element.value !== '') {
						var data_loadtime_average = element.value;
					} else {
						var data_loadtime_average = 0;
					}
					
					var element = document.getElementById('data_loadtime_max'+elem);
					if (element && element.value !== '') {
						var data_loadtime_max = element.value;
					} else {
						var data_loadtime_max = 0;
					}
					
                    if (data_loadtime_average <= 150) { var color = 'rgba(255, 30, 14, 0.3)';}
                    else { var color = 'rgba(15, 220, 99, 0.6)';}                   
                    
                    chartLoadTimeElement[elem-1].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartLoadTimeElement[elem-1], {
                            type: "tsgauge",
                            data: {
                                    datasets: [{
                                            backgroundColor: [
                                                'rgba(255, 30, 14, 0.3)',
                                                'rgba(15, 220, 99, 0.4)'
                                            ],
                                            borderColor: [
                                                'rgba(255, 30, 14, 1)',
                                                'rgba(15, 220, 99, 1)'
                                            ],
                                            borderWidth: 1,
                                            gaugeData: {
                                                    value: data_loadtime_average,
                                                    valueColor: color,
                                                    valueLabel: "/uur"
                                            },
                                            gaugeLimits: [0, 150, data_loadtime_max]
                                    }]
                            },
                            options: {
                                events: [],
                                showMarkers: true,
                                title: {
                                    display: true,
                                    text: ["<?php echo $lang['loading_speed']; ?>","<?php echo $lang['today']; ?>"],
                                    fontStyle: 'bold',
                                    fontColor: 'black',
                                    fontSize: 13
                                },
                                responsive: true,
                                maintainAspectRatio: false
                            }
                    });
                    chartLoadTime.push(currentChart);
                });  
			
				$.get( "dashboard_4.php", function( data ) {
                var d4_data_html = data.data_html;
                
                $("#tabs").append( d4_data_html );
                $.each(cgs, function (i, elem) {
                    chartMisseizedElement.push(document.getElementById('chartMisseized'+elem));
                    var data_misseized_average = document.getElementById('data_misseized_average'+elem).value;
					chartMisseizedElement[elem-1].height = 178;
                    
                    if (data_misseized_average <= 2) { var color = 'rgba(15, 220, 99, 0.6)';} else
                    if (data_misseized_average <= 10 && data_misseized_average > 2) { var color = 'rgba(253, 151, 4, 0.6)';}
                    else { var color = 'rgba(255, 30, 14, 0.3)';}


                    var currentChart = new Chart(chartMisseizedElement[elem-1], {
                            type: "tsgauge",
                            data: {
                                    datasets: [{
                                            backgroundColor: [
                                            'rgba(15, 220, 99, 0.4)',
                                            'rgba(255, 30, 14, 0.3)'
                                            ],
                                            borderColor: [
                                                'rgba(15, 220, 99, 1)',
												'rgba(255, 30, 14, 1)'
                                            ],
                                            borderWidth: 1,
                                            gaugeData: {
                                                    value: data_misseized_average,
                                                    valueColor: color,
                                                    valueLabel: "%"
                                            },
                                            gaugeLimits: [0, 2, 20]
                                    }]
                            },
                            options: {
                                events: [],
                                showMarkers: true,
                                title: {
                                    display: true,
                                    text: ["<?php echo $lang['misseized']; ?>","<?php echo $lang['today']; ?>"],
                                    fontStyle: 'bold',
                                    fontColor: 'black',
                                    fontSize: 13
                                },
                                responsive: true,
                                maintainAspectRatio: false
                            }
                    });
                    chartMisseized.push(currentChart);
                }); 

$.get( "dashboard_5.php", function( data ) {
                var d5_data_html = data.data_html;
                
                $("#tabs").append( d5_data_html );
                $.each(cgs, function (i, elem) {
                    chartLoginElement.push(document.getElementById('chartLogin'+elem));
                    var data_login_average = document.getElementById('data_login_average'+elem).value;

                    chartLoginElement[elem-1].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartLoginElement[elem-1], {
                            type: "tsgauge",
                            data: {
                                    datasets: [{
                                            backgroundColor: [
                                            'rgba(15, 220, 99, 0.4)',
                                            ],
                                            borderColor: [
                                                'rgba(15, 220, 99, 1)',
                                            ],
                                            borderWidth: 1,
                                            gaugeData: {
                                                    value: data_login_average,
                                                    valueColor: color
                                            },
                                            gaugeLimits: [0, data_login_average]
                                    }]
                            },
                            options: {
                                events: [],
                                showMarkers: true,
                                title: {
                                    display: true,
                                    text: ["<?php echo $lang['number_of_unique_logged_in_users']; ?>","<?php echo $lang['today']; ?>"],
                                    fontStyle: 'bold',
                                    fontColor: 'black',
                                    fontSize: 13
                                },
                                responsive: true,
                                maintainAspectRatio: false
                            }
                    });
                    chartLogin.push(currentChart);
                });    
				
				$.get( "dashboard_10.php", function( data ) {
                var d10_data_html = data.data_html;
                
                $("#tabs").append( d10_data_html );
                $.each(cgs, function (i, elem) {
                    chartDistributioncountElement.push(document.getElementById('chartDistributioncount'+elem));
                    var data_distributioncount_average = document.getElementById('data_distributioncount_average'+elem).value;

                    chartDistributioncountElement[elem-1].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartDistributioncountElement[elem-1], {
                            type: "tsgauge",
                            data: {
                                    datasets: [{
                                            backgroundColor: [
                                                'rgba(15, 220, 99, 0.4)'
                                            ],
                                            borderColor: [
                                                'rgba(15, 220, 99, 1)'
                                            ],
                                            borderWidth: 1,
                                            gaugeData: {
                                                    value: data_distributioncount_average,
                                                    valueColor: color
                                            },
                                            gaugeLimits: [0, data_distributioncount_average]
                                    }]
                            },
                            options: {
                                events: [],
                                showMarkers: true,
                                title: {
                                    display: true,
                                    text: ["<?php echo $lang['number_of_distributions']; ?>","<?php echo $lang['today']; ?>"],
                                    fontStyle: 'bold',
                                    fontColor: 'black',
                                    fontSize: 13
                                },
                                responsive: true,
                                maintainAspectRatio: false
                            }
                    });
                    chartDistributioncount.push(currentChart);
                });    
				$.get( "dashboard_11.php", function( data ) {
                var d11_data_html = data.data_html;
                
                $("#tabs").append( d11_data_html );
                $.each(cgs, function (i, elem) {
                    chartDistributionuserElement.push(document.getElementById('chartDistributionuser'+elem));
                    var data_distributionuser_average = document.getElementById('data_distributionuser_average'+elem).value;

                    chartDistributionuserElement[elem-1].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartDistributionuserElement[elem-1], {
                            type: "tsgauge",
                            data: {
                                    datasets: [{
                                            backgroundColor: [
                                                'rgba(15, 220, 99, 0.4)'
                                            ],
                                            borderColor: [
                                                'rgba(15, 220, 99, 1)'
                                            ],
                                            borderWidth: 1,
                                            gaugeData: {
                                                    value: data_distributionuser_average,
                                                    valueColor: color
                                            },
                                            gaugeLimits: [0, data_distributionuser_average]
                                    }]
                            },
                            options: {
                                events: [],
                                showMarkers: true,
                                title: {
                                    display: true,
                                    text: ["<?php echo $lang['number_of_distributions']; ?>", "<?php echo $lang['per_user_per_week']; ?>"],
                                    fontStyle: 'bold',
                                    fontColor: 'black',
                                    fontSize: 13
                                },
                                responsive: true,
                                maintainAspectRatio: false
                            }
                    });
                    chartDistributionuser.push(currentChart);
                });    
				$.get( "dashboard_12.php", function( data ) {
                var d12_data_html = data.data_html;
                
                $("#tabs").append( d12_data_html );
                $.each(cgs, function (i, elem) {
                    chartDistributionstationElement.push(document.getElementById('chartDistributionstation'+elem));
                    var data_distributionstation_average = document.getElementById('data_distributionstation_average'+elem).value;

                    chartDistributionstationElement[elem-1].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartDistributionstationElement[elem-1], {
                            type: "tsgauge",
                            data: {
                                    datasets: [{
                                            backgroundColor: [
                                                'rgba(15, 220, 99, 0.4)'
                                            ],
                                            borderColor: [
                                                'rgba(15, 220, 99, 1)'
                                            ],
                                            borderWidth: 1,
                                            gaugeData: {
                                                    value: data_distributionstation_average,
                                                    valueColor: color
                                            },
                                            gaugeLimits: [0, data_distributionstation_average]
                                    }]
                            },
                            options: {
                                events: [],
                                showMarkers: true,
                                title: {
                                    display: true,
                                    text: ["<?php echo $lang['number_of_distributions_per_station']; ?>","<?php echo $lang['today']; ?>"],
                                    fontStyle: 'bold',
                                    fontColor: 'black',
                                    fontSize: 13
                                },
                                responsive: true,
                                maintainAspectRatio: false
                            }
                    });
                    chartDistributionuser.push(currentChart);
                });   
				$(document).trigger("afterLastDocumentReady");
	}, "json" );
				
	}, "json" );
				
	}, "json" );
	}, "json" );				

				
	}, "json" );
				
	}, "json" );
			
	}, "json" );
				
	}, "json" );
	 }, "json" );
				
    });
    
	$(document).on("afterLastDocumentReady", function () {
        $.get( "dashboard_13.php", function( data ) {
			var d13_data_html = data.data_html;
			$("#tabs").append( d13_data_html );
			
			$.get( "dashboard_14.php", function( data ) {
				var d14_data_html = data.data_html;
				$("#tabs").append( d14_data_html );
				
				$.get( "dashboard_15.php", function( data ) {
					var d15_data_html = data.data_html;
					$("#tabs").append( d15_data_html );
					
					$.get( "dashboard_16.php", function( data ) {
						var d16_data_html = data.data_html;
						$("#tabs").append( d16_data_html );
						
						$.get( "dashboard_17.php", function( data ) {
							var d17_data_html = data.data_html;
							$("#tabs").append( d17_data_html );
								
								$.get( "dashboard_18.php", function( data ) {
									var d18_data_html = data.data_html;
									$("#tabs").append( d18_data_html );
									
									$('input[type=radio]').removeAttr("disabled");
									$(".radioset").buttonset("refresh");
								}, "json" );
						}, "json" );
					}, "json" );
				}, "json" );
			}, "json" );
		}, "json" );
    });
	
	
    $(window).on('load', function() {
        $('label[name ="radiolabel"] span').css({'padding-right':'0.5em'});
        $('label[name ="radiolabel"] span').css({'padding-left':'0.5em'});
        $('label[name ="radiolabel"] span').css({'padding-top':'0.3em'});
        $('label[name ="radiolabel"] span').css({'padding-bottom':'0.3em'});
		
		$("#div-top-row-1").css({'padding-right':'1px'});
		$("#div-top-row-1").css({'padding-left':'2px'});
		$("#div-top-row-3").css({'padding-right':'1px'});
		$("#div-top-row-3").css({'padding-left':'2px'});
		$("#div-top-row-4").css({'padding-right':'1px'});
		$("#div-top-row-4").css({'padding-left':'2px'});
    });
	
	
</script>
