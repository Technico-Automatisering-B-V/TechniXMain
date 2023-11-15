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
    <input name="custom" type="hidden" value="1" />
    <input name="dsearch" id="dsearch" type="hidden" value="<?=$urlinfo["dsearch"]?>" />
    <div class="filter">
        <table>
            <tr>
                <td class="name"><?=$lang["status"]?>:</td>
                <td class="value">
                    <input id="s[1]" name="s[1]" type="checkbox" <?=(isset($_SESSION["garmentusers"]["show"][1]))?" checked=\"checked\"":""?> onClick="submit()"><label for="s[1]"><?=$lang["access"]?></label>&nbsp;&nbsp;|&nbsp;
                    <input id="s[2]" name="s[2]" type="checkbox" <?=(isset($_SESSION["garmentusers"]["show"][2]))?" checked=\"checked\"":""?> onClick="submit()"><label for="s[2]"><?=$lang["no_access"]?></label>&nbsp;&nbsp;|&nbsp;
                    <input id="s[3]" name="s[3]" type="checkbox" <?=(isset($_SESSION["garmentusers"]["show"][3]))?" checked=\"checked\"":""?> onClick="submit()"><label for="s[3]"><?=$lang["deleted"]?></label>
                </td>
            </tr>
            <?php if ($circulationgroup_count > 1): ?>
            <tr>
                <td class="name"><?=$lang["location"]?>:</td>
                <td class="value"><?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <? if ($clientdepartments_count > 0): ?>
            <tr>
                <td class="name"><?=$lang["clientdepartment"]?>:</td>
                <td class="value"><?=html_selectbox_submit("clientdepartment_id", $clientdepartments, $urlinfo["clientdepartment_id"], $lang["(all_clientdepartments)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <? if ($costplaces_count > 0): ?>
            <tr>
                <td class="name"><?=$lang["costplace"]?>:</td>
                <td class="value"><?=html_selectbox_submit("costplace_id", $costplaces, $urlinfo["costplace_id"], $lang["(all_costplaces)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <? if ($functions_count > 0): ?>
            <tr>
                <td class="name"><?=$lang["function"]?>:</td>
                <td class="value"><?=html_selectbox_submit("function_id", $functions, $urlinfo["function_id"], $lang["(all_functions)"], "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["profession"]?>:</td>
                <td class="value"><?=html_selectbox_submit("pid", $professions, $urlinfo["pid"], $lang["(all_professions)"], "style='width:100%'")?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["in_possession"]?>:</td>
                <td class="value"><?=html_selectbox_array_submit("ip", $yes_no, $urlinfo["ip"], $lang["make_a_choice"], true, false, "style='width:100%'")?></td>
            </tr>
            <tr>
                <td class="name top" width="50"><?=$lang["rendering"]?>:</td>
                <td class="value" style="white-space: nowrap">
                        <fieldset style="border-style: hidden;margin: 0px;padding: 0px;">
                        <input type="checkbox" id="showcols" class="cols" name="showcols" onclick="toggleVis(this)" checked="checked" style="margin-left: 9px;">
                        <table class="columns" id="columns" name="tshowcols">
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-surname" id="col-surname" onclick="toggleVis(this)" checked="checked"><label for="col-surname"><?=$lang["surname"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-name" id="col-name" onclick="toggleVis(this)" checked="checked"><label for="col-name"><?=$lang["first_name"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-personnelcode" id="col-personnelcode" onclick="toggleVis(this)" checked="checked"><label for="col-personnelcode"><?=$lang["personnelcode"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-code" id="col-code" onclick="toggleVis(this)" checked="checked"><label for="col-code"><?=$lang["passcode"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-code2" id="col-code2" onclick="toggleVis(this)" checked="checked"><label for="col-code2"><?=$lang["passcode"]?> 2</label></td>
                                <td><input type="checkbox" class="cols" name="col-clientdepartment" id="col-clientdepartment" onclick="toggleVis(this)" checked="checked"><label for="col-clientdepartment"><?=$lang["clientdepartment"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-profession" id="col-profession" onclick="toggleVis(this)" checked="checked"><label for="col-profession"><?=$lang["profession"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-access" id="col-access" onclick="toggleVis(this)" checked="checked"><label for="col-access"><?=$lang["access"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-clothing" id="col-clothing" onclick="toggleVis(this)" checked="checked"><label for="col-clothing"><?=$lang["clothing"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-garments_in_use" id="col-garments_in_use" onclick="toggleVis(this)" checked="checked"><label for="col-garments_in_use"><?=$lang["in_possession"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-lockernumber" id="col-lockernumber" onclick="toggleVis(this)" checked="checked"><label for="col-lockernumber"><?=$lang["lockernumber"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-costplace" id="col-costplace" onclick="toggleVis(this)" checked="checked"><label for="col-costplace"><?=$lang["costplace"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-function" id="col-function" onclick="toggleVis(this)" checked="checked"><label for="col-function"><?=$lang["function"]?></label></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
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
if (!isset($pi["note"])){ print($resultinfo); }

if (isset($pi["note"]) && $pi["note"] != ""){
    echo $pi["note"];
}elseif ($urlinfo["limit_total"] != 0){

    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        echo "<form id=\"". $row["id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"currentTab\" value=\"0\"><input type=\"hidden\" name=\"id\" value=\"". $row["id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"false\"></form>";

        $rows .= "<tr class=\"". ((!empty($row["deleted_on"])) ? "listgrey": (($row["active"]!=1) ? "listspec":"list")) ."\" onClick=\"document.getElementById('". $row["id"] ."').submit();\">
            <td name=\"tcol-surname\" id=\"tcol-surname\" class=\"list\">". generate_garmentuser_label($row["title"], $row["gender"], $row["initials"], $row["intermediate"], $row["surname"], $row["maidenname"], $row["personnelcode"]) ."</td>
            <td name=\"tcol-name\" id=\"tcol-name\" class=\"list\">". ((empty($row["name"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["name"] ) ."</td>
            <td name=\"tcol-personnelcode\" id=\"tcol-personnelcode\" class=\"list\">". ((empty($row["personnelcode"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["personnelcode"]) ."</td>
            <td name=\"tcol-code\" id=\"tcol-code\" class=\"list\">". ((empty($row["code"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["code"]) ."</td>
            <td name=\"tcol-code2\" id=\"tcol-code2\" class=\"list\">". ((empty($row["code2"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["code2"]) ."</td>
            <td name=\"tcol-clientdepartment\" id=\"tcol-clientdepartment\" class=\"list\">". ((empty($row["clientdepartment_name"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["clientdepartment_name"]) ."</td>
            <td name=\"tcol-profession\" id=\"tcol-profession\" class=\"list\">". ((empty($row["profession_name"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["profession_name"]) ."</td>
            <td name=\"tcol-access\" id=\"tcol-access\" class=\"midlist\">". (($row["active"] == 1) ? $lang["yes"] : $lang["no"]) ."</td>
            <td name=\"tcol-clothing\" id=\"tcol-clothing\" class=\"midlist\">". ((!empty($row["distributor_id"])) ? $lang["self"] : $lang["size"]) ."</td>
            <td name=\"tcol-garments_in_use\" id=\"tcol-garments_in_use\" class=\"midlist\">". (($row["garments_in_use"] == 0) ? "<span class=\"empty\">-</span>" : $row["garments_in_use"]) ."</td>
            <td name=\"tcol-lockernumber\" id=\"tcol-lockernumber\" class=\"list\">". ((empty($row["lockernumber"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["lockernumber"]) ."</td>
            <td name=\"tcol-costplace\" id=\"tcol-costplace\" class=\"list\">". ((empty($row["costplace_value"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["costplace_value"]) ."</td>
            <td name=\"tcol-function\" id=\"tcol-function\" class=\"list\">". ((empty($row["function_value"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["function_value"]) ."</td>
            </tr>";
    } ?>

    <table class="list">
        <tr class="listtitle">
            <th name="tcol-surname" id="tcol-surname" class="list"><?=$sortlinks["surname"]?></th>
            <th name="tcol-name" id="tcol-name" class="list"><?=$sortlinks["name"]?></th>
            <th name="tcol-personnelcode" id="tcol-personnelcode" class="list"><?=$sortlinks["personnelcode"]?></th>
            <th name="tcol-code" id="tcol-code" class="list"><?=$sortlinks["code"]?></th>
            <th name="tcol-code2" id="tcol-code2" class="list"><?=$sortlinks["code2"]?></th>
            <th name="tcol-clientdepartment" id="tcol-clientdepartment" class="list"><?=$sortlinks["clientdepartment"]?></th>
            <th name="tcol-profession" id="tcol-profession" class="list"><?=$sortlinks["profession"]?></th>
            <th name="tcol-access" id="tcol-access" class="midlist"><?=$sortlinks["access"]?></th>
            <th name="tcol-clothing" id="tcol-clothing" class="midlist"><?=$sortlinks["clothing"]?></th>
            <th name="tcol-garments_in_use" id="tcol-garments_in_use" class="midlist"><?=$sortlinks["garments_in_use"]?></th>
            <th name="tcol-lockernumber" id="tcol-lockernumber" class="midlist"><?=$sortlinks["lockernumber"]?></th>
            <th name="tcol-costplace" id="tcol-costplace" class="midlist"><?=$sortlinks["costplace"]?></th>
            <th name="tcol-function" id="tcol-function" class="midlist"><?=$sortlinks["function"]?></th>
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
