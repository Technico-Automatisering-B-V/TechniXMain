<? if (!empty($pi["note"])) echo $pi["note"] ?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>" />

    <? if (!empty($detailsdata["id"])){ ?>
        <input type="hidden" name="id" value="<?=$detailsdata["id"]?>" />
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
    <? } ?>

    <div id="tabs">
        <ul>
            <li><a href="#tab1"><?=$lang["repair"]?></a></li>
        </ul>
        <div id="tab1">
            <table class="detailstab">
                <tr>
                    <td class="name"><?=$lang["description"]?>:</td>
                    <td class="value"><input type="text" id="description" name="description" value="<?=$detailsdata["description"]?>" size="30" /></td>
                </tr>
            </table>
        </div>
    </div>

    <?=html_submitbuttons_detailsscreen($pi)?>

</form>

<script type="text/javascript">
    $(function() {
        $("#description").focus();
    });
</script>