<?php
// Start session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "phonebook");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve user input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security

    // Prepare the SQL statement to insert the user data
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $password);

        // Execute the statement
        if ($stmt->execute()) {
            // Get the auto-generated user ID
            $generated_id = $stmt->insert_id;

            // Store user ID in the session
            $_SESSION['user_id'] = $generated_id;

            echo "Signup successful! <a href='../php/login.php'>Login here</a>";
            // Redirect to the phonebook page or any other page
            // header("Location: phonebook.php");
        } else {
            echo "Error during signup: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>
<body>
    <div class="form-container">
        <form method="POST" action="signup.php">
            <h2>Signup</h2>
            <label>Name:</label>
            <input type="text" name="name" required>
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Signup</button>
        </form>
    </div>
</body>
</html>
