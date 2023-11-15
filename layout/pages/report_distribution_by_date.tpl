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
                    <td class="value"><?=html_selectbox("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], "style='width:100%'")?></td>
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
                    <th class="list"><?=$sortlinks["distribution"]?></th>
                    <th class="list"><?=$sortlinks["garmentuser"]?></th>
                    <th class="list"><?=$sortlinks["personnelcode"]?></th>
                    <th class="list"><?=$sortlinks["profession"]?></th>
                    <th class="list"><?=$sortlinks["clientdepartment"]?></th>
                    <th class="list"><?=$sortlinks["costplace"]?></th>
                    <th class="list"><?=$sortlinks["function"]?></th>
                    <th class="list"><?=$sortlinks["article"]?></th>
                    <th class="list"><?=$sortlinks["size"]?></th>
                    <th class="list"><?=$sortlinks["tag"]?></th>
                </tr>
                <? while ($row = db_fetch_assoc($listdata)){ ?>
                <tr class="listnc">
                    <form id="gu<?=$row['garmentusers_id']?>" enctype="multipart/form-data" method="POST" action="garmentuser_details.php">
                        <input type="hidden" name="page" value="details">
                        <input type="hidden" name="id" value="<?=$row['garmentusers_id']?>">
                        <input type="hidden" name="gosubmit" value="false">
                    </form>
                    <td class="list"><?=$row["log_garmentusers_garments_endtime"]?></td>
                    <td class="list lpointer" onClick="document.getElementById('gu<?=$row["garmentusers_id"]?>').submit();">
                        <?php
                        if (!empty($row["garmentusers_surname"]))
                        {
                            print(generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"], $row["garmentusers_personnelcode"]));
                        }
                        ?>
                    </td>
                    <td class="list lpointer" onClick="document.getElementById('gu<?=$row["garmentusers_id"]?>').submit();"><?=$row["garmentusers_personnelcode"]?></td>
                    <td class="list"><?=$row["professions_name"]?></td>
                    <td class="list"><?=$row["clientdepartments_name"]?></td>
                    <td class="list"><?=$row["costplaces_value"]?></td>
                    <td class="list"><?=$row["functions_value"]?></td>
                    <td class="list"><?=$row["articles_description"]?></td>
                    <td class="midlist"><?=$row["sizes_name"]?><?=(!empty($row["garmentmodifications_name"])) ? " ". $row["garmentmodifications_name"] : ""?></td>
                    <td class="midlist lpointer" onClick="document.location.href='garment_details.php?ref=<?=$row["garments_id"]?>'"><?=$row["garments_tag"]?></td>
                </tr>
                <? } ?>
            
        </table>

        <?=$pagination?>

    <? } ?>

<? } ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>
