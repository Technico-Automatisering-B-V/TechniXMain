<?php

/**
 * Engine
 *
 * @author    G. I. Voros <gabor@technico.nl> - E. van de Pol <edwin@technico.nl>
 * @copyright (c) 2006-${date} Technico Automatisering B.V.
 * @version   $$Id$$
 */

/**
 * Start a session
 */
//session_cache_limiter('private_no_expire'); //to return to the previous page
session_start();

/**
 * Require necessary files
 */
require_once 'database.php';
require_once 'settings.php';
require_once 'accesscontrol.php';
require_once 'geturl.php';
require_once 'pagecontrol.php';
require_once 'contentcontrol.php';
require_once 'htmlobjects.php';
require_once 'functions.garments.php';
require_once 'locale/localeparser.php';
require_once 'template.php';