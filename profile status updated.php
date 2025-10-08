<?php
// Start the session to manage user login state
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// IMPORTANT: Replace 'connection.php' with your actual database connection file.
include("connection.php"); 

// --- Security and Initialization ---
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- Database Query (Secure with Prepared Statements) ---
// Fetches the data needed for the wide profile card
$query = "SELECT user_name, profile_photo, email, course, year_level FROM users WHERE id = ?";
$stmt = mysqli_prepare($con, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id); 
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    // Handle database error
    $user = null;
}

// --- Data Assignment and Sanitization ---
if (!$user) {
    // Default values if user is not found or query failed
    $name = 'User Not Found';
    $email = 'N/A';
    $course = 'N/A';
    $year_level = 'N/A';
    $photo_path = "default.png";
    $status = "Inactive";
} else {
    // Assign fetched and sanitized values
    $name = htmlspecialchars($user['user_name'] ?? 'User Name');
    $email = htmlspecialchars($user['email'] ?? 'user@example.com');
    $course = htmlspecialchars($user['course'] ?? 'Course Name');
    $year_level = htmlspecialchars($user['year_level'] ?? 'N/A');

    $profile_photo_name = $user['profile_photo'] ?? null;
    $photo_path = !empty($profile_photo_name) ? "uploads/" . htmlspecialchars($profile_photo_name) : "default.png";

    // Example Static Data (can be replaced with DB fields if available)
    $status = "Active Student"; 
}
$student_id = "06-24XX-004XXX"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        /* --- General Reset and Layout --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5dc; /* Light beige background */
            color: #333;
        }

        /* --- Top Navigation Bar (Header) --- */
        .navbar {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background: #0f4229; /* Primary Dark Green */
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            background: #1a7a2e; 
            border-radius: 50%;
        }

        .search-bar {
            flex-grow: 1;
            max-width: 400px;
            margin-right: auto;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 20px;
            border: none;
            outline: none;
        }

        .nav-icons > * {
            margin-left: 15px;
            font-size: 1.2em;
            cursor: pointer;
        }

        /* --- Main Content Layout (Flexbox Columns) --- */
        .dashboard-content {
            display: flex;
            padding: 20px;
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .left-panel {
            flex: 2.5; /* Takes up more space for the wide card */
        }

        .right-sidebar {
            flex: 1; /* Sidebar */
            min-width: 300px;
        }

        /* --- Profile Status Card (Wide Box) --- */
        .profile-status-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .photo-area {
            flex-shrink: 0;
            text-align: center;
        }
        
        .photo-area img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 5px solid #0f4229;
            object-fit: cover;
        }

        .profile-info {
            flex-grow: 1;
        }

        .profile-info h3 {
            color: #0f4229;
            margin-bottom: 5px;
            font-size: 1.6em;
        }
        
        .profile-info .status-line {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .status-value {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            background: #e6ffe6;
            color: #1a7a2e;
            margin-left: 10px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Two columns for details */
            gap: 10px 20px;
            font-size: 0.95em;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
        }

        .detail-label {
            font-weight: 600;
            color: #1a7a2e; 
            margin-right: 10px;
        }

        /* --- Tasks/Timeline (Bottom of Left Panel) --- */
        .tasks-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .task-tabs {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
        }

        .task-tab {
            padding: 10px 15px;
            cursor: pointer;
            font-weight: 600;
            color: #777;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }

        .task-tab.active {
            background: #0f4229; /* Darker background for active tab */
            color: white;
            /* Remove border-bottom on active tab for a clean look */
            border-radius: 6px 6px 0 0;
        }

        .task-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 0.95em;
        }

        .task-item:last-child {
            border-bottom: none;
        }
        .task-item a {
            color: #1a7a2e;
            text-decoration: none;
        }
        .task-item span {
            color: #555;
        }
        
        /* --- Sidebar Cards (Right Panel) --- */
        .sidebar-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .sidebar-card h4 {
            color: #0f4229;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .calendar-nav {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
            font-size: 0.8em;
            margin-top: 10px;
        }

        .calendar-nav div {
            padding: 5px 0;
        }
        
        .calendar-nav .day-num {
            font-weight: 600;
            background: #ffcc00; /* Yellow highlight */
            border-radius: 4px;
            color: #0f4229;
            cursor: pointer;
        }
        
        .collaborator-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.9em;
        }
        .collaborator-item .status-dot {
            width: 8px;
            height: 8px;
            background: #1a7a2e;
            border-radius: 50%;
            margin-right: 8px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo-container"></div>
    <div class="search-bar">
        <input type="text" placeholder="Search...">
    </div>
    <div class="nav-icons">
        <span>🔔</span>
        <span>✉️</span>
        <span>👤</span>
    </div>
</div>

<div class="dashboard-content">
    
    <div class="left-panel">
        
        <div class="profile-status-card">
            <div class="photo-area">
                <img src="<?php echo $photo_path; ?>" alt="Profile Photo">
            </div>

            <div class="profile-info">
                <h3><?php echo $name; ?></h3>
                
                <div class="status-line">
                    <span>Status:</span>
                    <span class="status-value active"><?php echo $status; ?></span>
                </div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">ID:</span>
                        <span><?php echo $student_id; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span>
                        <span><?php echo $email; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Course:</span>
                        <span><?php echo $course; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Year:</span>
                        <span><?php echo $year_level; ?></span>
                    </div>
                </div>
                
            </div>
        </div>

        <div class="tasks-container">
            <div class="task-tabs">
                <div class="task-tab active">Today</div>
                <div class="task-tab">Later</div>
                <div class="task-tab">Next week</div>
                <div class="task-tab">Missing</div>
                <div class="task-tab">Done</div>
            </div>
            
            <div class="task-list">
                <div class="task-item">
                    <a href="#">ENTITY RELATIONSHIP DIAGRAM - ITE 083</a>
                    <span>Today, 11:59 PM</span>
                </div>
                <div class="task-item">
                    <a href="#">TITLE PROPOSAL DOCUMENTATION - ITE 083</a>
                    <span>Today, 11:59 PM</span>
                </div>
                </div>
        </div>
    </div>
    
    <div class="right-sidebar">
        
        <div class="sidebar-card">
            <h4>Campus Events</h4>
            <p>There are no upcoming events</p>
            <button style="padding: 8px 15px; background: #ffcc00; border: none; border-radius: 6px; cursor: pointer; margin-top: 10px; font-weight: 600;">Go to Calendar</button>
        </div>
        
        <div class="sidebar-card">
            <h4>Online Collaborators</h4>
            <div class="collaborator-item"><span class="status-dot"></span> Jake Israel Ferrancullo</div>
            <div class="collaborator-item"><span class="status-dot"></span> Ma Jholouise Llerena</div>
            <div class="collaborator-item"><span class="status-dot" style="background: #999;"></span> Other Users (1)</div>
        </div>
        
        <div class="sidebar-card">
            <h4>Academic Timeline</h4>
            <div class="calendar-nav">
                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                <div>29</div><div>30</div><div class="day-num">31</div><div>1</div><div>2</div><div>3</div><div>4</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>