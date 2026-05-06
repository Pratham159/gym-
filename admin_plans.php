<?php
session_start();
if (!isset($_SESSION['name'])) { header("Location: login.php"); exit(); }
include('Database.php');

// Delete plan
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM plans WHERE id=$id");
    header("Location: admin_plans.php");
    exit();
}

// Update plan
if (isset($_POST['update_plan'])) {
    $id = (int)$_POST['edit_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $duration = $_POST['duration_days'];
    $features = $_POST['features'];
    $stmt = mysqli_prepare($conn, "UPDATE plans SET name=?, price=?, duration_days=?, features=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $name, $price, $duration, $features, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: admin_plans.php");
    exit();
}

// Add plan
if (isset($_POST['add_plan'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $duration = $_POST['duration_days'];
    $features = $_POST['features'];
    $stmt = mysqli_prepare($conn, "INSERT INTO plans (name, price, duration_days, features, is_active) VALUES (?, ?, ?, ?, 1)");
    mysqli_stmt_bind_param($stmt, "ssss", $name, $price, $duration, $features);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: admin_plans.php");
    exit();
}

$plans = mysqli_query($conn, "SELECT * FROM plans ORDER BY price ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Plans — Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #0a0a0a; --surface: #111111; --card: #161616; --border: #222222; --accent: #ff4500; --accent2: #ff7300; --text: #f0f0f0; --muted: #666; }
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
        .section { background: var(--card); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; margin-bottom: 24px; }
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
        .edit-form { display: grid; grid-template-columns: 1fr 1fr 1fr 2fr auto; gap: 10px; align-items: end; }
        .edit-form input, .edit-form textarea { background: var(--bg); border: 1px solid var(--border); color: var(--text); padding: 8px 12px; border-radius: 6px; font-size: 13px; font-family: 'DM Sans', sans-serif; }
        .edit-form input:focus, .edit-form textarea:focus { outline: none; border-color: var(--accent2); }
        .save-btn { background: linear-gradient(135deg, var(--accent), var(--accent2)); color: white; border: none; padding: 8px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; }
        .no-data { padding: 40px; text-align: center; color: var(--muted); font-size: 14px; }
        .add-form { padding: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); }
        input, textarea { background: var(--bg); border: 1px solid var(--border); color: var(--text); padding: 10px 14px; border-radius: 8px; font-size: 14px; font-family: 'DM Sans', sans-serif; transition: border 0.2s; }
        input:focus, textarea:focus { outline: none; border-color: var(--accent2); }
        textarea { resize: vertical; min-height: 80px; }
        .submit-btn { background: linear-gradient(135deg, var(--accent), var(--accent2)); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif; }
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
        <a href="admin_members.php"><span class="icon">👥</span> Members</a>
        <a href="admin_subscriptions.php"><span class="icon">📋</span> Subscriptions</a>
        <a href="admin_plans.php" class="active"><span class="icon">💪</span> Plans</a>
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
        <h2>Plans</h2>
        <p>Manage gym membership plans</p>
    </div>

    <!-- Add Plan Form -->
    <div class="section" style="margin-bottom:24px">
        <div class="section-header"><h3>Add New Plan</h3></div>
        <form method="POST" class="add-form">
            <div class="form-group">
                <label>Plan Name</label>
                <input type="text" name="name" placeholder="e.g. Premium" required>
            </div>
            <div class="form-group">
                <label>Price (₹)</label>
                <input type="number" name="price" placeholder="e.g. 1299" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Duration (Days)</label>
                <input type="number" name="duration_days" placeholder="e.g. 30" required>
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" name="add_plan" class="submit-btn">Add Plan</button>
            </div>
            <div class="form-group full">
                <label>Features</label>
                <textarea name="features" placeholder="e.g. Access to gym equipment, Personal trainer, Diet plan"></textarea>
            </div>
        </form>
    </div>

    <!-- Plans Table -->
    <div class="section">
        <div class="section-header">
            <h3>All Plans</h3>
            <span class="badge"><?php echo mysqli_num_rows($plans); ?> Plans</span>
        </div>
        <?php if (mysqli_num_rows($plans) > 0): mysqli_data_seek($plans, 0); ?>
        <table>
            <tr><th>ID</th><th>Name</th><th>Price</th><th>Duration</th><th>Features</th><th>Active</th><th>Action</th></tr>
            <?php while($p = mysqli_fetch_array($plans)): ?>
            <tr>
                <td>#<?php echo $p['id']; ?></td>
                <td><?php echo $p['name']; ?></td>
                <td>₹<?php echo number_format($p['price'], 2); ?></td>
                <td><?php echo $p['duration_days']; ?> days</td>
                <td><?php echo $p['features'] ?? '-'; ?></td>
                <td><?php echo $p['is_active'] ? '✅' : '❌'; ?></td>
                <td>
                    <a class="edit-btn" onclick="toggleEdit(<?php echo $p['id']; ?>)">Edit</a>
                    <a href="?delete=<?php echo $p['id']; ?>" class="delete-btn" onclick="return confirm('Delete this plan?')">Delete</a>
                </td>
            </tr>
            <tr class="edit-row" id="edit-<?php echo $p['id']; ?>">
                <td colspan="7">
                    <form method="POST" class="edit-form">
                        <input type="hidden" name="edit_id" value="<?php echo $p['id']; ?>">
                        <div><label>Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required></div>
                        <div><label>Price (₹)</label><input type="number" name="price" value="<?php echo $p['price']; ?>" step="0.01" required></div>
                        <div><label>Duration (Days)</label><input type="number" name="duration_days" value="<?php echo $p['duration_days']; ?>" required></div>
                        <div><label>Features</label><input type="text" name="features" value="<?php echo htmlspecialchars($p['features'] ?? ''); ?>"></div>
                        <div><label>&nbsp;</label><button type="submit" name="update_plan" class="save-btn">Save</button></div>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <div class="no-data">No plans found</div>
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
