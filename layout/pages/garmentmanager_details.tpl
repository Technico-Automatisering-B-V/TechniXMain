<?php
if (!empty($pi["note"])){ echo $pi["note"]; }
?>

<form name="dataform" enctype="multipart/form-data" method="POST" action="<?=$_SERVER["PHP_SELF"]?>">
  <input type="hidden" name="page" value="<?=$pi["page"]?>">
  <input type="hidden" name="deleted_on" value="<?=$detailsdata["deleted_on"]?>">

  <? if (!empty($detailsdata["garmentuser_id"])){ ?>
      <input type="hidden" name="old_garmentuser_id" value="<?=$detailsdata["garmentuser_id"]?>">
      <input type="hidden" name="content-changed" id="content-changed" value="<?=(isset($_POST["content-changed"]) ? 1 : 0)?>" />
  <? } ?>

  <div id="tabs">
    <ul>
      <li><a href="#tab1"><?=$lang["garmentmanager"]?></a></li>
    </ul>

    <div id="tab1">
      <table class="detailstab">
        <tr>
          <td class="name"><?=$lang["name"]?>:</td>
          <td class="value"><? generate_garmentusers_select($detailsdata["garmentuser_id"], $lang["make_a_choice"]) ?></td>
          <td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
        </tr>
        <tr>
          <td class="name"><?=$lang["credit"]?>:</td>
          <td class="value"><input type="text" id="maxcredit" name="maxcredit" value="<?=$detailsdata["maxcredit"]?>" size="2"> <?=strtolower($lang["garments"])?></td>
          <td><button class="required" title="<?=$lang["field_required"]?>">*</button></td>
        </tr>
        <?php
        $limselnone = "";
        $limselpro = "";
        $limselart = "";

        if ($detailsdata["limit_to_articles"] === "1") {
            $limselart = " selected=\"selected\"";
        } elseif ($detailsdata["limit_to_profession"] === "1") {
            $limselpro = " selected=\"selected\"";
        } else {
            $limselnone = " selected=\"selected\"";
        }
        ?>
        <tr>
          <td class="name"><?=$lang["limitation"]?>:</td>
          <td class="value">
            <select id="selLimitation" name="limitation">
              <option value="0"<?=$limselnone?>>Geen</option>
              <option value="to_profession"<?=$limselpro?>>Tot beroep</option>
              <option value="to_articles"<?=$limselart?>>Tot artikelen</option>
            </select>

          </td>
          <td>&nbsp;</td>
        </tr>
        <?php
        if ($detailsdata["allow_normaluser"] === "y"){ $c_anu = " checked=\"checked\""; } else { $c_anu = ""; }
        if ($detailsdata["allow_supercard"] === "y"){ $c_asc = " checked=\"checked\""; } else { $c_asc = ""; }
        if ($detailsdata["allow_supername"] === "y"){ $c_asn = " checked=\"checked\""; } else { $c_asn = ""; }
        if ($detailsdata["allow_station"] === "y"){ $c_ast = " checked=\"checked\""; } else { $c_ast = ""; }
        if ($detailsdata["allow_overloaded"] === "y"){ $c_aso = " checked=\"checked\""; } else { $c_aso = ""; }
        ?>
        <tr>
          <td class="name top"><?=$lang["privileges"]?>:</td>
          <td class="value">
              <input id="chkNormaluser" name="allow_normaluser" type="checkbox" value="y"<?=$c_anu?> /> <?=$lang["personal_distribution"]?><br />
              <input id="chkSupercard" name="allow_supercard" type="checkbox" value="y"<?=$c_asc?> /> <?=$lang["super_distribution"]?><br />
              <input id="chkSupername" name="allow_supername" type="checkbox" value="y"<?=$c_asn?> /> <?=$lang["distribution_by_name"]?><br />
              <input id="chkStation" name="allow_station" type="checkbox" value="y"<?=$c_ast?> /> <?=$lang["super_distribution_per_station"]?><br />
              <input id="chkOverloaded" name="allow_overloaded" type="checkbox" value="y"<?=$c_aso?> /> <?=$lang["super_distribution_overloaded"]?>
          </td>
          <td>&nbsp;</td>
        </tr>

        <tr class="trArticles">
          <td class="name top"><?=$lang["articles"]?>:</td>
          <td>
              <div id="exportcodes">
                <?php
                    while ($row = db_fetch_assoc($articles)){                     
                            echo "<span id=\"". $row["id"] ."\"></span>";
                            echo "<h3><a href=\"#\">". $row["description"] ." (". $row["articlenumber"] .")</a></h3>";
                            echo "<div class=\"fpanel\">";
                                $arsimos_query = "SELECT
                                            `arsimos`.`id` AS 'arsimo_id',
                                            IFNULL(CONCAT(`sizes`.`name`, ' ', `modifications`.`name`),`sizes`.`name`) AS 'size_name'
                                        FROM `arsimos`
                                        INNER JOIN `articles` ON `arsimos`.`article_id` = articles.id
                                        INNER JOIN `sizes` ON `arsimos`.`size_id` = sizes.id
                                        LEFT JOIN `modifications` ON `arsimos`.`modification_id` = `modifications`.`id`
                                        WHERE ISNULL(`arsimos`.`deleted_on`) AND `arsimos`.`article_id` = ". $row["id"] ."
                                        ORDER BY `sizes`.`position` ASC";

                                $arsimos_sql = db_query($arsimos_query) or die("ERROR LINE ". __LINE__);

                                if (db_num_rows($arsimos_sql) > 0){
                                        

                                            while ($arsimos_result = db_fetch_assoc($arsimos_sql)){
                                                $chk = "";
                                                if (is_array($arsimosEnabled) && array_key_exists($arsimos_result["arsimo_id"], $arsimosEnabled)) {
                                                  $chk = " checked=\"checked\"";
                                                }

                                                echo "<input name=\"chkArsimos[]\" type=\"checkbox\" value=\"". $arsimos_result["arsimo_id"] ."\"". $chk ." /> ". $arsimos_result["size_name"] ."<br />";
                                            }

                                        echo "<div class=\"buttons\">";
                                            echo "<input type=\"button\" class=\"select-all2\" value=\"". $lang["fill_all"] ."\" />";
                                        echo "</div>";


                                }else{
                                    echo $lang["no_items_found"];
                                }

                                echo "</div>";
                        }
                ?>
            </div>  
          </td>
        </tr>

        <? if ($detailsdata["deleted_on"] != NULL): ?>
        <tr>
          <td class="name"><?=$lang["deleted"]?>:</td>
          <td class="value"><input name="deleted" type="checkbox" checked="checked" value="1" /></td>
        </tr>
        <? endif ?>
      </table>
    </div>
  </div>

  <?=html_submitbuttons_detailsscreen($pi)?>

</form>

<script type="text/javascript">
  $(function() {
    triggerArticles();

    $("#maxcredit").focus();
    $("#selLimitation").on("change", function (e) {
      triggerArticles();
    });
  });

  function triggerArticles() {
    if ($("#selLimitation").val() === "to_articles") {
      //$("#chkSupercard").prop("checked", true);
      //$("#chkSupercard").prop("disabled", true);
      $(".trArticles").show();
    } else {
      //$("#chkSupercard").prop("disabled", false);
      $(".trArticles").hide();
    }
  }
  
  $(".select-all2").on("click", function() {
        $(this).closest("div.fpanel").find(':checkbox').each(function() {
            $(this).attr('checked', 'checked');
        });
    });
</script>