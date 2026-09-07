<?php
session_start();
include 'db.php';

$errorMsg = "";
$username = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($userId, $hashedPassword);
                $stmt->fetch();

                if (password_verify($password, $hashedPassword)) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $username;

                    // Redirect to user page after successful login
                    header("Location: userpage.php");
                    exit();
                } else {
                    $errorMsg = "❌ Incorrect password.";
                }
            } else {
                $errorMsg = "⚠️ Username not found.";
            }
            $stmt->close();
        } else {
            $errorMsg = "Database error. Please try again later.";
        }
        $conn->close();
    } else {
        $errorMsg = "Please enter both username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - FACE IT</title>
<style>
  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #E0F7FA, #FCE4EC);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
  }
  .login-box {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    padding: 40px 35px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
    animation: fadeIn 0.8s ease;
  }
  h1 {
    color: #1C1C3C;
    margin-bottom: 25px;
    letter-spacing: 1px;
  }
  input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
    transition: all 0.3s ease;
  }
  input:focus {
    border-color: #1C1C3C;
    box-shadow: 0 0 5px rgba(28,28,60,0.3);
    outline: none;
  }
  button {
    width: 100%;
    padding: 12px;
    font-size: 1rem;
    background-color: #1C1C3C;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.3s ease;
  }
  button:hover {
    background-color: firebrick;
    transform: scale(1.02);
  }
  label {
    display: flex;
    align-items: center;
    justify-content: start;
    font-size: 0.9rem;
    margin: 10px 0;
  }
  input[type="checkbox"] {
    margin-right: 8px;
  }
  .error {
    color: firebrick;
    margin-bottom: 15px;
    font-weight: bold;
  }
  .extra, .forgot-password {
    margin-top: 15px;
    font-size: 0.9rem;
    color: #333;
  }
  .extra a, .forgot-password a {
    color: firebrick;
    text-decoration: none;
    font-weight: bold;
  }
  .extra a:hover, .forgot-password a:hover {
    text-decoration: underline;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>
</head>
<body>

<div class="login-box">
  <h1>Welcome to FACE IT 💫</h1>

  <?php if (!empty($errorMsg)): ?>
    <div class="error"><?= htmlspecialchars($errorMsg) ?></div>
  <?php endif; ?>

  <form method="POST" action="login.php" novalidate onsubmit="saveUsername()">
    <input type="text" id="username" name="username" placeholder="Username" required value="<?= htmlspecialchars($username) ?>">
    <input type="password" name="password" placeholder="Password" required>
    <label><input type="checkbox" id="rememberMe"> Remember me</label>
    <button type="submit">Login</button>
	<label style="display:block; text-align:left;">

  </form>

  <div class="forgot-password">
    <a href="forgot_password.php">Forgot Password?</a>
  </div>

  <div class="extra">
    Don’t have an account? <a href="register.php">Register here</a>
  </div>
</div>

<script>
const rememberBox = document.getElementById('rememberMe');
const usernameInput = document.querySelector('input[name="username"]');
const passwordInput = document.querySelector('input[name="password"]');

// Autofill
window.addEventListener('DOMContentLoaded', function() {
  const savedUsername = localStorage.getItem('faceit_username');
  const savedPassword = localStorage.getItem('faceit_password');
  if (savedUsername) usernameInput.value = savedUsername;
  if (savedPassword) passwordInput.value = savedPassword;
});

// Save when user clicks login
document.querySelector('form').addEventListener('submit', function() {
  if (rememberBox.checked) {
    localStorage.setItem('faceit_username', usernameInput.value);
    localStorage.setItem('faceit_password', passwordInput.value);
  } else {
    localStorage.removeItem('faceit_password');
  }
});
</script>



</body>
</html>
