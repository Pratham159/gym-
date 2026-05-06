<?php
session_start();
if (!isset($_SESSION['name'])) { header("Location: login.php"); exit(); }
include('Database.php');

// Delete member
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM members WHERE id=$id");
    header("Location: admin_members.php");
    exit();
}

// Update member
if (isset($_POST['update_member'])) {
    $id = (int)$_POST['edit_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $stmt = mysqli_prepare($conn, "UPDATE members SET name=?, email=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: admin_members.php");
    exit();
}

$members = mysqli_query($conn, "SELECT * FROM members ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Members — Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0a; --surface: #111111; --card: #161616;
            --border: #222222; --accent: #ff4500; --accent2: #ff7300;
            --text: #f0f0f0; --muted: #666;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; position: fixed; height: 100vh; }
        .logo { padding: 28px 24px; border-bottom: 1px solid var(--border); }
        .logo h1 { font-family: 'Bebas Neue', sans-serif; font-size: 26px; letter-spacing: 2px; background: linear-gradient(135deg, var(--accent), var(--accent2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .logo p { font-size: 11px; color: var(--muted); letter-spacing: 1px; text-transform: uppercase; margin-top: 2px; }
        .nav { padding: 20px 12px; flex: 1; }
        .nav-label { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: var(--muted); padding: 0 12px; margin: 16px 0 8px; }
        .nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; color: var(--muted); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; margin-bottom: 2px; }
        .nav a:hover, .nav a.active { background: rgba(255,69,0,0.1); color: var(--accent2); }
        .nav a .icon { font-size: 16px; width: 20px; text-align: center; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid var(--border); }
        .admin-info { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: var(--card); border-radius: 8px; margin-bottom: 8px; }
        .avatar { width: 32px; height: 32px; background: linear-gradient(135deg, var(--accent), var(--accent2)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0; }
        .admin-info .name { font-size: 13px; font-weight: 600; }
        .admin-info .role { font-size: 11px; color: var(--muted); }
        .logout-btn { display: block; text-align: center; padding: 8px; background: rgba(255,69,0,0.1); color: var(--accent); border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; transition: all 0.2s; }
        .logout-btn:hover { background: var(--accent); color: white; }
        .main { margin-left: 240px; flex: 1; padding: 32px; }
        .page-header { margin-bottom: 32px; }
        .page-header h2 { font-family: 'Bebas Neue', sans-serif; font-size: 36px; letter-spacing: 2px; }
        .page-header p { color: var(--muted); font-size: 14px; margin-top: 4px; }
        .section { background: var(--card); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
        .section-header { padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .section-header h3 { font-family: 'Bebas Neue', sans-serif; font-size: 20px; letter-spacing: 1px; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 12px 24px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border); }
        td { padding: 14px 24px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.04); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,0.02); }
        .delete-btn { background: rgba(239,68,68,0.1); color: #ef4444; border: none; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
        .delete-btn:hover { background: #ef4444; color: white; }
        .edit-btn { background: rgba(255,115,0,0.1); color: var(--accent2); border: none; padding: 5px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; text-decoration: none; transition: all 0.2s; margin-right: 6px; }
        .edit-btn:hover { background: var(--accent2); color: white; }
        .edit-row { display: none; background: rgba(255,115,0,0.05); }
        .edit-row td { padding: 16px 24px; }
        .edit-form { display: flex; gap: 10px; align-items: end; flex-wrap: wrap; }
        .edit-form input { background: var(--bg); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 6px; font-size: 13px; font-family: 'DM Sans', sans-serif; }
        .edit-form input:focus { outline: none; border-color: var(--accent2); }
        .save-btn { background: linear-gradient(135deg, var(--accent), var(--accent2)); color: white; border: none; padding: 8px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .no-data { padding: 40px; text-align: center; color: var(--muted); font-size: 14px; }
        .badge { background: rgba(255,69,0,0.15); color: var(--accent2); font-size: 12px; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="logo"><h1>U.S. FITNESS</h1><p>Admin Panel</p></div>
    <nav class="nav">
        <div class="nav-label">Overview</div>
        <a href="admin_dashboard.php"><span class="icon">⚡</span> Dashboard</a>
        <div class="nav-label">Manage</div>
        <a href="admin_members.php" class="active"><span class="icon">👥</span> Members</a>
        <a href="admin_subscriptions.php"><span class="icon">📋</span> Subscriptions</a>
        <a href="admin_plans.php"><span class="icon">💪</span> Plans</a>
        <a href="admin_trainers.php"><span class="icon">🏋️</span> Trainers</a>
        <a href="messages.php"><span class="icon">✉️</span> Messages</a>
    </nav>
    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
            <div><div class="name"><?php echo $_SESSION['name']; ?></div><div class="role">Administrator</div></div>
        </div>
        <a href="user_logout.php" class="logout-btn">Logout</a>
    </div>
</aside>
<main class="main">
    <div class="page-header">
        <h2>Members</h2>
        <p>All registered gym members</p>
    </div>
    <div class="section">
        <div class="section-header">
            <h3>All Members</h3>
            <span class="badge"><?php echo mysqli_num_rows($members); ?> Total</span>
        </div>
        <?php if (mysqli_num_rows($members) > 0): ?>
        <table>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Joined</th><th>Action</th></tr>
            <?php while($m = mysqli_fetch_array($members)): ?>
            <tr>
                <td>#<?php echo $m['id']; ?></td>
                <td><?php echo htmlspecialchars($m['name']); ?></td>
                <td><?php echo htmlspecialchars($m['email']); ?></td>
                <td><?php echo date('d M Y', strtotime($m['created_at'])); ?></td>
                <td>
                    <a class="edit-btn" onclick="toggleEdit(<?php echo $m['id']; ?>)">Edit</a>
                    <a href="?delete=<?php echo $m['id']; ?>" class="delete-btn" onclick="return confirm('Delete this member?')">Delete</a>
                </td>
            </tr>
            <tr class="edit-row" id="edit-<?php echo $m['id']; ?>">
                <td colspan="5">
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="edit_id" value="<?php echo $m['id']; ?>">
                        <div><label>Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($m['name']); ?>" required></div>
                        <div><label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($m['email']); ?>" required></div>
                        <div style="align-self:flex-end"><button type="submit" name="update_member" class="save-btn">Save</button></div>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <div class="no-data">No members found</div>
        <?php endif; ?>
    </div>
</main>
<script>
function toggleEdit(id) {
    var row = document.getElementById('edit-' + id);
    row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row';
}
</script>
</body>
</html>
