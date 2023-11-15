<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="x-ua-compatible" content="IE=8" >
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>TechniX - <?=$pi["title"]?></title>
        <link href="layout/common/custom-theme/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css" />
        <link href="layout/common/jquery.ui.spinner.css" rel="stylesheet" type="text/css" />
        <link href="layout/common/jquery.ui.tooltip.css" rel="stylesheet" type="text/css" />
        <link href="layout/common/style.css" rel="stylesheet" type="text/css" />
        <link href="layout/common/Chart.min.css" rel="stylesheet" type="text/css" />
        <link href="layout/images/favicon.ico" rel="shortcut icon" />

        <script type="text/javascript" src="layout/common/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="layout/common/jquery-ui.min.js"></script>
        <script type="text/javascript" src="layout/common/jquery.ui.datepicker-nl.js"></script>
        <script type="text/javascript" src="layout/common/jquery.cookie.js"></script>
        <script type="text/javascript" src="layout/common/jquery.floatheader.min.js"></script>
        <script type="text/javascript" src="layout/common/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="layout/common/popup.js"></script>
        <script type="text/javascript" src="layout/common/Chart.js"></script>
        <script type="text/javascript" src="layout/common/moment-with-locales.js"></script>
        <script type="text/javascript" src="layout/common/Gauge.js"></script>
        <script type="text/javascript" src="layout/common/palette.js"></script>

    </head>

    <?
    //do we need the menubar?
    $menubar = ($pi["page"] == "list" || $pi["page"] == "add" || $pi["page"] == "details" || $pi["page"] == "simple") ? true : false;
    //do we need the toolbar?
    $toolbar = ($pi["page"] == "list" || $pi["page"] == "add" || $pi["page"] == "details") ? true : false;
    ?>
    <script language="javascript" type="text/javascript">function focusIt(){}</script>   
    <body onLoad = "focusIt()">
        <div id="modal"></div>
        <? if ($pi["page"] == "report"): ?>
            <?=$page_content?>
        <? else: ?>
        <table class="page">
            <tr class="topbar">
                <td class="topbar">
                    <table class="topbarcontent">
                        <tr>
                            <td class="logo">
                                <img class="logo" src="layout/images/xgs_topbar_logo_gs.png">
                            </td>
                            <td class="head">
                                <? if (isset($pi["group"])) echo $pi["group"] ?>
                                <? if (isset($pi["group"]) && isset($pi["title"])) echo "&raquo;" ?>
                                <? if (isset($pi["title"])) echo $pi["title"] ?>
                                <? if (isset($pi["title"]) && isset($pi["subtitle"])) echo "&raquo;" ?>
                                <? if (isset($pi["subtitle"])) echo $pi["subtitle"] ?>
                            </td>
                            <td class="logout">
                                <? if (isset($_SESSION["username"])): ?>
                                    <?=$lang["you_are_logged_in"]?> - TechniX <?=$config["version"]?><br />
                                    <form name="topbar_user_details" enctype="multipart/form-data" method="POST" action="user_details.php">
                                        <input type="hidden" name="page" value="details" />
                                        <input type="hidden" name="id" value="<?=$_SESSION["userid"]?>" />
                                        <input type="hidden" name="gosubmit" value="true" />
                                        <a class="link" onClick="document.topbar_user_details.submit()"><?=$_SESSION["username"]?></a>
                                    </form>
                                    [<a href="login.php?logout"><?=$lang["logout"]?></a>]
                                <? else: ?>
                                    &nbsp;
                                <? endif ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%">
            <tr>
            <?php if (basename($_SERVER["PHP_SELF"]) !== "login.php" && basename($_SERVER["PHP_SELF"]) !== "dashboard_tv.php"){ ?>
                <td class="menubar" style="width:500px">
                    <?=$menu_content?>
                </td>
                <td class="content" width="100%">
            <?php }else{ ?>
                <td class="content">
            <?php } ?>
                    <? if ($toolbar): ?>
                        <?=$toolbar_content?>
                    <? endif ?>
                   <?=$page_content?>
                </td>
            </tr>
        </table>
        <table class="copyright">
            <tr>
                <td>Copyright &copy; 2006-<?=date("Y")?> Technico Automatisering B.V.&nbsp;</td>
            </tr>
        </table>
        <? endif ?>

        <script type="text/javascript">
            $(document).ready(function() {
                var modal = $("#modal");

                var exportcodes = $("#exportcodes");
                var index_export = $.cookie("exportcodes");
                var active_export;
                if (index_export !== undefined) {
                    active_export = exportcodes.find("h3:eq(" + index_export + ")");
                }
                exportcodes.accordion({
                    header: "h3",
                    active: active_export,
                    autoHeight: false,
                    change: function(event, ui) {
                        var index_export = $(this).find("h3").index ( ui.newHeader[0] );
                        $.cookie("exportcodes", index_export);
                    }
                });

                $("form[name='dataform'] input, form[name='dataform'] select, form[name='dataform'] textarea").change(function() {
                    var content_changed = $("#content-changed");
                    var changed = content_changed.val();
                    if (changed != undefined){
                        content_changed.val("1");
                    }
                });

                $("#btn-back-to-list, input[name='detailssubmitnone']").click(function(e){
                    var changed = $("#content-changed").val();
                    if (changed != undefined){
                        if (changed == "1"){
                            e.preventDefault();
                            $(modal).attr("title", "<?=$lang["data_changed"]?>").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 0 0;'></span><?=$lang["want_save_changes"]?></p>");
                            $(modal).dialog({
                                resizable: false,
                                height: 140,
                                width: 350,
                                modal: true,
                                buttons: {
                                    "<?=$lang["no"]?>": function() {
                                        $(this).dialog("close");
                                        $("#frm-back-to-list").submit();
                                    },
                                    "<?=$lang["yes"]?>": function() {
                                        var inputSubmit = $("<input>").attr("type", "hidden").attr("name", "detailssubmit").val("1");
                                        $("form[name='dataform']").append($(inputSubmit));
                                        $(this).dialog("close");
                                        $("form[name='dataform']").submit();
                                    }
                                }
                            });
                        }
                    }
                });

                $(".radioset").buttonset();
                $(".float").floatHeader({recalculate:true});
                $(".spinner").spinner({ max : 365 });
                $("button.required").tooltip({
                    position: { my: "left+5" }
                });

                $("#menu a").click(function(){
                    $.cookie("ui-tabs-1", null);
                    $.cookie("exportcodes", null);
                });

                $("img[src*='delete.png']").hover(function(){
                    $(this).css("cursor", "pointer");
                }, function(){
                    $(this).css("cursor", "default");
                });

                $("input[name*='detailssubmit']").click(function(){
                    $.cookie("ui-tabs-1", null);
                });

                $("#tabs").tabs({
                    cookie: {
                        expires: 1
                    }
                });

                $("input:submit, input:button, input:reset").button();

                $("button.arrowreturnthick-1-s").button({ icons: { primary: "ui-icon-arrowreturnthick-1-s" } });
                $("button.arrowreturnthick-1-w").button({ icons: { primary: "ui-icon-arrowreturnthick-1-w" } });
                $("button.closethick-nt").button({ icons: { primary: "ui-icon-closethick" } });
                $("button.plusthick").button({ icons: { primary: "ui-icon-plusthick" } });
                $("button.search").button({ icons: { primary: "ui-icon-search" } });
                $("button.trash").button({ icons: { primary: "ui-icon-trash" } });
                $("button.required").button({
                    icons:{
                        primary: "ui-icon-info"
                    },
                    text: false
                }).hover(function(){
                    $(this).css("cursor","help")
                }).click(function(e){
                    e.preventDefault();
                });

                $("input.date").datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'yy-mm-dd'
                });

                var userpanel = $("#menu");
                var index = $.cookie("menu");
                var active;
                if (index !== undefined) {
                    active = userpanel.find("h3:eq(" + index + ")");
                }
                userpanel.accordion({
                    header: "h3",
                    active: active,
                    autoHeight: false,
                    change: function(event, ui) {
                        var index = $(this).find("h3").index ( ui.newHeader[0] );
                        $.cookie("menu", index);
                        $.cookie("ui-tabs-1", null);
                    }
                });
            });
        </script>
    </body>
</html>
