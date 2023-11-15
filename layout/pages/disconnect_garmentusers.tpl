<? if (isset($pi['note']) && $pi['note'] != "") echo $pi['note'] ?>

<? if ($urlinfo['limit_total'] != 0): ?>
    <table class="list">
        <tr class="listtitle">
            <td class="list"><?=$sortlinks['circulationgroup_name']?></td>
            <td class="list"><?=$sortlinks['distributorlocation_name']?></td>
            <td class="list"><?=$sortlinks['count']?></td>
            <td class="list"><?=$lang['disconnect']?></td>
        </tr>
        <? while ($row = db_fetch_assoc($listdata)): ?>
        <form id="<?=$row['distributorlocation_id']?>" enctype="multipart/form-data" method="POST" action="">
            <input type="hidden" name="page" value="details">
            <input type="hidden" name="id" value="<?=$row['distributorlocation_id']?>">
            <input type="hidden" name="gosubmit" value="true">
        </form>
        <tr class="listnc">
            <td class="list"><?=$row['circulationgroup_name']?></td>
            <td class="list"><?=$row['distributorlocation_name']?></td>
            <td class="list"><?=$row['count']?></td>
            <? if (isset($row['count']) && $row['count'] > 0): ?>
            <td class="midlist lpointer" width="25" onclick="if(confirm('<?=$lang["disconnect_confirm_set_disconnected"]?>')){document.getElementById('<?=$row['distributorlocation_id']?>').submit();}else{return false}">
                <img src="layout/images/dialog-ok.png" width="16" height="16" border="0" title="<?=$lang["disconnect"]?>">
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