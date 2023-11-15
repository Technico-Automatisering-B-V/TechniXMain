<?php

/***************************************************************************
 *
 *   File:           : localeparser.php
 *   Copyright       : (c) 2006-2009 Technico Automatisering B.V.
 *   Email           : support@technico.nl
 *
 *   This program is not free software; it is not to be redistributed and/or
 *   modified. Property of Technico Automatisering B.V., The Netherlands.
 *
 ***************************************************************************/


if (isset($_SESSION['locale_map'])) {
	require_once('locale/' . $_SESSION['locale_map'] . '/language.php');
}
elseif (isset($pi["page"]) && $pi["page"] == "login" && !isset($_POST["loginsubmit"]))
{
    $l_sql = mysqli_query($_SESSION["conn"], "SELECT
                    locales.locale_map AS 'locale_map'
                    FROM
                    settings
                    INNER JOIN locales ON settings.`value` = locales.id
                    WHERE settings.`name` = 'default_locale_id'");
    $localedata = mysqli_fetch_object($l_sql);

    $_SESSION['locale_map'] = $localedata['locale_map'];
    require_once('locale/' . $localedata['locale_map'] . '/language.php');
}
else
{
    if (isset($_SESSION['locale_id']))
    {
        $localedata = db_fetch_assoc(db_read_row_by_id("locales", $_SESSION['locale_id']));
        if (!empty($localedata))
        {
            $_SESSION['locale_map'] = $localedata['locale_map'];
            require_once('locale/' . $localedata['locale_map'] . '/language.php');
        } else {
            echo "Locale data could not be read. Please inform the Technico helpdesk.";
            require_once('locale/english/language.php');
        }
    } else {
        $settings_defaultlocaleid = db_fetch_assoc(db_read_where("settings", "name", "default_locale_id"));
        if (!empty($settings_defaultlocaleid['value']))
        {
            $localedata = db_fetch_assoc(db_read_row_by_id("locales", $settings_defaultlocaleid['value']));
            if (!empty($localedata)) {
                require_once('locale/' . $localedata['locale_map'] . '/language.php');
            } else {
                echo "Default locale value could not be read. Please inform the Technico helpdesk.";
                require_once('locale/english/language.php');
            }
        } else {
            echo "Default locale ID could not be read. Please inform the Technico helpdesk.";
            require_once('locale/english/language.php');
        }
    }
}

?>
