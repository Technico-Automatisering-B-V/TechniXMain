<?php
if (!empty($pi["note"])){
    echo $pi["note"];
}
?>

<div class="clear" />

<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <div class="filter">
        <table>
            <? if ($circulationgroup_count > 1): ?>
                <tr>
                    <td class="name"><?=$lang["location"]?>:</td>
                    <td class="value"><?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'")?></td>
                </tr>
            <? endif ?>
            <? if ($depositlocation_count > 1): ?>
                <tr>
                    <td class="name"><?=$lang["depositlocation"]?>:</td>
                    <td class="value"><?=html_selectbox_submit("did", $depositlocations, $urlinfo["did"], $lang["(all_locations)"], "style='width:100%'")?></td>
                </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["deposit_of"]?>:</td>
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
                                <td><input type="checkbox" class="cols" name="col-tag" id="col-tag" onclick="toggleVis(this)" checked="checked"><label for="col-tag"><?=$lang["tag"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-deposited_laundry_in" id="col-deposited_laundry_in" onclick="toggleVis(this)" checked="checked"><label for="col-deposited_laundry_in"><?=$lang["deposited"]."->".$lang["laundry_inscan"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-deposited_loaded" id="col-deposited_loaded" onclick="toggleVis(this)" checked="checked"><label for="col-deposited_loaded"><?=$lang["deposited"]."->".$lang["loaded"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-article" id="col-article" onclick="toggleVis(this)" checked="checked"><label for="col-article"><?=$lang["article"]?></label></td>
                                <td><input type="checkbox" class="cols" name="col-laundry_in_laundry_out" id="col-laundry_in_laundry_out" onclick="toggleVis(this)" checked="checked"><label for="col-laundry_in_laundry_out"><?=$lang["laundry_inscan"]."->".$lang["laundry_outscan"]?></label></td>
								<td><input type="checkbox" class="cols" name="col-loaded_distributed" id="col-loaded_distributed" onclick="toggleVis(this)" checked="checked"><label for="col-loaded_distributed"><?=$lang["loaded"]."->".$lang["distributed"]?></label></td>
							</tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-size" id="col-size" onclick="toggleVis(this)" checked="checked"><label for="col-size"><?=$lang["size"]?></label></td>
								<td><input type="checkbox" class="cols" name="col-laundry_out_loaded" id="col-laundry_out_loaded" onclick="toggleVis(this)" checked="checked"><label for="col-laundry_out_loaded"><?=$lang["laundry_outscan"]."->".$lang["loaded"]?></label></td>
								<td><input type="checkbox" class="cols" name="col-depositlocation" id="col-depositlocation" onclick="toggleVis(this)" checked="checked"><label for="col-depositlocation"><?=$lang["depositlocation"]?></label></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="cols" name="col-deposited" id="col-deposited" onclick="toggleVis(this)" checked="checked"><label for="col-deposited"><?=$lang["deposited"]?></label></td>
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

    <? if ($circulationgroup_count <= 1){ print("<input name=\"cid\" type=\"hidden\" value=\"1\" />"); } ?>
    <? if ($depositlocation_count <= 1){ print("<input name=\"did\" type=\"hidden\" value=\"1\" />"); } ?>

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
        echo "<form id=\"g". $row["garment_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garment_details.php\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row['garment_id'] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";
        $rows .= "<tr class=\"listnc\">";
        $rows .= "<td name=\"tcol-tag\" id=\"tcol-tag\"  class=\"list lpointer\" onClick=\"document.getElementById('g". $row["garment_id"] ."').submit();\">". $row["tag"] ."</td>";
        $rows .= "<td name=\"tcol-article\" id=\"tcol-article\"  class=\"list\">". $row["article"] ."</td>";
        $rows .= "<td name=\"tcol-size\" id=\"tcol-size\"  class=\"list\">". $row["size"] ."</td>";
        $rows .= "<td name=\"tcol-deposited\" id=\"tcol-deposited\"  class=\"list\">". $row["deposited"] ."</td>";
        $rows .= "<td name=\"tcol-deposited_laundry_in\" id=\"tcol-deposited_laundry_in\"  class=\"list\">". $row["deposited_laundry_in"] ."</td>";
        $rows .= "<td name=\"tcol-laundry_in_laundry_out\" id=\"tcol-laundry_in_laundry_out\"  class=\"list\">". $row["laundry_in_laundry_out"] ."</td>";
        $rows .= "<td name=\"tcol-laundry_out_loaded\" id=\"tcol-laundry_out_loaded\"  class=\"list\">". $row["laundry_out_loaded"] ."</td>";
        $rows .= "<td name=\"tcol-deposited_loaded\" id=\"tcol-deposited_loaded\"  class=\"list\">". $row["deposited_loaded"] ."</td>";
        $rows .= "<td name=\"tcol-loaded_distributed\" id=\"tcol-loaded_distributed\"  class=\"list\">". $row["loaded_distributed"] ."</td>";
        $rows .= "<td name=\"tcol-depositlocation\" id=\"tcol-depositlocation\"  class=\"list\">". $row["depositlocation"] ."</td>";
        $rows .= "</tr>";
    }
    ?>
    
    <table class="list">
        
            <tr class="listtitle">
                <th name="tcol-tag" id="tcol-tag"  class="list"><?=$sortlinks["tag"]?></th>
                <th name="tcol-article" id="tcol-article"  class="list"><?=$sortlinks["name"]?></th>
                <th name="tcol-size" id="tcol-size"  class="list"><?=$sortlinks["size"]?></th>
                <th name="tcol-deposited" id="tcol-deposited"  class="list"><?=$sortlinks["deposited"]?></th>
                <th name="tcol-deposited_laundry_in" id="tcol-deposited_laundry_in"  class="list"><?=$sortlinks["deposited_laundry_in"]?></th>
                <th name="tcol-laundry_in_laundry_out" id="tcol-laundry_in_laundry_out"  class="list"><?=$sortlinks["laundry_in_laundry_out"]?></th>
                <th name="tcol-laundry_out_loaded" id="tcol-laundry_out_loaded"  class="list"><?=$sortlinks["laundry_out_loaded"]?></th>
                <th name="tcol-deposited_loaded" id="tcol-deposited_loaded"  class="list"><?=$sortlinks["deposited_loaded"]?></th>
                <th name="tcol-loaded_distributed" id="tcol-loaded_distributed"  class="list"><?=$sortlinks["loaded_distributed"]?></th>
                <th name="tcol-depositlocation" id="tcol-depositlocation"  class="list"><?=$sortlinks["depositlocation"]?></th>
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