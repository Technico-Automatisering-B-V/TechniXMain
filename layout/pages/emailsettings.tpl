<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" id="mainform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>" />

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang["common"]?></a></li>
            <li><a href="#tab2">SMTP <?=strtolower($lang["settings"])?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["enabled"]?>:</td>
                    <td class="value">
                        <input type="radio" id="enabled" name="email[enabled]" value="1"<? if ($ar["enabled"]){ echo " checked=\"checked\""; }?> /> <?=$lang["yes"]?>&nbsp;&nbsp;
                        <input type="radio" id="disabled" name="email[enabled]" value=""<? if (!$ar["enabled"]){ echo " checked=\"checked\""; }?> /> <?=$lang["no"]?>
                    </td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["send_from"]?>:</td>
                    <td class="value"><input type="text" name="email[from]" value="<?=trim($ar["from"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name">Transport:</td>
                    <?php if (empty($ar["transport"])){ $transport = "smtp"; } else { $transport = $ar["transport"]; } ?>
                    <td class="value">
                        <input type="radio" name="email[transport]" value="smtp"<? if ($transport == "smtp"){ echo " checked=\"checked\""; }?> /> SMTP&nbsp;&nbsp;
                        <input type="radio" name="email[transport]" value="sendmail"<? if ($transport == "sendmail"){ echo " checked=\"checked\""; }?> /> Sendmail&nbsp;&nbsp;
                        <input type="radio" name="email[transport]" value="mail"<? if ($transport == "mail"){ echo " checked=\"checked\""; }?> /> Mail
                    </td>
                </tr>
            </table>

            <input type="submit" name="testmsg" value="Verstuur testbericht" title="Verstuur testbericht" />
        </div>
        <div id="tab2">
            <table class="detailstab">
                <tr>
                    <td class="name">Server:</td>
                    <td class="value">
                        <input type="text" name="email[smtp][server]" value="<?=$ar["smtp"]["server"]?>" size="30" />
                    </td>
                </tr>
                <tr>
                    <td class="name">Port:</td>
                    <?php if (empty($ar["smtp"]["port"])){ $port = 25; } else { $port = $ar["smtp"]["port"]; } ?>
                    <td class="value"><input type="text" name="email[smtp][port]" value="<?=$port?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["security"]?>:</td>
                    <td class="value">
                        <input type="radio" name="email[smtp][security]" value=""<? if (!$ar["smtp"]["security"]){ echo " checked=\"checked\""; }?> /> <?=$lang["none"]?>&nbsp;&nbsp;
                        <input type="radio" name="email[smtp][security]" value="ssl"<? if ($ar["smtp"]["security"] == "ssl"){ echo " checked=\"checked\""; }?> /> SSL&nbsp;&nbsp;
                        <input type="radio" name="email[smtp][security]" value="tls"<? if ($ar["smtp"]["security"] == "tls"){ echo " checked=\"checked\""; }?> /> TLS
                    </td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["username"]?>:</td>
                    <td class="value"><input type="text" name="email[smtp][user]" value="<?=trim($ar["smtp"]["user"])?>" size="30" /></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["password"]?>:</td>
                    <td class="value"><input type="text" name="email[smtp][pass]" value="<?=trim($ar["smtp"]["pass"])?>" size="30" /></td>
                </tr>
            </table>
        </div>
    </div>

    <?=html_submitbuttons_detailsscreen($pi)?>
</form>