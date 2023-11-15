<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>" />

    <? if (!empty($detailsdata["id"])){ ?>
        <input type="hidden" name="id" value="<?=$detailsdata["id"]?>" />
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
    <? } ?>

    <div id="tabs">
        <ul>
            <li><a href="#general"><?=$lang["profession"]?></a></li>
            <li><a href="#articles"><?=$lang["garmentprofile"]?></a></li>
        </ul>

        <div id="general">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["name"]?>:</td>
                    <td class="value"><input type="text" id="name" name="name" value="<?=$detailsdata["name"]?>" size="30" /> <button class="required" title="<?=$lang["field_required"]?>">*</button></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["timelock"]?>:</td>
                    <td class="value"><input type="text" id="timelock" name="timelock" value="<?=$detailsdata["timelock"]?>" size="6" /> <?=strtolower($lang["minutes"])?></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["warning"]?>:</td>
                    <td class="value"><input type="text" id="daysbeforewarning" name="daysbeforewarning" value="<?=($detailsdata["daysbeforewarning"] == null ? "" : $detailsdata["daysbeforewarning"]) ?>" size="6" /> <?=strtolower($lang["days_garments_in_possession"])?></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["blockage"]?>:</td>
                    <td class="value"><input type="text" id="daysbeforelock" name="daysbeforelock" value="<?=($detailsdata["daysbeforelock"] == null ? "" : $detailsdata["daysbeforelock"]) ?>" size="6" /> <?=strtolower($lang["days_after_warning"])?></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["importcode"]?>:</td>
                    <td class="value"><input type="text" id="importcode" name="importcode" value="<?=($detailsdata["importcode"] == null ? "" : $detailsdata["importcode"]) ?>" size="6" /></td>
                </tr>
            </table>
        </div>

        <div id="articles">
            <table class="detailstab">
                <?php
                while ($article = db_fetch_assoc($articles_all))
                {
                    $id = $article["id"];
                    $name = $article["description"];

                    if (!empty($articles_selected[$id]) && $articles_selected[$id] > 0)
                    {
                        $article_checked = " checked=\"checked\"";
$credit = $articles_selected[$id];
                    }else{
                        $article_checked = "";
$credit = 1;
                    }
                    print("<tr><td><input name='articles_selected[". $id ."]' type='checkbox'". $article_checked. " /></td><td>". $name ."</td><td class='value'><input type='text' id='articles_selected_c[". $id ."]' name='articles_selected_c[". $id ."]' value='$credit' size='50' /></td>
                </tr>");
                }
                ?>
            </table>
        </div>
    </div>

    <?=html_submitbuttons_detailsscreen($pi)?>

</form>

<script type="text/javascript">
    $(function() {
        $("#name").focus();
    });
</script>