<table class="detailstab">
    <? if ($_POST["page"] !== "add"): ?>
    <tr>
        <td class="top right"><?=$lang["distributed_garments"]?>:</td>
        <td class="value">
            <? if (!empty($historydata) && db_num_rows($historydata)): ?>
            <span class="shortlist">
                <table class="list">
                    <tr class="listtitle">
                        <td class="list"><?=$lang["tag"]?></td>
                        <td class="list"><?=$lang["description"]?></td>
                        <td class="list"><?=$lang["size"]?></td>
                        <td class="list"><?=$lang["modification"]?></td>
                        <td class="list"><?=$lang["owner"]?></td>
                        <td class="list"><?=$lang["location"]?></td>
                        <td class="list"><?=$lang["distributed"]?></td>
                    </tr>

                    <? while ($row = db_fetch_assoc($historydata)): ?>
                        <tr class="listnc">
                            <td class="list lpointer" onClick="document.location.href='<?=$pi["filename_next"]?>?ref=<?=$row["garments_id"]?>'" ><?=$row["garments_tag"]?></td>
                            <td class="list"><?=$row["articles_description"]?></td>
                            <td class="midlist"><?=$row["sizes_name"]?></td>
                            <td class="midlist"><? if(!empty($row["modifications_name"])){ ?> <?=$row["modifications_name"]?> <? }else{ ?> <span class="empty"><?=$lang["none"]?></span> <? } ?></td>
                            <?php
                                echo "<td class=\"list\">";
                                if (!empty($row["garmentusers_surname"])) {
                                    echo generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]);
                                } else {
                                    echo "<span class=\"empty\">" . $lang["garment_by_size"] . "</span>";
                                }
                                echo "</td>";
                            ?>
                            <td class="list"><?=$row["distributorlocations_name"]?></td>
                            <td class="list"><?=strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($row["log_garmentusers_garments_starttime"]))?></td>
                        </tr>
                    <? endwhile ?>
                </table>
            </span>
            <div style="border-top: 1px solid #98AAB1;padding-top: 10px;text-align: right;margin-top: 10px;">
                <input type="submit" name="export_history" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
            </div>
            <? else: ?>
            <span class="empty"><?=$lang["none"]?></span>
            <? endif ?>
        </td>
    </tr>
    
    <hr style="color: #98AAB1">
    
    <tr>
        <td class="top right"><?=$lang["deposited_garments"]?>:</td>
        <td class="value">
            <? if (!empty($historydata_deposited) && db_num_rows($historydata_deposited)): ?>
            <span class="shortlist">
                <table class="list">
                    <tr class="listtitle">
                        <td class="list"><?=$lang["tag"]?></td>
                        <td class="list"><?=$lang["description"]?></td>
                        <td class="list"><?=$lang["size"]?></td>
                        <td class="list"><?=$lang["modification"]?></td>
                        <td class="list"><?=$lang["owner"]?></td>
                        <td class="list"><?=$lang["location"]?></td>
                        <td class="list"><?=$lang["depositlocation"]?></td>
                        <td class="list"><?=$lang["deposited"]?></td>
                    </tr>

                    <? while ($row = db_fetch_assoc($historydata_deposited)): ?>
                        <tr class="listnc">
                            <td class="list lpointer" onClick="document.location.href='<?=$pi["filename_next"]?>?ref=<?=$row["garments_id"]?>'" ><?=$row["garments_tag"]?></td>
                            <td class="list"><?=$row["articles_description"]?></td>
                            <td class="midlist"><?=$row["sizes_name"]?></td>
                            <td class="midlist"><? if(!empty($row["modifications_name"])){ ?> <?=$row["modifications_name"]?> <? }else{ ?> <span class="empty"><?=$lang["none"]?></span> <? } ?></td>
                            <?php
                                echo "<td class=\"list\">";
                                if (!empty($row["garmentusers_surname"])) {
                                    echo generate_garmentuser_label($row["garmentusers_title"], $row["garmentusers_gender"], $row["garmentusers_initials"], $row["garmentusers_intermediate"], $row["garmentusers_surname"], $row["garmentusers_maidenname"]);
                                } else {
                                    echo "<span class=\"empty\">" . $lang["garment_by_size"] . "</span>";
                                }
                                echo "</td>";
                            ?>
                            <td class="list"><?=$row["distributorlocations_name"]?></td>
                            <td class="list"><?=$row["depositlocations_name"]?></td>
                            <td class="list"><?=strftime($lang["dB_FULLDATETIME_FORMAT"], strtotime($row["deposited_date"]))?></td>
                        </tr>
                    <? endwhile ?>
                </table>
            </span>
            <div style="border-top: 1px solid #98AAB1;padding-top: 10px;text-align: right;margin-top: 10px;">
                <input type="submit" name="export_history_deposited" value="<?=$lang["export"]?>" title="<?=$lang["export"]?>" />
            </div>
            <? else: ?>
            <span class="empty"><?=$lang["none"]?></span>
            <? endif ?>
        </td>
    </tr>
    <? endif ?>
</table>