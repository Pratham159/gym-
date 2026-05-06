<?php
    ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include('Database.php');

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "INSERT INTO members (name, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($result) {
        echo "<script>alert('Registered Successfully!'); window.location='user_login.php';</script>";
    } else {
        echo "<script>alert('Something went wrong!');</script>";
    }
}
?>

<html>
<style>
    body {
        text-align: center;
        font-family: Arial;
        background: #111;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;

    }
    .output {
        display: block;
        width: fit-content;
        margin: 5px auto;
        padding: 15px 25px;
        box-shadow: 0 0 20px rgba(255, 115, 0, 0.2);
        background-color: #1c1c1c;
        text-align: left;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        background: #2a2a2a;
        border: 1px solid #444;
        color: white;
        padding: 6px 10px;
        border-radius: 4px;
        width: 200px;
    }

    input[type="submit"] {
        background: linear-gradient(to right, #ff0000, #ff7300);
        border: none;
        color: white;
        font-weight: bold;
        cursor: pointer;
        padding: 8px 25px;
        border-radius: 4px;
    }

    a {
        color: #ff7300;
    }

    td {
        padding: 5px 8px;
    }
</style>

<body>
    <div class="output">
        <h2>Register</h2>
        <form method="POST" action="">
            <table align="center">
                <tr>
                    <td><strong>Name:</strong></td>
                    <td><input type="text" name="name" required></td>
                </tr>
                <tr>
                    <td><strong>E-mail:</strong></td>
                    <td><input type="email" name="email" required></td>
                </tr>
                <tr>
                    <td><strong>Password:</strong></td>
                    <td><input type="password" name="password" required></td>
                </tr>
            </table>
            <br>
            <input type="submit" name="submit" value="Register">
        </form>
        <br>
        <p>Already have an account? <a href="user_login.php">Login here</a></p>
    </div>
</body>

</html>