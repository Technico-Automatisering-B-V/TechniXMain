<div class="clear">
<table>
<tr>
<td>
    <div class="charts"></div>
</td>
<td style="vertical-align:top;">
    <div class="filter">
        <table>
            <tr>
                <td colspan="2"><h2>Overzicht kostenplaats instellingen</h2></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="td-label">Aantal Wasbeurten Standaard Drempelwaarde Inspectie</td>
                <th class="td-data" style="text-align:right;">150</th>
            </tr>
            <tr>
                <td class="td-label">Uiterste Wasbeurten Limiet</td>
                <th class="td-data" style="text-align:right;"><?=$aData[0]['maxlimit']?></th>
            </tr>
            <tr>
                <td class="td-label">% Voor Melding Inspectie</td>
                <th class="td-data" style="text-align:right;" id='percentageValue' percentage='<?=$aData[0]["checking_percentage"]?>'><?=$aData[0]['checking_percentage']?>%</th>
            </tr>
            <tr>
                <td class="td-label">Prijs Beheer</td>
                <th class="td-data" style="text-align:right;">&euro;<?=$aData[0]['maintenance_cost']?></th>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><h2>Overzicht kostenplaats</h2></td>
            </tr>
            <tr>
            <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="td-label">Totaal Prijs incl. huur</td>
                <th class="td-data">&euro;<?=number_format($aRental[0]['rental_price'], 2, ',', '.')?></th>
            </tr>
            <tr>
                <td class="td-label">Totaal Prijs Beheer boven controlegrens</td>
                <th class="td-data" style="text-align:right;">&euro;<?=number_format($aMaintenance[0]['maintenance_cost'], 2, ',', '.')?></th>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
    </div>
    <?php if($bHasTMP) { ?>
    <form method="POST">
        <input type="hidden" name="undoLast" value="yes" />
        <input type="submit" value="Laatste verhoging ongedaan maken" />
    </form>
    <?php } ?>
</td>
</tr>
</table>
    <script type="text/javascript">
        function generatePie(ArticlePercentages, iId) {
            var barColors = [];
            var labels_list = [];
            var data_list = [];
            var article_ids_list = [];
            var title = "";
        
            for(i in ArticlePercentages) {
                article = ArticlePercentages[i].article;
                labels_list.push(article.article + " (" + article.percentage + "%)");
                data_list.push(article.percentage);
                article_ids_list.push(article.article_id);
                
                if(article.amount_over_maxlimit > 0) {
                    barColors.push('#E50F2D');
                }
                else if(parseFloat(article.percentage) > parseFloat(window.percentageValue.getAttribute('percentage'))) {
                    barColors.push('#FFA500');
                }
                else {
                    barColors.push('#228B22');
                }
                
                title = ArticlePercentages[i].category;
            };
            
            divfilter = document.createElement('div');
            divfilter.setAttribute('class', 'filter');
            canvasEL = document.createElement('canvas');
            canvasEL.setAttribute('id', iId);
            canvasEL.setAttribute('style', 'height:50em; width:50em');
            
            divfilter.appendChild(canvasEL);
            var oCharts = document.querySelector('.charts');
            oCharts.appendChild(divfilter);
            
            var ctx = canvasEL.getContext('2d');
            
            data = {
                datasets: [{
                    data: data_list,
                    backgroundColor: barColors
                }],
                labels: labels_list
            };
            
            options = {
                title: {
                    display: true,
                    fontSize: 24,
                    text: title
                },
                onClick: function(e, item) {
                    if(item.length > 0) {
                        var selected_slice = item[0]["_index"];
                        getArticleArsimoDetails(selected_slice);
                    }
                }
            }

            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: data,
                options: options
            });

            /*
            TODO: this needs to be fixed!, right now the path is hardcoded and will not be usable when it is deployed.
            */
            function getArticleArsimoDetails(slice_index) {
                var article_id = article_ids_list[slice_index];
                var details_url = window.location.origin + "/dev.technico.local/technix-workwear/workwear_dashboard_details.php?article_id=" + article_id;
                window.location.href = details_url;
            }
        }
        var xmlhttp = new XMLHttpRequest();
        var jsondata = '';
        xmlhttp.open('GET', '?getArticlePercentages', false);
        xmlhttp.onreadystatechange = function() {
            if(this.status == 200 && this.readyState == 4) {
                jsondata = this.responseText;
            }
        }
        xmlhttp.send();
        if(jsondata == '') {
            console.log("ERROR: Data could not be loaded");
        }
        else {
            var oParsedData = JSON.parse(jsondata);
            for(var i in oParsedData) {
                generatePie(oParsedData[i], i);
            }
        }
    </script>
</div>