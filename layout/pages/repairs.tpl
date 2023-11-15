<?=$resultinfo?>

<? if (isset($pi['note']) && $pi['note'] != "") echo $pi['note'] ?>

<? if ($urlinfo['limit_total'] != 0): ?>
<table class="list">
    <tr class="listtitle">
        <td class="list"><?=$sortlinks['description']?></td>
    </tr>
    <? while ($row = db_fetch_assoc($listdata)): ?>
    <form id="<?=$row['id']?>" enctype="multipart/form-data" method="POST" action="<?=$pi['filename_details']?>">
        <input type="hidden" name="page" value="details">
        <input type="hidden" name="id" value="<?=$row['id']?>">
        <input type="hidden" name="gosubmit" value="true">
        <tr class="list" onClick="document.getElementById('<?=$row['id']?>').submit();">
            <td class="list"><?=$row['description']?></td>
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