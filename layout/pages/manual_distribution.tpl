<? if (!empty($pi["note"])) echo $pi["note"] ?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">

    <input type="hidden" name="garmentuser_id" value="<?=$garmentuser_id?>" />
    <input type="hidden" name="garment_id_to_cancel" value="" />
    <input type="hidden" name="remove_from_list" value="" />

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang["manual_distribution"]?></a></li>
        </ul>
        <div id="tab1">
            <table class="detailstab">

                <?php if (empty($garmentuser_id)){ ?>
                <tr><td class="right"><?=$lang["passcode"]?>:</td><td><input type="text" id="searchScanPas" name="searchScanPas" value="<?=$searchdata["scanpas"]?>" size="30"> <input type="submit" name="searchSubmit" value="<?=$lang["search"]?>" /></td></tr>
                <tr><td class="right"><?=$lang["personnelcode"]?>:</td><td><input type="text" id="searchPersonnelcode" name="searchPersonnelcode" value="<?=$searchdata["personnelcode"]?>" size="30"> <input type="submit" name="searchSubmit" value="<?=$lang["search"]?>" /></td></tr>
                <tr><td class="right"><?=$lang["garmentuser"]?>:</td><td><input type="text" id="searchGarmentuser" name="searchGarmentuser" value="<?=$searchdata["garmentuser"]?>" size="30"> <input type="submit" name="searchSubmit" value="<?=$lang["search"]?>" /></td></tr>
                <?php
                }

                if ($error == 0)
                {
                    // Meerdere resultaten terug //
                    if ($garmentuser_multiple == 1){ ?>
                        </table>

                        <?=$lang["multiple_garmentusers_found"]?>:

                        <table class="list">
                            <tr class="listtitle">
                                <td class="list"><?=$lang["name"]?></td>
                                <td class="list"><?=$lang["first_name"]?></td>
                                <td class="list"><?=$lang["personnelcode"]?></td>
                                <td class="list">&nbsp;</td>
                            </tr>
                            <? while ($row = db_fetch_assoc($garmentusers_data)): ?>
                            <tr class="list" onClick="location.href='manual_distribution.php?garmentuser_id=<?=$row["garmentusers_id"]?>';">
                                <td class="list">
                                    <?=generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"])?>
                                </td>
                                <td class="list">
                                    <?=((empty($row["garmentusers_name"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : ucfirst($row["garmentusers_name"]) )?>
                                </td>
                                <td class="list"><?=(empty($row["garmentusers_personnelcode"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_personnelcode"]?></td>
                                <td class="list"><span style="color:#25814E"><?=$lang["select"]?></span></td>
                            </tr>
                            <? endwhile ?>
                        </table>
                    <?php
                    }

                    if (!empty($garmentuser_id)){ ?>

                        <tr><td class="right"><?=$lang["passcode"]?>:</td><td><?=(empty($garmentuser_data["garmentusers_code"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $garmentuser_data["garmentusers_code"] ?></td></tr>
                        <tr><td class="right"><?=$lang["personnelcode"]?>:</td><td><?=(empty($garmentuser_data["garmentusers_personnelcode"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $garmentuser_data["garmentusers_personnelcode"] ?></td></tr>
                        <tr>
                            <td class="right"><?=$lang["garmentuser"]?>:</td>
                            <td>
                                <?php
                                if (!empty($garmentuser_data["garmentusers_surname"])) {
                                    print(generate_garmentuser_label($garmentuser_data["garmentusers_title"], $garmentuser_data["garmentusers_gender"], $garmentuser_data["garmentusers_initials"], $garmentuser_data["garmentusers_intermediate"], $garmentuser_data["garmentusers_surname"], $garmentuser_data["garmentusers_maidenname"]));
                                } else {
                                    echo "<span class=\"empty\">". $lang["unknown"] ."</em></font>";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="right"><?=$lang["profession"]?>:</td>
                            <td><?=ucfirst($garmentuser_data["profession_name"])?></td>
                        </tr>

                        <?php
                        /** Dragergebonden kleding (indien van toepassing) **/
                        if (!empty($articles_all)){
                        ?>
                            <tr>
                                <td class="right" valign="top"><?=$lang["profile"]?> (<?=strtolower($lang["size"])?>):</td>
                                <td valign="top">
                                    <table class="list">
                                        <tr class="listtitle">
                                            <td class="list"><?=$lang["article"]?></td>
                                            <td class="list"><?=$lang["size"]?></td>
                                            <td class="list"><?=$lang["modification"]?></td>
                                        </tr>
                                        <?php foreach ($articles_all as $arsimo_id => $article_props) { ?>
                                            <tr class="listnc">
                                                <td class="list"><?=$article_props["article"]?></td>
                                                <td class="list"><?=$article_props["size"]?></td>
                                                <td class="list"><?=(empty($article_props["modifications"])) ? "<span class=\"empty\">". $lang["none"] ."</span>" : $article_props["modifications"] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </td>
                            </tr>
                        <?php
                        }

                        /** Dragergebonden kleding (indien van toepassing) **/
                        if (!empty($userbound_articles)){
                        ?>
                            <tr>
                                <td class="right" valign="top"><?=$lang["profile"]?> (<?=strtolower($lang["garmentuser"])?>):</td>
                                <td valign="top">
                                    <table class="list">
                                        <tr class="listtitle">
                                            <td class="list"><?=$lang["article"]?></td>
                                            <td class="list"><?=$lang["size"]?></td>
                                            <td class="list"><?=$lang["modification"]?></td>
                                        </tr>
                                        <?php foreach ($userbound_articles as $arsimo_id => $article_props) { ?>
                                            <tr class="listnc">
                                                <td class="list"><?=$article_props["article"]?></td>
                                                <td class="list"><?=$article_props["size"]?></td>
                                                <td class="list"><?=(empty($article_props["modifications"])) ? "<span class=\"empty\">". $lang["none"] ."</span>" : $article_props["modifications"] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </td>
                            </tr>
                        <?php
                        } ?>

                        <tr>
                            <td class="right"><?=$lang["max_credit"]?>:</td>
                            <td><?=($garmentuser_data["maxcredit"] == 0) ? "<span class=\"empty\">". $lang["none"] ."</span>" : $garmentuser_data["maxcredit"] ?></td>
                        </tr>

                        <?php
                        $remaining = $garmentuser_data["maxcredit"] - $garmentuser_data["garments_in_use"];
                        ?>
                        <tr>
                            <td class="right"><?=$lang["remaining_credit"]?>:</td>
                            <td><?=(($remaining - count($scanned_garments)) <= 0) ? "<font style=\"color:#B40404;\">". $lang["none"] ."</font>" : ($remaining - count($scanned_garments)) ?></td>
                        </tr>


                        <?php
                        /** Kleding in gebruik (indien van toepassing) **/
                        if (!empty($garmentuser_garments_inuse) && db_num_rows($garmentuser_garments_inuse)){ ?>
                            <tr>
                                <td class="right" valign="top"><?=$lang["garments_in_use"]?>:</td>
                                <td>
                                    <table class="list">
                                        <tr class="listtitle">
                                            <td class="list"><?=$lang["tag"]?></td>
                                            <td class="list"><?=$lang["description"]?></td>
                                            <td class="list"><?=$lang["size"]?></td>
                                            <td class="list"><?=$lang["in_use_since"]?></td>
                                            <td class="list">&nbsp;</td>
                                        </tr>

                                        <? while ($row = db_fetch_assoc($garmentuser_garments_inuse)){ ?>
                                            <tr class="listnc">
                                                <td class="list"><?=$row["garments_tag"]?></td>
                                                <td class="list"><?=$row["articles_description"]?></td>
                                                <td class="midlist"><?=$row["sizes_name"]?></td>
                                                <td class="list"><?=strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($row["garmentusers_garments_date_received"]))?></td>
                                                <td class="midlist" width="25" onClick="if(confirm('<?=htmlentities("Let op: De uitgifte van dit kledingstuk wordt ongedaan gemaakt. Het kledingstuk wordt als vermist gemarkeerd zolang het niet bij een scanpunt teruggevonden wordt. Wilt u doorgaan?\\n\\n" . $lang["tag"] . ": " . $row["garments_tag"] . "\\n" . $lang["article"] . ": " . $row["articles_description"] . ", " . strtolower($lang["size"]) . " " . $row["sizes_name"] . "\\n" . $lang["distribution"] . ": " . $row["garmentusers_garments_date_received"])?>')){document.dataform.garment_id_to_cancel.value='<?=$row["garments_id"]?>';document.dataform.submit();}else{return false}">
                                                    <img src="layout/images/delete.png" width="14" height="14" border="0" title="Uitgifte ongedaan maken en als vermist markeren">
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </td>
                            </tr>
                        <?php
                        } ?>

                        <!--[if IE]><input type="text" style="display: none;" disabled="disabled" size="1" /><![endif]-->
                        <tr>
                            <td class="right"><?=$lang["scan_garment"]?>:</td>
                            <td><input id="garment_tag" type="text" name="garment_tag" size="30" value="" />
                                <button type="submit"  name="garment_tag_submit" class="search"><?=$lang["add_to_list"]?></button></td>
                        </tr>

                        <?php
                        if(!empty($scanned_garments)){
                            print("<tr>");
                                print("<td>&nbsp;</td>");
                                print("<td>");
                                    print("<strong>". $lang["distributions"] ."</strong><br />");
                                    print("<table cellpadding=\"0\" cellspacing=\"2\" class=\"list\">");
                                        print("<tr class=\"listtitle\">");
                                            print("<td class=\"list\">". $lang["tag"] ."</td>");
                                            print("<td class=\"list\">". $lang["description"] ."</td>");
                                            print("<td class=\"list\">". $lang["size"] ."</td>");
                                        print("</tr>");
                                        $i=0;
                                        foreach ($scanned_garments as $garment_id => $garments_props) {
                                            print("<tr class=\"listnc\">");
                                                print("<td class=\"list\"><input name=\"scanned_garments[". $garment_id ."]\" type=\"hidden\" value=\"". $garments_props ."\" />". $garment_details[$garments_props]["tag"] ."</td>");
                                                print("<td class=\"list\">". $garment_details[$garments_props]["article"] ."</td>");
                                                print("<td class=\"list\">". $garment_details[$garments_props]["size"] ."</td>");
                                                print("<td class=\"midlist\" width=\"25\" onClick=\"document.dataform.remove_from_list.value='". $scanned_garments[$i] ."';document.dataform.submit();\">");
                                                    print("<img src=\"layout/images/delete.png\" width=\"14\" height=\"14\" border=\"0\" title=\"Verwijderen uit uitgiftelijst\">");
                                                print("</td>");
                                            print("</tr>");
                                            $i++;
                                        }
                                    print("</table>");
                                print("</td>");
                            print("</tr>");
                        }
                    }
                }

                if (!empty($error)){ print("<tr><td>&nbsp;</td><td style=\"color:#B40404;\">". $error ."</td></tr>"); }
                ?>

            </table>
        </div>
    </div>

    <?php
    if (!empty($garmentuser_id)){ ?>
    <input type="submit" name="saveclose" value="<?=$lang["link_and_save"]?>" /> <input type="submit" name="cancel" value="<?=$lang["cancel"]?>" />
    <?php } ?>

</form>

<script type="text/javascript">
	$(function() {
		<?php
		if (!empty($garmentuser_id)){ ?>
			$("#garment_tag").focus();
		<? }else{ ?>
			$("#searchScanPas").focus();
		<? } ?>
	});
</script>