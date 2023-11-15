<?php

/**
 * Generate password
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

require_once 'include/accesscontrol.php';

$user = $_GET['u'];
$pass = $_GET['p'];
$hash = userdata_hash($user, $pass);

echo "<html><body><pre>";
    echo "Username: ". $user ."<br />";
    echo "Password: ". $pass ."<br />";
    echo "Hash    : ". $hash;
echo "</pre></body></html>";