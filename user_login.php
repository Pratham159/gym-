<?php
session_start();
include('Database.php');

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM members WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            echo "<script>alert('Logged In Successfully!'); window.location='user_dashboard.php';</script>";
        } else {
            echo "<script>alert('Invalid Email or Password!');</script>";
        }
    } else {
        echo "<script>alert('Invalid Email or Password!');</script>";
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
        box-shadow: 0 0 20px rgba(255,115,0,0.2);
        background-color: #1c1c1c;
        text-align: left;
    }
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
    a { color: #ff7300; }
    td { padding: 5px 8px; }
</style>
<body>
    <div class="output">
        <h2>Login</h2>
        <form method="POST" action="">
            <table align="center">
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
            <input type="submit" name="submit" value="Login">
        </form>
        <br>
        <p>Don't have an account? <a href="user_register.php">Register here</a></p>
    </div>
</body>
</html>