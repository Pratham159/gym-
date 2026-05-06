<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}
include('Database.php');

// Stats
$total_members = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM members"))['cnt'];
$total_messages = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM contact_messages"))['cnt'];
$total_plans = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM plans"))['cnt'];
$total_trainers = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM trainers"))['cnt'];
$active_subs = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM subscriptions WHERE status='active'"))['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — U.S. Fitness</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0a;
            --surface: #111111;
            --card: #161616;
            --border: #222222;
            --accent: #ff4500;
            --accent2: #ff7300;
            --text: #f0f0f0;
            --muted: #666;
            --success: #22c55e;
            --warning: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .logo {
            padding: 28px 24px;
            border-bottom: 1px solid var(--border);
        }

        .logo h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 26px;
            letter-spacing: 2px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo p {
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .nav {
            padding: 20px 12px;
            flex: 1;
        }

        .nav-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--muted);
            padding: 0 12px;
            margin: 16px 0 8px;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .nav a:hover, .nav a.active {
            background: rgba(255,69,0,0.1);
            color: var(--accent2);
        }

        .nav a .icon { font-size: 16px; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: var(--card);
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .admin-info .name { font-size: 13px; font-weight: 600; }
        .admin-info .role { font-size: 11px; color: var(--muted); }

        .logout-btn {
            display: block;
            text-align: center;
            padding: 8px;
            background: rgba(255,69,0,0.1);
            color: var(--accent);
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .logout-btn:hover { background: var(--accent); color: white; }

        /* MAIN */
        .main {
            margin-left: 240px;
            flex: 1;
            padding: 32px;
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-header h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 36px;
            letter-spacing: 2px;
        }

        .page-header p { color: var(--muted); font-size: 14px; margin-top: 4px; }

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-2px); }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
        }

        .stat-icon { font-size: 24px; margin-bottom: 12px; }
        .stat-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 40px;
            line-height: 1;
            color: var(--text);
        }
        .stat-label { font-size: 12px; color: var(--muted); margin-top: 4px; text-transform: uppercase; letter-spacing: 1px; }

        /* TABLES */
        .section {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .section-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-header h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 20px;
            letter-spacing: 1px;
        }

        .badge {
            background: rgba(255,69,0,0.15);
            color: var(--accent2);
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        table { width: 100%; border-collapse: collapse; }

        th {
            padding: 12px 24px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--muted);
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 14px 24px;
            font-size: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,0.02); }

        .status-active {
            background: rgba(34,197,94,0.15);
            color: var(--success);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-expired {
            background: rgba(239,68,68,0.15);
            color: #ef4444;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .no-data {
            padding: 40px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .view-all {
            color: var(--accent2);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }

        .delete-btn {
            background: rgba(239,68,68,0.1);
            color: #ef4444;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .delete-btn:hover { background: #ef4444; color: white; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="logo">
        <h1>U.S. FITNESS</h1>
        <p>Admin Panel</p>
    </div>
    <nav class="nav">
        <div class="nav-label">Overview</div>
        <a href="admin_dashboard.php" class="active">
            <span class="icon">⚡</span> Dashboard
        </a>
        <div class="nav-label">Manage</div>
        <a href="admin_members.php">
            <span class="icon">👥</span> Members
        </a>
        <a href="admin_subscriptions.php">
            <span class="icon">📋</span> Subscriptions
        </a>
        <a href="admin_plans.php">
            <span class="icon">💪</span> Plans
        </a>
        <a href="admin_trainers.php">
            <span class="icon">🏋️</span> Trainers
        </a>
        <a href="messages.php">
            <span class="icon">✉️</span> Messages
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
            <div>
                <div class="name"><?php echo $_SESSION['name']; ?></div>
                <div class="role">Administrator</div>
            </div>
        </div>
        <a href="user_logout.php" class="logout-btn">Logout</a>
    </div>
</aside>

<main class="main">
    <div class="page-header">
        <h2>Dashboard</h2>
        <p>Welcome back, <?php echo $_SESSION['name']; ?>. Here's what's happening at U.S. Fitness.</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-value"><?php echo $total_members; ?></div>
            <div class="stat-label">Total Members</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-value"><?php echo $active_subs; ?></div>
            <div class="stat-label">Active Subscriptions</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💪</div>
            <div class="stat-value"><?php echo $total_plans; ?></div>
            <div class="stat-label">Plans Available</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏋️</div>
            <div class="stat-value"><?php echo $total_trainers; ?></div>
            <div class="stat-label">Trainers</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✉️</div>
            <div class="stat-value"><?php echo $total_messages; ?></div>
            <div class="stat-label">Messages</div>
        </div>
    </div>

    <!-- Recent Members & Subscriptions -->
    <div class="grid-2">
        <div class="section">
            <div class="section-header">
                <h3>Recent Members</h3>
                <a href="admin_members.php" class="view-all">View All →</a>
            </div>
            <?php
            $members = mysqli_query($conn, "SELECT * FROM members ORDER BY id DESC LIMIT 5");
            if (mysqli_num_rows($members) > 0): ?>
            <table>
                <tr><th>ID</th><th>Phone</th><th>Gender</th><th>Joined</th></tr>
                <?php while($m = mysqli_fetch_array($members)): ?>
                <tr>
                    <td>#<?php echo $m['id']; ?></td>
                    <td><?php echo $m['phone'] ?? '-'; ?></td>
                    <td><?php echo $m['gender'] ?? '-'; ?></td>
                    <td><?php echo isset($m['joined_at']) ? date('d M Y', strtotime($m['joined_at'])) : '-'; ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <div class="no-data">No members yet</div>
            <?php endif; ?>
        </div>

        <div class="section">
            <div class="section-header">
                <h3>Recent Messages</h3>
                <a href="messages.php" class="view-all">View All →</a>
            </div>
            <?php
            $msgs = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5");
            if (mysqli_num_rows($msgs) > 0): ?>
            <table>
                <tr><th>Name</th><th>Email</th><th>Message</th></tr>
                <?php while($msg = mysqli_fetch_array($msgs)): ?>
                <tr>
                    <td><?php echo $msg['name']; ?></td>
                    <td><?php echo $msg['email']; ?></td>
                    <td><?php echo substr($msg['message'], 0, 30) . '...'; ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <div class="no-data">No messages yet</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Subscriptions -->
    <div class="section">
        <div class="section-header">
            <h3>Active Subscriptions</h3>
            <span class="badge"><?php echo $active_subs; ?> Active</span>
        </div>
        <?php
        $subs = mysqli_query($conn, "SELECT s.*, p.name as plan_name FROM subscriptions s JOIN plans p ON s.plan_id = p.id ORDER BY s.id DESC LIMIT 5");
        if (mysqli_num_rows($subs) > 0): ?>
        <table>
            <tr><th>ID</th><th>Member ID</th><th>Plan</th><th>Start</th><th>End</th><th>Status</th></tr>
            <?php while($s = mysqli_fetch_array($subs)): ?>
            <tr>
                <td>#<?php echo $s['id']; ?></td>
                <td>#<?php echo $s['member_id']; ?></td>
                <td><?php echo $s['plan_name']; ?></td>
                <td><?php echo date('d M Y', strtotime($s['start_date'])); ?></td>
                <td><?php echo date('d M Y', strtotime($s['end_date'])); ?></td>
                <td><span class="status-<?php echo $s['status']; ?>"><?php echo ucfirst($s['status']); ?></span></td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
        <div class="no-data">No subscriptions yet</div>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
