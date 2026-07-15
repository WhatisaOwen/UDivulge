<?php
session_start();

// syncs to mysqli database thingy
$conn = new mysqli("localhost", "story_user", "Testing3", "story_list");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = ""; // useless now after its all working but KEEP

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        if (!$stmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $user_id = null;
            $hashed_password = null;
            $stmt->bind_result($user_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION["user_id"] = $user_id;
                $_SESSION["username"] = $username;

                $stmt->close();
                $conn->close();
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that username.";
        }

        $stmt->close();
    } else {
        $error = "Please enter both fields.";
    }
} // css below cant remember class names

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - UDivulge</title>
    <style>
        body {
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
        .subtext {
            color: #777;
            font-size: 12px;
            margin-top: -5px;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }
        .bottom-subtext {
            position: fixed;
            bottom: 15px;
            width: 100%;
            text-align: center;
            color: #666;
            font-size: 12px;
            letter-spacing: 1px; /* adding the rest below for easy access to css im adjusting a lot right now */
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
    <h2>UDivulge Login</h2>
    <p class="subtext">A Place To Be Yourself</p>

    <?php if (!empty($error)) echo "<p style='color:#d9534f; font-size:13px;'>$error</p>"; ?>

    <form method="post" action="login.php">
        <div style="text-align:left; width:85%; margin:0 auto;">
            <label>Username</label><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <label>Password</label><br>
            <input type="password" name="password" placeholder="Password" required><br>
        </div>
        <button type="submit">Login</button>
    </form>
    <p style="font-size:12px; margin-top:12px;">Don’t have an account? <a href="register.php">Register</a></p>
</div>

</body>
</html>
