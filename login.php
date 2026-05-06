<?php
session_start();
include('Database.php');

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email=?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['name'] = $row['name'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid Email or Password!');</script>";
        }
    } else {
        echo "<script>alert('Invalid Email or Password!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login — U.S. Fitness</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0a0a0a; --card: #161616; --border: #222; --accent: #ff4500; --accent2: #ff7300; --text: #f0f0f0; --muted: #666; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 40px; width: 360px; }
        .logo { font-family: 'Bebas Neue', sans-serif; font-size: 28px; letter-spacing: 3px; background: linear-gradient(135deg, var(--accent), var(--accent2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 4px; }
        .subtitle { font-size: 13px; color: var(--muted); margin-bottom: 32px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); margin-bottom: 6px; }
        input { width: 100%; background: var(--bg); border: 1px solid var(--border); color: var(--text); padding: 12px 16px; border-radius: 8px; font-size: 14px; font-family: 'DM Sans', sans-serif; transition: border 0.2s; }
        input:focus { outline: none; border-color: var(--accent2); }
        .submit-btn { width: 100%; background: linear-gradient(135deg, var(--accent), var(--accent2)); color: white; border: none; padding: 13px; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: 'DM Sans', sans-serif; margin-top: 8px; letter-spacing: 1px; }
        .submit-btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">U.S. FITNESS</div>
        <div class="subtitle">Admin Panel Login</div>
        <form method="POST" action="">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="admin@gmail.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="pass" placeholder="••••••••" required>
            </div>
            <button type="submit" name="submit" class="submit-btn">LOGIN</button>
        </form>
    </div>
</body>
</html>
