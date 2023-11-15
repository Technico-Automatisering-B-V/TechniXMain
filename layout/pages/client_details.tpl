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
            <li><a href="#tab1"><?=$lang["location"]?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr><td class="name"><?=$lang["name"]?>:</td><td class="value"><input class="required" type="text" id="name" name="name" value="<?=$detailsdata["name"]?>" size="30" tabindex="1" /> <button class="required" title="<?=$lang["field_required"]?>">*</button></td></tr>
                <tr><td class="name"><?=$lang["address"]?>:</td><td class="value"><input type="text" id="address_street" name="address_street" value="<?=$detailsdata["address_street"]?>" size="30" tabindex="2" /></td></tr>
                <tr><td class="name"><?=$lang["zipcode"]?>:</td><td class="value"><input type="text" id="address_zipcode" name="address_zipcode" value="<?=$detailsdata["address_zipcode"]?>" size="30" tabindex="3" /></td></tr>
                <tr><td class="name"><?=$lang["city"]?>:</td><td class="value"><input type="text" id="address_city" name="address_city" value="<?=$detailsdata["address_city"]?>" size="30" tabindex="4" /></td></tr>
                <tr><td class="name"><?=$lang["phone"]?>:</td><td class="value"><input type="text" id="phone" name="phone" value="<?=$detailsdata["phone"]?>" size="30" tabindex="5" /></td></tr>
                <tr><td class="name"><?=$lang["fax"]?>:</td><td class="value"><input type="text" id="fax" name="fax" value="<?=$detailsdata["fax"]?>" size="30" tabindex="6" /></td></tr>
                <tr><td class="name"><?=$lang["country"]?>:</td><td class="value"><input type="text" id="country" name="country" value="<?=$detailsdata["country"]?>" size="30" tabindex="6" /></td></tr>
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