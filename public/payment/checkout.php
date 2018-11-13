<?php
require_once("braintree_init.php");
require_once 'lib/Braintree.php';
$_POST = json_decode(file_get_contents('php://input'), true);
$nonce = $_POST['nonce'];
$amount = $_POST['amount'];
$result = Braintree_Transaction::sale([

		'amount' => $amount,
		'paymentMethodNonce' => $nonce,
		'options' =>[
			'submitForSettlement' => True
		]

]); 
if(isset($result)) {
    //$transaction = $result->transaction;
    echo $result;
} else {
    echo "Pas de reponse";
}
?>