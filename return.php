<?php
session_start();
 
$token = $_GET['token'] ?? null;
$orderId = $_GET['order-id'] ?? null;

if (!empty($token) && !empty($orderId)) {
    
    $merchantId = 'MERCHANT ID'; 
    $apiKey     = 'API KEY';   

    $queryPayload = [
        'merchant-id' => $merchantId,
        'api-key'     => $apiKey,
        'token'       => $token
    ];

    $ch = curl_init('https://sandbox.expresspaygh.com/api/query.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($queryPayload));
    
    $response = curl_exec($ch);
    

    $responseData = json_decode($response, true);

    if (isset($responseData['result']) && $responseData['result'] == 1) {
        
        if (!isset($_SESSION['lives'])) {
            $_SESSION['lives'] = 0;
        }
        $_SESSION['lives'] += 3;

        $cctoken = null;

        if (isset($responseData['card'])) {
            if (isset($responseData['card'][0]['cctoken'])) {
                $cctoken = $responseData['card'][0]['cctoken']; 
            } elseif (isset($responseData['card']['cctoken'])) {
                $cctoken = $responseData['card']['cctoken'];    
            }
        }

        // If the token was found, save it and return to the game
        if ($cctoken) {
            setcookie('cctoken', $cctoken, time() + 259200, "/");
            $_SESSION['cctoken'] = $cctoken; 
            header("Location: game.php");
            exit;
        }
        
    } else {
        echo "<h2>Payment verification failed or was declined.</h2>";
        echo "<a href='game.php'>Return to Game</a>";
    }

} else {
    echo "<h2>Payment failed.</h2>";
    echo "<a href='game.php'>Return to Game</a>";
}
