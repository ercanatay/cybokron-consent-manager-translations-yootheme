<?php
// phpcs:ignoreFile -- Development-only CLI assertions.
if (!defined('ABSPATH') && PHP_SAPI === 'cli') {
	define('ABSPATH', dirname(__DIR__) . '/');
}

if (!defined('ABSPATH')) {
	exit;
}

require_once __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__) . '/includes/class-admin.php';

class CYBOCOMA_Admin_Ajax_Headers_Test_Double extends CYBOCOMA_Admin {
	public $mock_headers_sent = false;
	public $mock_response_headers = [];

	public function __construct() {}

	public function invoke_send_ajax_security_headers() {
		$this->send_ajax_security_headers();
	}

	protected function headers_already_sent() {
		return (bool) $this->mock_headers_sent;
	}

	protected function get_response_headers() {
		return is_array($this->mock_response_headers) ? $this->mock_response_headers : [];
	}
}

$failures = [];
$admin = new CYBOCOMA_Admin_Ajax_Headers_Test_Double();

$GLOBALS['cybocoma_send_nosniff_calls'] = 0;
$GLOBALS['cybocoma_send_frame_options_calls'] = 0;
$admin->mock_headers_sent = false;
$admin->mock_response_headers = [];
$admin->invoke_send_ajax_security_headers();
if ((int) $GLOBALS['cybocoma_send_nosniff_calls'] !== 1) {
	$failures[] = 'Expected nosniff header to be sent when headers are not already sent.';
}
if ((int) $GLOBALS['cybocoma_send_frame_options_calls'] !== 1) {
	$failures[] = 'Expected frame-options header to be sent when no prior X-Frame-Options header exists.';
}

$GLOBALS['cybocoma_send_nosniff_calls'] = 0;
$GLOBALS['cybocoma_send_frame_options_calls'] = 0;
$admin->mock_headers_sent = false;
$admin->mock_response_headers = ['X-Frame-Options: DENY'];
$admin->invoke_send_ajax_security_headers();
if ((int) $GLOBALS['cybocoma_send_nosniff_calls'] !== 1) {
	$failures[] = 'Expected nosniff header to still be sent when X-Frame-Options already exists.';
}
if ((int) $GLOBALS['cybocoma_send_frame_options_calls'] !== 0) {
	$failures[] = 'Expected frame-options header not to be resent when X-Frame-Options already exists.';
}

$GLOBALS['cybocoma_send_nosniff_calls'] = 0;
$GLOBALS['cybocoma_send_frame_options_calls'] = 0;
$admin->mock_headers_sent = true;
$admin->mock_response_headers = [];
$admin->invoke_send_ajax_security_headers();
if ((int) $GLOBALS['cybocoma_send_nosniff_calls'] !== 0 || (int) $GLOBALS['cybocoma_send_frame_options_calls'] !== 0) {
	$failures[] = 'Expected no security headers to be sent after headers are already sent.';
}

if (!empty($failures)) {
	fwrite(STDERR, "test_admin_headers failed:\n- " . implode("\n- ", $failures) . "\n");
	exit(1);
}

echo "test_admin_headers passed\n";
