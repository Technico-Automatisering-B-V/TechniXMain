<div id="tabs">
  <ul>
    <li><a href="#detail"><?=$title?></a></li>
  </ul>

  <div id="detail">
    <table class="detailstab">
      <tr>
        <td>
          <table cellpadding="5" cellspacing="2">
            <tr style="vertical-align: initial;">
              <td>&nbsp;</td>
              <td>Totaal carriers</td>
              <td>Maten carriers</td>
              <td>Dragergebonden<br />carriers</td>
            </tr>

            <?php
            while ($data = db_fetch_assoc($listdata)){?>
                <tr>
                    <td>Station <?=$data["doornumber"]?></td>
                    <td><input type="text" disabled="disabled" value=" <?=$data["hooks"]?>" style="width:60px" /></td>
                    <td><input type="text" disabled="disabled" value=" <?=$data["hooks_sizebound"]?>" style="width:60px" /></td>
                    <td><input type="text" disabled="disabled" value=" <?=$data["hooks_userbound"]?>" style="width:30px" /></td>
                </tr>
            <?php } ?>
          </table>
        </td>
      </tr>
    </table>
  </div>
</div>

<script type="text/javascript">
  $(function() {
    $("ul.css-tabs").tabs("div.css-panes > div");
  });
</script>