<div id="headerRightDiv" class="filter" style="margin-left:3px; float:left; width:370px;">
    <table>
        <tr>
            <td colspan="2"><h2>Overzicht kostenplaats instellingen</h2></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="td-label">Prijs incl. huur</td>
            <td class="td-data" style="text-align:right;">€<?=number_format($fTotalRental, 2, ",", ".")?></td>
        </tr>
        <tr>
            <td class="td-label">Prijs beheer</td>
            <td class="td-data" style="text-align:right;">€<?=number_format($fTotalMaintenance, 2, ",", ".")?></td>
        </tr>
        <tr>
            <td class="td-label">Percentage over limiet: </td>
            <td class="td-data" style="text-align:right"><?=number_format($article_percentage, 2, ",", ".")?>%</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
</div>
<?php if($article_percentage >= $checking_percentage) { ?>
<div class="clear">
    <form method="POST">
        <input type="hidden" name="export_orange" value="<?=$article_id?>"/>
        <input type="submit" class="wwmSubmit" value="Steekproef"/>
    </form>
    <form method="POST">
        Controlegrens verhogen met: 
        <input type="number" name="raise_limits" value="0"/>
        <input type="submit" class="wwmSubmit" value="Verhoog controlegrens"/>
        <?=$warn?>
    </form>
</div>
<?php } ?>

<?php if(count($aRed) > 0) { ?>
<div class="clear" style="padding-bottom:8px;">
    <table class="list float" style="width:100%;">
        <thead>
            <tr class="listtitle">
                <th class="muColTitle" colspan="8" style="font-size:12px; padding:10px; background-color:#E50F2D;"><?=$aRed[end(array_keys($aRed))]['article']?></td>
            </tr>
            <tr>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Maat</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal in roulatie</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Uitgevoerde aantal wasbeurten</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">% Boven Drempelwaarde</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal Fact. incl. huur</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal Fact. beheer</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal ongeoorloofd in roulatie</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($aRed as $aArsimo) {
            ?>
            <tr class="listlt">
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['size']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['total']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['total_washcount']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['percentage']?>%</td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aRental[$aArsimo['id']]['garments_amount']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aMaintenance[$aArsimo['id']]['garments_amount']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['amount_over_maxlimit']?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</div>

<div class="clear">
    <form method="POST">
        <input type="hidden" name="export_red" value="<?=$article_id?>"/>
        <input class="wwmSubmit" type="submit" value="Exporteren"/>
    </form>
</div>
<?php } ?>

<?php if(count($aOrange) > 0) { ?>
<div class="clear" style="padding-bottom:8px;">
    <table class="list float" style="width:100%;">
        <thead>
            <tr class="listtitle">
                <th class="muColTitle" colspan="8" style="font-size:12px; padding:10px; background-color:#FFA500;"><?=$aOrange[end(array_keys($aOrange))]['article']?></td>
            </tr>
            <tr>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Maat</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal in maat</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Uitgevoerde wasbeurten</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">% Boven Drempelwaarde</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal Fact. incl. huur</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal Fact. beheer</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal ongeoorloofd in roulatie</th>
            </tr>
        <thead>
        <tbody>
            <?php
            foreach($aOrange as $aArsimo) {
            ?>
            <tr class="listlt">
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['size']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['total']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['total_washcount']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['percentage']?>%</td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aRental[$aArsimo['id']]['garments_amount']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aMaintenance[$aArsimo['id']]['garments_amount']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['amount_over_maxlimit']?></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>
<?php } ?>

<?php if(count($aGreen) > 0) { ?>
<div class="clear">
    <table class="list float" style="width:100%;">
        <thead>
            <tr class="listtitle">
                <th class="muColTitle" colspan="8" style="font-size:12px; padding:10px; background-color:#228B22;"><?=$aGreen[end(array_keys($aGreen))]['article']?></td>
            </tr>
            <tr>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Maat</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal in maat</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Uitgevoerde wasbeurten</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">% Boven Drempelwaarde</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal Fact. incl. huur</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal Fact. beheer</th>
                <th class="listtitle midlistsmall" style="width:10%; padding:5px;">Aantal ongeoorloofd in roulatie</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($aGreen as $aArsimo) {
            ?>
            <tr class="listlt">
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['size']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['total']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['total_washcount']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['percentage']?>%</td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aRental[$aArsimo['id']]['garments_amount']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aMaintenance[$aArsimo['id']]['garments_amount']?></td>
                <td class="midlist" style="width:10%; padding:5px;"><?=$aArsimo['amount_over_maxlimit']?></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>
<?php } ?>