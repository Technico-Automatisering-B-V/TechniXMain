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
        <table style="white-space: nowrap;">
            <tr>
                <td class="name"><?=$lang["function_changed"]?>:</td>
                <td class="value"><?=html_selectbox_array_submit("function_changed", $yes_no, $urlinfo["function_changed"], $lang["make_a_choice"], true, false, "style='width:100%'")?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["clientdepartment_changed"]?>:</td>
                <td class="value"><?=html_selectbox_array_submit("clientdepartment_changed", $yes_no, $urlinfo["clientdepartment_changed"], $lang["make_a_choice"], true, false, "style='width:100%'")?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["service_on"]?>:</td>
                <td class="value"><?=html_selectbox_array_submit("date_service_on_today", $yes_no, $urlinfo["date_service_on_today"], $lang["make_a_choice"], true, false, "style='width:100%'")?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["service_off"]?>:</td>
                <td class="value"><?=html_selectbox_array_submit("date_service_off_today", $yes_no, $urlinfo["date_service_off_today"], $lang["make_a_choice"], true, false, "style='width:100%'")?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["date"]?>:</td>
                <td class="value">
                    <input class="date" name="from_date" id="date1" type="text" value="<?=$urlinfo["from_date"]?>" />
                    <? if (!empty($lotsadays)): ?> t/m <input class="date" name="to_date" id="date2" type="text" value="<?=$urlinfo["to_date"]?>" /><? endif ?>
                    <input type="checkbox" name="lotsadays" id="lotsadays" onClick="submit()" <?=$lotsadays?> /> <label for="lotsadays"><?=$lang["multiple_dates"]?></label>
                </td>
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
                                <td><input type="checkbox" class="cols" name="col-date_service_on" id="col-date_service_on" onclick="toggleVis(this)" checked="checked"><label for="col-date_service_on"><?=$lang["service_on"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-date_service_off" id="col-date_service_off" onclick="toggleVis(this)" checked="checked"><label for="col-date_service_off"><?=$lang["service_off"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-old_function" id="col-old_function" onclick="toggleVis(this)" checked="checked"><label for="col-old_function"><?=$lang["old_function"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-function" id="col-function" onclick="toggleVis(this)" checked="checked"><label for="col-function"><?=$lang["new_function"]?></label></td>
                               </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-old_clientdepartment" id="col-old_clientdepartment" onclick="toggleVis(this)" checked="checked"><label for="col-old_clientdepartment"><?=$lang["old_clientdepartment"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-clientdepartment" id="col-clientdepartment" onclick="toggleVis(this)" checked="checked"><label for="col-clientdepartment"><?=$lang["new_clientdepartment"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-profession" id="col-profession" onclick="toggleVis(this)" checked="checked"><label for="col-profession"><?=$lang["profession"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-garments_in_use" id="col-garments_in_use" onclick="toggleVis(this)" checked="checked"><label for="col-garments_in_use"><?=$lang["in_possession"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-clothing" id="col-clothing" onclick="toggleVis(this)" checked="checked"><label for="col-clothing"><?=$lang["clothing"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-date" id="col-date" onclick="toggleVis(this)" checked="checked"><label for="col-date"><?=$lang["date"]?></label></td>
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
        echo "<form id=\"". $row["garmentuser_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"currentTab\" value=\"0\"><input type=\"hidden\" name=\"id\" value=\"". $row["garmentuser_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"false\"></form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["garmentuser_id"] ."').submit();\">
            <td name=\"tcol-surname\" id=\"tcol-surname\" class=\"list\">". ((empty($row["garmentusers_surname"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_surname"] ) ."</td>
            <td name=\"tcol-name\" id=\"tcol-name\" class=\"list\">". ((empty($row["garmentusers_name"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_name"] ) ."</td>
            <td name=\"tcol-personnelcode\" id=\"tcol-personnelcode\" class=\"list\">". ((empty($row["garmentusers_personnelcode"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_personnelcode"]) ."</td>
            <td name=\"tcol-code\" id=\"tcol-code\" class=\"list\">". ((empty($row["garmentusers_code"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_code"]) ."</td>
            <td name=\"tcol-date_service_on\" id=\"tcol-date_service_on\" class=\"list\">". ((empty($row["garmentusers_date_service_on"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_date_service_on"]) ."</td>
            <td name=\"tcol-date_service_off\" id=\"tcol-date_service_off\" class=\"list\">". ((empty($row["garmentusers_date_service_off"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_date_service_off"]) ."</td>
            <td name=\"tcol-old_function\" id=\"tcol-old_function\" class=\"list\">". ((empty($row["old_function"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["old_function"]) ."</td>
            <td name=\"tcol-function\" id=\"tcol-function\" class=\"list\">". ((empty($row["function"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["function"]) ."</td>
            <td name=\"tcol-old_clientdepartment\" id=\"tcol-old_clientdepartment\" class=\"list\">". ((empty($row["old_clientdepartment"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["old_clientdepartment"]) ."</td>
            <td name=\"tcol-clientdepartment\" id=\"tcol-clientdepartment\" class=\"list\">". ((empty($row["clientdepartment"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["clientdepartment"]) ."</td>
            <td name=\"tcol-profession\" id=\"tcol-profession\" class=\"list\">". ((empty($row["profession"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["profession"]) ."</td>
            <td name=\"tcol-garments_in_use\" id=\"tcol-garments_in_use\" class=\"midlist\">". (($row["garments_in_use"] == 0) ? "<span class=\"empty\">-</span>" : $row["garments_in_use"]) ."</td>
            <td name=\"tcol-clothing\" id=\"tcol-clothing\" class=\"midlist\">". ($row["clothing"]==='yes' ? $lang["self"] : $lang["size"]) ."</td> 
            <td name=\"tcol-date\" id=\"tcol-date\" class=\"list\">". ((empty($row["date"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["date"]) ."</td>
            </tr>";
    } ?>

    <table class="list">
        <tr class="listtitle">
            <th name="tcol-surname" id="tcol-surname" class="list"><?=$sortlinks["surname"]?></th>
            <th name="tcol-name" id="tcol-name" class="list"><?=$sortlinks["name"]?></th>
            <th name="tcol-personnelcode" id="tcol-personnelcode" class="list"><?=$sortlinks["personnelcode"]?></th>
            <th name="tcol-code" id="tcol-code" class="list"><?=$sortlinks["code"]?></th>
            <th name="tcol-date_service_on" id="tcol-date_service_on" class="list"><?=$sortlinks["date_service_on"]?></th>
            <th name="tcol-date_service_off" id="tcol-date_service_off" class="list"><?=$sortlinks["date_service_off"]?></th>
            <th name="tcol-old_function" id="tcol-old_function" class="midlist"><?=$sortlinks["old_function"]?></th>
            <th name="tcol-function" id="tcol-function" class="midlist"><?=$sortlinks["function"]?></th>
            <th name="tcol-old_clientdepartment" id="tcol-old_clientdepartment" class="list"><?=$sortlinks["old_clientdepartment"]?></th>
            <th name="tcol-clientdepartment" id="tcol-clientdepartment" class="list"><?=$sortlinks["clientdepartment"]?></th>
            <th name="tcol-profession" id="tcol-profession" class="list"><?=$sortlinks["profession"]?></th>
            <th name="tcol-garments_in_use" id="tcol-garments_in_use" class="midlist"><?=$sortlinks["garments_in_use"]?></th>
            <th name="tcol-clothing" id="tcol-clothing" class="midlist"><?=$sortlinks["clothing"]?></th>
            <th name="tcol-date" id="tcol-date" class="midlist"><?=$sortlinks["date"]?></th>
        </tr>
        <?=$rows?>
    </table>
    
    <?=$pagination?>



<? } ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
    $('#date1').change(function(){
        $('#showform').submit();
      });
	$('#date2').change(function(){
        $('#showform').submit();
      });
</script>
