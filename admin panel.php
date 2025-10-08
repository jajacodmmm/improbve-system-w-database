<?php
// PHP code would start here. 
// For example, you might include a configuration file, start a session, or perform a security check.
// session_start();
// include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Panel</title>
  <style>
    :root {
      --bg: #F5F0E1;
      --panel: #fff;
      --muted: #6b7280;
      --text: #1f2937;
      --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      --green: #2F4F2F;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--bg);
      color: var(--text);
    }

    .topbar {
      background-color: var(--panel);
      box-shadow: var(--shadow);
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .topbar h1 {
      margin: 0;
      font-size: 20px;
      color: var(--green);
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--muted);
    }

    .container {
      display: flex;
      height: calc(100vh - 60px);
    }

    .sidebar {
      width: 250px;
      background-color: var(--panel);
      box-shadow: var(--shadow);
      padding: 20px 0;
    }

    .nav-list {
      list-style: none;
      padding: 0;
    }

    .nav-list li {
      padding: 15px 20px;
      font-size: 16px;
      display: flex;
      align-items: center;
      cursor: pointer;
      color: var(--text);
      transition: background 0.3s;
    }

    .nav-list li i {
      margin-right: 10px;
      color: var(--muted);
    }

    .nav-list li:hover {
      background-color: var(--bg);
      color: var(--green);
    }

    .nav-list li.active {
      background-color: var(--green);
      color: #fff;
    }

    .nav-list li.active i {
      color: #fff;
    }

    .dashboard {
      flex: 1;
      padding: 40px;
      overflow-y: auto;
    }

    .dashboard h2 {
      margin-bottom: 20px;
      color: var(--green);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 20px;
    }

    .stat-card {
      background-color: var(--panel);
      box-shadow: var(--shadow);
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      font-weight: bold;
      color: var(--text);
    }

    .stat-card span {
      display: block;
      margin-top: 10px;
      font-size: 24px;
      color: var(--green);
    }

    #content-area {
      margin-top: 40px;
      background-color: var(--panel);
      box-shadow: var(--shadow);
      padding: 20px;
      border-radius: 10px;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
  <header class="topbar">
    <h1>ADMIN Panel</h1>
    <div class="user-info">
      <i class="fas fa-user-circle"></i>
      <span>saint jude admin</span>
    </div>
  </header>

  <div class="container">
    <aside class="sidebar">
      <ul class="nav-list">
        <li><i class="fas fa-home"></i> Dashboard</li>
        <li><i class="fas fa-book"></i> Subject</li>
        <li><i class="fas fa-chalkboard"></i> Class</li>
        <li><i class="fas fa-user-shield"></i> Admin Users</li>
        <li><i class="fas fa-user-tie"></i> Registered Teacher</li>
        <li><i class="fas fa-user-graduate"></i> Students</li>
        <li><i class="fas fa-users"></i> Registered Students</li>
        <li><i class="fas fa-upload"></i> Uploaded Assignments</li>
        <li><i class="fas fa-user-clock"></i> User Log</li>
        <li><i class="fas fa-tasks"></i> Activity Log</li>
        <li><i class="fas fa-calendar"></i> Calendar of Events</li>
      </ul>
    </aside>

    <main class="dashboard">
      <h2>Data Numbers</h2>
      <div class="stats-grid">
        <div class="stat-card">Registered Teacher<br><span>0</span></div>
        <div class="stat-card">Teachers<br><span>0</span></div>
        <div class="stat-card">Registered Students<br><span>0</span></div>
        <div class="stat-card">Students<br><span>0</span></div>
        <div class="stat-card">Class<br><span>0</span></div>
        <div class="stat-card">Subjects<br><span>0</span></div>
      </div>

      <section id="content-area">
        <p>Select a menu item to view details.</p>
      </section>
    </main>
  </div>

  <script>
    const navItems = document.querySelectorAll('.nav-list li');
    const contentArea = document.getElementById('content-area');

    const contentMap = {
      "Dashboard": "<h3>Dashboard Overview</h3><p>Welcome to the admin dashboard.</p>",
      "Subject": "<h3>Subjects</h3><p>Manage subjects offered in the school.</p>",
      "Class": "<h3>Classes</h3><p>View and organize class schedules.</p>",
      "Admin Users": "<h3>Admin Users</h3><p>List and manage admin accounts.</p>",
      "Registered Teacher": "<h3>Registered Teachers</h3><p>Teachers who have completed registration.</p>",
      "Students": "<h3>Students</h3><p>General student information.</p>",
      "Registered Students": "<h3>Registered Students</h3><p>Students who have completed registration.</p>",
      "Uploaded Assignments": "<h3>Uploaded Assignments</h3><p>View assignments submitted by teachers.</p>",
      "User Log": "<h3>User Log</h3><p>Track user login and activity history.</p>",
      "Activity Log": "<h3>Activity Log</h3><p>Monitor system-wide activities.</p>",
      "Calendar of Events": "<h3>Calendar of Events</h3><p>Upcoming school events and holidays.</p>"
    };

    navItems.forEach(item => {
      item.addEventListener('click', () => {
        navItems.forEach(i => i.classList.remove('active'));
        item.classList.add('active');

        const label = item.textContent.trim();
        contentArea.innerHTML = contentMap[label] || "<p>No content available.</p>";
      });
    });

    // Simulate dynamic data update
    window.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        const stats = document.querySelectorAll('.stat-card span');
        const sampleData = [5, 3, 120, 100, 8, 12];
        stats.forEach((el, idx) => {
          el.textContent = sampleData[idx];
        });
      }, 2000);
    });
  </script>
</body>
</html>