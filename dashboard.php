<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once 'db.php';

// Access username from session
$username = $_SESSION['username'];

// Handle form submission to create a new note
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // SQL injection protection
    $title = mysqli_real_escape_string($conn, $title);
    $content = mysqli_real_escape_string($conn, $content);

    // Get user ID
    $sql_user_id = "SELECT id FROM users WHERE username='$username'";
    $result_user_id = $conn->query($sql_user_id);
    $row_user_id = $result_user_id->fetch_assoc();
    $user_id = $row_user_id['id'];

    // Insert new note into database
    $sql_insert = "INSERT INTO notes (user_id, title, content) VALUES ('$user_id', '$title', '$content')";
    if ($conn->query($sql_insert) === TRUE) {
        echo "New note created successfully";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

// Retrieve existing notes by the user
$sql_notes = "SELECT * FROM notes WHERE user_id=(SELECT id FROM users WHERE username='$username')";
$result_notes = $conn->query($sql_notes);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $username; ?>!</h2>
        <a href="logout.php">Logout</a>
        <div class="main-container">
        <div class="notes-container">
        <h3>Existing Notes</h3>
        <?php if ($result_notes->num_rows > 0) { ?>
            <ul style="list-style-type:none; padding:0px;">
                <?php while ($row_notes = $result_notes->fetch_assoc()) { ?>
                    <li class="note"> 
                        <strong><?php echo $row_notes['title']; ?></strong>
                        <p><?php echo $row_notes['content']; ?></p>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>No notes found.</p>
        <?php } ?>
        </div>

        <div class="form-container">
        <h3 style="color:white;">Create New Note</h3>
        <form class="form" action="" method="post">
            <div class="form-group">
                <label for="title">Title:</label>
                <input class="text-box" type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea class="content-text-box" name="content" id="content" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <input class="btn" type="submit" value="Create Note">
            </div>
        </form>
        </div>
        </div>


    </div>
</body>
</html>
