<?php

session_start();
require_once('lib/autoload.php');
if (file_exists(__DIR__.'/../.env')) {
	$dotenv = new Dotenv\Dotenv(__DIR__ . "/Rest/");
	$dotenv->load();
}
Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('hqzhp75r7yght5n6');
Braintree_Configuration::publicKey('jc2dm26dq435pk3x');
Braintree_Configuration::privateKey('74cae484c191c03b25973667d45210cc');

?>