<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
//should redirect if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "story_user", "Testing3", "story_list");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";
$stmt = $conn->prepare("SELECT created_at FROM stories WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
if ($stmt) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($last_time);
    $stmt->fetch();
    $stmt->close();

    if (!empty($last_time)) {
        if (time() - strtotime($last_time) < 3600) {
            $error = "You can only submit one story per hour. Please wait before submitting again.";
        }
    }
} else {
    $error = "Database error: (" . $conn->errno . ") " . $conn->error;
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && empty($error)) {
    $story = '';
    if (isset($_POST['story'])) {
        $story = trim($_POST['story']);
    }

    if (!empty($story)) {
        if (isset($_POST['confirm']) && $_POST['confirm'] === "yes") {
           // should move into db
            $stmt = $conn->prepare("INSERT INTO stories (content, user_id) VALUES (?, ?)");
            if (!$stmt) {
                die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            $stmt->bind_param("si", $story, $_SESSION['user_id']);
            if ($stmt->execute()) {
                $success = "Story submitted successfully! You must wait one hour before submitting another.";
            } else {
                $error = "Error submitting story: (" . $stmt->errno . ") " . $stmt->error;
            }
            $stmt->close();
        } else {
            
            $safe_story = htmlspecialchars($story);
            echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>Confirm Submission</title>
<style>
body { background:#2b2b2b; color:white; font-family:Arial,sans-serif; text-align:center; }
.box { margin:100px auto; padding:20px; background:#000; border:2px solid #fff; border-radius:8px; max-width:500px; }
button { padding:8px 16px; font-size:16px; cursor:pointer; background:#3650ff; border:none; border-radius:4px; color:white; font-weight:bold; }
</style>
</head>
<body>
<div class="box">
<p>
By clicking Submit, you swear the story you have just made is the truth,<br>
the whole truth and nothing but the truth.
</p>
<form method="post" action="writepastes.php">
<input type="hidden" name="story" value="{$safe_story}">
<input type="hidden" name="confirm" value="yes">
<button type="submit">Submit For Real</button>
</form>
</div>
</body>
</html>
HTML;
            exit;
        }
    } else {
        $error = "Please write something before submitting!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Write a Story</title>
    <style>
        body { background:#2b2b2b; color:#ffffff; font-family:Arial,sans-serif; }
        h1 { font-size:32px; margin-bottom:10px; }
        textarea { font-size:16px; padding:8px; width:90%; height:200px; background:#e0e0e0; color:black; border-radius:4px; border:1px solid #999; }
        .prompt-box { margin-top:20px; padding:15px; background:rgba(0,0,0,0.3); border-radius:6px; }
        .prompt-box h2 { margin:0 0 10px 0; font-size:22px; color:#fff; }
        .prompt-box p { margin:6px 0; font-size:16px; color:#dcdcdc; }
        .main-link { position: fixed; bottom: 20px; left: 20px; color: rgba(32, 214, 241, 0.6); text-decoration: none; font-size: 25px; }
        .main-link:hover { text-decoration: underline; }
        .top-right-header {
            position: absolute; top: 20px; right: 20px; display:flex; align-items:center;
            font-family: Verdana, Geneva, sans-serif; font-size:18px; color: #ffffff;
        }
        .top-right-header img {
            height:35px; width:auto; margin-right:8px; border:2px solid black; border-radius:4px;
        }
        .top-right-header a { color:white; text-decoration:none; margin-left:5px; }
        .top-right-header a:hover { text-decoration:underline; }
    </style>
</head>
<body>

<div class="top-right-header">
    <a href="index.php" style="display:flex; align-items:center; text-decoration:none;">
        <img src="img/logo2.png" alt="Logo">
        <span>UDivulge</span>
    </a>
    <?php if (isset($_SESSION["user_id"])): ?>
        | Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>
        | <a href="logout.php">Logout</a>
    <?php else: ?>
        | <a href="login.php">Login</a> | <a href="register.php">Register</a>
    <?php endif; ?>
</div>

<h1>Write your experience</h1>
<?php
if (!empty($error)) echo "<p style='color:red;'>$error</p>";
if (!empty($success)) echo "<p style='color:#1142e3; font-weight:bold;'>$success</p>";
?>
<form method="post" action="writepastes.php">
    <textarea name="story" rows="20" cols="75" placeholder="Hello I am..."></textarea><br><br>
    <button type="submit">Submit Story</button>
</form>

<div class="prompt-box">
    <h2>Don't know what to write.. Think?</h2>
    <p>- What's the worst thing you have had happen to you?</p>
    <p>- What's the best thing that you had happen to you?</p>
</div>

<a href="index.php" class="main-link">Go Back!</a>
</body>
</html>
