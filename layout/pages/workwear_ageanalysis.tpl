<div class="filter">
    <form>
    <table>
        <tbody>
            <tr>
                <td class="name"><?=$lang["article"]?>:</td>
                <td class="value"><select name="article_id" style="width:300px" onchange="submit();">
                <option value="0"><?=$lang["(all_articles)"]?></option>
                <?php 
                    foreach($articleNames as $article) {
                ?>
                        <option value="<?=$article['id']?>" <?php echo ((isset($selectedArticle) && $selectedArticle == $article['id']) ? 'selected' : null); ?>><?=$article['article_name']?></option>
                <?php
                    }
                ?>
            </tr>
        </tbody>
    </table>
    <div class="buttons">
        <input type="submit" name="hassubmit" value="Weergeven" title="Weergeven" class="ui-button ui-widget ui-state-default ui-corner-all" role="button" aria-disabled="false">
    </div>
    </form>
</div>

<div id="headerRightDiv" class="filter" style="margin-left:3px; float:right;">
    <table>
        <tbody>
            <tr>
                <td><h2><?=$lang["package_overview"]?></h2></td>
            </tr>
            <tr>
                <td><h3><?=$lang["general_info_washes"]?></h3></td>
            </tr>
            <tr>
                <td><?=$lang["total_garments_in_circulation"]?>:</td>
                <td></td>
                <td style="text-align: right;"><strong><?=$aTotals[0]["garments"]?></strong></td>
            </tr>
            <tr>
                <td><?=$lang["higest_max_washcount"]?>:</td>
                <td></td>
                <td style="text-align: right;"><strong><?=$aTotals[0]["highest_max"]?></strong></td>
            </tr>
            <tr>
                <td><?=$lang["lowest_max_washcount"]?>:</td>
                <td></td>
                <td style="text-align: right;"><strong><?=$aTotals[0]["lowest_max"]?></strong></td>
            </tr>
            <tr>
                <td>Gem. wasbeurten</td>
                <td></td>
                <td style="text-align: right;"><strong><?=number_format($aTotals[0]["average_washcount"], "2", ",", ".")?></strong></td>
            </tr>
            <tr>
                <td>Gem. wasbeurten per week</td>
                <td></td>
                <td style="text-align: right;"><strong><?=number_format($aTotals[0]["average_weekly_washcount"], "2", ",", ".")?></strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><h3>Gem. Leeftijd volledig pakket</h3></td>
            </tr>
            <tr>
                <td>Verwacht max. weken:</td>
                <td></td>
                <td style="text-align: right;"><strong><?=number_format($aTotals[0]["weeksremaining"], "2", ",", ".")?></strong></td>
            </tr>
            <tr>
                <td>Gem. leeftijd pakket in weken:</td>
                <td></td>
                <td style="text-align: right;"><strong><?= $overviewAgeGarments["average_age_all_garments"]?></strong></td>
            </tr>
            <tr>
                <td>Gem. resterende weken:</td>
                <td></td>
                <td style="text-align: right;"><strong><?= $overviewAgeGarments["average_remaining_weeks"]?></strong></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="clear">
    <?php
        foreach($aAverageWeeksPerArticle as $aAverageWeeks) {
    ?>
    <table class="list float" style="width:100%;">
        <thead>
            <tr class="listtitle">
                <th class="muColTitle" colspan="10" style="font-size:12px; padding:10px;"><?=$aAverageWeeks["article"]?> | Gem. Resterende weken: <?=number_format($aAverageWeeks["average_weeksremaining"], 2, ",", ".")?></th>
            </tr>
            <tr>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Maat</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal garments (ARSIMO)</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Gem. aantal wasbeurten</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Gem. wasbeurten per week</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Gem. resterende weken</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Datum alles uit roulatie</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal boven max. wasbeurten</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($aAveragesPerArsimo[$aAverageWeeks['article_id']] as $aArsimos) {
            ?>
                <tr class="listlt">
                    <?php
                        $sAverageWeeksRemaining = $aArsimos["average_weeksremaining"];
                        $bIsExtended = false;
                        if(round($aArsimos['extended_weeksremaining'], 2) > round($sAverageWeeksRemaining, 2)) {
                            $sAverageWeeksRemaining = $aArsimos['extended_weeksremaining'];
                            $bIsExtended = true;
                        }
                    ?>
                    <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimos["size"]?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimos["garments"]?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><font><?=number_format($aArsimos["average_washcount"], 2, ',', '.')?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><font><?=number_format($aAverageWashesPerArsimo[$aArsimos['id']]['average_weekly_washes'], 2, ',', '.')?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><font color="<?php echo (($bIsExtended) ? 'ORANGE' : 'BLACK'); ?>"><?=number_format($sAverageWeeksRemaining, 2, ",", ".")?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><?=date('Y-m-d', strtotime($aArsimos["full_replacement_date"]))?></td>
                    <td class="midlist" style="width:10%; padding:5px;"><font color="<?php echo (($aArsimos['garments_over_max'] > 0) ? 'RED' : 'BLACK'); ?>"><?=($aArsimos["garments_over_max"] > 0 ? $aArsimos["garments_over_max"] : 0)?></td>
                </tr>
        <?php
            }
        ?>
        </tbody>
    </table>
<?php
}
?>
</div>
