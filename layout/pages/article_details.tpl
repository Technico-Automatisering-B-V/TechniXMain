<?php
if (!empty($pi['note'])){ echo $pi['note']; }
?>

<style>
    .wrapper {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        column-gap: 10px;
        row-gap: 1em;
    }
</style>

<script>
function selectPricingMethod() {
    const pricing_selection = document.getElementsByClassName("pricing_choice");
    alert("hello");
    console.log(pricing_selection);
}
</script>


<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi['page']?>" />

    <? if (!empty($detailsdata['id'])){ ?>
        <input type="hidden" name="id" value="<?=$detailsdata['id']?>" />
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST['content-changed']) ? 1 : 0)?>" />
    <? } ?>

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang['article']?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                
                <tr>
                    <td class="name"><?=$lang['description']?>:</td>
                    <td class="value"><input type="text" id="description" name="description" value="<?=$detailsdata['description']?>" size="50" /></td>
					<td class="value" rowspan="5" style="padding: .4em 1em;"><img src="data:image/jpeg;base64,<?=base64_encode( $detailsdata['image_data'] )?>" style="border-radius: 4px;border: 1px solid #98AAB1;max-width: 200px;background-color: white;"/></td>
                </tr>
                
                <tr>
                    <td class="name"><?=$lang['articlenumber']?>:</td>
                    <td class="value"><input type="text" id="articlenumber" name="articlenumber" value="<?=$detailsdata['articlenumber']?>" size="50" /></td>
                </tr>
                
                <tr>
                    <td class="name"><?=$lang['extra_info']?>:</td>
                    <td class="value"><input type="text" id="extra_info" name="extra_info" value="<?=$detailsdata['extra_info']?>" size="50" /></td>
                </tr>
                
                <tr>
                    <td class="name"><?=$lang['credit']?>:</td>
                    <td class="value"><input type="text" id="credit" name="credit" value="<?=$detailsdata['credit']?>" size="50" /></td>
                </tr>
                
                <tr>
                    <td class="name"><?=$lang["location"]?>:</td>
                    <td class="value"><? html_selectbox("distributorlocation_id", $distributorlocations, $detailsdata["distributorlocation_id"], $lang["make_a_choice"]) ?></td>
                </tr>
                
                <tr>
                    <td class="name"><?=$lang["station"]?>:</td>
                    <td class="value"><? html_selectbox_multi_value("distributor_id", $stations, $detailsdata["distributor_id"], $lang["make_a_choice"]) ?></td>
					<td>&nbsp;</td>
                </tr>
                
                <tr>
                    <td class="name"><?=$lang["distribution_from"]?>:</td>
                    <td class="value"><? html_selectbox_array("distribution_from", $hours, $detailsdata["distribution_from"], $lang["make_a_choice"], "style='width:100%'") ?></td>
                    <td>&nbsp;</td>
                </tr>
		        
                <tr>
                    <td class="name"><?=$lang["distribution_to"]?>:</td>
                    <td class="value"><? html_selectbox_array("distribution_to", $hours, $detailsdata["distribution_to"], $lang["make_a_choice"], "style='width:100%'") ?></td>
                    <td>&nbsp;</td>
                </tr>		
                
                <tr>
                    <td class="name"><?=$lang['sizegroup']?>:</td>
                    <td class="value">
                        <?php
                        if (!empty($sizes_inuse))
                        {
                            echo html_selectbox_disabled("sizegroup_id_disabled", $sizegroups, $detailsdata['sizegroup_id']);
                            echo "<input type='hidden' name='sizegroup_id' value='". $detailsdata['sizegroup_id'] ."' />";
                        }
                        else
                        {
                            echo html_selectbox_submit("sizegroup_id", $sizegroups, $detailsdata['sizegroup_id'], $lang['make_a_choice']);
                        }
                        ?>
                    </td>
                    <td>&nbsp;</td>
                </tr>

                <?php
                if (!empty($sizes_all))
                {
                ?>
                    <tr>
                        <td class="top right"><?=$lang['sizes']?>:</td>
                        <td class="value" colspan="2">
                            <?php

                            $checkboxlist = array();
                            $checked = null;
                            $disabled = null;

                            while ($size = db_fetch_row($sizes_all))
                            {
                                if (isset($checkboxlist_more)){ array_push($checkboxlist, ""); }

                                if (isset($sizes_selected[$size[0] . "_"])){ $checked = " checked=\"checked\""; }
                                if (isset($sizes_inuse[$size[0] . "_"])){ $disabled = " disabled=\"disabled\""; }
                                array_push($checkboxlist, '<input name="sizes_selected[]" type="checkbox" value="' . $size[0] . '_"' . $checked . $disabled . ' />' . $size[1]);
                                $checked = null;
                                $disabled = null;

                                if (!empty($modifications_all))
                                {
                                    while ($mod = db_fetch_row($modifications_all))
                                    {
                                        if (isset($sizes_selected[$size[0] . "_" . $mod[0]])){ $checked = " checked=\"checked\""; }
                                        if (isset($sizes_inuse[$size[0] . "_" . $mod[0]])){ $disabled = " disabled=\"disabled\""; }
                                        array_push($checkboxlist, '<input name="sizes_selected[]" type="checkbox" value="' . $size[0] . '_' . $mod[0] . '"' . $checked . $disabled . ' />' . $size[1] . ' (' . $mod[1] . ')');
                                        $checked = null;
                                        $disabled = null;
                                    }
                                    db_data_seek($modifications_all, 0);
                                }

                                $checkboxlist_more = true;
                            }

                            $columns = 3;
                            $i = 1;

                            echo "<table class='checkboxlist'><tr><td class='checkboxlist'>";
                            foreach($checkboxlist as $check_num => $value)
                            {
                                if (empty($value))
                                {
                                    if ($i >= $columns)
                                    {
                                        print("</td></tr><tr><td class='checkboxlist' valign='top'>");
                                        $i=0;
                                    }
                                    else
                                    {
                                        print("</td><td class='checkboxlist' valign='top'>");
                                    }
                                    $i++;
                                }

                                if (!empty($value))
                                {
                                    print($value . "<br />");
                                }
                            }

                            if (!empty($sizes_inuse))
                            {
                                echo "</td></tr><tr><td class='small' colspan='" . $columns . "'><em>". $lang["some_sizes_cannot_be_disabled_because_they_are_in_use_or_loaded"] ."</em></td>";
                            }

                            echo "</td></tr></table>";

                            ?>
                        </td>
                    </tr>
                <?php
                }
                ?>

                <?php
                    $bHasWorkwear = false;
                    foreach($_SESSION['user_privileges']['workwearmanagement'] as $permission=>$value) {
                            if($value == "on") {
                                $bHasWorkwear = true;
                                break;
                            }
                    }
                    if($bHasWorkwear) {
                ?>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["category"]?>:</td>
                    <td class="value"><? html_selectbox("workwear_category_id", $workwear_categories, $detailsdata["workwear_category_id"], $lang["make_a_choice"], "style='width:100%'") ?></td>
                </tr>
                    <tr>
                        <td class="name"><?=$lang["set_max_washcount"]?>:</td>
                        <td><input type="number" style="width:100%; border:1PX SOLID #CCC; border-radius:4px; padding:0.4em 0.5em;" step="1" name="maxWasbeurtenArtikel" value='<?=$workwear_article_data[0]["maxwashcount"]?>'></td>
                    </tr>
                    <tr>
                        <td class="name"><?=$lang["article_price"]?>:</td>
                        <td><input type="number" style="width:100%; border:1PX SOLID #CCC; border-radius:4px; padding:0.4em 0.5em;" step="0.01" name="fKostPrijs" value='<?=$workwear_article_data[0]["price"]?>'></td>
                    </tr>
                    <tr>
                        <td class="name">Huurprijs in &euro;:</td>
                        <td><input type="number" style="width:100%; border:1PX SOLID #CCC; border-radius:4px; padding:0.4em 0.5em;" step="0.01" name="fHuurPrijs" value='<?=$workwear_article_data[0]["rental_price"]?>'></td>
                    </tr>
                </div>
                <td>&nbsp;</td>
                <tr>
                    <td title="Als een prijs anders is door de maat"><?=$lang["unique_price_arsimo"]?>:</td>
                    <td>
                        <div class="wrapper">
                            <?php
                                if(!empty($arsimos_for_workwear_data)) {
                                    $aSanitizedSizes = xss_sanitize($arsimos_for_workwear_data);
                                    foreach($aSanitizedSizes as $size_value) {
                                        echo("<div><input type=\"number\" style=\"width:70px; border:1PX SOLID #CCC; border-radius:5px; padding:0.4em 0.5em;\" name=\"arsimocost[{$size_value["arsimo_id"]}]\" step=\"0.01\" value=\"{$workwear_arsimo_data[$size_value["arsimo_id"]]["price"]}\"></input>
                                        <label for=\"arsimocost[{$size_value["arsimo_id"]}]\">{$size_value["name"]}</label></div>");
                                    }
                                }
                            ?>
                        </div>
                    </td>
                </tr>

                <?php
                        }
                ?>
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