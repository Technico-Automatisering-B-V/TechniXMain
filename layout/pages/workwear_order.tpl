<div id="headerLeftDiv" class="filter">
    <?php 
        if($bShowInterval) { 
    ?>
        <table>
            <thead>
                <tr>
                    <th>
                        <h3><?=$lang["order_for_next_interval"]?> <?=$sPrev?> <?=$lang["to"]?> <?=$sNext?></h3>
                    </th>
                </tr>    
            </thead>
            <tbody>
                <tr>
                    <td><?=$lang["order_for"]?>:</td>
                    <?php
                        if($bIntervalOrderIsLate) {
                    ?>
                        <td style="text-align: right;"><strong style="color: RED; text-decoration: line-through;"><?=$interval_order_date?></strong> <strong><?=date('Y-m-d')?></strong></td>
                    <?php
                        } else {
                    ?>
                        <td style="text-align: right;"><strong><?=$interval_order_date?></strong></td>
                    <?php
                        }
                    ?>
                </tr>
                <tr>
                    <td><?=$lang["expected_delivery_date"]?>:</td>
                    <?php
                        if($bIntervalOrderIsLate) {
                    ?>
                        <td style="text-align: right;"><strong style="color: RED; text-decoration: line-through;"><?=$interval_delivery_date?></strong> <strong><?=$new_interval_delivery_date?></strong></td>
                    <?php
                        } else {
                    ?>
                        <td style="text-align: right;"><strong><?=$interval_delivery_date?></strong></td>
                    <?php
                        }
                    ?>
                </tr>
            </tbody>
        </table>
</div>

<div class="clear">
    <table class="list" style="width:100%;">
        <tbody>
            <tr class="listtitle">
                <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["article"]?></th>
                <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["size"]?></th>
                <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["wornout_count"]?></th>
                <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["price_per_unit"]?></th>
                <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["in_storage"]?></th>
                <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["order_count"]?></th>
                <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["billing_amount"]?></th>
            </tr>
            <?php
                $totalgarments = 0;
                $totalfullprice = 0;
                $totalinvestment = 0;
                $totalactualinvestment = 0;
                $totalstock = 0;
                foreach($aIntervalData as $OrderRow) {
                    $totalgarments += $OrderRow['garmentsperarsimo'];
                    $totalfullprice += $OrderRow['fullprice'];
                    $totalinvestment += $OrderRow['total'];
                    $totalactualinvestment += $OrderRow['actual_total'];
                    $totalstock += $OrderRow['arsimos_in_stock'];
            ?>
            <tr class="list">
                <td class="list" style="width:10%; padding:5px;"><?=$OrderRow['description']?></td>
                <td class="list" style="width:10%; padding:5px;"><?=$OrderRow['name']?></td>
                <td class="list" style="width:10%; padding:5px;"><?=$OrderRow['garmentsperarsimo']?></td>
                <td class="list" style="width:10%; padding:5px;">&euro;<?=$OrderRow['fullprice']?></td>
                <td class="list" style="width:10%; padding:5px;"><?=$OrderRow['arsimos_in_stock']?></td>
                <td class="list" style="width:10%; padding:5px;"><?=$OrderRow['garmentsperarsimo'] - $OrderRow['arsimos_in_stock']?></td>
                <td class="list" style="width:10%; padding:5px;">&euro;<?=number_format($OrderRow['actual_total'], 2, ",", ".")?></td>
            </tr>
            <?php
                }
            ?>
            <tr class="listtitle">
                <th class="list" style="width:10%; padding:5px;"><?=$lang["total"]?>:</th>
                <th class="list" style="width:10%; padding:5px;">-</th>
                <th class="list" style="width:10%; padding:5px;"><?=$totalgarments?></th>
                <th class="list" style="width:10%; padding:5px;">-</th>
                <th class="list" style="width:10%; padding:5px;"><?=$totalstock?></th>
                <th class="list" style="width:10%; padding:5px;"><?=$totalgarments - $totalstock?></th>
                <th class="list" style="width:10%; padding:5px;">&euro;<?=number_format($totalactualinvestment, 2, ",", ".")?></th>
            </tr>
        </tbody>
    </table>
</div>
<?php 
    }
?>

<div class="clear">
<h3><?=$lang["generate_orderlist_from_current_date"]?></h3>
    <div id="headerLeftDiv" class="filter">
        <form>
            <table>
                <tbody>
                    <tr id="dateSelector">
                        <td class="name"><?=$lang["select_a_date"]?>:</td>
                        <td class="value">
                            <input type=date name="dateSelector" style="width:285px; border:1px SOLID #CCC; border-radius: 4px; padding: 0.4em 0.5em"></input>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="buttons">
                <input type="submit" name="hassubmit" value="Weergeven" title="Weergeven" class="ui-button ui-widget ui-state-default ui-corner-all" role="button" aria-disabled="false">
            </div>
        </form>
    </div>
</div>


<?php
    if(isset($urlinfo['dateSelector']) && !empty($urlinfo['dateSelector'])) {
?>
    <div class="clear">
        <table class="list" style="width:100%;">
            <tbody>
                    <tr class="listtitle">
                    <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["article"]?></th>
                    <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["size"]?></th>
                    <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["count"]?></th>
                    <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["price_per_unit"]?></th>
                    <th class="list" style="width:10%; padding:5px; color:#1C5A39;"><?=$lang["total_price"]?></th>
                </tr>
        
                <?php
                    $totalgarments = 0;
                    $totalfullprice = 0;
                    $totalinvestment = 0;
                    foreach($OrderData as $OrderRow) {
                        $totalgarments += $OrderRow['garmentsperarsimo'];
                        $totalfullprice += $OrderRow['fullprice'];
                        $totalinvestment += $OrderRow['total'];
                ?>
                    <tr class="list">
                        <td class="list" style="width:10%; padding:5px;"><?=$OrderRow['description']?></td>
                        <td class="list" style="width:10%; padding:5px;"><?=$OrderRow['name']?></td>
                        <td class="list" style="width:10%; padding:5px;"><?=$OrderRow['garmentsperarsimo']?></td>
                        <td class="list" style="width:10%; padding:5px;">&euro;<?=$OrderRow['fullprice']?></td>
                        <td class="list" style="width:10%; padding:5px;">&euro;<?=number_format($OrderRow['total'], 2, ",", ".")?></td>
                    </tr>
                <?php
                    }
                ?>
            
                <tr class="listtitle">
                    <th class="list" style="width:10%; padding:5px;><?=$lang["total"]?>:</th>
                    <th class="list" style="width:10%; padding:5px;></th>
                    <th class="list" style="width:10%; padding:5px;><?=$totalgarments?></th>
                    <th class="list" style="width:10%; padding:5px;><!--&euro;<?=$totalfullprice?>--></th>
                    <th class="list" style="width:10%; padding:5px;>&euro;<?=number_format($totalinvestment, 2, ",", ".")?></th>
                </tr>
            </tbody>
        </table>
    </div>
<?php
    }
?>
