<strong><?=$lang["distributions"]?>:</strong>
<table class="list">
    <tr class="listtitle">
        <td class="list"><?=$lang["tag"]?></td>
        <td class="list"><?=$lang["description"]?></td>
        <td class="list"><?=$lang["size"]?></td>
        <td class="list"><?=$lang["in_use_since"]?></td>
        <td class="list">&nbsp;</td>
    </tr>

    <? while ($row_super = db_fetch_assoc($garmentuser_garments_superuser)): ?>
        <tr class="listnc">
            <td class="list"><?=$row_super["garments_tag"]?></td>
            <td class="list"><?=$row_super["articles_description"]?></td>
            <td class="midlist"><?=$row_super["sizes_name"]?></td>
            <td class="list"><?=strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($row_super["garmentusers_garments_date_received"]))?></td>
            <td class="midlist" width="25" onClick="if(confirm('<?=htmlentities($lang["cancel_distribution_set_missing_more"] . "\\n\\n" . $lang["tag"] . ": " . $row_super["garments_tag"] . "\\n" . $lang["article"] . ": " . $row_super["articles_description"] . ", " . strtolower($lang["size"]) . " " . $row_super["sizes_name"] . "\\n" . $lang["distribution"] . ": " . $row_super["garmentusers_garments_date_received"] . "")?>')){document.dataform.garment_id_to_cancel.value='<?=$row_super["garments_id"]?>';document.dataform.submit();}else{return false}">
                <img src="layout/images/delete.png" width="14" height="14" border="0" title="<?=$lang["cancel_distribution_set_missing"]?>" />
            </td>
        </tr>
    <? endwhile ?>
</table>