<? if ($circulationgroup_count > 1): ?>
    <form name="dataform" enctype="multipart/form-data" method="GET" action="<?=$_SERVER["PHP_SELF"]?>">
        <div class="filter">
            <table>
                <tr>
                    <td class="name"><?=$lang["location"]?>:</td>
                    <td class="value"><?=html_selectbox_submit("cid", $circulationgroups, $urlinfo["cid"], $lang["(all_locations)"], " style=\"width:300px\"")?></td>
                </tr>
            </table>
        </div>
    </form>

    <div class="clear" />
<? endif ?>
<? if ($circulationgroup_count <= 1){ print("<input name=\"cid\" type=\"hidden\" value=\"1\" />"); } ?>

<?=$resultinfo?>

<? if (isset($pi['note']) && $pi['note'] != "") echo $pi['note'] ?>

<? if ($urlinfo['limit_total'] != 0): ?>
<table class="list">
	<tr class="listtitle">
		<td class="list"><?=$sortlinks['date']?></td>
	</tr>
	<? while ($row = db_fetch_assoc($listdata)): ?>
	<tr class="list" onClick="window.location='<?=$pi['filename_details']?>?p=<?=$row['id']?>'">
		<td class="list"><?=$row['date']?></td>
	</tr>
	<? endwhile ?>
</table>

<?=$pagination?>
<? endif ?>
