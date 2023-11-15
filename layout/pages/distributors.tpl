<? if (isset($pi['note']) && $pi['note'] != "") echo $pi['note'] ?>

<? if ($urlinfo['limit_total'] != 0): ?>
    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$sortlinks['distributorlocations_name']?></td>
            <td class="list"><?=$sortlinks['distributors_doornumber']?></td>
            <td class="list"><?=$sortlinks['distributors_hooks']?></td>
            <td class="list"><?=$lang['clear_load']?></td>
        </tr>
        <? while ($row = db_fetch_assoc($listdata)): ?>
        <form id="<?=$row['distributors_id']?>" enctype="multipart/form-data" method="POST" action="">
            <input type="hidden" name="page" value="details">
            <input type="hidden" name="id" value="<?=$row['distributors_id']?>">
            <input type="hidden" name="gosubmit" value="true">
            <tr class="list">
                <td class="list"><?=$row['distributorlocations_name']?></td>
                <td class="list"><?=$row['distributors_doornumber']?></td>
                <td class="list"><?=$row['distributors_hooks']?></td>
                <td class="midlist" width="25" onclick="if(confirm('<?=$lang["clear_load_confirm"]?>')){document.getElementById('<?=$row['distributors_id']?>').submit();}else{return false}">
                    <img src="layout/images/delete.png" width="14" height="14" border="0" title="<?=$lang["clear_load"]?>" style="cursor: default;">
                </td>
            </tr>
        </form>
        <? endwhile ?>
    </table>
    <?=$pagination?>
<? endif ?>

<script type="text/javascript">
    $(function() {
        $("#search").focus();
    });
</script>