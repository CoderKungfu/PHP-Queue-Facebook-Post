<?php
require_once dirname(__DIR__) . '/config.php';

/**
 * Opauth example
 * 
 * This is an example on how to instantiate Opauth
 * For this example, Opauth config is loaded from a separate file: opauth.conf.php
 * 
 */

/**
 * Define paths
 */
define('CONF_FILE', dirname(__FILE__).'/'.'opauth.conf.php');

/**
* Load config
*/
if (!file_exists(CONF_FILE)){
	trigger_error('Config file missing at '.CONF_FILE, E_USER_ERROR);
	exit();
}
require CONF_FILE;

/**
 * Instantiate Opauth with the loaded config
 */

$Opauth = new Opauth( $config );