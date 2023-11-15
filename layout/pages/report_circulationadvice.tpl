<form id="circulationadvice" enctype="multipart/form-data" method="GET" action="<?=$pi["filename_details"]?>">
    <div class="filter">
        <table>
           <? if ($circulationgroup_count > 1): ?>
            <tr>
                <td class="name"><?=$lang["location"]?>:</td>
                <td class="value"><?=html_selectbox("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'") ?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["weeks_in_past"]?>:</td>
                <td class="value"><input class="spinner" style="text-align:center" name="w" value="<?=$urlinfo["w"]?>" size="4" /> </td>
            </tr>
            <tr>
                <td class="name"><?=$lang["__circulation_advice_longer_garmentuser"]?>:</td>
                <td class="value"><input class="spinner" style="text-align:center" name="gu_day" value="<?=$urlinfo["gu_day"]?>" size="4" /> <?=strtolower($lang["days"])?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["__circulation_advice_longer_deposited"]?>:</td>
                <td class="value"><input class="spinner" style="text-align:center" name="de_day" value="<?=$urlinfo["de_day"]?>" size="4" /> <?=strtolower($lang["days"])?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["__circulation_advice_longer_container"]?>:</td>
                <td class="value"><input class="spinner" style="text-align:center" name="co_day" value="<?=$urlinfo["co_day"]?>" size="4" /> <?=strtolower($lang["days"])?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["__circulation_advice_longer_laundry"]?>:</td>
                <td class="value"><input class="spinner" style="text-align:center" name="la_day" value="<?=$urlinfo["la_day"]?>" size="4" /> <?=strtolower($lang["days"])?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["__circulation_advice_longer_chaoot"]?>:</td>
                <td class="value"><input class="spinner" style="text-align:center" name="re_day" value="<?=$urlinfo["re_day"]?>" size="4" /> <?=strtolower($lang["days"])?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["last_scanned_from"]?>:</td> 
                <td class="value"><input class="date" onchange="submit()" name="from_date" type="text" value="<?=$urlinfo["from_date"]?>" />
                    <input type="checkbox" name="filter_last_scanned" id="filter_last_scanned" onClick="submit()" <?=$filter_last_scanned?> /> <label for="filter_last_scanned"><?=$lang["filter_last_scanned"]?></label>
                </td>
            </tr>
            <tr>
                <td class="name"><?=$lang["circulationadvice_increase_or_reduce"]?>:</td>
                <td class="value"><input type="text" id="multiply_required" name="multiply_required" value="<?=$urlinfo["multiply_required"]?>" size="4" /></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["show_weeks"]?>:</td>
                <td class="value"><input type="checkbox" class="cols" name="col-weeks" id="col-weeks" onclick="toggleVis(this)" checked="checked"></td>   
            </tr>
            <tr>
                <td class="name"><?=$lang["show_stock"]?>:</td>
                <td class="value"><input type="checkbox" class="cols" name="col-stock" id="col-stock" onclick="toggleVis(this)" ></td>
            </tr>
        </table>
        <div class="buttons">
            <input type="submit" name="export" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
            <input type="submit" name="export_too_much_in_circulation" value="<?=$lang["export_too_much_in_circulation"]?>" title="<?=$lang["export_too_much_in_circulation"]?>" />
            <input type="submit" name="hassubmit" value="<?=$lang["view"]?>" title="<?=$lang["view"]?>" /> <br /><br />
            <input type="submit" name="export_order_stock" value="<?=$lang["export_order_with_stock"]?>" title="<?=$lang["export_order_with_stock"]?>" />
            <input type="submit" name="export_order" value="<?=$lang["export_order_without_stock"]?>" title="<?=$lang["export_order_without_stock"]?>" />
        </div>
    </div>
</form>

<script type="text/javascript">
    var showMode = "table-cell";
    if (document.all) showMode = "block";

    $(function() {
        if(window.localStorage) {
            var cols = document.querySelectorAll(".cols");
            for (var i=0;i<cols.length;i++) {
                if(window.localStorage.getItem(cols[i].name)) {
                    $("#"+cols[i].name).prop("checked", (window.localStorage.getItem(cols[i].name)=="true"?true:false));
                    toggleVis(cols[i]);
                }
            }
        }
    });

    function toggleVis(btn) {
        cells = document.getElementsByName("t"+btn.name);
        mode = btn.checked ? showMode : "none";
        window.localStorage.setItem(btn.name, btn.checked ? "true" : "false");
        for(j = 0; j < cells.length; j++) cells[j].style.display = mode;
    }
</script>
        
<div class="clear" />  

<?php
$rows = "";
$t = 0;
$a = 0;
$r = 0;
$c = 0;
$o = 0;
$m = 0;

foreach ($mupapu["mup"] as $ars => $row) {

$rows .= "<tr class=\"listnc\">";
$rows .= "<td class=\"list\">". $row["articlecode"] ."</td>";
$rows .= "<td class=\"list\">". $row["description"] ."</td>";
$rows .= "<td class=\"list\">". $row["size"] . ((!empty($row["modification"])) ? " " . $row["modification"] : "") ."</td>";

    $all_periods = 0;
    $all_hit     = 0;
    $all_miss    = 0;
    for($i=0; $i<$urlinfo['w']; $i++) {
        $period_max  = 0;
        $period_hit  = 0;
        $period_miss = 0;
        for($p=0; $p < $periods_num; $p++) {
            if($period_max < $row["hitmiss_w". $i .".p". $p]){
                $period_max  = $row["hitmiss_w". $i .".p". $p];
                $period_hit  = $row["hit_w". $i .".p". $p];
                $period_miss = $row["miss_w". $i .".p". $p];
            }
        }
        $all_periods = $all_periods+$period_max;
        $all_hit     = $all_hit+$period_hit;
        $all_miss    = $all_miss+$period_miss;
        $rows .= "<td name=\"tcol-weeks\" id=\"tcol-weeks\" class=\"list\" onmouseover=\"popup('". $lang["distributed"] .": ". $period_hit ."<br />". $lang["misseized"] .": ". $period_miss ."')\" onmouseout=\"kill()\" >". $period_max ."</td>";
    }
    $all_periods_average = ceil($all_periods/$urlinfo['w']);
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
    $req = $req * $urlinfo["multiply_required_auto"] * $urlinfo["multiply_required"];
    $ad = ceil($req)-$row["cir_cur"];
    $sto_ad = $ad + $row["sto_diff"];

    if($ad < 0) {
        $order = 0;
        $out = abs($ad);
        $order_color = null;
        $out_color = " style=\"color:black;\" bgcolor=\"#FFEEBB\"";
    } else {
        $order = $ad;
        $out = 0;
        if($ad > 0) { $order_color = " style=\"color:black;\" bgcolor=\"#FFCCCC\""; }
        else { $order_color = null; }
        $out_color = null;
    }

    if($sto_ad < 0) {
        $sto_order = 0;
        $sto_out = abs($sto_ad);
    } else {
        $sto_order = $sto_ad;
        $sto_out = 0;
    }   
    
    $rows .= "<td class=\"list\" onmouseover=\"popup('". $lang["distributed"] .": ". $all_hit ."<br />". $lang["misseized"] .": ". $all_miss ."')\" onmouseout=\"kill()\">". $all_periods ."</td>";
    $rows .= "<td class=\"list\">". $all_periods_average ."</td>";
    $rows .= "<td class=\"list\">". ceil($req) ."</td>";
    $rows .= "<td class=\"list\">". $row["cir_cur"] ."</td>";      
    $rows .= "<td class=\"list\"". $order_color .">". $order ."</td>";
    $rows .= "<td class=\"list\"". $out_color .">". $out ."</td>";
    $rows .= "<td class=\"list lpointer\" onclick=\"document.location.href='report_beyond_and_in_circulation.php?s[4]=on&aid=". $row[aarticle_id] ."&sid=". $row[asize_id] ."&daysback=". $urlinfo["gu_day"] ."&cid=". $urlinfo["cid"] ."&type=in_circulation&hassubmit=Weergeven'\">". ((isset($gu_garment[$ars]))?$gu_garment[$ars]:"0") ."</td>";
    $rows .= "<td class=\"list lpointer\" onclick=\"document.location.href='report_beyond_and_in_circulation.php?s[5]=on&aid=". $row[aarticle_id] ."&sid=". $row[asize_id] ."&daysback=". $urlinfo["de_day"] ."&cid=". $urlinfo["cid"] ."&type=in_circulation&hassubmit=Weergeven'\">". ((isset($de_garment[$ars]))?$de_garment[$ars]:"0") ."</td>";
    $rows .= "<td class=\"list lpointer\" onclick=\"document.location.href='report_beyond_and_in_circulation.php?s[6]=on&aid=". $row[aarticle_id] ."&sid=". $row[asize_id] ."&daysback=". $urlinfo["co_day"] ."&cid=". $urlinfo["cid"] ."&type=in_circulation&hassubmit=Weergeven'\">". ((isset($co_garment[$ars]))?$co_garment[$ars]:"0") ."</td>";
    $rows .= "<td class=\"list lpointer\" onclick=\"document.location.href='report_beyond_and_in_circulation.php?s[7]=on&aid=". $row[aarticle_id] ."&sid=". $row[asize_id] ."&daysback=". $urlinfo["la_day"] ."&cid=". $urlinfo["cid"] ."&type=in_circulation&hassubmit=Weergeven'\">". ((isset($la_garment[$ars]))?$la_garment[$ars]:"0") ."</td>";
    $rows .= "<td class=\"list lpointer\" onclick=\"document.location.href='report_beyond_and_in_circulation.php?s[3]=on&aid=". $row[aarticle_id] ."&sid=". $row[asize_id] ."&daysback=". $urlinfo["re_day"] ."&cid=". $urlinfo["cid"] ."&type=in_circulation&hassubmit=Weergeven'\">". ((isset($re_garment[$ars]))?$re_garment[$ars]:"0") ."</td>";
    $rows .= "<td name=\"tcol-stock\" id=\"tcol-stock\" class=\"list\">". $row["sto_new"] ."</td>";
    $rows .= "<td name=\"tcol-stock\" id=\"tcol-stock\" class=\"list\">". $row["sto_h_cur"] ."</td>";
    $rows .= "<td name=\"tcol-stock\" id=\"tcol-stock\" class=\"list\">". $row["sto_l_cur"] ."</td>";
    $rows .= "<td name=\"tcol-stock\" id=\"tcol-stock\" class=\"list\">". $row["sto_diff"] ."</td>";
    $rows .= "<td name=\"tcol-stock\" id=\"tcol-stock\" class=\"list\">". $sto_order ."</td>";
    $rows .= "<td name=\"tcol-stock\" id=\"tcol-stock\" class=\"list\">". $sto_out ."</td>";
    $rows .= "</tr>";
    
    $t += $all_periods;
    $a += $all_periods_average;
    $r += ceil($req);
    $c += $row["cir_cur"];
    $o += $order;
    $m += $out;
}
?>

<table class="list float">
    <thead>
        <tr class="listtitle">
            <th class="muColTitle" colspan="3" style="text-align:left;"><?=$lang["article"]?></th>
            <th name="tcol-weeks" id="tcol-weeks" class="muColTitle" colspan="<?=$urlinfo['w']?>"><?=$lang["highest_distribution_period_per_week"]?></th>
            <th class="muColTitle" colspan="6"><?=$lang["circulationadvice"]?></th>
            <th class="muColTitle" colspan="5"><?=$lang["to_long_in_circulation"]?></th>
            <th name="tcol-stock" id="tcol-stock" class="muColTitle" colspan="6"><?=$lang["stock"]?></th>
        </tr>

        <tr class="listtitle">    
            <th class="list" style="text-align:left"><?=$lang["articlenumber"]?></th>
            <th class="list" style="text-align:left"><?=$lang["description"]?></th>
            <th class="list"><?=$lang["size"]?></th>
            <?php
                for($i=0; $i<$urlinfo['w']; $i++) {
                    print "<th name=\"tcol-weeks\" id=\"tcol-weeks\" class=\"list\">". $lang["week"] . ($i+1) ."</th>";
                }
            ?>
            <th class="midlist" onmouseover="popup('<strong><?=$lang["total"]?>: </strong><?=$t?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><?=$lang["total"]?></th>
            <th class="midlist" onmouseover="popup('<strong><?=$lang["average"]?>: </strong><?=$a?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><?=$lang["average"]?></th>
            <th class="midlist" onmouseover="popup('<strong><?=$lang["required"]?>: </strong><?=$r?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><?=$lang["required"]?></th>
            <th class="midlist" onmouseover="popup('<strong><?=$lang["now_circulating"]?>: </strong><?=$c?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><?=$lang["now_circulating"]?></th>
            <th class="midlist" onmouseover="popup('<strong><?=$lang["order"]?>: </strong><?=$o?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><?=$lang["order"]?></th>
            <th class="midlist" onmouseover="popup('<strong><?=$lang["too_much"]?>: </strong><?=$m?> <?=strtolower($lang["garments"])?>')" onmouseout="kill()"><?=$lang["too_much"]?></th>
            <th class="midlist"><?=$lang["__circulation_advice_longer_garmentuser"]?></th>
            <th class="midlist"><?=$lang["__circulation_advice_longer_deposited"]?></th>
            <th class="midlist"><?=$lang["__circulation_advice_longer_container"]?></th>
            <th class="midlist"><?=$lang["__circulation_advice_longer_laundry"]?></th>
            <th class="midlist"><?=$lang["__circulation_advice_longer_chaoot"]?></th>
            <th name="tcol-stock" id="tcol-stock" class="midlist"><?=$lang["required"]?></th>
            <th name="tcol-stock" id="tcol-stock" class="midlist"><?=$lang["stock_hospital"]?></th>
            <th name="tcol-stock" id="tcol-stock" class="midlist"><?=$lang["stock_laundry"]?></th>
            <th name="tcol-stock" id="tcol-stock" class="midlist"><?=$lang["complement"]?></th>
            <th name="tcol-stock" id="tcol-stock" class="midlist"><?=$lang["order"]?></th>
            <th name="tcol-stock" id="tcol-stock" class="midlist"><?=$lang["too_much"]?></th>
        </tr>
    </thead>
    <tbody>      
    
    <?=$rows?>
    </tbody>
</table>

