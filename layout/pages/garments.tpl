<script type="text/javascript">
$(document).ready(function(){
    $("form[name='searchform']").submit(function(){
        $("#dsearch").val($("#search").val());
        $("#showform").submit();
        return false;
    });
    
});
</script>

<form name="showform" id="showform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <input name="dsearch" id="dsearch" type="hidden" value="<?=$urlinfo["dsearch"]?>" />
    <div class="filter">
        <table>
            <tr>
                <td class="name" width="50"><?=$lang["status"]?>:</td>
                <td class="value" width="200"><input id="del" name="del" type="checkbox" value="on" <?=(!empty($urlinfo["del"])) ? " checked=\"checked\"" : ""?> onClick="submit()"><label for="del"><?=$lang["deleted"]?></label></td>
            </tr>
            <? if ($circulationgroup_count > 1): ?>
            <tr>
                <td class="name"><?=$lang["location"]?>:</td>
                <td class="value"><?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["article"]?>:</td>
                <td class="value"><?=html_selectbox_submit("aid", $articles, $urlinfo["aid"], $lang["(all_articles)"], "style='width:100%'")?></td>
            </tr>
            <?if(!empty($sizes)):?>
            <tr>
                <td class="name"><?=$lang["size"]?>:</td>
                <td class="value"><?=html_selectbox_array_submit("sid", $sizes, $urlinfo["sid"], $lang["(all_sizes)"], true, false, "style='width:100%'")?></td>
            </tr>
                <?if(!empty($modifications)):?>
                <tr>
                    <td class="name"><?=$lang["modification"]?>:</td>
                    <td class="value"><?=html_selectbox_array_submit("mid", $modifications, $urlinfo["mid"], $lang["(all_modifications)"], true, false, "style='width:100%'")?></td>
                </tr>
                <? endif ?>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["status"]?>:</td>
                <td class="value"><?=html_selectbox_translate_submit("scid", $statuses, $urlinfo["scid"], $lang["(all_statuses)"], "style='width:100%'")?></td>
            </tr>
            <tr>
                <td class="name top" width="50"><?=$lang["rendering"]?>:</td>
                <td class="value" style="white-space: nowrap;min-width: 350px;">
                    <fieldset style="border-style: hidden;margin: 0px;padding: 0px;">
                        <input type="checkbox" id="showcols" class="cols" name="showcols" onclick="toggleVis(this)" checked="checked" style="margin-left: 9px;">
                        <table class="columns" id="columns" name="tshowcols">
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-tag" id="col-tag" onclick="toggleVis(this)" checked="checked"><label for="col-tag"><?=$lang["tag"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-tag2" id="col-tag2" onclick="toggleVis(this)" checked="checked"><label for="col-tag2"><?=$lang["tag"]?> 2</label></td>
                                <td><input type="checkbox" class="cols" name="col-article" id="col-article" onclick="toggleVis(this)" checked="checked"><label for="col-article"><?=$lang["article"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-size" id="col-size" onclick="toggleVis(this)" checked="checked"><label for="col-size"><?=$lang["size"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-modification" id="col-modification" onclick="toggleVis(this)" checked="checked"><label for="col-modification"><?=$lang["modification"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-washcount" id="col-washcount" onclick="toggleVis(this)" checked="checked"><label for="col-washcount"><?=$lang["washed"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-owner" id="col-owner" onclick="toggleVis(this)" checked="checked"><label for="col-owner"><?=$lang["owner"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-lastscan" id="col-lastscan" onclick="toggleVis(this)" checked="checked"><label for="col-lastscan"><?=$lang["last_scanned"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-status" id="col-status" onclick="toggleVis(this)" checked="checked"><label for="col-status"><?=$lang["status"]?></label></td>
                            </tr>      
                            <?if(!empty($urlinfo["del"])):?>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-deleted" id="col-deleted" onclick="toggleVis(this)" checked="checked"><label for="col-deleted"><?=$lang["deleted"]?></label></td> 
                            </tr>
                            <? endif ?>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>

        <div class="buttons">
            <input type="submit" name="hassubmit" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
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
if (!isset($pi["note"])){print($resultinfo);}
?>

<?php
if (isset($pi["note"]) && $pi["note"] != ""){
    echo $pi["note"];
}elseif ($urlinfo["limit_total"] != 0){

    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        echo "<form id=\"". $row["garments_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["garments_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

        $rows .= "<tr class=\"". (!empty($row["garments_deleted_on"]) ? "listgrey" : "list") ."\" onClick=\"document.getElementById('". $row["garments_id"] ."').submit();\">
            <td name=\"tcol-tag\" id=\"tcol-tag\" class=\"list\">". $row["garments_tag"] ."</td>
            <td name=\"tcol-tag2\" id=\"tcol-tag2\" class=\"list\">";
            if(!empty($row["garments_tag2"])) {
                $rows .= $row["garments_tag2"];
            } else {
                $rows .= "<span class=\"empty\">". $lang["none"] ."</span>";
            }
            $rows .= "</td>
            <td name=\"tcol-article\" id=\"tcol-article\" class=\"list\">". ucfirst($row["articles_description"]) ."</td>
            <td name=\"tcol-size\" id=\"tcol-size\" class=\"midlist\">". $row["sizes_name"] ."</td>
            <td name=\"tcol-modification\" id=\"tcol-modification\" class=\"midlist\">";

        if ($row["modifications_name"]) { $rows .= $row["modifications_name"]; }else{ $rows .= "<span class=\"empty\">". $lang["none"] ."</span>"; }
        $rows .= "</td>";
        $rows .= "<td name=\"tcol-washcount\" id=\"tcol-washcount\" class=\"midlist\">". (($row["garments_washcount"] == 0) ? "<span class=\"empty\">-</span>" : $row["garments_washcount"] ."x" ) ."</td>";
        $rows .= "<td name=\"tcol-owner\" id=\"tcol-owner\" class=\"list\">";

        if (!empty($row["garmentusers_surname"])) {
            $rows .= generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]);
        } else {
            $rows .= "<span class=\"empty\">" . $lang["garment_by_size"] . "</span>";
        }

        $rows .= "</td><td name=\"tcol-lastscan\" id=\"tcol-lastscan\" class=\"list\">";
        if(!empty($row["garments_lastscan"]))
        {
            $rows .= $row["garments_lastscan"];
        }else{
            $rows .= "<span class=\"empty\">". $lang["never_scanned"] ."</span>";
        }
        
        $rows .= "</td><td name=\"tcol-status\" id=\"tcol-status\" class=\"list\">";
        if(!empty($row["scanlocations_translate"]))
        {
            if(!empty($row["sub_scanlocations_translate"]))
            {
                $rows .= $lang[$row["sub_scanlocations_translate"]];
            }else{
                if($row["scanlocations_scanlocationstatus_id"] == 9) {
                    $rows .= $lang[$row["scanlocations_translate"]] . " - " . $row["scanlocations_description"];
                }else{
                    $rows .= $lang[$row["scanlocations_translate"]];
                }
            }
            
        }else{
            $rows .= "<span class=\"empty\">". $lang["none"] ."</span>";
        }
        
        if(!empty($urlinfo["del"])) {
            $rows .= "</td><td name=\"tcol-deleted\" id=\"tcol-deleted\" class=\"list\">";
            if(!empty($row["garments_deleted_on"]))
            {
                $rows .= $row["garments_deleted_on"];
            }else{
                $rows .= "<span class=\"empty\">". $lang["none"] ."</span>";
            }
        }
        
        $rows .= "</td></tr>";

    } ?>

    <table class="list">
        <tr class="listtitle">
            <td name="tcol-tag" id="tcol-tag" class="list"><?=$sortlinks["tag"]?></td>
            <td name="tcol-tag2" id="tcol-tag2" class="list"><?=$sortlinks["tag2"]?></td>
            <td name="tcol-article" id="tcol-article" class="list"><?=$sortlinks["description"]?></td>
            <td name="tcol-size" id="tcol-size" class="midlist"><?=$sortlinks["size"]?></td>
            <td name="tcol-modification" id="tcol-modification" class="midlist"><?=$sortlinks["modification"]?></td>
            <td name="tcol-washcount" id="tcol-washcount" class="midlist"><?=$sortlinks["washcount"]?></td>
            <td name="tcol-owner" id="tcol-owner" class="list"><?=$sortlinks["owner"]?></td>
            <td name="tcol-lastscan" id="tcol-lastscan" class="list"><?=$sortlinks["lastscan"]?></td>
            <td name="tcol-status" id="tcol-status" class="list"><?=$sortlinks["status"]?></td>
            <?if(!empty($urlinfo["del"])):?>
                <td name="tcol-deleted" id="tcol-deleted" class="list"><?=$sortlinks["deleted"]?></td>
            <? endif ?>
        </tr>
        <?=$rows?>
    </table>

    <?=$pagination?>

<? } ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>