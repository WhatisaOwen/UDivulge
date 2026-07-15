<?php
session_start();
$logged_in = false;
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $logged_in = true;
}
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>UDivulge</title>
<style>
body {
    margin: 0;
    background-color: #2b2b2b;
    color: white;
    font-family: 'Open Sans', sans-serif;
}
h1, .subtitle { font-family: 'Open Sans', sans-serif; }

h1 {
    margin: 15px 0 15px 20px;
    font-size: 50px;
    display: inline-block;
    padding-bottom: 5px;
    color: #ffffff;
    -webkit-text-stroke: 1px black;
    text-shadow: 2px 2px 6px rgba(80, 80, 80, 0.7), 0 0 3px #000;
}
p.subtitle {
    margin: 5px 20px 20px 20px;
    font-size: 24px;
    color: #ece5e5;
}
a { color: #00ffff; text-decoration: none; font-family: 'Open Sans', sans-serif; }
a:hover { text-decoration: underline; }
.infobox, .notes { background-color: #2b2b2b; padding: 15px; text-align: center; border-radius: 6px; margin: 20px; border: 3px solid black; }
.forum-box { width: 1800px; margin: 40px 0 0 60px; border: 2px solid #000; background-color: #2b2b2b; box-shadow: 0 0 10px rgba(0,0,0,0.6); }
.forum-entry { padding: 18px 24px; border-bottom: 2px solid #; background-color: #2b2b2b; transition: background-color 0.2s ease, border-color 0.2s ease; }
.forum-entry + .forum-entry { margin-top: 8px; }
.forum-entry:last-child { border-bottom: none; }
.forum-entry a { text-decoration: none; display: block; color: inherit; }
.forum-entry:hover { background-color: #1f1f1f; border-color: #3a3a3a; }
.forum-title { font-size: 16px; font-weight: bold; color: #d0d0d0; }
.forum-subtitle { font-size: 13px; font-weight: normal; color: #777; text-transform: uppercase; letter-spacing: 0.5px; }
.top-right-login { position: absolute; top: 20px; right: 20px; font-family: 'Open Sans', sans-serif; font-size: 16px; color: white; text-align: right; }
.top-right-login a { color: white; text-decoration: none; margin-left: 5px; margin-right: 5px; display: inline-block; }
.top-right-login a:hover { text-decoration: underline; }
.about-us-link { font-size: 12px; display: block; margin-top: 4px; }
.header-logo { height: 45px; margin-left: 10px; vertical-align: middle; border: 2px solid black; border-radius: 4px; }
</style>
</head>
<body>

<h1>UDivulge</h1>
<img src="img/mainlogo1.png" alt="Logo" class="header-logo">

<div class="top-right-login">
    <a href="links.php">Forums</a> |
    <a href="updates.php">Updates</a> |
    <?php if ($logged_in): ?>
        Welcome, <?php echo htmlspecialchars($username); ?> |
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> | <a href="register.php">Register</a>
    <?php endif; ?>
    <a href="aboutus.php" class="about-us-link">About Us</a>
</div>

<p class="subtitle">Please share your craziest or worst life experiences</p>

<div class="infobox">
    <h2>Rules</h2>
    <p>1. Keep your personal information private and do not share sensitive details.</p>
    <p>2. Do not share links/photos here under any circumstances.</p>
</div>

<div class="infobox">
    <h2>Reminder!</h2>
    <p>Please keep these stories mature and reasonable or they will be removed</p>
</div>

<div class="forum-box">
    <div class="forum-entry">
        <a href="writepastes.php">
            <span class="forum-title">Create</span>
        </a>
    </div>
    <div class="forum-entry">
        <a href="pastelists.php">
            <span class="forum-title">Read Stories</span>
        </a>
    </div>
</div>

<div class="notes">
    <h2>Notes</h2>
    <p>Don't forget to check out our <a href="aboutus.php" style="color:#8a8a8a; text-decoration:underline;">About Us</a> page.</p>
    <p>
        Every story posted is
        <p>
</div>

</body>
</html>
