<script>
function toggle() {
    filter_value = document.getElementById("period_filter_select").value;
    date_field = document.getElementById("dateSelector");
    number_field = document.getElementById("numberSelector");

    if (filter_value === "maanden") {
        if(date_field.style.display = "table-row") {
            date_field.style.display = "none";
        }
        document.getElementById("period_filter_select").value = filter_value;
        number_field.style.display = "table-row";  
    
    } else if (filter_value === "weken") {
        if(date_field.style.display = "table-row") {
            date_field.style.display = "none";
        }
        document.getElementById("period_filter_select").value = filter_value;
        number_field.style.display = "table-row";
    
    } else if (filter_value === "date_now") {
        if(number_field.style.display === "table-row") {
            number_field.style.display = "none";
        }
        document.getElementById("period_filter_select").value = filter_value;
        date_field.style.display = "table-row";
    
    } else {
        number_field.style.display = "none";
        date_field.style.display = "none";
    }
}
</script>

<div class="filter">
    <form>
    <table>
        <tbody>
            <tr>
                <td class="name"><?=$lang["article"]?>:</td>
                <td class="value"><select name="artikel_id" style="width:300px" onchange="submit();">
                <option value=""><?=$lang["(all_articles)"]?></option>
                <?php
                    foreach($PricesPerArticle as $aInfo) {
                    ?>
                    <option value="<?=$aInfo['id']?>" <?php echo ((isset($urlinfo['artikel_id']) && $urlinfo['artikel_id'] == $aInfo['id']) ? 'selected' : null); ?>><?=$aInfo['description']?></option>
                    <?php
                    }
                ?>
            </tr>
            <tr>
                <td class="name"><?=$lang["interval_types"]?>:</td>
                <td class="value">
                    <select name="periode" id="period_filter_select" style="width:300px" onchange="toggle();">
                        <option value="default"><?=$lang["set_periode"]?></option>
                        <option value="maanden"><?=$lang["months"]?></option>
                        <option value="weken"><?=$lang["weeks"]?></option>
                        <option value="date_now"><?=$lang["today"]?></option>
                    </select>
                </td>
            </tr>
            <tr style="display:none;" id="numberSelector">
                <td class="name"><?=$lang["count"]?></td>
                <td class="value">
                    <input type="number" name="numberSelector" style="width:285px; border:1px SOLID #CCC; border-radius: 4px; padding: 0.4em 0.5em"></input>
                </td>
            </tr>
            <tr style="display:none;" id="dateSelector">
                <td class="name"><?=$lang["to_date"]?>:</td>
                <td class="value">
                    <input type=date name="dateSelector" style="width:285px; border:1px SOLID #CCC; border-radius: 4px; padding: 0.4em 0.5em"></input>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="buttons">
        <input type="submit" name="hassubmit" value="<?=$lang["rendering"]?>" title="<?=$lang["show_value_for_set_periode_help_message"]?>" class="ui-button ui-widget ui-state-default ui-corner-all" role="button" aria-disabled="false">
        <input type="submit" name="hassubmit" value="<?=$lang["export"]?>" title="<?=$lang["export_value_report_help_message"]?>" class="ui-button ui-widget ui-state-default ui-corner-all" role="button" aria-disabled="false">
    </div>
    </form>
</div>

<div id="headerRightDiv" class="filter" style="margin-left:3px; float:right;">
    <h2><?=$lang["overview_garments_value"]?></h2>
    <table>
        <tbody>
            <tr>
                <td><h3><?=$lang["overview_garments_value"]?></h3></td>
            </tr>
            <tr>
                <td><?=$lang["value_when_aquired"]?>:</td>
                <td></td>
                <td style="text-align: right;"><strong>&euro;<?=number_format($TotalOriginalValue, 2, ",", ".")?></strong></td>
            </tr>
            <tr>
                <td><?=$lang["current_value"]?>:</td>
                <td></td>
                <td style="text-align: right;"><strong>&euro;<?=number_format($TotalCurrentValue, 2, ",", ".")?></strong></td>
            </tr>
            <tr>
                <td><?=$lang["value_loss"]?>:</td>
                <td></td>
                <td style="text-align: right;"><strong>&euro;<?=number_format($TotalDepreciation, 2, ",", ".")?></strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><h3><?=$lang["effect_of_price_increase"]?></h3></td>
            </tr>
            <tr>
                <td><?=$lang["price_after_increase"]?>:</td>
                <td></td>
                <td style="text-align: right;"><strong>&euro;00,00</strong></td>
            </tr>
            <tr>
                <td><?=$lang["difference_with_previous_price"]?></td>
                <td></td>
                <td style="text-align: right;"><strong>&euro;00,00</strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><h3><?=$lang["make_up_of_inventory"]?></h3></td>
            </tr>
            <tr>
                <td><?=$lang["in_use"]?>: <span style="text-align: right;"><strong><?=$GarmentsInRoulation?></strong></span></td>
                <td><?=$lang["value"]?>:</td>
                <td style="text-align: left;"><strong>&euro;<?=number_format($TotalCurrentValueInRoulation, 2, ",", ".")?></strong></td>
            </tr>
            <tr>
                <td><?=$lang["in_storage"]?>: <span style="text-align: right;"><strong><?=$GarmentsInStock?></strong></span></td>
                <td><?=$lang["value"]?>:</td>
                <td style="text-align: left;"><strong>&euro;<?=number_format($TotalCurrentValueInStock, 2, ",", ".")?></strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <?php
                if(isset($urlinfo['periode']) && !empty($urlinfo['periode'])) {
                    if(isset($urlinfo['numberSelector']) && !empty($urlinfo['numberSelector'])) {
            ?>
            <tr>
                <td><h3><?=$lang["investment_amount"]?></h3></td>
            </tr>
            <tr>
                <td><?=$lang["investment_over"]?> <?=$urlinfo['numberSelector']?> <?=$urlinfo['periode']?></td>
                <td>&nbsp;</td>
                <td style="text-align: left;"><strong>&euro;<?=number_format($IntervalInvestment, 2, ",", ".")?></strong></td>
            </tr>
            <?php
                    }
                }
            ?>
            <?php
                if(isset($urlinfo['periode']) && !empty($urlinfo['periode'])) {
                    if(isset($urlinfo['dateSelector']) && !empty($urlinfo['dateSelector'])) {
            ?>
            <tr>
                <td><h3><?=$lang["investment"]?></h3></td>
            </tr>
            <tr>
                <td><?=$lang["investment_until"]?> <?=$urlinfo['dateSelector']?></td>
                <td>&nbsp;</td>
                <td style="text-align: left;"><strong>&euro;<?=number_format($InvestmentDate, 2, ",", ".")?></strong></td>
            </tr>
            <?php
                    }
                }
            ?>
        </tbody>
    </table>
</div>


<div class="clear">
    <?php
        foreach($PriceInfo as $article_id=>$aInfo) {
    ?>
    <table class="list float" style="width:100%;">
        <thead>
            <tr class="listtitle">
                <th class="muColTitle" colspan="10" style="font-size:12px; padding:10px;"><?=$aInfo['description']?></th>
            </tr>     
            <tr>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=$lang["size"]?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=$lang["total_in_use"]?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=$lang["current_value"]?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=$lang["total_worn_out"]?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=$lang["replenishment_costs"]?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=$lang["in_storage"]?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=$lang["value_in_storage"]?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $ArticleReplacementCost = 0;
                foreach($aInfo['arsimos'] as $Arsimo) {
            ?>
                <tr class="listlt">
                    <td class="midlist" style="width:10%; padding:5px;"><?=$Arsimo['name']?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><?=$Arsimo['garmentsperarsimo']?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><font color="<?php echo (($Arsimo['currentarsimovalue'] <= 0) ? 'RED' : null); ?>">&euro;<?=number_format($Arsimo['currentarsimovalue'], 2, ",", ".")?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><?=((isset($ArsimoReplacementInfo[$article_id]['arsimos'][$Arsimo['id']]['amount'])) ? $ArsimoReplacementInfo[$article_id]['arsimos'][$Arsimo['id']]['amount'] : 0)?></td>
                    <td class="midlist" style="width:10%; padding:5px;">
                    <?php
                        if(array_key_exists($article_id, $ArsimoReplacementInfo) && array_key_exists($Arsimo['id'], $ArsimoReplacementInfo[$article_id]['arsimos'])) {
                            $ArticleReplacementCost += $ArsimoReplacementInfo[$article_id]['arsimos'][$Arsimo['id']]['replacement_cost'];
                            echo "&euro;". number_format($ArsimoReplacementInfo[$article_id]['arsimos'][$Arsimo['id']]['replacement_cost'], 2, ",", ".");
                        } else {
                            echo "&euro;". number_format(0, 2, ",", ".");
                        }
                    ?>
                    </td>
                    <td class="midlist" style="width:10%; padding:5px;"><?=((array_key_exists($Arsimo['id'], $StockPriceInfo[$article_id]['arsimos'])) ? $StockPriceInfo[$article_id]['arsimos'][$Arsimo['id']]['garmentsperarsimo'] : '0')?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><?=((array_key_exists($Arsimo['id'], $StockPriceInfo[$article_id]['arsimos'])) ? "&euro;". number_format($StockPriceInfo[$article_id]['arsimos'][$Arsimo['id']]['currentarsimovalue'], 2, ",", ".") : '&euro;0,00')?></td>
                </tr>
            <?php
                }
            ?>
            <tr class="article-totals">
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=$lang["total"]?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=$aInfo['garmentsperarticle']?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><font color="<?php echo (($aInfo['currentarticlevalue'] <= 0) ? 'RED;' : null); ?>">&euro;<?=number_format($aInfo['currentarticlevalue'], 2, ",", ".")?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">&euro;<?=number_format($ArticleReplacementCost, "2", ",", ".")?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=((array_key_exists($article_id, $StockPriceInfo)) ? $StockPriceInfo[$article_id]['garmentsperarticle'] : '0')?></th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;"><?=((array_key_exists($article_id, $StockPriceInfo)) ? "&euro;". number_format($StockPriceInfo[$article_id]['currentarticlevalue'], 2, ",", ".") : '&euro;0,00')?></th>
            </tr>
        </tbody>
    </table>
<?php
    }
?>
</div>