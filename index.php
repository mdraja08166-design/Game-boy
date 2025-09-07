<?php
// verify-membership.php ke bajaye, hum is file me hi PHP code rakh rahe hain

// Agar POST request hai toh membership verify karein
if ($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['user_id'])) {
    $botToken = '7873957402:AAFBseNGXQ8ir8grNavfN2ERnwvcxnA-pjI'; // Apna bot token
    $channelUsername = '@z4TS_JKKlGQ0MzY1'; // Apna channel username

    $userId = intval($_POST['user_id']);

    $apiUrl = "https://api.telegram.org/bot$botToken/getChatMember";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'chat_id' => $channelUsername,
        'user_id' => $userId
    ]));

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    $is_member = false;
    if ($data && $data['ok']) {
        $status = $data['result']['status'];
        if (in_array($status, ['member', 'administrator', 'creator'])) {
            $is_member = true;
        }
    }

    echo json_encode(['is_member' => $is_member]);
    exit; // POST request ke liye sirf PHP backend response
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Join Telegram Channel & Play Game</title>
<style>
  body { font-family: Arial, sans-serif; text-align: center; background:#222; color:#fff; padding:20px; }
  #gameContainer { display:none; margin-top:20px; }
  button { padding:10px 20px; font-size:16px; cursor:pointer; margin-top:10px; }
  #status { margin-top:20px; font-size:18px; color: lightgreen; }
</style>
</head>
<body>

<h2>Join Our Telegram Channel to Play</h2>

<!-- Telegram Login Widget -->
<script async src="https://telegram.org/js/telegram-widget.js?15"
        data-telegram-login="YourBotUsername"  <!-- Replace with your bot username (no @) -->
        data-size="large"
        data-userpic="false"
        data-auth-url="" <!-- Yahan URL blank rakhain, kyunki PHP to same file me hai -->
        data-request-access="true"
        style="margin:auto;"
        onload="setupTelegramAuth(this)"></script>

<div id="status"></div>

<div id="gameContainer">
  <h3>Welcome! Click below to start the game</h3>
  <button onclick="startGame()">Start Game</button>
</div>

<script>
let userData = null;

// Is function ko hum widget ke load hone ke baad call karte hain
function setupTelegramAuth(widget) {
  window.handleTelegramAuth = function(data) {
    userData = data;
    document.getElementById('status').textContent='Verifying your membership...';

    // AJAX request to same PHP file
    fetch('', { // Blank URL se same file call hoti hai
      method:'POST',
      headers:{ 'Content-Type':'application/x-www-form-urlencoded' },
      body:'user_id=' + encodeURIComponent(userData.id)
    })
    .then(res => res.json())
    .then(result => {
      if(result.is_member) {
        document.getElementById('status').textContent='✅ Verified! You can now play.';
        document.getElementById('gameContainer').style.display='block';
      } else {
        document.getElementById('status').textContent='❌ Join our channel first!';
      }
    })
    .catch(err => {
      document.getElementById('status').textContent='Error verifying! Try again.';
    });
  };
}

function startGame() {
  alert('Game start! Yahan pe aap apna game embed kar sakte hain.');
}
</script>

</body>
</html>
