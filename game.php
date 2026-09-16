<?php
// game.php
session_start();

if (!isset($_SESSION['first_name'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['current_enemy'])) {
    $_SESSION['current_enemy'] = 1; 
}

$savedToken = $_COOKIE['cctoken'] ?? $_SESSION['cctoken'] ?? null;
$message = "Enemy " . $_SESSION['current_enemy'] . " blocks your path. Choose your weapon!";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['auto_pay']) && $savedToken) {
    
    $merchantId = '042624216522'; 
    $apiKey     = 'u7HL2aenabmiiPcL96czt-sXU0wvcqfTMwQVmw6kUx-u7RdFILFvNBeFG7foKsO-x6MJarUsaH768pZP68A';   
    
    $orderId = "AUTO_" . time() . "_" . rand(100, 999);

    $submitPayload = [
        'merchant-id'  => $merchantId,
        'api-key'      => $apiKey,
        'firstname'    => $_SESSION['first_name'],
        'lastname'     => $_SESSION['last_name'],
        'email'        => $_SESSION['email'],
        'phonenumber'  => $_SESSION['phone'],
        'amount'       => 10.00,
        'currency'     => 'GHS',
        'order-id'     => $orderId,
        'order-desc'   => 'ADVENTURE BROS - Auto Revive (3 Lives)',
        'redirect-url' => 'http://localhost:8000/www/return.php'
    ];

    $chSubmit = curl_init('https://sandbox.expresspaygh.com/api/submit.php');
    curl_setopt($chSubmit, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chSubmit, CURLOPT_POST, true);
    curl_setopt($chSubmit, CURLOPT_POSTFIELDS, http_build_query($submitPayload));
    $submitResponse = curl_exec($chSubmit);

    $submitData = json_decode($submitResponse, true);

    if (isset($submitData['status']) && $submitData['status'] == 1 && isset($submitData['token'])) {
        
        $checkoutPayload = [
            'token'   => $submitData['token'],
            'cctoken' => $savedToken
        ];

        $chCheckout = curl_init('https://sandbox.expresspaygh.com/api/checkout.php');
        curl_setopt($chCheckout, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chCheckout, CURLOPT_POST, true);
        curl_setopt($chCheckout, CURLOPT_POSTFIELDS, http_build_query($checkoutPayload));
        $checkoutResponse = curl_exec($chCheckout);

        $checkoutData = json_decode($checkoutResponse, true);

        if (isset($checkoutData['result']) && $checkoutData['result'] == 1) {
            $_SESSION['lives'] = 3; 
            $message = "<strong style='color: #4CAF50;'>Payment successful! 10 GHS charged to your saved card. (+3 Lives)</strong>";
        } else {
            $errorReason = $checkoutData['result-text'] ?? 'Unknown Error';
            $message = "<strong style='color: #ff5252;'>Payment declined by bank: " . htmlspecialchars($errorReason) . "</strong>";
        }
    } else {
        $message = "<strong style='color: #ff5252;'>Failed to connect to billing server. Please try manual payment.</strong>";
    }
}

//  GAME LOGIC
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['restart_game'])) {
    $_SESSION['current_enemy'] = 1;
    $_SESSION['lives'] = 3; 
    header("Location: game.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['weapon'])) {
    if ($_SESSION['lives'] > 0 && $_SESSION['current_enemy'] <= 15) {
        
        $weapons = ['Spear', 'Shield', 'Bow'];
        $enemyWeapon = $weapons[array_rand($weapons)]; 
        $playerWeapon = $_POST['weapon'];
        
        if ($playerWeapon == $enemyWeapon) {
            $message = "You both chose $playerWeapon! Your weapons clash, but no ground is given.";
        } else {
            $playerWins = false;
            if ($playerWeapon == 'Spear' && $enemyWeapon == 'Shield') $playerWins = true;
            if ($playerWeapon == 'Shield' && $enemyWeapon == 'Bow') $playerWins = true;
            if ($playerWeapon == 'Bow' && $enemyWeapon == 'Spear') $playerWins = true;
            
            if ($playerWins) {
                $_SESSION['current_enemy']++; 
                if ($_SESSION['current_enemy'] > 15) {
                    $message = "You struck down the final enemy with your $playerWeapon!</span>";
                } else {
                    $message = "Your $playerWeapon shattered their $enemyWeapon! Advancing to Enemy " . $_SESSION['current_enemy'] . "";
                }
            } else {
                $_SESSION['lives'] -= 1; 
                $message = "Their $enemyWeapon bested your $playerWeapon! You took a critical hit. (-1 Life)";
            }
        }
    }
}

$lives = $_SESSION['lives'];
$currentEnemy = $_SESSION['current_enemy'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>ADVENTURE BROS - Battle</title>
    <style>
        body { font-family: sans-serif; background: #111; color: white; text-align: center; padding-top: 50px; }
        .box { background: #222; padding: 30px; border-radius: 10px; width: 450px; margin: auto; }
        button { padding: 12px; margin: 10px 5px; cursor: pointer; font-weight: bold; border-radius: 5px; border: none; }
        .btn-action { background-color: #2196F3; color: white; flex: 1; font-size: 16px; }
        .btn-action:hover { background-color: #1976D2; }
        .btn-buy { background-color: #4CAF50; color: white; width: 100%; font-size: 16px; }
        .game-status { display: flex; justify-content: space-between; font-size: 14px; color: #aaa; margin-bottom: 20px; border-bottom: 1px solid #444; padding-bottom: 10px; }
    </style>
</head>
<body>

<div class="box">
    
    <?php if ($lives > 0 && $currentEnemy <= 15): ?>
        
        <div class="game-status">
            <span>Player: <strong><?= htmlspecialchars($_SESSION['first_name']) ?></strong></span>
            <span>Lives: <strong style="color: #4CAF50;"><?= $lives ?></strong></span>
            <span>Enemy: <strong><?= $currentEnemy ?> / 15</strong></span>
            <?php if ($savedToken): ?>
                <span style="color: #4CAF50; font-size: 12px;">💳 Card Saved</span>
            <?php endif; ?>
        </div>
        
        <h2>Combat Phase</h2>
        <div style="background: #333; padding: 20px; border-radius: 5px; margin-top: 10px; min-height: 60px;">
            <p style="margin: 0; font-size: 18px;"><?= $message ?></p>
        </div>
        
        <form action="game.php" method="POST" style="display: flex; justify-content: space-between; margin-top: 20px;">
            <button type="submit" name="weapon" value="Spear" class="btn-action">🗡️ Spear</button>
            <button type="submit" name="weapon" value="Shield" class="btn-action">🛡️ Shield</button>
            <button type="submit" name="weapon" value="Bow" class="btn-action">🏹 Bow</button>
        </form>

    <?php elseif ($currentEnemy > 15): ?>
        
        <h2 style="color: #4CAF50;"> VICTORY! </h2>
        <p>You have defeated all 15 enemies and survived the gauntlet.</p>
        <p><?= $message ?></p>
        
        <form action="game.php" method="POST" style="margin-top: 30px;">
            <button type="submit" name="restart_game" class="btn-action" style="width: 100%;">Play Again</button>
        </form>

    <?php else: ?>
        
        <h2 style="color: #ff5252;">You have fallen.</h2>
        <p>Enemy <?= $currentEnemy ?> stands victorious over you.</p>
        
        <?php if (strpos($message, 'declined') !== false || strpos($message, 'Failed') !== false): ?>
            <div style="background: #4a141c; color: #ff8a80; padding: 10px; font-size: 13px; margin: 10px 0; border-radius: 4px; text-align: left;">
                <?= strip_tags($message) ?>
            </div>
        <?php endif; ?>
        
        <hr style="border-color: #444; margin: 20px 0;">
        
        <h3>Revive & Continue (10 GHS)</h3>
        <p style="font-size: 14px; color: #aaa; margin-bottom: 20px;">Purchasing lives will return you directly to Enemy <?= $currentEnemy ?>.</p>
        
        <?php if ($savedToken): ?>
            <!-- IF TOKEN EXISTS:  -->
            <form action="game.php" method="POST">
                <button type="submit" name="auto_pay" value="1" class="btn-buy" style="background-color: #2196F3;">Revive instantly (Saved Card)</button>
            </form>
            <p style="font-size: 12px; margin-top: 15px;"><a href="init.php" style="color: #aaa; text-decoration: underline;">Pay with a different card</a></p>
        
        <?php else: ?>
            <!-- IF NO TOKEN EXISTS: Force manual payment to get a new token -->
            <form action="init.php" method="POST">
                <button type="submit" class="btn-buy">Pay via expressPay</button>
            </form>
        <?php endif; ?>

    <?php endif; ?>
</div>

</body>
</html>