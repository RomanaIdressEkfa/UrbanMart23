<?php

// Mohammad Hassan
// SSLCommerz configuration

$isTestMode = filter_var(env('SSLCZ_TESTMODE', false), FILTER_VALIDATE_BOOLEAN);
$apiDomain = $isTestMode ? "https://sandbox.sslcommerz.com" : "https://securepay.sslcommerz.com";
return [
	'apiCredentials' => [
		'store_id' => env("SSLCZ_STORE_ID"),
		// Use the same env key the admin panel writes: SSLCZ_STORE_PASSWD
		'store_password' => env("SSLCZ_STORE_PASSWD"),
	],
	'apiUrl' => [
		'make_payment' => "/gwprocess/v4/api.php",
		'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
		'order_validate' => "/validator/api/validationserverAPI.php",
		'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
		'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
	],
	'apiDomain' => $apiDomain,
	'connect_from_localhost' => filter_var(env("IS_LOCALHOST", false), FILTER_VALIDATE_BOOLEAN), // For Sandbox, use true, For Live, use false
	'success_url' => '/success',
	'failed_url' => '/fail',
	'cancel_url' => '/cancel',
	'ipn_url' => '/ipn',
];