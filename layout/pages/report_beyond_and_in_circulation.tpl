<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <div class="filter">
        <table>
            <? if ($circulationgroup_count > 1): ?>
            <tr>
                <td class="name"><?=$lang["location"]?>:</td>
                <td class="value"><?=html_selectbox("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'") ?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["article"]?>:</td>
                <td class="value"><?=html_selectbox_submit("aid", $articles, $urlinfo["aid"], $lang["(all_articles)"], "style='width:100%'")?></td>
            </tr>
            <? if (!empty($sizes)): ?>
            <tr>
                <td class="name"><?=$lang["size"]?>:</td>
                <td class="value"><?=html_selectbox("sid", $sizes, $urlinfo["sid"], $lang["(all_sizes)"], " style='width:100%'")?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["longer_then"]?>:</td>
                <td class="value"><input class="spinner" style="text-align:center" name="daysback" value="<?=$urlinfo["daysback"]?>" size="4" /> <?=strtolower($lang["days"])?></td>
            </tr>
            <tr>
                <td class="name"><?=$lang["type"]?>:</td>
                <td class="value">
                    <?=html_selectbox_array_submit("type", $types, $urlinfo['type'], $lang["(all)"], "style='width:100%'")?>
                </td>    
            </tr>
            <? if (isset($urlinfo['type']) && $urlinfo['type'] == 'in_circulation'): ?>
            <tr>
                <td class="top right"><?=$lang["status"]?>:</td>
                <td class="value" style="white-space: nowrap" rowspan="2">
                    <table>
                        <tr>
                            <td><input id="s[1]" name="s[1]" type="checkbox" <?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"][1]))?" checked=\"checked\"":""?> /><label for="s[1]"><?=$lang["conveyor"]?></label></td>
                            <td><input id="s[2]" name="s[2]" type="checkbox" <?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"][2]))?" checked=\"checked\"":""?> /><label for="s[2]"><?=$lang["loaded"]?></label></td>
                            <td><input id="s[3]" name="s[3]" type="checkbox" <?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"][3]))?" checked=\"checked\"":""?> /><label for="s[3]"><?=$lang["rejected"]?></label></td>
                        </tr>
                        <tr>
                            <td><input id="s[4]" name="s[4]" type="checkbox" <?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"][4]))?" checked=\"checked\"":""?> /><label for="s[4]"><?=$lang["distributed"]?></label></td>
                            <td><input id="s[5]" name="s[5]" type="checkbox" <?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"][5]))?" checked=\"checked\"":""?> /><label for="s[5]"><?=$lang["deposited"]?></label></td>
                            <td><input id="s[6]" name="s[6]" type="checkbox" <?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"][6]))?" checked=\"checked\"":""?> /><label for="s[6]"><?=$lang["container"]?></label></td>
                        </tr>
                        <tr>
                            <td><input id="s[7]" name="s[7]" type="checkbox" <?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"][7]))?" checked=\"checked\"":""?> /><label for="s[7]"><?=$lang["transport_to_laundry"]?></label></td>
                            <td><input id="s[8]" name="s[8]" type="checkbox" <?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"][8]))?" checked=\"checked\"":""?> /><label for="s[8]"><?=$lang["laundry"]?></label></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <? endif ?>
            <? if (isset($urlinfo['type']) && $urlinfo['type'] == 'beyond_circulation'): ?>
            <tr>
                <td class="top right"><?=$lang["status"]?>:</td>
                <td class="value" style="white-space: nowrap">
                    <table>
                        <tr>
                            <td><input id="s[1]" name="s[1]" type="checkbox"<?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"]["1"])) ? " checked=\"checked\"" : ""?> /><label for="s[1]"><?=$lang["missing"]?></label></td>
                            <td><input id="s[2]" name="s[2]" type="checkbox"<?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"]["2"])) ? " checked=\"checked\"" : ""?> /><label for="s[2]"><?=$lang["stock_hospital"]?></label></td>
                            <td><input id="s[3]" name="s[3]" type="checkbox"<?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"]["3"])) ? " checked=\"checked\"" : ""?> /><label for="s[2]"><?=$lang["stock_laundry"]?></label></td>
                        </tr>
                        <tr>
                            <td><input id="s[4]" name="s[4]" type="checkbox"<?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"]["4"])) ? " checked=\"checked\"" : ""?> /><label for="s[3]"><?=$lang["homewash"]?></label></td>
                            <td><input id="s[5]" name="s[5]" type="checkbox"<?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"]["5"])) ? " checked=\"checked\"" : ""?> /><label for="s[4]"><?=$lang["repair"]?></label></td>
                            <td><input id="s[6]" name="s[6]" type="checkbox"<?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"]["6"])) ? " checked=\"checked\"" : ""?> /><label for="s[6]"><?=$lang["never_scanned"]?></label></td>
                            </tr>
                        <tr>
                            <td><input id="s[7]" name="s[7]" type="checkbox"<?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"]["7"])) ? " checked=\"checked\"" : ""?> /><label for="s[5]"><?=$lang["despeckle"]?></label></td>
                            <td><input id="s[8]" name="s[8]" type="checkbox"<?=(isset($_SESSION["report_beyond_and_in_circulation"]["show"]["8"])) ? " checked=\"checked\"" : ""?> /><label for="s[7]"><?=$lang["disconnected_from_garmentuser"]?></label></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <? endif ?>
        </table>
        <div class="buttons">
            <input type="submit" name="hassubmit" value="<?=$lang["view"]?>" title="<?=$lang["view"]?>" />
            <input type="submit" name="hassubmit" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
        </div>
    </div>

    <? if ($circulationgroup_count <= 1){ print("<input name=\"cid\" type=\"hidden\" value=\"1\" />"); } ?>

</form>

<div class="clear" />

<? if (isset($urlinfo["limit_total"]) && $urlinfo["limit_total"] != 0): ?>

<table cellspacing="0" cellpadding="0">
    <tr>
        <td class="value">
            <?=((!empty($filterdate)) ? $lang["clothing_longer_in_circulation"] . " ".$filterdate.".":"")?> <?=$resultinfo?>
        </td>
    </tr>
</table>

<table class="list">
    <tr class="listtitle">
        <td class="list"><?=$sortlinks["lastscan"]?></td>
        <td class="list"><?=$sortlinks["tag"]?></td>
        <td class="list"><?=$sortlinks["article"]?></td>
        <td class="list"><?=$sortlinks["size"]?></td>
        <td class="list"><?=$sortlinks["modification"]?></td>
        <td class="list"><?=$sortlinks["status"]?></td>
        <td class="list"><?=$sortlinks["days"]?></td>
    </tr>
    <? while ($row = db_fetch_assoc($listdata)): ?>
    <tr class="listlt" onclick="document.location.href='garment_details.php?ref=<?=$row["garments_id"]?>&sec='">
        <td class="list"><?=$row["garments_lastscan"]?></td>
        <td class="list"><?=$row["garments_tag"]?></td>
        <td class="list"><?=$row["articles_description"]?></td>
        <td class="midlist"><?=$row["sizes_name"]?></td>
        <td class="midlist"><?=(($row["modifications_name"])?$row["modifications_name"]:"<span class=\"empty\">". $lang["none"] ."</span>")?> </td>
        <td class="list">
            <?
            if(!empty($row["scanlocations_translate"]))
            {
                if(!empty($row["sub_scanlocations_translate"]))
                {
                    echo $lang[$row["sub_scanlocations_translate"]];
                }else{
                    echo $lang[$row["scanlocations_translate"]];
                }
            }else{
                echo "<span class=\"empty\">". $lang["none"] ."</span>";
            }
            ?>
        </td>
        <td class="midlist">
            <?
                $days = (strtotime('now') - strtotime($row["garments_lastscan"])) / 86400;
                echo ceil($days);
            ?>
        </td>
    </tr>
    <? endwhile ?>
</table>

<?=$pagination?>
<? endif ?>
