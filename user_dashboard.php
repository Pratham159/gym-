<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: user_login.php");
    exit();
}
include('Database.php');

$member_id = $_SESSION['user_id'] ?? null;

// Handle subscription
if (isset($_POST['subscribe']) && $member_id) {
    $plan_id = (int)$_POST['plan_id'];
    $plan = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM plans WHERE id=$plan_id"));
    $start = date('Y-m-d');
    $end = date('Y-m-d', strtotime("+{$plan['duration_days']} days"));

    // Insert subscription
    mysqli_query($conn, "INSERT INTO subscriptions (member_id, plan_id, start_date, end_date, status) VALUES ('$member_id', '$plan_id', '$start', '$end', 'active')");

    // Get new subscription ID and insert payment
    $sub_id = mysqli_insert_id($conn);
    $amount = $plan['price'];
    mysqli_query($conn, "INSERT INTO payments (subscription_id, amount, method, status) VALUES ('$sub_id', '$amount', 'cash', 'paid')");

    echo "<script>alert('Subscribed Successfully!');</script>";
}

$plans = mysqli_query($conn, "SELECT * FROM plans WHERE is_active=1");
$current_sub = null;
if ($member_id) {
    $current_sub = mysqli_fetch_array(mysqli_query($conn, "SELECT s.*, p.name as plan_name FROM subscriptions s JOIN plans p ON s.plan_id=p.id WHERE s.member_id=$member_id AND s.status='active' ORDER BY s.id DESC LIMIT 1"));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard — U.S. Fitness</title>
</head>
<style>
    body {
        font-family: Arial;
        background: #111;
        color: white;
        margin: 0;
        padding: 20px;
    }
    .container { max-width: 800px; margin: 0 auto; }
    .card {
        background: #1c1c1c;
        border-radius: 8px;
        padding: 20px 25px;
        margin-bottom: 20px;
        box-shadow: 0 0 20px rgba(255,115,0,0.1);
    }
    h2 { color: #ff7300; }
    h3 { color: #ff7300; margin-bottom: 15px; }
    a { color: #ff7300; text-decoration: none; }
    .btn-logout {
        background: linear-gradient(to right, #ff0000, #ff7300);
        border: none;
        color: white;
        font-weight: bold;
        cursor: pointer;
        padding: 8px 25px;
        border-radius: 4px;
        text-decoration: none;
    }
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 15px;
    }
    .plan-card {
        background: #222;
        border: 1px solid #333;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
    }
    .plan-card h4 { color: #ff7300; font-size: 18px; margin-bottom: 8px; }
    .plan-price { font-size: 24px; font-weight: bold; margin: 8px 0; }
    .plan-duration { color: #aaa; font-size: 13px; margin-bottom: 8px; }
    .plan-features { color: #ccc; font-size: 13px; margin-bottom: 16px; text-align: left; }
    .btn-subscribe {
        background: linear-gradient(to right, #ff4500, #ff7300);
        border: none;
        color: white;
        font-weight: bold;
        cursor: pointer;
        padding: 8px 20px;
        border-radius: 4px;
        width: 100%;
    }
    .btn-subscribe:hover { opacity: 0.9; }
    .sub-info {
        background: rgba(255,115,0,0.1);
        border: 1px solid #ff7300;
        border-radius: 8px;
        padding: 16px;
    }
    .sub-info p { margin: 5px 0; }
    .status-active { color: #22c55e; font-weight: bold; }
    .no-member-warning {
        background: rgba(255,69,0,0.1);
        border: 1px solid #ff4500;
        border-radius: 8px;
        padding: 16px;
        color: #ff7300;
    }
</style>
<body>
<div class="container">

    <!-- Welcome Card -->
    <div class="card">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
        <p>You are logged in as a U.S. Fitness member.</p>
        <br>
        <a href="index.php">← Back to Home</a>
        &nbsp;&nbsp;
        <a href="user_logout.php" class="btn-logout">Logout</a>
    </div>

    <!-- Current Subscription -->
    <div class="card">
        <h3>My Subscription</h3>
        <?php if ($current_sub): ?>
        <div class="sub-info">
            <p><strong>Plan:</strong> <?php echo $current_sub['plan_name']; ?></p>
            <p><strong>Start Date:</strong> <?php echo date('d M Y', strtotime($current_sub['start_date'])); ?></p>
            <p><strong>End Date:</strong> <?php echo date('d M Y', strtotime($current_sub['end_date'])); ?></p>
            <p><strong>Status:</strong> <span class="status-active">Active ✅</span></p>
        </div>
        <?php else: ?>
        <p style="color:#aaa">You have no active subscription. Choose a plan below!</p>
        <?php endif; ?>
    </div>

    <!-- Available Plans -->
    <div class="card">
        <h3>Available Plans</h3>
        <?php if (!$member_id): ?>
        <div class="no-member-warning">
            ⚠️ Your session has expired. Please <a href="user_login.php">login again</a>.
        </div>
        <?php else: ?>
        <div class="plans-grid">
            <?php while($plan = mysqli_fetch_array($plans)): ?>
            <div class="plan-card">
                <h4><?php echo $plan['name']; ?></h4>
                <div class="plan-price">₹<?php echo number_format($plan['price'], 0); ?></div>
                <div class="plan-duration"><?php echo $plan['duration_days']; ?> days</div>
                <div class="plan-features"><?php echo $plan['features'] ?? ''; ?></div>
                <form method="POST">
                    <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                    <button type="submit" name="subscribe" class="btn-subscribe">Subscribe</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
