<?php
// PHP Script added on top for server-side processing
$pageTitle = "Online Collaborators";
$currentDate = date("F j, Y");
$online_collaborators_count = 2;

// Function to determine a simple greeting based on the hour
function getGreeting() {
    $hour = date('H');
    if ($hour < 12) {
        return "Good Morning! ☀️";
    } elseif ($hour < 18) {
        return "Good Afternoon! 🌅";
    } else {
        return "Good Evening! 🌙";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> Widget Preview</title>
    
    <style>
        /* CSS STYLING */
        :root {
            --primary-green: #38761D;
            --light-green: #E8F5E9;
            --text-color: #333;
            --online-color: #4CAF50;
            --away-color: #FFC107;
            --widget-bg: #fff;
            --border-color: #eee;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5; /* Simulating the main background */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        /* Widget Container */
        .collaborators-widget {
            width: 300px; /* Standard sidebar widget size */
            padding: 15px;
            border-radius: 8px;
            background-color: var(--widget-bg);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Clearer shadow for preview */
        }

        .collaborators-widget h3 {
            font-size: 16px;
            color: var(--primary-green);
            margin-bottom: 5px; /* Reduced margin to fit subtitle */
            /* border-bottom: 1px solid var(--border-color); */
            padding-bottom: 0px;
        }

        /* Added style for the dynamic subtitle */
        .collaborators-widget .subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-color);
        }

        /* Collaborator Item */
        .collaborator-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            text-decoration: none;
            color: var(--text-color);
            border-radius: 4px;
            transition: background-color 0.2s;
            cursor: pointer; /* Indicate clickability */
        }

        .collaborator-item:hover {
            background-color: var(--light-green);
        }

        /* Avatar Styling */
        .avatar-container {
            position: relative;
            width: 40px;
            height: 40px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .initials {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: var(--primary-green);
            color: #fff;
            font-size: 14px;
            font-weight: bold;
        }

        /* Status Dot */
        .status-indicator {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid var(--widget-bg);
        }

        .collaborator-item.online .status-indicator {
            background-color: var(--online-color);
        }

        .collaborator-item.away .status-indicator {
            background-color: var(--away-color);
        }

        /* Information Text */
        .info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Prevent text overflow */
        }

        .name {
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .detail {
            font-size: 12px;
            color: #888;
        }

        /* Action Icon (Chat Bubble) */
        .action-icon {
            font-size: 18px;
            color: var(--primary-green);
            margin-left: 10px;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .collaborator-item:hover .action-icon {
            opacity: 1;
        }

        /* Other Users/View All Link */
        .other-users-link {
            margin-top: 15px;
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
        }

        .avatar-stack .initials {
            background-color: #ccc;
        }

        .view-all-icon {
            font-size: 14px;
            margin-right: 5px;
            transform: rotate(90deg);
        }
    </style>
</head>
<body>

    <div class="collaborators-widget">
        <h3><?php echo $pageTitle; ?></h3>
        <p class="subtitle">
            <?php echo getGreeting(); ?> Today's Date: <?php echo $currentDate; ?>
        </p>
        
        <div id="collaborator-1" class="collaborator-item online" onclick="toggleStatus('collaborator-1')">
            <div class="avatar-container">
                <span class="initials">JF</span>
                <div class="status-indicator"></div>
            </div>
            <div class="info">
                <span class="name">Jake Israel Ferrarullo</span>
                <span class="detail status-text">Online</span>
            </div>
            <i class="action-icon">💬</i>
        </div>
        
        <div id="collaborator-2" class="collaborator-item away" onclick="toggleStatus('collaborator-2')">
            <div class="avatar-container">
                <span class="initials">ML</span>
                <div class="status-indicator"></div>
            </div>
            <div class="info">
                <span class="name">Ma Polouise Llerena</span>
                <span class="detail status-text">Away (Active 15m ago)</span>
            </div>
            <i class="action-icon">💬</i>
        </div>
        
        <div class="collaborator-item other-users-link">
            <div class="avatar-container avatar-stack">
                <span class="initials">+1</span>
            </div>
            <div class="info">
                <span class="name">Other Collaborators (1)</span>
                <span class="detail">You have <?php echo $online_collaborators_count; ?> active user(s) shown above.</span>
            </div>
            <i class="action-icon view-all-icon">▶</i>
        </div>
    </div>

    <script>
        // JAVASCRIPT FOR INTERACTIVITY
        function toggleStatus(id) {
            const item = document.getElementById(id);
            const statusText = item.querySelector('.status-text');

            if (item.classList.contains('online')) {
                // Change from Online to Away
                item.classList.remove('online');
                item.classList.add('away');
                statusText.textContent = 'Away (Simulated)';
            } else if (item.classList.contains('away')) {
                // Change from Away to Online
                item.classList.remove('away');
                item.classList.add('online');
                statusText.textContent = 'Online';
            }
        }

        // Note: In a real application, clicking the item would typically navigate or open a chat window.
    </script>

</body>
</html>