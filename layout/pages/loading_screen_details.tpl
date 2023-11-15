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
            <li><a href="#tab1"><?=$lang["loading_screen"]?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr><td class="name"><?=$lang["stations_sort"]?>:</td><td class="value">
                    <?php
                    if (empty($detailsdata["sort"]) || $detailsdata["sort"] == "article"){ $article_select = " checked=\"checked\""; }else{ $article_select = ""; }
                    if ($detailsdata["sort"] == "loadable"){ $loadable_select = " checked=\"checked\""; }else{ $loadable_select = ""; }
                    ?>
                    <span class="radioset">
                        <input name="sort" id="sortASC" type="radio" value="article"<?=$article_select?>><label for="sortArticle"><?=$lang["article"]?></label>
                        <input name="sort" id="sortDESC" type="radio" value="loadable"<?=$loadable_select?>><label for="sortLoadable"><?=$lang["loadable"]?></label>
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