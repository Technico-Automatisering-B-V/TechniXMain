<?php
if (!empty($pi['note'])){ echo $pi['note']; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi['page']?>" />

    <? if (!empty($detailsdata['id'])){ ?>
        <input type="hidden" name="id" value="<?=$detailsdata['id']?>" />
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST['content-changed']) ? 1 : 0)?>" />
    <? } ?>

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang['articlegroup']?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["article"]?> 1:</td>
                    <td class="value" colspan="2" width="250"><? html_selectbox_array("articlelink_article_1_id", $articlelink_articles, $detailsdata["article_1_id"], $lang["make_a_choice"], true) ?></td>
                    <td><button class="required" title="<?=$lang['field_required']?>">*</button></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["article"]?> 2:</td>
                    <td class="value" colspan="2" width="250"><? html_selectbox_array("articlelink_article_2_id", $articlelink_articles, $detailsdata["article_2_id"], $lang["make_a_choice"], true) ?></td>
                    <td><button class="required" title="<?=$lang['field_required']?>">*</button></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["profession"]?> 1:</td>
                    <td class="value" colspan="2" width="250"><?=html_selectbox_array("articlelink_profession_id", $professions, $detailsdata["profession_id"], $lang["make_a_choice"]) ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["combined_credit"]?>:</td>
                    <td class="value">
                    <?php
                        if (empty($detailsdata["combined_credit"]) || $detailsdata["combined_credit"] == "0"){ $cout_select = " checked=\"checked\""; $con_select = "";$co_warning = " class=\"disabled\" disabled=\"disabled\"";}else{ $cout_select = ""; $con_select = " checked=\"checked\"";$co_warning = "";}
                    ?>
                    <span class="radioset">
                        <input name="articlelink_combined_creditOn" id="combined_creditOut" type="radio" value="0"<?=$cout_select?>><label for="combined_creditOut"><?=$lang["inactive"]?></label>
                        <input name="articlelink_combined_creditOn" id="combined_creditOn" type="radio" value="1"<?=$con_select?>><label for="combined_creditOn"><?=$lang["active"]?></label>
                    </span>
                    </td>
                    <td class="value"><input type="text" id="articlelink_combined_credit" name="articlelink_combined_credit" value="<?=$detailsdata['combined_credit']?>" size="4"<?=$co_warning?> /> </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["extra_credit"]?>:</td>
                    <td class="value">
                        <?php
                            if (empty($detailsdata["extra_credit"]) || $detailsdata["extra_credit"] == "0"){ $out_select = " checked=\"checked\""; }else{ $out_select = ""; }
                            if ($detailsdata["extra_credit"] == "1"){ $on_select = " checked=\"checked\""; }else{ $on_select = ""; }
                        ?>
                        <span class="radioset">
                            <input name="articlelink_extra_credit" id="extra_creditOut" type="radio" value="0"<?=$out_select?>><label for="extra_creditOut"><?=$lang["inactive"]?></label>
                            <input name="articlelink_extra_credit" id="extra_creditOn" type="radio" value="1"<?=$on_select?>><label for="extra_creditOn"><?=$lang["active"]?></label>
                        </span>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
				<tr>
                    <td class="name"><?=$lang["only_main_article"]?>:</td>
                    <td class="value">
                        <?php
                            if (empty($detailsdata["only_main_article"]) || $detailsdata["only_main_article"] == "0"){ $oout_select = " checked=\"checked\""; }else{ $oout_select = ""; }
                            if ($detailsdata["only_main_article"] == "1"){ $oon_select = " checked=\"checked\""; }else{ $oon_select = ""; }
                        ?>
                        <span class="radioset">
                            <input name="articlelink_only_main_article" id="only_main_articleOut" type="radio" value="0"<?=$oout_select?>><label for="only_main_articleOut"><?=$lang["inactive"]?></label>
                            <input name="articlelink_only_main_article" id="only_main_articleOn" type="radio" value="1"<?=$oon_select?>><label for="only_main_articleOn"><?=$lang["active"]?></label>
                        </span>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
    </div>

    <?=html_submitbuttons_detailsscreen($pi)?>

</form>

<script type="text/javascript">
    $(function() {
        $("ul.css-tabs").tabs("div.css-panes > div");
        $("#description").focus();
    });
</script>

<script type="text/javascript">
    $(function() {       
        $("input[name='articlelink_combined_creditOn']").click(function() {
            var option = this.value;
            if (option == "0") {
                $("#articlelink_combined_credit").attr("disabled", "disabled").attr("class", "disabled");
            } else if (option == '1') {
                $("#articlelink_combined_credit").removeAttr("disabled").removeAttr("class");
            }
        });

    });
</script>