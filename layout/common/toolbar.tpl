<table class="toolbar">
	<tr>
            <td>
		<? if (($pi["page"] == "list" || $pi["page"] == "details") && !isset($pi["toolbar"]["no_new"])): ?>
                    <div style="display:inline; float:left;">
                        <form enctype="multipart/form-data" method="POST" action="<?=$pi["filename_details"]?>">
                        <input type="hidden" name="page" value="add" />
                        <input type="hidden" name="gosubmit" value="yes" />
                            <button type="submit" class="plusthick"><?=$lang["new"]?></button>
                        </form>
                    </div>
		<? endif ?>

		<? if ($pi["page"] == "add" || ($pi["page"] == "details" && !isset($pi["toolbar"]["no_list"]))): ?>
                    <div style="display:inline; float:left;">
                        <form enctype="multipart/form-data" id="frm-back-to-list" method="POST" action="<?=$pi["filename_list"]?>">
                            <button type="submit" id="btn-back-to-list" class="arrowreturnthick-1-w"><?=$lang["back_to_list"]?></button>
                        </form>
                    </div>
		<? endif ?>
                
                <? if ($pi["page"] == "add" || ($pi["page"] == "details" && !isset($pi["toolbar"]["no_list"]))): ?>
                    <div style="display:inline; float:left;">
                        <form enctype="multipart/form-data" id="frm-back-to-previous" action="javascript:history.go(-1)">
                            <button type="submit" id="btn-back-to-previous" class="arrowreturnthick-1-w"><?=$lang["back_to_previous"]?></button>
                        </form>
                    </div>
                <? endif ?>
		<? if ($pi["page"] == "details" && !isset($pi["toolbar"]["no_delete"])): ?>
                    <div style="display:inline; float:left;">
                        <form enctype="multipart/form-data" method="POST" action="<?=$pi["filename_details"]?>">
                        <input type="hidden" name="page" value="details" />
                        <input type="hidden" name="id" value="<?=$urlinfo["id"]?>" />
                        <input type="hidden" name="delete" value="yes" />
                        <input type="hidden" name="gosubmit" value="yes" />
                            <button type="submit" class="trash"><?=$lang["delete"]?></button>
                        </form>
                    </div>
		<? endif ?>

		<? if ($pi["page"] == "list" && !isset($pi["toolbar"]["no_search"])): ?>
                    <div style="display:inline; float:left;">
                        <form name="searchform" enctype="multipart/form-data" method="GET" action="<?=$pi["filename_list"]?>">
                        &nbsp;<input type="text" name="search" id="search" value="<?=((isset($urlinfo["search"])) ? $urlinfo["search"] : "")?>" title="<?=$lang["enter_text_to_search_for"]?>" />
                        <? if (isset($urlinfo["search"])): ?>
                            <button type="button" class="closethick-nt" onClick="window.location.href='<?=$pi["filename_list"]?>'"><?=$lang["reset_search_command"]?></button>
                        <? endif ?>
                        <button type="submit" class="search"><?=$lang["search"]?></button>
                        </form>

                        <? if (isset($pi["toolbar_extra"])): ?>
                            <?=$pi["toolbar_extra"]?>
                        <? endif ?>
                    </div>
                <? endif ?>

                <? if ($pi["page"] == "list" && isset($pi["toolbar"]["export"])): ?>
                    <div style="display:inline; float:left;">
                        <form enctype="multipart/form-data" method="POST" action="<?=$pi["filename_list"]?>?<?=$_SERVER['QUERY_STRING']?>">
                        <input type="hidden" name="page" value="list" />
                        <?php
                        if (isset($_POST)){
                            foreach ($_POST as $key => $value) {
                                echo "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\" />";
                            }
                        }
                        ?>
                        <input type="hidden" name="export" value="yes" />
                            <button type="submit" class="arrowreturnthick-1-s"><?=$lang["export"]?></button>
                        </form>
                    </div>
                <? endif ?>

                <? if ($_SESSION["username"] === "Technico" &&
                       $pi["page"] == "details" &&
                       $pi["toolbar"] !== "no" &&
                       isset($pi["toolbar"]["full_delete"])): ?>
                    <div style="display:inline; float:left;">
                        <form enctype="multipart/form-data" method="POST" action="<?=$pi["filename_details"]?>">
                        <input type="hidden" name="page" value="details" />
                        <input type="hidden" name="id" value="<?=$urlinfo["id"]?>" />
                        <input type="hidden" name="delete" value="yes" />
                        <input type="hidden" name="full_delete" value="yes" />
                        <input type="hidden" name="gosubmit" value="yes" />
                            <button type="submit" class="trash"><?=$lang["full_delete"]?></button>
                        </form>
                    </div>
		<? endif ?>
            </td>
	</tr>
</table>