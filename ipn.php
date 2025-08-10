<?php
require_once "keys.example.php";

if (empty($_POST)) {
    throw new Exception("No post data received!");
}

// Validación de firma en IPN
if (!checkHash(PASSWORD)) {
    throw new Exception("Invalid signature");
}

$answer = json_decode($_POST["kr-answer"], true);

$transaction = $answer['transactions'][0];

//Verificar orderStatus: PAID / UNPAID
$orderStatus = $answer['orderStatus'];
$orderId = $answer['orderDetails']['orderId'];
$transactionUuid = $transaction['uuid'];

print 'OK! OrderStatus is ' . $orderStatus;