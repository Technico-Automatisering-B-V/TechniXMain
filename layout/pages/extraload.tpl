<form name="dataform" id="mainform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="arsimo_id_to_cancel" value="">
    <input type="hidden" name="distributorlocation_id_to_cancel" value="">
    
    <div class="filter">
        <table>
            <tr>
                <td class="name"><?=$lang["location"]?>:</td>
                <td class="value" width="150"><?=html_selectbox_submit("distributorlocation_id", $distributorlocations, $distributorlocation_id, $lang["make_a_choice"], "style='width:100%'"); ?></td>
            </tr>
        </table>
    </div>

    <div class="clear"></div>
    
    <? if (isset($pi["note"]) && $pi["note"] != "") echo $pi["note"] ?>
    
    <? if ($urlinfo["limit_total"] != 0){ 
        print($resultinfo); ?>
        <table class="list">
                <tr class="listtitle">
                    <th class="list"><?=$sortlinks["circulationgroup"]?></th>
                    <th class="midlist"><?=$sortlinks["article"]?></th>
                    <th class="midlist"><?=$sortlinks["size"]?></th>
                    <th class="midlist"><?=$sortlinks["modification"]?></th>
                    <th class="midlist"><?=$sortlinks["demand"]?></th>
                    <th class="midlist">&nbsp;</th>
                </tr>
            <? while ($row = db_fetch_assoc($listdata)){ ?>
                <tr class="listnc">
                    <td class="list"><?=$row["circulationgroups_name"]?></td>
                    <td class="list"><?=$row["articles_description"]?></td>
                    <td class="midlist"><?=$row["sizes_name"]?></td>
                    <td class="midlist">
                        <? if ($row["modifications_name"]) {
                                echo $row["modifications_name"];
                            } else {
                                echo "<span class=\"empty\">" . $lang["none"] . "</span>";
                            }
                        ?>
                    <td class="midlist"><?=$row["distributorlocations_loadadvice_demand"]?></td>
                    <td class="midlist" width="25" onClick="if(confirm('<?=htmlentities("Deze extra belading verwijderen? Het gaat hier alleen om de extra toelating. Eventuele kleding blijft gewoon beladen.\\n\\n" . $lang["article"] . ": " . $row["articles_description"] . ", " . strtolower($lang["size"]) . " " . $row["sizes_name"] . " " . $row["modifications_name"] . "\\n" . $lang["count"] . ": " . $row["distributorlocations_loadadvice_demand"] . "")?>'))
            {document.dataform.arsimo_id_to_cancel.value='<?=$row["distributorlocations_loadadvice_arsimo_id"]?>';document.dataform.distributorlocation_id_to_cancel.value='<?=$row["distributorlocations_loadadvice_distributorlocation_id"]?>';document.dataform.submit();}else{return false}">
                        <img src="layout/images/delete.png" width="14" height="14" border="0" title="Extra beladingstoelating verwijderen">
                    </td>
                </tr>
            <? } ?>
            
        </table>

        <?=$pagination?>

    <? }else{ echo $lang["no_items_found"]; } ?>
</form>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>
