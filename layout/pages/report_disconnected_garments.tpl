<form name="showform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <input name="custom" type="hidden" value="1" />
    <div class="filter">
        <table>
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
                <td class="name"><?=$lang["date"]?>:</td>
                <td class="value">
                    <input class="date" name="from_date" type="text" value="<?=$urlinfo["from_date"]?>" />
                    <? if (!empty($lotsadays)): ?> t/m <input class="date" name="to_date" type="text" value="<?=$urlinfo["to_date"]?>" /><? endif ?>
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
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-clientdepartment" id="col-clientdepartment" onclick="toggleVis(this)" checked="checked"><label for="col-clientdepartment"><?=$lang["clientdepartment"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-profession" id="col-profession" onclick="toggleVis(this)" checked="checked"><label for="col-profession"><?=$lang["profession"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-lockernumber" id="col-lockernumber" onclick="toggleVis(this)" checked="checked"><label for="col-lockernumber"><?=$lang["lockernumber"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-costplace" id="col-costplace" onclick="toggleVis(this)" checked="checked"><label for="col-costplace"><?=$lang["costplace"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-tag" id="col-tag" onclick="toggleVis(this)" checked="checked"><label for="col-tag"><?=$lang["tag"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-article" id="col-article" onclick="toggleVis(this)" checked="checked"><label for="col-article"><?=$lang["article"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-size" id="col-size" onclick="toggleVis(this)" checked="checked"><label for="col-size"><?=$lang["size"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-modification" id="col-modification" onclick="toggleVis(this)" checked="checked"><label for="col-modification"><?=$lang["modification"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-comments" id="col-comments" onclick="toggleVis(this)" checked="checked"><label for="col-comments"><?=$lang["comments"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-date" id="col-date" onclick="toggleVis(this)" checked="checked"><label for="col-date"><?=$lang["date"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-status" id="col-status" onclick="toggleVis(this)" checked="checked"><label for="col-status"><?=$lang["status"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-next_status" id="col-next_status" onclick="toggleVis(this)" checked="checked"><label for="col-next_status"><?=$lang["next_status"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-function" id="col-function" onclick="toggleVis(this)" checked="checked"><label for="col-function"><?=$lang["function"]?></label></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>
        <div class="buttons">
            <input type="submit" name="hassubmit" value="<?=$lang["view"]?>" title="<?=$lang["view"]?>" />
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

<? if (!isset($pi["note"])){
    print($resultinfo);
    if (isset($urlinfo["limit_total"]) && $urlinfo["limit_total"] != 0){ ?>

<?php

$rows = "";

while ($row = db_fetch_assoc($listdata)){
      echo "<form id=\"gu". $row["garmentusers_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"". $pi["filename_details"] ."\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row["garmentusers_id"] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";
      echo "<form id=\"g". $row["garments_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garment_details.php\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row['garments_id'] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";
          $rows .= "<tr class=\"listnc\">
            <td name=\"tcol-surname\" id=\"tcol-surname\" class=\"list lpointer\" onClick=\"document.getElementById('gu". $row["garmentusers_id"] ."').submit();\">". generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"], $row["garmentusers_personnelcode"]) ."</td>
            <td name=\"tcol-name\" id=\"tcol-name\" class=\"list lpointer\" onClick=\"document.getElementById('gu". $row["garmentusers_id"] ."').submit();\">". ((empty($row["garmentusers_name"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_name"] ) ."</td>
            <td name=\"tcol-personnelcode\" id=\"tcol-personnelcode\" class=\"list lpointer\" onClick=\"document.getElementById('gu". $row["garmentusers_id"] ."').submit();\">". ((empty($row["garmentusers_personnelcode"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_personnelcode"]) ."</td>
            <td name=\"tcol-clientdepartment\" id=\"tcol-clientdepartment\" class=\"list\">". ((empty($row["clientdepartments_name"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["clientdepartments_name"]) ."</td>
            <td name=\"tcol-profession\" id=\"tcol-profession\" class=\"list\">". ((empty($row["professions_name"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["professions_name"]) ."</td>
            <td name=\"tcol-lockernumber\" id=\"tcol-lockernumber\" class=\"list\">". ((empty($row["garmentusers_lockernumber"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_lockernumber"]) ."</td>
            <td name=\"tcol-costplace\" id=\"tcol-costplace\" class=\"list\">". ((empty($row["costplaces_value"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["costplaces_value"]) ."</td>  
            <td name=\"tcol-function\" id=\"tcol-function\" class=\"list\">". ((empty($row["functions_value"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["functions_value"]) ."</td>  
            <td name=\"tcol-tag\" id=\"tcol-tag\" class=\"midlist lpointer\" onClick=\"document.getElementById('g". $row["garments_id"] ."').submit();\">". $row["garments_tag"] ."</td>
            <td name=\"tcol-article\" id=\"tcol-article\" class=\"list\">". $row["articles_description"] ."</td>
            <td name=\"tcol-size\" id=\"tcol-size\" class=\"midlist\">". $row["sizes_name"] ."</td>
            <td name=\"tcol-modification\" id=\"tcol-modification\" class=\"midlist\">";
                if ($row["modifications_name"]) { $rows .= $row["modifications_name"]; }else{ $rows .= "<span class=\"empty\">". $lang["none"] ."</span>"; }
            $rows .= "</td>
            <td name=\"tcol-comments\" id=\"tcol-comments\" class=\"list\">";
                if ($row["log_disconnected_garments_comments"]) { $rows .= $row["log_disconnected_garments_comments"]; }else{ $rows .= "<span class=\"empty\">". $lang["none"] ."</span>"; }
            $rows .= "</td>
            <td name=\"tcol-date\" id=\"tcol-date\" class=\"midlist\">". $row["log_disconnected_garments_date"] ."</td>
            <td name=\"tcol-status\" id=\"tcol-status\" class=\"midlist\">";
                if(!empty($row["tmp_log_disc_id"])){ $rows .= $lang["active"]; }else{ $rows .= $lang["inactive"]; }
            $rows .= "</td>
            <td name=\"tcol-next_status\" id=\"tcol-next_status\" class=\"midlist\">";
                if(!empty($row["tmp_next_disc_next_status"])){ $rows .= $lang[$row["tmp_next_disc_next_status"]]; }else{ $rows .= $lang["none"]; }
            $rows .= "</td>
        </tr>";
} ?>

<table class="list">
    <thead>
        <tr class="listtitle">
            <th name="tcol-surname" id="tcol-surname" class="list"><?=$sortlinks["surname"]?></th>
            <th name="tcol-name" id="tcol-name" class="list"><?=$sortlinks["name"]?></th>
            <th name="tcol-personnelcode" id="tcol-personnelcode" class="list"><?=$sortlinks["personnelcode"]?></th>
            <th name="tcol-clientdepartment" id="tcol-clientdepartment" class="list"><?=$sortlinks["clientdepartment"]?></th>
            <th name="tcol-profession" id="tcol-profession" class="list"><?=$sortlinks["profession"]?></th>
            <th name="tcol-lockernumber" id="tcol-lockernumber" class="midlist"><?=$sortlinks["lockernumber"]?></th>
            <th name="tcol-costplace" id="tcol-costplace" class="midlist"><?=$sortlinks["costplace"]?></th>
            <th name="tcol-function" id="tcol-function" class="midlist"><?=$sortlinks["function"]?></th>
            <th name="tcol-tag" id="tcol-tag" class="list"><?=$sortlinks["tag"]?></th>
            <th name="tcol-article" id="tcol-article" class="list"><?=$sortlinks["article"]?></th>
            <th name="tcol-size" id="tcol-size" class="list"><?=$sortlinks["size"]?></th>
            <th name="tcol-modification" id="tcol-modification" class="list"><?=$sortlinks["modification"]?></th>
            <th name="tcol-comments" id="tcol-comments" class="list"><?=$sortlinks["comments"]?></th>
            <th name="tcol-date" id="tcol-date" class="list"><?=$sortlinks["date"]?></th>
            <th name="tcol-status" id="tcol-status" class="list"><?=$sortlinks["status"]?></th>
            <th name="tcol-next_status" id="tcol-next_status" class="list"><?=$sortlinks["next_status"]?></th>
        </tr>
    </thead>
    <tbody>
        <?=$rows?>
    </tbody>
</table>

<?=$pagination?>

    <? } ?>

<? } ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>