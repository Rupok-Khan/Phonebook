<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = new mysqli("localhost", "root", "", "phonebook");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $stmt = $conn->prepare("INSERT INTO contacts (user_id, name, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $name, $phone);
        $stmt->execute();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['contact_id'];
        $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['contact_id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $stmt = $conn->prepare("UPDATE contacts SET name = ?, phone = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $name, $phone, $id, $user_id);
        $stmt->execute();
    }
}

$result = $conn->query("SELECT id, name, phone FROM contacts WHERE user_id = $user_id");
?>

<!DOCTYPE html>s
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phonebook</title>
    <link rel="stylesheet" href="../css/phonebook.css">
</head>
<body>
    <div class="container">
        <h2>Phonebook</h2> <br><br>

        <form method="POST" action="" class="contact-form">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>
            <button type="submit" name="add">Add Contact</button>
        </form>

        <table class="phonebook-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <form method="POST" action="">
                        <td><input type="text" name="name" value="<?= $row['name'] ?>"></td>
                        <td><input type="text" name="phone" value="<?= $row['phone'] ?>"></td>
                        <td>
                            <button type="submit" name="edit">Edit</button>
                            <button type="submit" name="delete">Delete</button>
                            <input type="hidden" name="contact_id" value="<?= $row['id'] ?>">
                        </td>
                    </form>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

