<form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <div class="filter">
        <table>
            <? if ($circulationgroup_count > 1): ?>
            <tr>
                <td class="name"><?=$lang["location"]?>:</td>
                <td class="value"><?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], " style=\"width:300px\"")?></td>
            </tr>
            <? endif ?>
            <? if ($clientdepartments_count > 0): ?>
            <tr>
                <td class="name"><?=$lang["clientdepartment"]?>:</td>
                <td class="value"><?=html_selectbox_submit("clientdepartment_id", $clientdepartments, $urlinfo["clientdepartment_id"], $lang["(all_clientdepartments)"], " style=\"width:300px\"")?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["article"]?>:</td>
                <td class="value"><?=html_selectbox_submit("aid", $articles, $urlinfo["aid"], $lang["(all_articles)"], " style=\"width:300px\"")?></td>
            </tr>
            <? if (!empty($sizes)): ?>
            <tr>
                <td class="name"><?=$lang["size"]?>:</td>
                <td class="value"><?=html_selectbox_array_submit("sid", $sizes, $urlinfo["sid"], $lang["(all_sizes)"], true, false, " style=\"width:300px\"")?></td>
            </tr>
            <? endif ?>
            <tr>
                <td class="name"><?=$lang["longer_owns_then"]?>:</td>
                <td class="value"><input class="spinner" style="text-align:center" name="daysback" value="<?=$urlinfo["daysback"]?>" size="4" /> <?=strtolower($lang["days"])?></td>
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


<table cellspacing="0" cellpadding="0">
    <tr>
        <td class="value">
            <?=((!empty($filterdate)) ? $lang["garments_longer_owned_then"] . " ".$filterdate.".":"")?><br /><br />
        </td>
    </tr>
</table>

<table class="list">
        <tr class="listtitle">
            <th class="list"><?=$sortlinks["distribution"]?></th>
            <th class="list"><?=$sortlinks["surname"]?></th>
            <th class="list"><?=$sortlinks["name"]?></th>
            <th class="list"><?=$sortlinks["personnelcode"]?></th>
            <th class="list"><?=$sortlinks["article"]?></th>
            <th class="list"><?=$sortlinks["size"]?></th>
            <? if ($clientdepartments_count > 0): ?>
            <th class="list"><?=$sortlinks["clientdepartment"]?></th>
            <? endif ?>
            <th class="list"><?=$sortlinks["days"]?></th>
        </tr>
    

    <? while ($row = db_fetch_assoc($listdata)){ ?>
        <tr class="list" onClick="document.location.href='report_totals4.php?ref=<?=$row["garmentusers_garments_garmentuser_id"]?>'; return false;">
            <td class="list"><?=$row["garmentusers_garments_date_received"]?></td>
            <td class="list">
                <?
                if (!empty($row["garmentusers_surname"])) {
                    print(generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"], $row["garmentusers_personnelcode"]));
                } else {
                    echo "<span class=\"empty\">" . $lang["unknown"] . "</span></font>";
                }
                ?>
            </td>
            <td class="list">
                <? if (!empty($row["garmentusers_name"])) {
                    print($row["garmentusers_name"]);
                } else {
                    echo "<span class=\"empty\">" . $lang["unknown"] . "</span></font>";
                } ?>
            </td>
            <td class="list">
                <? if (!empty($row["garmentusers_personnelcode"])) {
                    print($row["garmentusers_personnelcode"]);
                } else {
                    echo "<span class=\"empty\">" . $lang["unknown"] . "</span></font>";
                } ?>
            </td>
            <td class="list"><?=ucfirst($row["articles_description"])?></td>
            <td class="list"><?=$row["sizes_name"]?></td>
            <? if ($clientdepartments_count > 0): ?>
                <td class="list"><?=(!empty($row["clientdepartments_name"])) ? $row["clientdepartments_name"] : "<span class=\"empty\">". $lang["unknown"] ."</span>"?></td>
            <? endif ?>
            <td class="list">
                <?
                $days = (strtotime("now") - strtotime($row["garmentusers_garments_date_received"])) / 86400;
                echo ceil($days);
                ?>
            </td>
        </tr>
    <? } ?>

    
</table>
<?=$pagination?>

    <? }else{ echo $lang["no_items_found"]; } ?>

<? } ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>