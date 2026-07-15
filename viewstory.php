<?php
session_start();
$conn = new mysqli("localhost", "story_user", "Testing3", "story_list");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$story = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT stories.content, stories.created_at, users.username 
                            FROM stories 
                            LEFT JOIN users ON stories.user_id = users.id 
                            WHERE stories.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($content, $created_at, $username);
        $stmt->fetch();
        $story = [
            'content' => nl2br(htmlspecialchars($content)),
            'created_at' => $created_at,
            'username' => $username ? htmlspecialchars($username) : "Anonymous"
        ];
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Story</title>
    <style>
        body { background:#2b2b2b; color:white; font-family:Arial,sans-serif; text-align:center; }
        .story-box {
            margin:80px auto; padding:20px;
            max-width:700px;
            background:rgba(0,0,0,0.7);
            border-radius:8px;
            border:2px solid #444;
            text-align:left;
        }
        a { color: #0999ff; }
    </style>
</head>
<body>
<?php if ($story): ?>
    <div class="story-box">
        <div style="font-size:14px; color:#aaa;">
            Posted by: <?= $story['username'] ?> at <?= $story['created_at'] ?>
        </div>
        <p><?= $story['content'] ?></p>
    </div>
<?php else: ?>
    <p>No story found.</p>
<?php endif; ?>
<p><a href="pastelists.php">← Back to list</a></p>
</body>
</html>
