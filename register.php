<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$conn = new mysqli("localhost", "story_user", "Testing3", "story_list");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error = "";

$captchas = ["cap1.png"=>"9M4BP","cap2.png"=>"XKWDN","cap3.png"=>"59CTR"]; // dumb png captcha list

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $keys = array_keys($captchas);
    $_SESSION["captcha_img"] = $keys[array_rand($keys)];
    $_SESSION["captcha_val"] = $captchas[$_SESSION["captcha_img"]];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $captcha_input = trim($_POST["captcha"]);

    if ($captcha_input !== ($_SESSION["captcha_val"] ?? '')) {
        $error = "Captcha incorrect. Please try again.";
    } elseif (!empty($username) && !empty($password)) {
        $check = $conn->prepare("SELECT id FROM users WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "That username is already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username,password) VALUES (?,?)");
            $stmt->bind_param("ss", $username, $hashed);

            if ($stmt->execute()) {
               
                $_SESSION["user_id"] = $stmt->insert_id;
                $_SESSION["username"] = $username;
               
                header("Location: index.php");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $error = "Please fill in both fields.";
    }

   
    $keys = array_keys($captchas); // should make new capts after each attempt/ tor identity refresh
    $_SESSION["captcha_img"] = $keys[array_rand($keys)];
    $_SESSION["captcha_val"] = $captchas[$_SESSION["captcha_img"]];
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - UDivulge</title>
    <style>  
        body { /*start of css for easy access */
            background: #1a1a1a;
            color: #f1f1f1;
            font-family: "Segoe UI", Arial, sans-serif; 
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .form-box {
            background: #222;
            border: 1px solid #444;
            margin: 100px auto;
            padding: 25px;
            max-width: 380px;
            box-shadow: 0 0 15px rgba(0,0,0,0.6);
        }
        input {
            width: 85%;
            padding: 8px;
            margin: 8px 0;
            background: #2e2e2e;
            border: 1px solid #555;
            color: #ddd;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #777;
        }
        button {
            background: #ccc;
            color: #111;
            font-weight: bold;
            border: none;
            padding: 8px 25px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #bbb;
        }
        a {
            color: #00bcd4;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        label {
            font-size: 11px;
            color: #ccc;
        }
        .top-right-brand {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
        }
        .top-right-brand img {
            height: 35px;
            width: auto;
            margin-right: 8px;
            border: 1px solid #111;
        }
        .top-right-brand span {
            font-size: 20px;
            font-weight: bold;
            color: #f1f1f1;
            letter-spacing: 0.5px;
        }
        .captcha-box img {
            border: 1px solid #555;
            margin: 5px 0;
        }
        .bottom-subtext {
            position: fixed;
            bottom: 15px;
            width: 100%;
            text-align: center;
            color: #666;
            font-size: 12px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
<div class="top-right-brand">
    <a href="index.php" style="display:flex; align-items:center;">
        <img src="img/logo2.png" alt="Logo">
        <span>UDivulge</span>
    </a>
</div>

<div class="form-box">
    <h2>Register</h2>
    <?php if (!empty($error)) echo "<p style='color:#d9534f; font-size:13px;'>$error</p>"; ?>

    <form method="post" action="register.php">
        <div style="text-align:left; width:85%; margin:0 auto;">
            <label>Username</label><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <label>Password</label><br>
            <input type="password" name="password" placeholder="Password" required><br>

            <div class="captcha-box">
                <label>Captcha</label><br>
                <img src="img/<?php echo $_SESSION['captcha_img']; ?>" alt="Captcha"><br>
                <input type="text" name="captcha" placeholder="Enter captcha" maxlength="5" required>
            </div>
        </div>

        <button type="submit">Register</button>
    </form>
    <p style="font-size:12px; margin-top:12px;">Already have an account? <a href="login.php">Login</a></p>
</div>

<div class="bottom-subtext">A Place To Be Yourself</div>
</body>
</html>
