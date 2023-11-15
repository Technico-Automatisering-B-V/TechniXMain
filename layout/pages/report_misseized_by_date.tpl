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
            <tr>
                <td class="name"><?=$lang["article"]?>:</td>
                <td class="value"><?=html_selectbox_submit("aid", $articles, $urlinfo["aid"], $lang["(all_articles)"], "style='width:100%'")?></td>
            </tr>
            <?if(!empty($sizes)):?>
            <tr>
                <td class="name"><?=$lang["size"]?>:</td>
                <td class="value"><?=html_selectbox_array("sid", $sizes, $urlinfo["sid"], $lang["(all_sizes)"], true, false, "style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["distribution_of"]?>:</td>
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

</form>

<div class="clear" />

<? if (!isset($pi["note"])){
    print($resultinfo);
    if (isset($urlinfo["limit_total"]) && $urlinfo["limit_total"] != 0){ ?>

        <table class="list">
                <tr class="listtitle">
                    <th class="list"><?=$sortlinks["date"]?></th>
                    <th class="list"><?=$sortlinks["article"]?></th>
                    <th class="list"><?=$sortlinks["size"]?></th>
                    <th class="list"><?=$sortlinks["type"]?></th>
                    <th class="list"><?=$sortlinks["count"]?></th>
                </tr>
                <? while ($row = db_fetch_assoc($listdata)){ ?>
                <tr class="list" onclick="document.getElementById('<?=$row["arsimo_id"]?><?=$row['date']?>').submit();">
                    <form id="<?=$row['arsimo_id']?><?=$row['date']?>" enctype="multipart/form-data" method="POST" action="report_misseized_garments.php">
                        <input type="hidden" name="arsimo_id" value="<?=$row['arsimo_id']?>">
                        <input type="hidden" name="date" value="<?=$row['date']?>">
                        <input type="hidden" name="gosubmit" value="true">
                    </form>
                    <td class="list"><?=$row["date"]?></td>
                    <td class="list"><?=$row["article"]?></td>
                    <td class="midlist"><?=$row["size"] . ((!empty($row["modification"])) ? " " . $row["modification"] : "") ?></td>
                    <td class="midlist"><?=($row["userbound"] ? $lang["userbound"] : $lang["sizebound"]) ?></td>
                    <td class="midlist"><?=$row["count"]?></td>
                </tr>
                <? } ?>
            
        </table>

        <?=$pagination?>

    <? } ?>

<? } ?>