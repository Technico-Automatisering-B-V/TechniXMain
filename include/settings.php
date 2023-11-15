<?php

/**
 * Setting function
 *
 * @author    Edwin van de Pol <edwin@technico.nl>
 * @copyright 2006-2012 Technico Automatisering B.V.
 * @version   1.0
 */

function config_rehash() {
	//make the settings table an SQL resource
	$config_resource = db_read("settings");

	//push the SQL resource rows into the global array
	while ($row = db_fetch_assoc($config_resource)) {
		$GLOBALS["config"][$row["name"]] = $row["value"];
	}
}

config_rehash();