<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>">

    <?php
    if ($pi["page"] == "details"){
        print("<input type=\"hidden\" name=\"id\" value=\"". $detailsdata["id"] ."\" />");
        print("<input type=\"hidden\" name=\"editsubmit\" value=\"1\" />");

        if (isset($detailsdata["garment_id"])){ ?>
           <input type="hidden" name="garment_id" value="<?=$detailsdata["garment_id"]?>" />
           <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
        <?php }
    }
    ?>

    <div id="tabs">
        <ul>
            <li><a href="#chaoot"><?=$lang["throw_off_garments"]?></a></li>
        </ul>
        <div id="chaoot">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["activate"]?>:</td>
                    <td class="value"><input type="checkbox" name="active" value="1"<?=(($detailsdata["active"] == 1) ? " checked=\"checked\"" : "") ?> /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["tag"]?>:</td>
                    <td class="value"><input type="text" id="tag" name="tag" value="<?=$bindingdata["tag"]?>" /> <button name="searchsubmit" type="submit" class="search"><?=$lang["search"]?></button></td>
                    <td><button class="required" title="<?=$lang["field_optional"]?>">*</button></td>
                </tr>
                <? if (!empty($tag_error)): ?>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="2"><span style="color:#B40404"><?=$tag_error?></span></td>
                </tr>
                <? endif ?>
                <tr>
                    <td class="name"><?=$lang["distributorlocation"]?>:</td>
                    <td class="value"><? html_selectbox_array("distributorlocation_id", $distributorlocations, $detailsdata["distributorlocation_id"], $lang["make_a_choice"]) ?></td>
                    <td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["article"]?>:</td>
                    <td class="value"><? html_selectbox_array_submit("article_id", $articles, $detailsdata["article_id"], $lang["make_a_choice"]) ?></td>
                    <td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
                </tr>
                <? if (!empty($detailsdata["article_id"])):?>
                <tr>
                    <td class="name"><?=$lang["size"]?>:</td>
                    <td class="value"><? html_selectbox_array_submit("size_id", $sizes, $bindingdata["size_id"], $lang["(all)"]) ?></td>
                    <td>&nbsp;</td>
                </tr>
                <? endif ?>
                <? if (!empty($bindingdata["size_id"]) && !empty($modifications)):?>
                <tr>
                    <td class="name"><?=$lang["adjustment"]?>:</td>
                    <td class="value"><? html_selectbox_array("modification_id", $modifications, $bindingdata["modification_id"], $lang["none"]) ?></td>
                    <td>&nbsp;</td>
                </tr>
                <? endif ?>
                <? if (empty($bindingdata["tag"])):?>
                <tr>
                    <td class="name"><?=$lang["max_washcount"]?>:</td>
                    <td class="value"><input type="text" id="max_washcount" name="max_washcount" value="<?=$detailsdata["max_washcount"]?>" /></td>
                    <td><button class="required" title="<?=$lang["field_optional"]?>">*</button></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["count"]?>:</td>
                    <td class="value"><input type="text" id="amount" name="amount" value="<?=(($detailsdata["amount"] > 0) ? $detailsdata["amount"] : "")?>" /></td>
                    <td><button class="required" title="<?=$lang["field_optional"]?>">*</button></td>
                </tr>
                <? endif ?>
                <tr>
                    <td class="name"><?=$lang["certain_date"]?>:</td>
                    <td class="value"><input type="checkbox" name="certain_date" onClick="submit()" value="1"<?=(($bindingdata["certain_date"] == 1) ? " checked=\"checked\"" : "")?> /></td>
                    <td>&nbsp;</td>
                </tr>
                <? if ($bindingdata["certain_date"] == 1):?>
                <tr>
                    <td class="name"><?=$lang["multiple_dates"]?>:</td>
                    <td class="value"><input type="checkbox" name="multiple_dates" onClick="submit()" value="1"<?=(($bindingdata["multiple_dates"] == 1) ? " checked=\"checked\"" : "") ?> /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["check_from"]?>:</td>
                    <td class="value">
                        <input class="date" name="date_from" type="text" value="<?=$detailsdata["date_from"]?>" />
                        <? if ($bindingdata["multiple_dates"] == 1): ?>
                            t/m <input class="date" name="date_to" type="text" value="<?=$detailsdata["date_to"]?>" />
                        <? endif ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <? endif ?>

            </table>
        </div>
    </div>

    <?=html_submitbuttons_detailsscreen_garmentuser($pi)?>

</form>

<script type="text/javascript">
    $(function() {
        $("#tag").focus();
    });
</script>