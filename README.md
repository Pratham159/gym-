# 🏋️ U.S. Fitness — Gym Management System

A full-stack web-based Gym Management System built with PHP and MySQL. It allows gym members to register, log in, and subscribe to plans, while admins can manage members, plans, trainers, subscriptions, and payments through a dedicated admin panel.

---

## 🌐 Live Demo

[zzxprathammm.xo.je](http://zzxprathammm.xo.je)

---

## ✨ Features

### Member Side
- Register and log in securely (hashed passwords)
- View available gym plans with pricing
- Subscribe to a plan
- Automatic payment record created on subscription

### Admin Side
- Secure admin login
- Dashboard with stats (total members, plans, subscriptions, trainers)
- **Members** — View, Edit, Delete members
- **Plans** — Add, Edit, Delete gym plans
- **Trainers** — Add, Edit, Delete trainers
- **Subscriptions** — View all subscriptions
- **Payments** — Auto-tracked on every subscription
- **Messages** — View and delete contact form messages

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, Bootstrap 5 |
| Backend | PHP 7.x |
| Database | MySQL / MariaDB |
| Hosting | InfinityFree |

---

## 🗄️ Database Schema

The project uses **7 tables**:

| Table | Description |
|-------|-------------|
| `members` | Stores registered gym members |
| `users` | Stores admin accounts |
| `plans` | Gym membership plans (Basic, Standard, Premium) |
| `subscriptions` | Tracks which member subscribed to which plan |
| `payments` | Auto-generated payment record on every subscription |
| `trainers` | Gym trainer profiles |
| `contact_messages` | Messages submitted via the contact form |

---

## ⚙️ Local Setup

### Prerequisites
- XAMPP (or any local server with PHP & MySQL)
- phpMyAdmin

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/us-fitness.git
   ```

2. **Move files to XAMPP**
   ```
   Copy all files to: C:/xampp/htdocs/us-fitness/
   ```

3. **Import the database**
   - Open phpMyAdmin
   - Create a new database: `if0_41652411_usfitness`
   - Import the file: `if0_41652411_usfitness.sql`

4. **Update database credentials**
   Open `Database.php` and update:
   ```php
   $servername = "localhost";
   $username   = "root";
   $pass       = "";
   $db         = "if0_41652411_usfitness";
   ```

5. **Run the project**
   ```
   http://localhost/us-fitness/
   ```

---

## 🔐 Default Admin Login

| Field | Value |
|-------|-------|
| Email | gaurav@gmail.com |
| Password | (set during registration) |

---

## 📁 File Structure

```
gym-/
├── index.php               # Home page with dynamic plans
├── Database.php            # DB connection
├── login.php               # Admin login
├── user_login.php          # Member login
├── user_register.php       # Member registration
├── user_dashboard.php      # Member dashboard + subscription
├── user_logout.php         # Logout
├── admin_dashboard.php     # Admin dashboard
├── admin_members.php       # Manage members
├── admin_plans.php         # Manage plans
├── admin_trainers.php      # Manage trainers
├── admin_subscriptions.php # View subscriptions
├── messages.php            # View contact messages
├── contact.php             # Contact form handler
└── if0_41652411_usfitness.sql  # Database dump
```

---

## 📝 License

This project is licensed under the [MIT License](LICENSE).

---

## 👤 Author

Made with ❤️ as part of an Information Technology / Web Development course project.
