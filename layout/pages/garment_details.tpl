<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<? if ($pi["page"] != "add"){ ?>
<form method="POST" id="form_to_missing" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="details">
    <input type="hidden" name="id" value="<?=$detailsdata["id"]?>">
    <input type="hidden" name="missing" value="yes">
</form>

<form method="POST" id="form_to_stock" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="details">
    <input type="hidden" name="id" value="<?=$detailsdata["id"]?>">
    <input type="hidden" name="stock" value="yes">
</form>

<form method="POST" id="form_to_laundry" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="details">
    <input type="hidden" name="id" value="<?=$detailsdata["id"]?>">
    <input type="hidden" name="laundry" value="yes">
</form>
<? } ?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
    <input type="hidden" name="page" value="<?=$pi["page"]?>">
    <?php
    if (!empty($detailsdata["id"])){ ?>
        <input type="hidden" name="id" value="<?=$detailsdata["id"]?>" />
        <input type="hidden" name="editsubmit" value="1" />
        <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
    <? } ?>

    <div id="tabs">
        <ul>
            <li><a href="#tab-common"><?=$lang["common"]?></a></li>
            <? if ($pi["page"] != "add"): ?>
            <li><a href="#tab-history"><?=$lang["history"]?></a></li>
            <? endif ?>
            <? if (!empty($detailsdata["deleted_on"])): ?>
            <li><a href="#tab-undelete"><?=$lang["undelete"]?></a></li>
            <? endif ?>
        </ul>

        <div id="tab-common"><? include("layout/pages/garment_details_tab1.tpl") ?></div>
        <? if ($pi["page"] != "add"): ?>
        <div id="tab-history"><? include("layout/pages/garment_details_tab2.tpl") ?></div>
        <? endif ?>
        <? if (!empty($detailsdata["deleted_on"])): ?>
        <div id="tab-undelete"><? include("layout/pages/garment_details_tab3.tpl") ?></div>
        <? endif ?>

    </div>

    <? if ($pi["page"] == "add"): ?>
    <?=html_submitbuttons_detailsscreen_extra($pi)?>
    <? else: ?>
    <?=html_submitbuttons_detailsscreen($pi)?>
    <? endif?>

</form>

<script type="text/javascript">

    $("#stockButton").on("click", function (e) {
        e.preventDefault();
        $("#form_to_stock").submit();
    });
    
    $("#missingButton").on("click", function (e) {
        e.preventDefault();
        $("#form_to_missing").submit();
    });
    
    $("#laundryButton").on("click", function (e) {
        e.preventDefault();
        $("#form_to_laundry").submit();
    });

<?php
if ($pi["page"] == "add"){
    if (empty($detailsdata["tag"])){ ?>
        focusIt = function() { $("#tag").focus(); };
    <? }elseif (empty($detailsdata["circulationgroup_id"])){ ?>
        focusIt = function() { $("select[name='circulationgroup_id']").focus(); };    
    <? }elseif (empty($bindingdata["article_id"])){ ?>
        focusIt = function() { $("select[name='article_id']").focus(); };
    <? }elseif (empty($bindingdata["size_id"])){ ?>
        focusIt = function() { $("select[name='size_id']").focus(); };
    <? }elseif (empty($bindingdata["modification_id"])){ ?>
        focusIt = function() { $("select[name='modification_id']").focus(); };
    <? }
} ?>

</script>
