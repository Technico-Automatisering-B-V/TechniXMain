<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" id="mainform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>" />

    <? if (!empty($detailsdata["id"])){ ?>
        <input type="hidden" name="id" value="<?=$detailsdata["id"]?>" />
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
    <? } ?>

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang["fifo_distribution"]?></a></li>
        </ul>

        <div id="tab1">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["location"]?>:</td>
                    <td class="value"><? html_selectbox("circulationgroup_id", $circulationgroups, $detailsdata["circulationgroup_id"], $lang["make_a_choice"]) ?></td>
                </tr>
                <tr>
                    <td class="name"><?=$lang["dayofweek"]?>:</td>
                    <td class="value"><? html_selectbox_array("dayofweek", $days, $detailsdata["dayofweek"], $lang["make_a_choice"], "style='width:100%'") ?></td>
                </tr>
		<tr>
                    <td class="name"><?=$lang["distribution_from"]?>:</td>
                    <td class="value"><? html_selectbox_array("from_hours", $hours, $detailsdata["from_hours"], $lang["make_a_choice"], "style='width:100%'") ?></td>
                </tr>
		<tr>
                    <td class="name"><?=$lang["distribution_to"]?>:</td>
                    <td class="value"><? html_selectbox_array("to_hours", $hours, $detailsdata["to_hours"], $lang["make_a_choice"], "style='width:100%'") ?></td>
                </tr>		
            </table>
        </div>
    </div>

    <?=html_submitbuttons_detailsscreen($pi)?>

</form>

<script type="text/javascript">
    $(function() {
        $("#name").focus();
    });
</script>