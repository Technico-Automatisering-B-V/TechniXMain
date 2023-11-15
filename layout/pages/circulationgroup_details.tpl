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
                    <td class="name"><?=$lang["fifo_distribution"]?>:</td>
                    <td class="value">
                    <?php
                    if (empty($detailsdata["fifo_distribution"]) || $detailsdata["sort"] == "y"){ $asc_select = " checked=\"checked\""; }else{ $asc_select = ""; }
                    if ($detailsdata["sort"] == "n"){ $desc_select = " checked=\"checked\""; }else{ $desc_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="sort" id="sortASC" type="radio" value="y"<?=$asc_select?>><label for="sortASC"><?=$lang["yes"]?></label>
                        <input name="sort" id="sortDESC" type="radio" value="n"<?=$desc_select?>><label for="sortDESC"><?=$lang["no"]?></label>
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