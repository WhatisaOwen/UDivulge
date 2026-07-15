<?php
session_start();
$conn = new mysqli("localhost", "story_user", "Testing3", "story_list");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["removeIndex"], $_POST["removeCode"])) {
        if (trim($_POST["removeCode"]) === "Temporary_Phrase1") {
            $removeId = intval($_POST["removeIndex"]);
            $stmt = $conn->prepare("DELETE FROM stories WHERE id = ?");
            $stmt->bind_param("i", $removeId);
            $stmt->execute();
            $stmt->close();
            header("Location: pastelists.php");
            exit;
        }
    }
    if (isset($_POST["showIndex"])) {
        $showIndex = intval($_POST["showIndex"]);
    }
}

$sql = "SELECT stories.id, stories.content, stories.created_at, users.username 
        FROM stories 
        LEFT JOIN users ON stories.user_id = users.id 
        ORDER BY stories.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stories</title>
    <style>
        body { margin:0; background:#2b2b2b; color:white; font-family:Arial,sans-serif; }
        h1 { margin:20px; font-size:28px; }
        .story { margin:20px; padding:15px; background:rgba(0,0,0,0.4); border:1px solid gray; border-radius:6px; position:relative; }
        .story .author { font-size:14px; color:#aaa; margin-bottom:6px; }
        .remove-btn {
            position:absolute; top:10px; right:10px;
            background:#444; color:white; border:none;
            padding:4px 8px; cursor:pointer;
            border-radius:4px; font-weight:bold;
        }
        .remove-box {
            margin-top:10px; padding:10px;
            background:black; border:1px solid #666;
            border-radius:4px;
        }
        .remove-box input[type="text"] {
            width:150px; padding:4px; margin-right:6px;
        }
        .back-link {
            margin:20px; display:inline-block; color: #2c9dff;
            text-decoration:underline; cursor:pointer;
        }
        a.story-link { color:white; text-decoration:none; display:block; }
        a.story-link:hover { text-decoration:underline; }
    </style>
</head>
<body>
<h1>Stories</h1>
<a class="back-link" href="writepastes.php"> Feel free to Write</a>

<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $content = nl2br(htmlspecialchars($row['content']));
        $author = isset($row['username']) ? htmlspecialchars($row['username']) : 'Anonymous';
        $time = $row['created_at'];
        $storyId = $row['id'];

        echo "<div class='story'>";
        echo "<div class='author'>Posted by: $author at $time</div>";
        echo "<a href='viewstory.php?id=$storyId' class='story-link'>";
        echo $content;
        echo "</a>";
        echo "<form method='post' style='position:absolute; top:10px; right:10px; margin:0;'>";
        echo "<input type='hidden' name='showIndex' value='$storyId'>";
        echo "<button type='submit' class='remove-btn'>?</button>";
        echo "</form>";

        if (isset($showIndex) && $showIndex == $storyId) {
            echo "<div class='remove-box'>";
            echo "<form method='post' style='margin:0;'>";
            echo "<input type='hidden' name='removeIndex' value='$storyId'>";
            echo "<input type='text' name='removeCode' placeholder='Reason for report?'>";
            echo "<button type='submit'>Confirm</button>";
            echo "</form>";
            echo "</div>";
        }

        echo "</div>";
    }
} else {
    echo "<p style='margin:20px;'>No stories yet.</p>";
}

$conn->close();
?>
</body>
</html>
