<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
    <input type="hidden" name="garment_id_to_cancel" value="" />

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang["manual_deposit"]?></a></li>
        </ul>
        <div id="tab1">
            <table class="detailstab">
                <tr>
                    <td class="right"><?=$lang["scan_garment"]?>:</td>
                    <td><input type="text" id="searchScanPas" name="searchScanPas" size="30" value="">
                        <button type="submit" name="searchSubmit" class="search"><?=$lang["add_to_list"]?></button>
                    <?php if (!empty($error)){ print("<tr><td>&nbsp;</td><td style=\"color:#B40404;\">". $error .".</td></tr>"); } ?>
                </tr>
                <?php
                if(!empty($garment_details)){
                    print("<tr>");
                        print("<td class=\"right\" valign=\"top\">". $lang["depositbatch"] .":</td>");
                        print("<td>");
                            print("<table cellpadding=\"0\" class=\"list\">");
                                print("<tr class=\"listtitle\">");
                                    print("<td class=\"list\">". $lang["tag"] ."</td>");
                                    print("<td class=\"list\">". $lang["description"] ."</td>");
                                    print("<td class=\"list\">". $lang["size"] ."</td>");
                                    print("<td class=\"list\">". $lang["garmentuser"] ."</td>");
                                    print("<td class=\"list\">". $lang["in_use_since"] ."</td>");
                                print("</tr>");

                                foreach ($garment_details as $garment_id => $garments_props)
                                {
                                    print("<tr class=\"listnc\">");
                                        print("<td class=\"list\"><input name=\"scanned_garments[". $garment_id ."]\" type=\"hidden\" value=\"". $garment_id ."\" />". $garment_id ."</td>");
                                        print("<td class=\"list\">". $garments_props["article"] ."</td>");
                                        print("<td class=\"list\">". $garments_props["size"] ."</td>");
                                        print("<td class=\"list\">");
                                        if (empty($garments_props["surname"])){
                                            print("<span class=\"empty\">". $lang["noone"] ."</span></font>");
                                        }else{
                                            print(generate_garmentuser_label($garments_props["title"], $garments_props["gender"], $garments_props["initials"], $garments_props["intermediate"], $garments_props["surname"], $garments_props["maidenname"], $garments_props["personnelcode"]));
                                        }
                                        print("</td>");
                                        print("<td class=\"list\">". (empty($garments_props["date_received"]) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($garments_props["date_received"]))) ."</td>");
                                    print("</tr>");
                                }
                            print("</table>");
                        print("</td>");
                    print("</tr>");
                }?>
            </table>
        </div>
    </div>

    <?php
    if (!empty($garment_details)){ print("<input type=\"submit\" name=\"saveclose\" value=\"". $lang["save_and_new"] ."\" /> <input type=\"submit\" name=\"cancel\" value=\"". $lang["cancel"] ."\" />"); }
    ?>

</form>

<script type="text/javascript">
    $(function() {
        $("#searchScanPas").focus();
    });
</script>