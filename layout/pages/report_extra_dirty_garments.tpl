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
        </table>
        <div class="buttons">
            <input type="submit" name="hassubmit" value="<?=$lang["view"]?>" title="<?=$lang["view"]?>" />
            <input type="submit" name="hassubmit" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
        </div>
    </div>

    <? if ($circulationgroup_count <= 1){ print("<input name=\"cid\" type=\"hidden\" value=\"1\" />"); } ?>
    <? if ($depositlocation_count <= 1){ print("<input name=\"did\" type=\"hidden\" value=\"1\" />"); } ?>

</form>

<div class="clear" />

<?php
if (!isset($pi["note"])){ print($resultinfo); }

if (isset($pi["note"]) && $pi["note"] != ""){
    echo $pi["note"];
}elseif ($urlinfo["limit_total"] != 0){

    $rows = "";

    while ($row = db_fetch_assoc($listdata)){
        echo "<form id=\"g". $row["extra_dirty_garments_garment_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garment_details.php\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $row['extra_dirty_garments_garment_id'] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";
        if(isset($garmentusers[$row["garments_tag"]])) {
            echo "<form id=\"gu". $garmentusers[$row["garments_tag"]] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"garmentuser_details.php\"><input type=\"hidden\" name=\"page\" value=\"details\"><input type=\"hidden\" name=\"id\" value=\"". $garmentusers[$row["garments_tag"]] ."\"><input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";
        }
        $rows .= "<tr class=\"listnc\">";
        $rows .= "<td class=\"list\">". $row["extra_dirty_garments_date"] ."</td>";
        $rows .= "<td class=\"list\">". $row["articles_description"] ."</td>";
        $rows .= "<td class=\"list\">". $row["sizes_name"] ."</td>";
        $rows .= "<td class=\"list lpointer\" onClick=\"document.getElementById('g". $row["extra_dirty_garments_garment_id"] ."').submit();\">". $row["garments_tag"] ."</td>";
        
        if(isset($garmentusers[$row["garments_tag"]])) {
            $rows .= "<td class=\"list lpointer\" onClick=\"document.getElementById('gu". $garmentusers[$row["garments_tag"]] ."').submit();\">". $history[$row["garments_tag"]] . "</td>";
        } else {
            $rows .= "<td class=\"list\">". $history[$row["garments_tag"]] . "</td>";
        }
        $rows .= "<td class=\"list\">". $row["depositlocations_name"] ."</td>";
        $rows .= "</tr>";
    }
    ?>

    <table class="list">
        
            <tr class="listtitle">
                <th class="list"><?=$sortlinks["date"]?></th>
                <th class="list"><?=$sortlinks["name"]?></th>
                <th class="list"><?=$sortlinks["size"]?></th>
                <th class="list"><?=$sortlinks["tag"]?></th>
                <th class="list"><?=$sortlinks["last_used_by"]?></th>
                <th class="list"><?=$sortlinks["depositlocation"]?></th>
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