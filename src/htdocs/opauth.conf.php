<?php
/**
 * Opauth configuration file template for advanced user
 * ====================================================
 * To use: rename to opauth.conf.php and tweak as you like
 * For quick and easy set up, refer to opauth.conf.php.default
 */

$config = array(
/**
 * Path where Opauth is accessed.
 * 
 * Begins and ends with /
 * eg. if Opauth is reached via http://example.org/auth/, path is '/auth/'
 * if Opauth is reached via http://auth.example.org/, path is '/'
 */
	'path' => '/',

/**
 * Uncomment if you would like to view debug messages
 */
	//'debug' => true,
	
/**
 * Callback URL: redirected to after authentication, successful or otherwise
 */
	'callback_url' => '{path}callback.php',

/**
 * Callback transport, for sending of $auth response
 * 
 * 'session': Default. Works best unless callback_url is on a different domain than Opauth
 * 'post'   : Works cross-domain, but relies on availability of client-side JavaScript.
 * 'get'    : Works cross-domain, but may be limited or corrupted by browser URL length limit 
 *            (eg. IE8/IE9 has 2083-char limit)
 */
	//'callback_transport' => 'session',

/**
 * A random string used for signing of $auth response.
 */
	'security_salt' => 'LDFmiilYf8Fyw5W10asdadsarx4W1KsVrieQCnpBzzpTBWA5vJidQKDx8pMJbmw28R1C4m',
	
/**
 * Higher value, better security, slower hashing;
 * Lower value, lower security, faster hashing.
 */
	//'security_iteration' => 300,	
	
/**
 * Time limit for valid $auth response, starting from $auth response generation to validation.
 */
	//'security_timeout' => '2 minutes',

/**
 * Directory where Opauth strategies reside
 */
	//'strategy_dir' => '{lib_dir}Strategy/',
		
/**
 * Strategy
 * Refer to individual strategy's documentation on configuration requirements.
 * 
 * eg.
 * 'Strategy' => array(
 * 
 *   'Facebook' => array(
 *      'app_id' => 'APP ID',
 *      'app_secret' => 'APP_SECRET'
 *    ),
 * 
 * )
 *
 */
	'Strategy' => array(
         'Facebook' => array(
            'app_id' => getenv('fb_app_id'),
            'app_secret' => getenv('fb_app_secret'),
            'scope' => 'publish_stream'
         ),
	),
);