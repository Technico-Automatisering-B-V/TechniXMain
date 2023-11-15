<table class="detailstab">
	<tr><td class="name"><?=$lang["current_washcount"]?>:</td><td class="value"><?=(($detailsdata["washcount"] == 0) ? "<span class=\"empty\">". $lang["never"] ."</span>" : $detailsdata["washcount"] ."x")?></td></tr>
	<tr><td class="name"><?=$lang["times_repaired"]?>:</td><td class="value"><?=(($counts["repairs"] == 0) ? "<span class=\"empty\">". $lang["never"] ."</span>" : $counts["repairs"] ."x")?></td></tr>
	<tr><td class="name"><?=$lang["times_despeckled"]?>:</td><td class="value"><?=(($counts["despeckles"] == 0) ? "<span class=\"empty\">". $lang["never"] ."</span>" : $counts["despeckles"] ."x")?></td></tr>
	<tr><td class="name"><?=$lang["deposit_date"]?>:</td><td class="value"><?=(($lastdeposit["date"] == "-") ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($lastdeposit["date"])))?></td></tr>
        <? if (!empty($detailsdata["deleted_on"])): ?>
        <tr><td class="name"><?=$lang["deleted_by"]?>:</td><td class="value"><?=(empty($detailsdata["deleted_by"]) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $lang[$detailsdata["deleted_by"]])?></td></tr>
	<tr><td class="name"><?=$lang["reason"]?>:</td><td class="value"><?=(empty($detailsdata["delete_reason"]) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $detailsdata["delete_reason"])?></td></tr>
	<? endif ?>
	<? if (!empty($detailsdata["id"])){ ?>
	<tr>
		<td class="top right"><?=$lang["last_used_by"]?>:</td>
		<td class="value">
			<? if (!empty($historydata) && db_num_rows($historydata)): ?>
			<span class="shortlist">
				<table class="list">
					<tr class="listtitle">
						<td class="list"><?=$lang["surname"]?></td>
                                                <td class="list"><?=$lang["name"]?></td>
                                                <td class="list"><?=$lang["personnelcode"]?></td>
						<td class="list"><?=$lang["distributed"]?></td>
					</tr>
                                        <? while ($row = db_fetch_assoc($historydata)): ?>
                                                <tr class="listnc">
                                                    <td class="list"><?=generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"], $row["garmentusers_personnelcode"])?></td>
                                                    <td class="list"><?=((empty($row["garmentusers_name"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_name"] )?></td>
                                                    <td class="list lpointer" onClick="document.location.href='garmentuser_details.php?ref=<?=$row["garmentusers_id"]?>'" ><?=((empty($row["garmentusers_personnelcode"])) ? "<span class=\"empty\">". $lang["unknown"] ."</span>" : $row["garmentusers_personnelcode"] )?></td>
                                                    <td class="list"><?=strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($row["log_garmentusers_garments_starttime"]))?></td>
						</tr>
					<? endwhile ?>
				</table>
			</span>
			<? else: ?>
			<span class="empty"><?=$lang["noone"]?></span>
			<? endif ?>
		</td>
	</tr>
	<? } ?>
        
        <tr>
		<td class="top right"><?=$lang["last_status"]?>:</td>
		<td class="value">
			<? if (!empty($scanlocation_history) && db_num_rows($scanlocation_history)): ?>
			<span class="shortlist">
				<table class="list">
					<tr class="listtitle">
						<td class="list"><?=$lang["status"]?></td>
						<td class="list"><?=$lang["date"]?></td>
					</tr>
					<? while ($row = db_fetch_assoc($scanlocation_history)): ?>
						<tr class="listnc">
							<td class="list"><?=$lang[$row["status"]]?>
							</td>
							<td class="list"><?=$row["date"]?></td>
						</tr>
					<? endwhile ?>
				</table>
			</span>
			<? else: ?>
			<span class="empty"><?=$lang["noone"]?></span>
			<? endif ?>
		</td>
	</tr>

</table>