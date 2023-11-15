<table class="detailstab">
	<tr>
            <td colspan="2"><strong><?=$lang["userbound_link_system"]?></strong></td>
	</tr>
	<tr>
		<td class="name" style="width: 110px;"><?=$lang["link_garmentuser"]?>:</td>
		<td class="value"><? html_radiobuttons_submit("station_bound_yesno", $station_bound_yesno_options, $station_bound_yesno) ?></td>
	</tr>
	<?if ($station_bound_yesno == 1):?>
	<tr>
		<td class="name">Aantal carriers:</td>
		<td class="value"><?=$station_max_positions?></td>
	</tr>
	<tr>
		<td class="name" valign="top">Koppelen aan:</td>
		<td class="value"><table cellpadding="0" cellspacing="0"><?=$distributor_id_output?></table></td>
	</tr>
	<?endif?>
</table>