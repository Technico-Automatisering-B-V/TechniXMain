<?php
    $weeks = "";
    for($i=0; $i<$urlinfo['w']; $i++) {
        $weeks .= "<strong>Week ". ($i+1) .":</strong> " . date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-(($urlinfo['w']-$i)*7),   date("Y"))) .
            " - " . date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-((($urlinfo['w']-$i-1)*7)+1),   date("Y")))."<br>";       
    }
?>

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
                <td class="name"><?=$lang["show_weeks"]?>:</td>
                <td class="value"><input type="checkbox" class="cols" name="col-weeks" id="col-weeks" onclick="toggleVis(this)" checked="checked"></td>
            </tr>
        </table>
        <div class="buttons">
            <input type="submit" name="export" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
            <input type="submit" name="export_order" value="<?=$lang["export_order"]?>" title="<?=$lang["export_order"]?>" />
            <input type="submit" name="hassubmit" value="<?=$lang["view"]?>" title="<?=$lang["view"]?>" />
        </div>
    </div>
</form>

<div class="filter" style="margin-left: 20px;float: left;">
    <table>
        <tbody>
            <tr>
                <td class="top left small">			                                                
                    <?=$weeks?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
        
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

if (isset($pi["note"]) && $pi["note"] != ""){
    echo $pi["note"];
}elseif ($urlinfo["limit_total"] != 0){
    
    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        if ($row['advice'] > 0) $advice_color = " style=\"color:black;\" bgcolor=\"#FFCCCC\"";
        elseif ($row['advice'] < 0) $advice_color = " style=\"color:black;\" bgcolor=\"#FFEEBB\"";
        else $advice_color = null;

    $sto_diff_color = null;
        
        $rows .= "<tr class=\"list\" onClick=\"\">
            <td class=\"list\">". $row["article"] ."</td>
            <td class=\"list\">". $row["size"] ."</td>";
        for($i=0; $i<$urlinfo['w']; $i++) {
            $rows .= "<td name=\"tcol-weeks\" id=\"tcol-weeks\" class=\"list\">". $row["w". ($i+1)] ."</td>";
        }    
        
        $rows .= "<td class=\"list\">". $row["total"] ."</td>
            <td class=\"list\">". $row["average"] ."</td>
            <td class=\"list\">". $row["upset"] ."</td>
            <td class=\"list\">". $row["measured"] ."</td>
            <td class=\"list\" $advice_color >". $row["advice"] ."</td>
            <td class=\"list\">". $row["gu_garment"] ."</td>
            <td class=\"list\">". $row["la_garment"] ."</td>
            <td class=\"list\">". $row["re_garment"] ."</td>
            </tr>";
    } ?>
    
    <table class="list">
        <tr class="listtitle">
            <th class="list"><?=$sortlinks["article"]?></th>
            <th class="list"><?=$sortlinks["size"]?></th>
            <?php
                for($i=0; $i<$urlinfo['w']; $i++) {
                    print "<th name=\"tcol-weeks\" id=\"tcol-weeks\" class=\"list\">". $sortlinks["w". ($i+1)] ."</th>";
                }
            ?>
            <th class="midlist"><?=$sortlinks["total"]?></th>
            <th class="midlist"><?=$sortlinks["average"]?></th>
            <th class="midlist"><?=$sortlinks["upset"]?></th>
            <th class="midlist"><?=$sortlinks["measured"]?></th>
            <th class="midlist"><?=$sortlinks["advice"]?></th>
            <th class="midlist"><?=$sortlinks["gu_garment"]?></th>
            <th class="midlist"><?=$sortlinks["la_garment"]?></th>
            <th class="midlist"><?=$sortlinks["re_garment"]?></th>
        </tr>
        <?=$rows?>
    </table>

<? } ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>
