r<?php

/**
 * PHPUnit bootstrap file.
 *
 * @package Ae_81
 */

// DO NOT CHANGE THIS CONST NAME AS ITS ALSO DEFINED IN OTHER FILES (IN THE DEV ENV PLUGIN FOR APIs)
const COUPONS_PLUS_TESTING = true;
$_SERVER['REQUEST_URI'] = '/';
const WP_DISABLE_FATAL_ERROR_HANDLER = true;
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
//$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
$_phpunit_polyfills_path = dirname(dirname(__FILE__)).'/vendor/rafark/phpunit-polyfills/phpunitpolyfills-autoload.php';
if ( false !== $_phpunit_polyfills_path ) {

	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once "{$_tests_dir}/includes/functions.php";

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
    $mainpluginurl = dirname( dirname( __FILE__ ) );

	require_once $mainpluginurl . '/index.php';

    foreach (CouponURLs\Original\Environment\Env::settings()->tests?->loadPlugins ?? [] as $relativePath) {
        require_once dirname( dirname( dirname(__FILE__) ) ) . "/{$relativePath}";
    }
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require_once "{$_tests_dir}/includes/bootstrap.php";
/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(CouponURLs\Tests\UnitTestCase::class)->in('unit');
uses(CouponURLs\Tests\IntegrationTestCase::class)->in('integration');

function buffer() {
    ob_end_flush();
    ob_end_clean();
    ob_implicit_flush(true);
}