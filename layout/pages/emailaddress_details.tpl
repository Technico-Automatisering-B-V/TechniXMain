<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" id="mainform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>" />

    <? if (!empty($detailsdata["id"])){ ?>
        <input type="hidden" name="id" value="<?=$detailsdata["id"]?>" />
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
    <? } ?>

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang["email_address"]?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["name"]?>:</td>
                    <td class="value"><input type="text" id="name" name="name" value="<?=$detailsdata["name"]?>" size="30" tabindex="1" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["email_address"]?>:</td>
                    <td class="value"><div style="width: 86%;float: left;margin-right: 6px;"><input type="text" id="email_address" name="email_address" value="<?=$detailsdata["email_address"]?>" size="30" tabindex="1" /></div> <button class="required" title="<?=$lang["field_required"]?>">*</button></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["group"]?>:</td>
                    <td class="value"><div style="width: 86%;float: left;margin-right: 6px;"><?=html_selectbox_array("group", $groups, $detailsdata['group'], $lang["make_a_choice"])?></div> <button class="required" title="<?=$lang["field_required"]?>">*</button></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["locale"]?>:</td>
                    <td class="value">
                        <? html_selectbox("locale_id", $locales, $detailsdata["locale_id"], $lang["make_a_choice"]) ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <?=html_submitbuttons_detailsscreen($pi)?>

</form>