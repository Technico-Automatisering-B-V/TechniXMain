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
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartInRoulation". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
			$tabs .="<div id=\"div-top-row-2\" style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartOutRoulation". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="<div id=\"div-top-row-3\" style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartLoad". $row["circulationgroup_id"] ."\"></canvas></div>";
			$tabs .="</div>";
            $tabs .="<div id=\"div-top-row-4\" style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
			$tabs .="<div style=\"height:250px;\"><canvas id=\"chartRoulationAdvice". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;vertical-align: top;\">";
			$tabs .="<div style=\"vertical-align: baseline;text-align: center;font-size: 13px;font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;color: black;font-weight: bold;padding: 10px;line-height: 1.2;\">";
			$tabs .= $lang["statistics"]."</div>";
			$tabs .="<div style=\"margin-left: 10px;margin-top:25px;margin-right: 10px;\">";
			$tabs .= "<table style=\"width: 100%;line-height: 1.5;\">";
			$tabs .= "<tr><td>".$lang["__circulation_advice_longer_garmentuser"].":</td><td style=\"text-align: right;\"><strong><span id=\"data_gu_garment_value".$row["circulationgroup_id"]."\"></span></strong></td></tr>";
            $tabs .= "<tr><td>".$lang["__circulation_advice_longer_deposited"].":</td><td style=\"text-align: right;\"><strong><span id=\"data_de_garment_value".$row["circulationgroup_id"]."\"></span></strong></td></tr>";
            $tabs .= "<tr><td>".$lang["__circulation_advice_longer_laundry"].":</td><td style=\"text-align: right;\"><strong><span id=\"data_la_garment_value".$row["circulationgroup_id"]."\"></span></strong></td></tr>";
            $tabs .= "<tr><td>".$lang["__circulation_advice_longer_chaoot"].":</td><td style=\"text-align: right;\"><strong><span id=\"data_re_garment_value".$row["circulationgroup_id"]."\"></span></strong></td></tr>";			
	    $tabs .= "</table></div>";
	    $tabs .="</div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table;width: 100%\">";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;vertical-align:top\">";
			$tabs .="<div style=\"height:250px;\"><canvas id=\"chartDistributionTime". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
			$tabs .="<div style=\"display: table-cell;width: 60%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartDistributionChart". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"position: relative;display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartLoadTime". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table;width: 100%\">";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartMisseized". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
			$tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartLogin". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartDistributioncount". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartDistributionuser". $row["circulationgroup_id"] ."\"></canvas></div>";
            $tabs .="</div>";
            $tabs .="<div style=\"display: table-cell;width: 20%;border: 1px solid #25824f;padding: 5px;\">";
            $tabs .="<div style=\"height:250px;\"><canvas id=\"chartDistributionstation". $row["circulationgroup_id"] ."\"></canvas></div>";
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

        chartInRoulationElement[elem-3].height = 220;
        		var currentChart = new Chart(chartInRoulationElement[elem-3], {
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
		

        chartLoadElement[elem-3].height = 220;
        var currentChart = new Chart(chartLoadElement[elem-3], {
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
		
		chartRoulationAdviceElement[elem-3].height = 220;
        var currentChart = new Chart(chartRoulationAdviceElement[elem-3], {
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
 		setInterval(function() {
                  window.location.reload();
                }, 60000); 

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
					
                    chartOutRoulationElement[elem-3].height = 220;
                    var currentChart = new Chart(chartOutRoulationElement[elem-3], {
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

                chartDistributionTimeElement[elem-3].height = 178;
                if (data_distributiontime_average <= 10) { var color = 'rgba(15, 220, 99, 0.6)';} else
                if (data_distributiontime_average <= 20 && data_distributiontime_average > 10) { var color = 'rgba(253, 151, 4, 0.6)';}
                else { var color = 'rgba(255, 30, 14, 0.3)';}

                var currentChart = new Chart(chartDistributionTimeElement[elem-3], {
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

			chartDistributionChartElement[elem-3].height = 220;
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

            var currentChart = new Chart(chartDistributionChartElement[elem-3], {
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
                    
                    chartLoadTimeElement[elem-3].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartLoadTimeElement[elem-3], {
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
					chartMisseizedElement[elem-3].height = 178;
                    
                    if (data_misseized_average <= 2) { var color = 'rgba(15, 220, 99, 0.6)';} else
                    if (data_misseized_average <= 10 && data_misseized_average > 2) { var color = 'rgba(253, 151, 4, 0.6)';}
                    else { var color = 'rgba(255, 30, 14, 0.3)';}


                    var currentChart = new Chart(chartMisseizedElement[elem-3], {
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

                    chartLoginElement[elem-3].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartLoginElement[elem-3], {
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

                    chartDistributioncountElement[elem-3].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartDistributioncountElement[elem-3], {
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

                    chartDistributionuserElement[elem-3].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartDistributionuserElement[elem-3], {
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

                    chartDistributionstationElement[elem-3].height = 178;
                    var color = 'rgba(15, 220, 99, 0.6)';

                    var currentChart = new Chart(chartDistributionstationElement[elem-3], {
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
