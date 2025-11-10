<?php
$response = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["prompt"])) {
    $prompt = $_POST["prompt"];
    $api_key = "AIzaSyAx681xZrWrfFbcGBlpkYG9aSeVCyk-Y40"; // 🔐 Keep this safe
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$api_key";
    

    $data = json_encode([
        "contents" => [[
            "parts" => [[ "text" => $prompt ]]
        ]]
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => $data
    ]);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = "cURL Error: " . curl_error($ch);
    }

    curl_close($ch);

    $decoded = json_decode($result, true);

    if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
        $response = $decoded['candidates'][0]['content']['parts'][0]['text'];
    } elseif (isset($decoded['error'])) {
        $error = "API Error: " . $decoded['error']['message'];
    } else {
        $error = "No response from API.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Explore AI</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'navbar.php'; ?>

  <div class="container">
    <h1>Explore AI ✍️</h1>

    <form method="POST">
      <textarea name="prompt" placeholder="What would you like AI to write about?" required></textarea>
      <button type="submit">Ask AI</button>
    </form>

    <?php if ($response): ?>
      <h2>AI Response:</h2>
      <div class="post ai-response">
        <?= nl2br(htmlspecialchars($response)) ?>
      </div>
    <?php elseif ($error): ?>
      <div style="color:red; margin-top:15px; text-align:center;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
