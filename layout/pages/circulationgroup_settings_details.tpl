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
                <tr>
                    <td class="name"><?=$lang["location"]?>:</td>
                    <td class="value"><?=$detailsdata["name"]?></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["loaded"]?>:</td>
                    <td class="value">
                    <?php
                    if ($detailsdata["optimal_load"] == "y"){ $yes_select = " checked=\"checked\""; }else{ $yes_select = ""; }
                    if (empty($detailsdata["optimal_load"]) || $detailsdata["optimal_load"] == "n"){ $no_select = " checked=\"checked\""; }else{ $no_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="optimal_load" id="optimal_loadYes" type="radio" value="y"<?=$yes_select?>><label for="optimal_loadYes"><?=$lang["optimal"]?></label>
                        <input name="optimal_load" id="optimal_loadNo" type="radio" value="n"<?=$no_select?>><label for="optimal_loadNo"><?=$lang["fast"]?></label>
                    </span>
                    </td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["fifo_distribution"]?>:</td>
                    <td class="value">
                    <?php
                    if ($detailsdata["fifo_distribution"] == "y"){ $yes_select = " checked=\"checked\""; }else{ $yes_select = ""; }
                    if (empty($detailsdata["fifo_distribution"]) || $detailsdata["fifo_distribution"] == "n"){ $no_select = " checked=\"checked\""; }else{ $no_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="fifo_distribution" id="fifo_distributionYes" type="radio" value="y"<?=$yes_select?>><label for="fifo_distributionYes"><?=$lang["yes"]?></label>
                        <input name="fifo_distribution" id="fifo_distributionNo" type="radio" value="n"<?=$no_select?>><label for="fifo_distributionNo"><?=$lang["no"]?></label>
                    </span>
                    </td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["credit_free_distribution"]?>:</td>
                    <td class="value">
                    <?php
                    if ($detailsdata["credit_free_distribution"] == "y"){ $yes_select = " checked=\"checked\""; }else{ $yes_select = ""; }
                    if (empty($detailsdata["credit_free_distribution"]) || $detailsdata["credit_free_distribution"] == "n"){ $no_select = " checked=\"checked\""; }else{ $no_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="credit_free_distribution" id="credit_free_distributionYes" type="radio" value="y"<?=$yes_select?>><label for="credit_free_distributionYes"><?=$lang["yes"]?></label>
                        <input name="credit_free_distribution" id="credit_free_distributionNo" type="radio" value="n"<?=$no_select?>><label for="credit_free_distributionNo"><?=$lang["no"]?></label>
                    </span>
                    </td>
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