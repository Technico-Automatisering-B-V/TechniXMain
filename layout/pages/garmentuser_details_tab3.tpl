
<table class="detailstab">
    <? if ($_POST["page"] !== "add"): ?>
    <tr>
        <td class="top right"><?=$lang["garments_in_use"]?>:</td>
        <td class="value">
            <? if (!empty($garmentuser_garments_inuse) && db_num_rows($garmentuser_garments_inuse)): ?>
            <span class="shortlist">
                <table class="list">
                    <tr class="listtitle">
                        <td class="list"><?=$lang["tag"]?></td>
                        <td class="list"><?=$lang["tag"]." 2"?></td>
                        <td class="list"><?=$lang["description"]?></td>
                        <td class="list"><?=$lang["size"]?></td>
                        <td class="list"><?=$lang["modification"]?></td>
                        <td class="list"><?=$lang["in_use_since"]?></td>
                        <td class="list">&nbsp;</td>
                    </tr>

                    <? while ($row = db_fetch_assoc($garmentuser_garments_inuse)): ?>
                        <tr class="listnc">
                            <td class="list lpointer" onClick="document.location.href='<?=$pi["filename_next"]?>?ref=<?=$row["garments_id"]?>'" ><?=$row["garments_tag"]?></td>
                            <td class="midlist"><? if(!empty($row["garments_tag2"])){ ?> <?=$row["garments_tag2"]?> <? }else{ ?> <span class="empty"><?=$lang["none"]?></span> <? } ?></td>
                            <td class="list"><?=$row["articles_description"]?></td>
                            <td class="midlist"><?=$row["sizes_name"]?></td>
                            <td class="midlist"><? if(!empty($row["modifications_name"])){ ?> <?=$row["modifications_name"]?> <? }else{ ?> <span class="empty"><?=$lang["none"]?></span> <? } ?></td>
                            <td class="list"><?=strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($row["garmentusers_garments_date_received"]))?></td>
                            <?
                            $garmentuser = $detailsdata["surname"] .
                                ((!empty($detailsdata["maidenname"])) ? '-'. $detailsdata["maidenname"] : '') .
                                    ', '.
                                    (($detailsdata["gender"] == "male") ? $lang["mr"] : $lang["ms"]) .
                                    ((!empty($detailsdata["initials"])) ? ' ' . $detailsdata["initials"] : '') .
                                    ((!empty($detailsdata["title"])) ? ' ' . $detailsdata["title"] : '') .
                                    ((!empty($detailsdata["intermediate"])) ? ' ' . $detailsdata["intermediate"] : '');
                            ?>
                            <td class="midlist" width="25" onClick="disconnectGarmentDialog('<?=htmlentities($lang["cancel_distribution_set_disconnected_more"] . "\\n\\n" . $lang["tag"] . ": " . $row["garments_tag"] . "\\n" . $lang["article"] . ": " . $row["articles_description"] . ", " . strtolower($lang["size"]) . " " . $row["sizes_name"] . "\\n" . $lang["distribution"] . ": " . $row["garmentusers_garments_date_received"] . "\\n" . $lang["garmentuser"] . ": " . addslashes($garmentuser) . "\\n". "\\n" . $lang["comments"] . ":" )?>',<?=$row["garments_id"]?>)">
                                <img src="layout/images/delete.png" width="14" height="14" border="0" title="<?=$lang["cancel_distribution_set_disconnected"]?>" />
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
    <tr>
        <td class="top right"><?=$lang["disconnected_from_garmentuser"]?>:</td>
        <td class="value">
            <? if (!empty($garmentuser_disconnected_garments) && db_num_rows($garmentuser_disconnected_garments)): ?>
            <span class="shortlist">
                <table class="list">
                    <tr class="listtitle">
                        <td class="list"><?=$lang["tag"]?></td>
                        <td class="list"><?=$lang["tag"]." 2"?></td>
                        <td class="list"><?=$lang["description"]?></td>
                        <td class="list"><?=$lang["size"]?></td>
                        <td class="list"><?=$lang["modification"]?></td>
                        <td class="list"><?=$lang["comments"]?></td>
                        <td class="list"><?=$lang["date"]?></td>
                        <td class="list"><?=$lang["status"]?></td>
                    </tr>

                    <? while ($row = db_fetch_assoc($garmentuser_disconnected_garments)): ?>
                        <tr class="listnc">
                            <td class="list lpointer" onClick="document.location.href='<?=$pi["filename_next"]?>?ref=<?=$row["garments_id"]?>'" ><?=$row["garments_tag"]?></td>
                            <td class="midlist"><? if(!empty($row["garments_tag2"])){ ?> <?=$row["garments_tag2"]?> <? }else{ ?> <span class="empty"><?=$lang["none"]?></span> <? } ?></td>
                            <td class="list"><?=$row["articles_description"]?></td>
                            <td class="midlist"><?=$row["sizes_name"]?></td>
                            <td class="midlist"><? if(!empty($row["modifications_name"])){ ?> <?=$row["modifications_name"]?> <? }else{ ?> <span class="empty"><?=$lang["none"]?></span> <? } ?></td>
                            <td class="midlist"><? if(!empty($row["log_disconnected_garments_comments"])){ ?> <?=$row["log_disconnected_garments_comments"]?> <? }else{ ?> <span class="empty"><?=$lang["none"]?></span> <? } ?></td>
                            <td class="list"><?=strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($row["log_disconnected_garments_date"]))?></td>
                            <td class="midlist"><? if(!empty($row["garments_scanlocation_id"]) && $row["garments_scanlocation_id"] === $disc_scanlocation){ ?> <?=$lang["active"]?> <? }else{ ?> <?=$lang["inactive"]?> <? } ?></td>
                        </tr>
                    <? endwhile ?>

                </table>
            </span>
            <? else: ?>
            <span class="empty"><?=$lang["none"]?></span>
            <? endif ?>
        </td>
    </tr>
    <? endif ?>
</table>
                                
                                
<script>
function disconnectGarmentDialog(message, garment) {
  var confirm = prompt(message, "");
  if (confirm != null) {
    document.dataform.garment_id_to_cancel.value=garment;
    document.dataform.garment_id_to_cancel_comments.value=confirm;
    document.dataform.submit();
  }
}
</script>