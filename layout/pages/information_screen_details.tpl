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
            <li><a href="#tab1"><?=$lang["information_screen"]?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr><td class="name top"><?=$lang["message_text"]?>:</td><td class="value"><textarea rows="6" id="message" name="message" cols="40"><?=$detailsdata["message"]?></textarea></td></tr>
                <tr><td class="name"><?=$lang["message_color"]?>:</td><td class="value">
                    <?php
                        if (empty($detailsdata["color"]) || $detailsdata["color"] == "green"){ $green_select = " checked=\"checked\""; }else{ $green_select = ""; }
                        if ($detailsdata["color"] == "red"){ $red_select = " checked=\"checked\""; }else{ $red_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="color" id="colorGreen" type="radio" value="green"<?=$green_select?>><label for="colorGreen"><?=$lang["green"]?></label>
                        <input name="color" id="colorRed" type="radio" value="red"<?=$red_select?>><label for="colorRed"><?=$lang["red"]?></label>
                    </span>
                </td></tr>
                <tr><td class="name"><?=$lang["message_size"]?>:</td><td class="value">
                    <?php
                        if (empty($detailsdata["size"]) || $detailsdata["size"] == "medium"){ $medium_select = " checked=\"checked\""; }else{ $medium_select = ""; }
                        if ($detailsdata["size"] == "small"){ $small_select = " checked=\"checked\""; }else{ $small_select = ""; }
                        if ($detailsdata["size"] == "large"){ $large_select = " checked=\"checked\""; }else{ $large_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="size" id="sizeSmall" type="radio" value="small"<?=$small_select?>><label for="sizeSmall"><?=$lang["small"]?></label>
                        <input name="size" id="sizeMedium" type="radio" value="medium"<?=$medium_select?>><label for="sizeMedium"><?=$lang["medium"]?></label>
                        <input name="size" id="sizeLarge" type="radio" value="large"<?=$large_select?>><label for="sizeLarge"><?=$lang["large"]?></label>
                    </span>
                </td></tr>
                <tr><td class="name"><?=$lang["message_speed"]?>:</td><td class="value">
                    <?php
                        if (empty($detailsdata["speed"]) || $detailsdata["speed"] == "normal"){ $normal_select = " checked=\"checked\""; }else{ $normal_select = ""; }
                        if ($detailsdata["speed"] == "slow"){ $slow_select = " checked=\"checked\""; }else{ $slow_select = ""; }
                        if ($detailsdata["speed"] == "fast"){ $fast_select = " checked=\"checked\""; }else{ $fast_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="speed" id="speedSlow" type="radio" value="slow"<?=$slow_select?>><label for="speedSlow"><?=$lang["slow"]?></label>
                        <input name="speed" id="speedNormal" type="radio" value="normal"<?=$normal_select?>><label for="speedNormal"><?=$lang["normal"]?></label>
                        <input name="speed" id="speedFast" type="radio" value="fast"<?=$fast_select?>><label for="speedFast"><?=$lang["fast"]?></label>
                    </span>
                </td></tr>
                <tr><td class="name"><?=$lang["stations_sort"]?>:</td><td class="value">
                    <?php
                    if (empty($detailsdata["sort"]) || $detailsdata["sort"] == "ASC"){ $asc_select = " checked=\"checked\""; }else{ $asc_select = ""; }
                    if ($detailsdata["sort"] == "DESC"){ $desc_select = " checked=\"checked\""; }else{ $desc_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="sort" id="sortASC" type="radio" value="ASC"<?=$asc_select?>><label for="sortASC">ASC</label>
                        <input name="sort" id="sortDESC" type="radio" value="DESC"<?=$desc_select?>><label for="sortDESC">DESC</label>
                    </span>
                </td></tr>
                <tr><td class="name"><?=$lang["show_username"]?>:</td><td class="value">
                    <?php
                    if (empty($detailsdata["show_fullname"]) || $detailsdata["show_fullname"] == "y"){ $yes_select = " checked=\"checked\""; }else{ $yes_select = ""; }
                    if ($detailsdata["show_fullname"] == "n"){ $no_select = " checked=\"checked\""; }else{ $no_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="show_fullname" id="sortYes" type="radio" value="y"<?=$yes_select?>><label for="sortYes"><?=$lang["yes"]?></label>
                        <input name="show_fullname" id="sortNo" type="radio" value="n"<?=$no_select?>><label for="sortNo"><?=$lang["no"]?></label>
                    </span>
                </td></tr>
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