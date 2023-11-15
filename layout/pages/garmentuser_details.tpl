<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" id="mainform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">

<input type="hidden" name="currentTab" value="<?=$_POST["currentTab"]?>" />
<input type="hidden" name="page" value="<?=$pi["page"]?>" />
<input type="hidden" name="editsubmit" value="1" />
<input type="hidden" name="old_profession_id" value="<?=$gu_data["profession_id"]?>" />
<input type="hidden" name="garment_id_to_cancel" value="" />
<input type="hidden" name="garment_id_to_cancel_comments" value="" />
<input type="hidden" name="garment_id_to_unbound" value="" />
<input type="hidden" name="article_id_to_unbound" value="" />

<? if (!empty($gu_data["id"])){ ?>
    <input type="hidden" name="id" value="<?=$gu_data["id"]?>" />
    <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
<? }

$deleted = ((!empty($gu_data["deleted_on"])) ? true : false) ?>

<script type="text/javascript">
    $(function() {

        $("#mainform").keypress(function(e) {
            if (e.which == 13) {
                return false;
            }
        });

        $("input[name='service_off_switch']").click(function() {
            var value = this.value;
            if (value == "unlimited") {
                $("input[name='date_service_off']").attr("disabled", "disabled");
                $("#date_service_off_wrapper").hide();
            } else if (value == 'date') {
                $("input[name='date_service_off']").removeAttr("disabled");
                $("#date_service_off_wrapper").show();
            }
        });

        $("input[name='service_on_switch']").click(function() {
            var value = this.value;
            if (value == "unlimited") {
                $("input[name='date_service_on']").attr("disabled", "disabled");
                $("#date_service_on_wrapper").hide();
            } else if (value == 'date') {
                $("input[name='date_service_on']").removeAttr("disabled");
                $("#date_service_on_wrapper").show();
            }
        });

        $("input[name='timelockoption']").click(function() {
            var option = this.value;
            if (option == "professiondefault") {
                $("#timelock").attr("disabled", "disabled").attr("class", "disabled");
            } else if (option == 'owntimelock') {
                $("#timelock").removeAttr("disabled").removeAttr("class");
            }
        });

        $("input[name='warningoption']").click(function() {
            var option = this.value;
            if (option == "professiondefault") {
                $("#daysbeforewarning").attr("disabled", "disabled").attr("class", "disabled");
            } else if (option == 'ownwarning') {
                $("#daysbeforewarning").removeAttr("disabled").removeAttr("class");
            }
        });

        $("input[name='blockageoption']").click(function() {
            var option = this.value;
            if (option == "professiondefault") {
                $("#daysbeforelock").attr("disabled", "disabled").attr("class", "disabled");
            } else if (option == 'ownblockage') {
                $("#daysbeforelock").removeAttr("disabled").removeAttr("class");
            }
        });

        $("input[name='garmentlink_enabled']").click(function() {
            var option = this.value;
            if (option == "1") {
                $("#garmentlink_table").show();
            } else if (option == "2") {
                $("#garmentlink_table").hide();
            }
        });
        
        $("input[name='articlelink_enabled']").click(function() {
            var option = this.value;
            if (option == "1") {
                $("#articlelink_table").show();
            } else if (option == "2") {
                $("#articlelink_table").hide();
            }
        });
        
        $("input[name='articlelink_combined_creditOn']").click(function() {
            var option = this.value;
            if (option == "0") {
                $("#articlelink_combined_credit").attr("disabled", "disabled").attr("class", "disabled");
            } else if (option == '1') {
                $("#articlelink_combined_credit").removeAttr("disabled").removeAttr("class");
            }
        });

    });
</script>

<div id="tabs">
    <ul>
        <li><a href="#tab1"><?=$lang["garmentuserdetails"]?></a></li>
        <?php
        if (empty($gu_data["deleted_on"])){
            print("<li><a href=\"#tab2\">". $lang["sizebound"] ."</a></li>");
            if ($pi["page"] !== "add"){
                print("<li><a href=\"#tab5\">". $lang["userbound"] ."</a></li>");
                print("<li><a href=\"#tab4\">". $lang["preferences"] ."</a></li>");
            }
        }
        if ($pi["page"] !== "add"){
            print("<li><a href=\"#tab3\">". $lang["clothes"] ."</a></li>");
        }
        if (empty($gu_data["deleted_on"])){
            if ($pi["page"] !== "add"){
                if (!empty($garmentuser_garments_superuser) && db_num_rows($garmentuser_garments_superuser)){
                    print("<li><a href=\"#tab7\">Superpas</a></li>");
                }
                print("<li><a href=\"#tab8\">". $lang["history"] ."</a></li>");
                if (!empty($superuser_historydata) && db_num_rows($superuser_historydata)){
                    print("<li><a href=\"#tab9\">". $lang["history"] ." Superpas</a></li>");
                }
            }
        }
        else
        {
             print("<li><a href=\"#tab6\">". $lang["undelete"] ."</a></li>");
        }
        ?>
    </ul>

    <div id="tab1"><? include("layout/pages/garmentuser_details_tab1.tpl"); ?></div>
    <?php if (empty($gu_data["deleted_on"])){?>
    <div id="tab2"><? include("layout/pages/garmentuser_details_tab2.tpl"); ?></div>
    <?php if ($pi["page"] !== "add"){?>
        <div id="tab5"><? include("layout/pages/garmentuser_details_tab5.tpl"); ?></div>
        <div id="tab4"><? include("layout/pages/garmentuser_details_tab4.tpl"); ?></div>
    <? }
    }
    if ($pi["page"] !== "add"){?>
        <div id="tab3"><? include("layout/pages/garmentuser_details_tab3.tpl"); ?></div>
    <? }
    if (empty($gu_data["deleted_on"])){
        if ($pi["page"] !== "add"){?>
        <?php if (!empty($garmentuser_garments_superuser) && db_num_rows($garmentuser_garments_superuser)){ ?>
        <div id="tab7"><? include("layout/pages/garmentuser_details_tab_super.tpl"); ?></div>
        <?php } ?>
        <div id="tab8"><? include("layout/pages/garmentuser_details_tab7.tpl"); ?></div>
        <?php if (!empty($superuser_historydata) && db_num_rows($superuser_historydata)){ ?>
        <div id="tab9"><? include("layout/pages/garmentuser_details_tab9.tpl"); ?></div>
        <?php } ?>
    <? }
    }
    if (!empty($gu_data["deleted_on"])){ ?>
        <div id="tab6"><? include("layout/pages/garmentuser_details_tab6.tpl"); ?></div>
    <? } ?>
</div>

<?php
if ($pi["page"] == "add"){
    html_submitbuttons_detailsscreen_garmentuser($pi);
} else {
    html_submitbuttons_detailsscreen($pi);
}
?>

</form>
