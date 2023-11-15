<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" id="mainform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>" />

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang["client"]?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["name"]?>:</td>
                    <td class="value"><input type="text" name="client[name]" value="<?=trim($ar["name"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["name_short"]?>:</td>
                    <td class="value"><input type="text" name="client[short]" value="<?=trim(strtolower($ar["short"]))?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["address"]?>:</td>
                    <td class="value"><input type="text" name="client[address]" value="<?=trim($ar["address"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["zipcode"]?>:</td>
                    <td class="value"><input type="text" name="client[zipcode]" value="<?=trim($ar["zipcode"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["city"]?>:</td>
                    <td class="value"><input type="text" name="client[city]" value="<?=trim($ar["city"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["country"]?>:</td>
                    <td class="value"><input type="text" name="client[country]" value="<?=trim($ar["country"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["phone"]?>:</td>
                    <td class="value"><input type="text" name="client[phone]" value="<?=trim($ar["phone"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["fax"]?>:</td>
                    <td class="value"><input type="text" name="client[fax]" value="<?=trim($ar["fax"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name">Website:</td>
                    <td class="value"><input type="text" name="client[web]" value="<?=trim($ar["web"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["exportcode"]?>:</td>
                    <td class="value"><input type="text" name="client[exportcode]" value="<?=trim($ar["exportcode"])?>" size="30" /></td>
                </tr>
            </table>
        </div>
    </div>

    <?=html_submitbuttons_detailsscreen($pi)?>
</form>