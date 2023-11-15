<? if (isset($pi['note']) && $pi['note'] != "") echo $pi['note'] ?>

<? if ($urlinfo['limit_total'] != 0): ?>
    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$sortlinks['distributorlocations_name']?></td>
            <td class="list"><?=$sortlinks['depositlocations_name']?></td>
            <td class="list"><?=$lang['garments']?></td>
            <td class="list"><?=$lang['clear_depositlocation']?></td>
        </tr>
        <? while ($row = db_fetch_assoc($listdata)): ?>
        <form id="<?=$row['depositlocations_id']?>" enctype="multipart/form-data" method="POST" action="">
            <input type="hidden" name="page" value="details">
            <input type="hidden" name="id" value="<?=$row['depositlocations_id']?>">
            <input type="hidden" name="gosubmit" value="true">
        </form>
        <tr class="listnc">
            <td class="list"><?=$row['distributorlocations_name']?></td>
            <td class="list"><?=$row['depositlocations_name']?></td>
            <td class="list"><?=(isset($deposit_garments[$row['depositlocations_scanlocation_id']])?$deposit_garments[$row['depositlocations_scanlocation_id']]:'0')?></td>
            <? if (isset($deposit_garments[$row['depositlocations_scanlocation_id']])): ?>
            <td class="midlist lpointer" width="25" onclick="if(confirm('<?=$lang["clear_depositlocation_confirm"]?>')){document.getElementById('<?=$row['depositlocations_id']?>').submit();}else{return false}">
                <img src="layout/images/dialog-ok.png" width="16" height="16" border="0" title="<?=$lang["clear_depositlocation"]?>">
            </td>
            <? else: ?>
            <td class="midlist" width="25">
                <img src="layout/images/dialog-error.png" width="16" height="16" border="0" style="cursor: default;">
            </td>
            <? endif ?>
        </tr>
        
        <? endwhile ?>
    </table>
<? endif ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>