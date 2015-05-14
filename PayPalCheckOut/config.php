<?php
session_start();

$PayPalMode = 'sandbox';
$PayPalApiUsername = 'wooxudong-facilitator_api1.gmail.com';
$PayPalApiPassword = 'SXUZJQ7CD4DCUPJZ';
$PayPalApiSignature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31Ap4gaxcbjwokITbavQ0EYBWKFAXC';
$PayPalCurrencyCode = 'SGD';
$PayPalReturnURL = 'http://localhost:8080/confirm.php';
$PayPalCancelURL = 'http://localhost:8080/index.php';
?>