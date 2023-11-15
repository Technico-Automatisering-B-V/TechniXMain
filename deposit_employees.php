<?php
require_once "locale/french/language.php";

// Database object
$db = new PDO(
    'mysql:host=localhost;port=3306;dbname=technix_stluc','root','st3chn1xl.sql',
    array(
        PDO::ATTR_PERSISTENT => true,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
    )
);

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Variables
$code = "";
$modalError   = false;
$modalSuccess = false;
$modalMsg = "";

// Check code
if (isset($_POST["code"]) && !empty($_POST["code"]))
{
    $code = trim($_POST["code"]);

    // Get garment from database
    $q = "SELECT COUNT(*)
            FROM `garments`
           WHERE `tag` LIKE '" . $code . "'
              OR `tag2` LIKE '" . $code . "'";

    $s = $db->query($q);
    $c = $s->fetchColumn();

    if ($c > 0) {
        if (depositGarment($code)) {
            $modalMsg = $lang["garment_deposited"];
            $modalSuccess = true;
        } else {
            $modalMsg = $lang["unknown_error"];
            $modalError = true;
        }
    } else {
        $modalMsg = $lang["garment_not_found"];
        $modalError = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>TechniX GS</title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="apple-mobile-web-app-capable" content="yes">

  <link href="layout/new/css/bootstrap.min.css" rel="stylesheet">
  <link href="layout/new/css/bootstrap-responsive.min.css" rel="stylesheet">

  <link href="layout/new/css/font-awesome.min.css" rel="stylesheet">
  <link href="layout/new/css/ui-lightness/jquery-ui-1.10.0.custom.min.css" rel="stylesheet">
  <link href="layout/new/css/base-admin-2.css" rel="stylesheet">
  <link href="layout/new/css/base-admin-2-responsive.css" rel="stylesheet">
  <link href="layout/new/css/pages/distributor.css" rel="stylesheet">
</head>

<body>
  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
      <a class="brand" href="javascript:;">TechniX GS</a>
    </div>
  </div>

  <div class="main">
    <form action="deposit_employees.php" id="depositForm" name="deposit" method="post">

      <div class="account-container stacked">
        <div class="content clearfix">
          <h1><?php echo $lang["scan_barcode"]; ?></h1><br />
          <img src="layout/new/img/barcode.png" width="380" />
        </div>
      </div>
      <input class="code" id="code" name="code" type="text" value="" autocomplete="off" />
    </form>
  </div>

  <?php
  if ($modalError) {
  ?>

    <div class="modal fade hide alert alert-error" id="modal" style="width:600px;background-color:#f2dede;border-color:#eed3d7;">
      <div class="modal-body" style="margin-bottom:0;">
        <p class="text-center"><i class="icon-remove" style="font-size:200px;"></i></p>
        <p class="text-center" style="font-size:30px;"><strong><?php echo $modalMsg; ?></strong></p>
        <p class="text-center"><?php echo $lang["touch_screen_or_new_garment"]; ?></p>
      </div>
    </div>
  <?php
  }

  if ($modalSuccess) {
  ?>

    <div class="modal fade hide alert alert-success" id="modal" style="width:600px;background-color:#dff0d8;border-color:#d6e9c6;">
      <div class="modal-body" style="margin-bottom:0;">
        <p class="text-center"><i class="icon-ok" style="font-size:200px;"></i></p>
        <p class="text-center" style="font-size:30px;"><strong><?php echo $modalMsg; ?></strong></p>
        <p class="text-center"><?php echo $lang["touch_screen_or_new_garment"]; ?></p>
      </div>
    </div>
  <?php
  }
  ?>

  <script src="layout/new/js/libs/jquery-1.8.3.min.js"></script>
  <script src="layout/new/js/libs/jquery-ui-1.10.0.custom.min.js"></script>
  <script src="layout/new/js/libs/bootstrap.min.js"></script>
  <script src="layout/new/js/libs/jquery.timer.js"></script>
  <script src="layout/new/js/manualdeposit.js"></script>
  <script type="text/javascript">
    setTimeout(function() { document.getElementById('code').focus(); }, 10);
  </script>
</body>
</html>

<?php

function depositGarment ($tag, $depositlocation_id=1)
{
    global $db;

    try {
        // Begin transaction
        $db->beginTransaction();

        $uq = "UPDATE `garments`, `depositlocations`
                  SET `garments`.`scanlocation_id` = `depositlocations`.`scanlocation_id`,
                      `garments`.`lastscan` = NOW(),
                      `garments`.`clean` = 0,
                      `garments`.`active` = 1
                WHERE (`garments`.`tag` LIKE '" . $tag . "'
                   OR  `garments`.`tag2` LIKE '" . $tag . "')
                  AND `garments`.`deleted_on` IS NULL
                  AND `depositlocations`.`id` = " . $depositlocation_id;
        $db->exec($uq);

        $dgq = "DELETE `garmentusers_garments`.*
                  FROM `garmentusers_garments`
            INNER JOIN `garments` ON `garmentusers_garments`.`garment_id` = `garments`.`id`
                 WHERE (`garments`.`tag` LIKE '" . $tag . "'
                    OR  `garments`.`tag2` LIKE '" . $tag . "')
                   AND `garments`.`deleted_on` IS NULL";
        $db->exec($dgq);

        $ddq = "DELETE `distributors_load`.*
                  FROM `distributors_load`
            INNER JOIN `garments` ON `distributors_load`.`garment_id` = `garments`.`id`
                 WHERE (`garments`.`tag` LIKE '" . $tag . "'
                    OR  `garments`.`tag2` LIKE '" . $tag . "')
                   AND `garments`.`deleted_on` IS NULL";
        $db->exec($ddq);

        $iq = "INSERT INTO `log_depositlocations_garments`
                    SELECT YEAR(NOW()), DAYOFYEAR(NOW()), `depositlocations`.`id`, `garments`.`id`, NOW()
                      FROM `garments`, `depositlocations`
                     WHERE (`garments`.`tag` LIKE '" . $tag . "'
                        OR  `garments`.`tag2` LIKE '" . $tag . "')
                       AND `garments`.`deleted_on` IS NULL
                       AND `depositlocations`.`id` = " . $depositlocation_id . "
                        ON DUPLICATE KEY UPDATE `log_depositlocations_garments`.`date` = NOW()";
        $db->exec($iq);

        // Commit all queries
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }

    return true;
}

?>