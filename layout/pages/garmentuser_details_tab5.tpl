<?php

if ($garmentlink["enabled"] == "1"){
    $gltdisplay = "block";
    $geon = " checked=\"checked\"";
    $geoff = "";
}else{
    $gltdisplay = "none";
    $geon = "";
    $geoff = " checked=\"checked\"";
}
?>

        <? if (!empty($user_articles_all) || ($_POST['page'] == 'add' && !empty($_POST["profession_id"]))){ ?>
        <table class="detailstab">
            <tr>
                <td class="listsmall"><?=$lang["access"]?></td>
                <td class="listsmall"><?=$lang["article"]?></td>
                <td class="listsmall"><?=$lang["size"]?></td>
                <td class="listsmall"><?=$lang["modification"]?></td>
                <?
                if (!empty($circulationgroups_all_active)) foreach ($circulationgroups_all_active as $circulationgroup_id => $circulationgroup_name)
                    {
                        print('<td class="listsmall">'. $lang["count_positions_reserved"] .' '. $circulationgroup_name .'</td>');
                    }
                ?>
            </tr>

            <?
            $user_article_checked = "";
            $user_article_enabled = "";

            foreach ($user_articles_all as $arsimo_id => $article_props) {
                if ($article_props["gua_1_enabled"] > 0
                        || $article_props["gua_2_enabled"] > 0
                        || $article_props["gua_3_enabled"] > 0
                        || $article_props["gua_4_enabled"] > 0
                        || $article_props["gua_5_enabled"] > 0
                        || $article_props["gua_6_enabled"] > 0
                        || $article_props["gua_7_enabled"] > 0
                        || $article_props["gua_8_enabled"] > 0
                        || $article_props["gua_9_enabled"] > 0
                        || $article_props["gua_10_enabled"] > 0){
                    $disabled = false;
                    $disabledAttr = "";
                    $class = "";
                    $user_article_checked = " checked=\"checked\"";
                }else{
                    $disabled = true;
                    $disabledAttr = " disabled=\"disabled\"";
                    $class = "class=\"disabled\" ";
                    $user_article_checked = "";
                }

                ?>

                <tr>
                    <td class="midvalue">
                        <input id="user_articles_id<?=$arsimo_id?>" name="user_articles_id[<?=$arsimo_id?>]" type="hidden" value="<?=$arsimo_id?>">
                        <input id="user_article<?=$arsimo_id?>" name="user_articles_selected[<?=$arsimo_id?>]" type="checkbox" value="<?=$arsimo_id?>"<?=$user_article_checked?>">
                    </td>
                    <td class="value">
                        <label for="user_article<?=$arsimo_id?>"><?=$article_props["article"]?></label>
                    </td>
                    <td class="midvalue"><?=$article_props["size"]?></td>
                    <td class="midvalue"><? if(empty($article_props["modifications"])){ print("<span class=\"empty\">". $lang["none"] ."</span>"); }else{ print($article_props["modifications"]); }?></td>
                    
                    <?
                    if (!empty($circulationgroups_all_active)) foreach ($circulationgroups_all_active as $circulationgroup_id => $circulationgroup_name)
                        {
                            print('<td><input '. $class .'id="user_count_'. $circulationgroup_id .'_'. $arsimo_id .'" type="text"'. $disabledAttr .' name="user_count['. $circulationgroup_id .']['. $arsimo_id .']" value="'. $article_props["gua_".$circulationgroup_id."_max_positions"] .'" size="10" /></td>');
                        }
                    ?>
                    
                </tr>
                <?
            }
            ?>
            </table>
            <?
            }else{
                print("<span class=\"empty\">". $lang["no_items_found"] ."</span>");
            }
            ?>


<hr style="color: #98AAB1">
<table class="detailstab">
    <tr>
        <td class="name" width="150"><?=$lang["link_garment"]?>:</td>
            <td class="value" colspan="2">
                <span class="radioset">
                    <input name="garmentlink_enabled" id="ga_on" type="radio" value="1"<?=$geon?> /><label for="ga_on"><?=$lang["yes"]?></label>
                    <input name="garmentlink_enabled" id="ga_off" type="radio" value="2"<?=$geoff?> /><label for="ga_off"><?=$lang["no"]?></label>
                </span>
            </td>
    </tr>
</table>

<table class="detailstab" id="garmentlink_table" style="display:<?=$gltdisplay?>">
    <tr>
        <td class="name" valign="top" style="padding-top:4px;" width="150"><?=$lang["tag"]?>:</td>
        <td class="value">
            <input type="text" name="garmentlink_tag" value="<?=$garmentlink["tag"]?>" size="30" onKeyPress="return send_tag(this,event)" />
            <input type="submit" id="garmentlink_search" name="garmentlink_search" value="<?=$lang["search"]?>" />
            <?=(!empty($garmentlink["message"]) ? "<br />". $garmentlink["message"] : "") ?>
        </td>
    </tr>
    <?if(!empty($garmentlink["circulationgroup_id"])):?> <tr><td class="name"><?=$lang["circulationgroup"]?>:</td><td class="value"><?=html_selectbox_disabled("garmentlink_circulationgroup_id", $circulationgroups, $garmentlink["circulationgroup_id"]);?></td></tr><? endif ?>
    <?if(!empty($garmentlink_articles)):?> <tr><td class="name"><?=$lang["article"]?>:</td><td class="value"><? html_selectbox_array_submit("garmentlink_article_id", $garmentlink_articles, $garmentlink["article_id"], $lang["make_a_choice"], true,true) ?></td></tr><? endif ?>
    <?if(!empty($garmentlink_sizes)):?> <tr><td class="name"><?=$lang["size"]?>:</td><td class="value"><? html_selectbox_array_submit("garmentlink_size_id", $garmentlink_sizes, $garmentlink["size_id"], $lang["make_a_choice"], true,true) ?></td></tr><? endif ?>
    <?if(!empty($garmentlink_modifications_all)):?> <tr><td class="name"><?=$lang["garmentmodification"]?>:</td><td class="value"><? html_selectbox_array_submit("garmentlink_modification_id", $garmentlink_modifications_all, $garmentlink["modification_id"], $lang["make_a_choice"], true,true) ?></td></tr><? endif ?>
    <tr><td class="name"><?=$lang["link_garment"]?>:</td><td class="value"><input type="submit" value="<?=$garmentlink["buttontext"]?>" name="garmentlinksubmit" /></td></tr>
</table>

<table class="detailstab">
    <tr>
        <td class="top right" width="150"><?=$lang["bound_to_garmentuser"]?>:</td>
        <td class="value">
            <? if (!empty($garmentuser_garments) && db_num_rows($garmentuser_garments)): ?>
            <span class="shortlist">
                <table class="list">
                    <tr class="listtitle">
                        <td class="list"><?=$lang["tag"]?></td>
                        <td class="list"><?=$lang["description"]?></td>
                        <td class="list"><?=$lang["size"]?></td>
                        <td class="list"><?=$lang["modification"]?></td>
                        <td class="list"><?=$lang["circulationgroup"]?></td>
                        <td class="list"><?=$lang["status"]?></td>
                        <td class="list">&nbsp;</td>
                    </tr>
                    <? while ($row = db_fetch_assoc($garmentuser_garments)): ?>
                        <tr class="listnc">
                            <td class="list lpointer" onClick="document.location.href='<?=$pi["filename_next"]?>?ref=<?=$row["garments_id"]?>'" ><?=$row["garments_tag"]?></td>
                            <td class="list"><?=$row["articles_description"]?></td>
                            <td class="midlist"><?=$row["sizes_name"]?></td>
                            <td class="midlist"><? if(!empty($row["modifications_name"])){ ?> <?=$row["modifications_name"]?> <? }else{ ?> <span class="empty"><?=$lang["none"]?></span> <? } ?></td>
                            <td class="midlist"><?=$row["circulationgroups_name"]?></td>
                            <td class="list"><?=$lang[$row["scanlocations_translate"]]?></td>
                            <td class="midlist" width="25" onClick="if(confirm('<?=htmlentities($lang["cancel_delete_userbound_garment_more"] . "\\n\\n" . $lang["tag"] . ": " . $row["garments_tag"] . "\\n" . $lang["article"] . ": " . $row["articles_description"] . ", " . strtolower($lang["size"]) . " " . $row["sizes_name"])?>')) {document.dataform.garment_id_to_unbound.value='<?=$row["garments_id"]?>'; document.dataform.submit();}else{return false}" />
                                <img src="layout/images/delete.png" width="14" height="14" border="0" title="<?=$lang["disconnect_garment"]?>" />
                            </td>
                        </tr>
                    <? endwhile ?>
                </table>
            </span>
            <? else: ?>
            <span class="empty"><?=$lang["none"]?></span>
            <? endif ?>
        </td>
    </tr>
</table>
<hr style="color: #98AAB1">
<table class="detailstab">
    <tr>
        <td class="name" width="150"><?=$lang["link_station"]?>:</td>
        <td class="value"><? html_radiobuttons_submit("station_bound_yesno", $station_bound_yesno_options, $station_bound_yesno) ?></td>
        <?=(!empty($distributor_id2_output) ? "<td class=\"value\"></td>" : "") ?>
        <?=(!empty($distributor_id3_output) ? "<td class=\"value\"></td>" : "") ?>
        <?=(!empty($distributor_id4_output) ? "<td class=\"value\"></td>" : "") ?>
        <?=(!empty($distributor_id5_output) ? "<td class=\"value\"></td>" : "") ?>
        <?=(!empty($distributor_id6_output) ? "<td class=\"value\"></td>" : "") ?>
        <?=(!empty($distributor_id7_output) ? "<td class=\"value\"></td>" : "") ?>
        <?=(!empty($distributor_id8_output) ? "<td class=\"value\"></td>" : "") ?>
        <?=(!empty($distributor_id9_output) ? "<td class=\"value\"></td>" : "") ?>
        <?=(!empty($distributor_id10_output) ? "<td class=\"value\"></td>" : "") ?>
    </tr>
    <?if ($station_bound_yesno == 1):?>
    <?if (!empty($distributor_id_output)):?>
    <!--<tr>
        <td class="name"><?=$lang["number_carriers"]?>:</td>
        <td class="value"><?=$station_max_positions?></td>
        <td class="value"></td>
        <td class="value"></td>
    </tr>-->
    <?endif?>
    <tr>
        <td class="name" valign="top"><?=$lang["link_to"]?>:</td>
        <td class="value top"><?=(!empty($distributor_id_output) ? $distributor_id_output : "") ?></td>
        <?=(!empty($distributor_id2_output) ? "<td class=\"value top\">". $distributor_id2_output . "</td>" : "") ?>
        <?=(!empty($distributor_id3_output) ? "<td class=\"value top\">". $distributor_id3_output . "</td>" : "") ?>
        <?=(!empty($distributor_id4_output) ? "<td class=\"value top\">". $distributor_id4_output . "</td>" : "") ?>
        <?=(!empty($distributor_id5_output) ? "<td class=\"value top\">". $distributor_id5_output . "</td>" : "") ?>
        <?=(!empty($distributor_id6_output) ? "<td class=\"value top\">". $distributor_id6_output . "</td>" : "") ?>
        <?=(!empty($distributor_id7_output) ? "<td class=\"value top\">". $distributor_id7_output . "</td>" : "") ?>
        <?=(!empty($distributor_id8_output) ? "<td class=\"value top\">". $distributor_id8_output . "</td>" : "") ?>
        <?=(!empty($distributor_id9_output) ? "<td class=\"value top\">". $distributor_id9_output . "</td>" : "") ?>
        <?=(!empty($distributor_id10_output) ? "<td class=\"value top\">". $distributor_id10_output . "</td>" : "") ?>
    </tr>
    <?endif?>
</table>

<script type="text/javascript">
    $(function() {
        $("input[name*='user_article']").click(function(){
            var value = $(this).attr('checked');
            if (value == "checked"){
                $(this).closest("tr").find('select,input[type="text"]').attr('disabled', false).removeClass("disabled");
            }else{
                $(this).closest("tr").find('select,input[type="text"]').attr('disabled', true).addClass("disabled");
            }
        });
    });
</script>

<script type="text/javascript">

function send_tag(myfield,e)
{

var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return true;

if (keycode == 13)
   {
      document.getElementById("garmentlink_search").click();
      return false;
   }
else
   return true;
}
</script>