<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER['PHP_SELF']?>">
    <div class="filter">
        <table>
            <tr>
                <td class="name"><?=$lang["type"]?>:</td>
                <td class="value">
                    <?=html_selectbox_array_submit("errormessages_type", $types, $urlinfo['errormessages_type'], $lang["make_a_choice"], $selected=null)?>
                    <input type="checkbox" name="showall" id="showall" onClick="submit()" <?=$showall?> /> <label for="showall"><?=$lang["show_all"]?></label>
                </td>    
            </tr>
            <?php if ($distributorlocation_count > 0): ?>
            <tr>
                <td class="name"><?=$lang["location"]?>:</td>
                <td class="value"><?=html_selectbox_submit("dlid", $distributorlocations, $urlinfo["dlid"], $lang["(all_locations)"], "style='width:100%'")?></td>
            </tr>
                <?if(!empty($distributors)):?>
                <tr>
                    <td class="name"><?=$lang["distributor"]?>:</td>
                    <td class="value"><?=html_selectbox_submit("did", $distributors, $urlinfo["did"], $lang["(all_stations)"], "style='width:100%'")?></td>
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
        </table>
        <div class="buttons">
            <input type="submit" name="hassubmit" value="<?=$lang["view"]?>" title="<?=$lang["view"]?>" />
            <input type="submit" name="hassubmit" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
        </div>
    </div>
</form>

<div class="clear" />

<?php
if (!isset($pi["note"])){ print($resultinfo); }

if (isset($pi["note"]) && $pi["note"] != ""){
    echo $pi["note"];
}elseif ($urlinfo["limit_total"] != 0){

    $rows = "";

    while ($row = db_fetch_assoc($listdata)){

        echo "<form id=\"" . $row["errormessages_id"] ."\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\">
                <input type=\"hidden\" name=\"page\" value=\"details\">
                <input type=\"hidden\" name=\"id\" value=\"" . $row["errormessages_id"] ."\">
                <input type=\"hidden\" name=\"gosubmit\" value=\"true\"></form>";

        $rows .= "<tr class=\"list\" onClick=\"document.getElementById('". $row["errormessages_id"] ."').submit();\">";
        $rows .= "<td class=\"list\">";
        if($row["errormessages_type"]!='station' && $row["errormessages_type"]!='load') {
            $rows .= $lang[$row["errormessages_type"]];
        } else {
            $rows .= $lang[$row["errormessages_type"].'_storing'];
        }
        $rows .= "</td>";
        $rows .= "<td class=\"list\">". $row["errormessages_message"] ."</td>";
        $rows .= "<td class=\"list\">". $row["distributorlocations_name"] ."</td>";
        $rows .= "<td class=\"list\">". $row["distributors_doornumber"] ."</td>";
        $rows .= "<td class=\"list\">". $row["errormessages_date"] ."</td>";
        $rows .= "</tr>";
    }
    ?>

    <table class="list">
        <tr class="listtitle">
            <th class="list"><?=$sortlinks["type"]?></th>
            <th class="list"><?=$sortlinks["error"]?></th>
            <th class="list"><?=$sortlinks["distributorlocation"]?></th>
            <th class="list"><?=$sortlinks["distributor"]?></th>
            <th class="list"><?=$sortlinks["date"]?></th>
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