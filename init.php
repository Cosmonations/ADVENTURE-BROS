<?php
session_start();

if (!isset($_SESSION['first_name'])) {
    die("User session not found. Please log in first.");
}

$merchantId = 'MERCHANT ID'; 
$apiKey     = 'API';   

$orderId = "ORD_" . time() . "_" . rand(100, 999);
$returnUrl = "http://localhost:8000/www/return.php";

$payload = [
    'merchant-id'      => $merchantId,
    'api-key'          => $apiKey,
    'firstname'        => $_SESSION['first_name'],
    'lastname'         => $_SESSION['last_name'],
    'email'            => $_SESSION['email'],
    'phonenumber'      => $_SESSION['phone'],
    'username'         => $_SESSION['email'], 
    'accountnumber'    => 'ACC_' . preg_replace('/\D/', '', $_SESSION['phone']), 
    'amount'           => 10.00,
    'currency'         => 'GHS',
    'order-id'         => $orderId,
    'order-desc'       => 'ADVENTURE BROS - 3 Extra Lives',
    'payment-tokenize' => 'TRUE', 
    'redirect-url'     => $returnUrl
];

$ch = curl_init('https://sandbox.expresspaygh.com/api/submit.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_POST, true); 
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload)); 

$response = curl_exec($ch);

$responseData = json_decode($response, true);

if (isset($responseData['status']) && $responseData['status'] == 1 && isset($responseData['token'])) {
    $token = $responseData['token'];
    $checkoutUrl = "https://sandbox.expresspaygh.com/api/checkout.php?token=" . $token;
    header("Location: " . $checkoutUrl);
    exit;
} 