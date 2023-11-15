<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>
<input type="hidden" name="issent" id="issent" value="<?=$issent?>" />

<div>
    <form id="contact" enctype="multipart/form-data" method="POST" action="<?=$pi["filename_details"]?>">
        <div class="filter">
            <h3><?=$lang["CONTACT_FORM"]?></h3>
            <table class="detailstab">
                <tr>
                    <td class="right"><?=$lang["message_type"]?>:</td>
                    <td class="value">
                        <?php
                            if (empty($detailsdata["type"]) || $detailsdata["type"] == "failure"){ $failure_select = " checked=\"checked\""; }else{ $failure_select = ""; }
                            if ($detailsdata["type"] == "question"){ $question_select = " checked=\"checked\""; }else{ $question_select = ""; }
                            if ($detailsdata["type"] == "wish"){ $wish_select = " checked=\"checked\""; }else{ $wish_select = ""; }
                        ?>
                        <span class="radioset">
                            <input name="type" id="typeFailure" type="radio" value="failure"<?=$failure_select?>><label for="typeFailure"><?=$lang["failure"]?></label>
                            <input name="type" id="typeQuestion" type="radio" value="question"<?=$question_select?>><label for="typeQuestion"><?=$lang["question"]?></label>
                            <input name="type" id="typeWish" type="radio" value="wish"<?=$wish_select?>><label for="typeWish"><?=$lang["wish"]?></label>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="right"><?=$lang['name']?>:</td>
                    <td class="value" width="365">
                        <input type="text" name="name" value="<?=$detailsdata["name"]?>" size="30">
                        <button class="required" title="<?=$lang['field_required']?>">*</button>
                    </td>
                </tr>
                <tr>
                    <td class="right"><?=$lang['phone_number']?>:</td>
                    <td class="value" width="365">
                        <input type="text" name="phone" value="<?=$detailsdata["phone"]?>" size="30">
                    </td>
                </tr>
                <tr>
                    <td class="right"><?=$lang['email_address']?>:</td>
                    <td class="value" width="365">
                        <input type="text" name="mail" value="<?=$detailsdata["mail"]?>" size="30">
                    </td>
                </tr>
                <tr>
                    <td class="right"><?=$lang['file']?>:</td>
                    <td class="value" width="365">
                         <input type="text" id="filename" value="" size="30" readonly>
                         <input type="file" id="sendfile" name="sendfile" style="display:none;"/>
                         <input type="button" value="<?=$lang["select_file"]?>" onclick="document.getElementById('sendfile').click();" />
                    </td>
                </tr>
                <tr>
                    <td class="name top"><?=$lang['message_text']?>:</td>
                    <td class="value" width="365">
                        <textarea rows="6" name="message" cols="32"><?=$detailsdata["message"]?></textarea>
                        <button class="required" style="vertical-align: top;" title="<?=$lang['field_required']?>">*</button>
                    </td>
                </tr>
            </table>                             

            <div class="buttons">
                <input type="submit" name="send" value="<?=$lang['send_message']?>" title="<?=$lang['send_message']?>" />
            </div>
        </div>
    </form>
</div>        

<div class="clear" />  

<div class="filter" style="text-align: center;">
    <?=$lang['in_case_of_malfunctions_and_or_questions_that_can_not_wait_please_call_us_at']?><br /><br />
    <?=$lang['on_working_days_between_8_30_and_17_00_by_phone_number']?><br />
    <h3 style="text-align: center;">003171-5424344</h3><br />
    <?=$lang['outside_office_hours_and_on_weekends_by_phone_number']?><br />
    <h3 style="text-align: center;">00316-53743518</h3>
</div>

<script type="text/javascript">
    $("#sendfile").on("change", function() {
        var file = this.files[0],
        fileName = file.name;
        document.getElementById('filename').value = fileName;  
    });
</script>



<script type="text/javascript">
$(document).ready(function() {
    var modal = $("#modal");
    var issent_input =  $("input[name='issent']");
    var issent = issent_input.val();
    if (issent != 'false'){
        $(modal).attr("title", "<?=$lang["message_sent"]?>").html("<p style='height: 22px;'><span class='ui-icon ui-icon-info' style='float:left; margin:0 7px 0 0;'></span><?=$lang["we_strive_to_answer_your_message_within_4_hours"]?></p>");
        $(modal).dialog({
            resizable: false,
            height: 140,
            width: 350,
            modal: true,
            buttons: {
                "<?=$lang["close"]?>": function() {
                    $(this).dialog("close");
                }
            }
        });
    }
});
</script>