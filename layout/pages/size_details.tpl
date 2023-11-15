<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>" />

    <? if (!empty($detailsdata["id"])){ ?>
        <input type="hidden" name="id" value="<?=$detailsdata["id"]?>" />
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
    <? } ?>
    
    <? if (!empty($detailsdata["position"])){ ?>
        <input type="hidden" name="position" value="<?=$detailsdata["position"]?>" />
    <? } ?>

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang["size"]?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["sizegroup"]?>:</td>
                    <td class="value"><? html_selectbox("sizegroup_id", $sizegroups, $detailsdata["sizegroup_id"], $lang["make_a_choice"]) ?></td>
                    <td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["size"]?>:</td>
                    <td class="value"><input type="text" id="name" name="name" value="<?=$detailsdata["name"]?>" size="30" /></td>
                    <td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
                </tr>
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