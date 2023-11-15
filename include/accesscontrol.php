<?php

/**
 * Access control functions
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2012 Technico Automatisering B.V.
 * @version   1.0
 */

function user_has_access_to($a) {
    if (!isset($_SESSION["user_privileges"])) {
        header("Location: /technix/");
    }
    return ((isset($_SESSION["user_privileges"][$a[0]][$a[1]])) ? true : false);
}

function shu($str) {
    $str1 = "";
    $str2 = "";
    for ($i = 0; $i < strlen($str); $i++) {
        ($i % 2 == 0) ? $str1 .= $str{$i} : $str2 .= $str{$i};
    }
    return strrev($str2 . $str1);
}

function shi($str) {
    return shu(substr($str, 5)) . shu(substr($str, 0, 5));
}

function userdata_hash($u,$p) {
    return shi(strrev(shu(shi(shu(crypt(strrev(shu($p)), substr(crypt($p, 'ta'), -2))) .
    shi(shu(crypt(strrev(shu(strtolower($u))), substr(crypt(strrev(strtolower($u)), 'gs'), -2))))))));
}

?>
