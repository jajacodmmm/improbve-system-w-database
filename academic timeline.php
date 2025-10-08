<?php
/**
 * PHP placeholder block.
 * * In a real application, you would use this space to:
 * 1. Connect to a database.
 * 2. Fetch academic deadlines and convert them to a JSON object for JavaScript.
 * (e.g., $deadlines_json = json_encode($db_results);)
 * 3. Calculate the current semester progress dynamically.
 * * For this example, we will keep the MOCK_DEADLINES data directly in the <script> block
 * but ensure the structure is ready for server-side implementation.
 */

// Example: Define variables that might be used later in HTML or JS
$user_name = "Angela Cunanan";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Improved Academic Timeline</title>
    
    <style>
        /* General Styling & Dashboard Context */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f0;
        }

        /* Wrapper for positioning the absolute popover correctly */
        .dashboard-card-wrapper {
            position: relative; 
            display: flex;
            justify-content: flex-start;
            padding: 20px;
        }

        /* --- Academic Timeline Container --- */
        .academic-timeline-container {
            background-color: #f7f3e8; /* Light cream background */
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            width: 300px;
            font-size: 14px;
            border: 1px solid #e0e0d0;
        }

        .timeline-header h3 {
            color: #38761d; /* Darker green for header */
            margin-top: 0;
            margin-bottom: 15px;
        }

        /* Calendar Controls */
        .calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-weight: bold;
            color: #333;
        }

        .control-btn {
            background: none;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
            color: #4CAF50; /* Primary green */
            padding: 5px;
            transition: color 0.2s;
        }

        /* Calendar Grid */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 3px;
            background-color: #fff;
            padding: 5px;
            border-radius: 5px;
        }

        .day-header {
            text-align: center;
            font-size: 0.75em;
            font-weight: bold;
            color: #555;
            padding: 5px 0;
        }

        .calendar-day {
            aspect-ratio: 1 / 1; /* Makes cells square */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 3px;
            font-size: 0.85em;
            cursor: pointer;
            border: 1px solid #eee;
            background-color: #fff;
            border-radius: 5px;
            position: relative;
            transition: background-color 0.2s;
            user-select: none;
        }

        .calendar-day.inactive {
            color: #ccc;
            background-color: #f9f9f9;
            pointer-events: none;
        }

        .calendar-day:not(.inactive):hover {
            background-color: #e6ffe6; /* Light green hover */
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        .day-number {
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
        }

        /* Status Indicators (Dots) */
        .indicators-row {
            display: flex;
            justify-content: center;
            margin-top: 2px;
        }
        .day-indicator {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            margin: 0 1px;
            display: inline-block;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
        }

        /* Color Coding */
        .exam { background-color: #FF6347; /* Red - Major */ }
        .project { background-color: #4682B4; /* Blue - Secondary */ }
        .quiz { background-color: #FFD700; /* Gold - Minor */ }

        /* Current Day Highlight */
        .today {
            border: 2px solid #4CAF50; /* Bright green border */
            background-color: #e0ffe0; /* Lightest green fill */
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.8);
        }

        /* --- Footer and Progress Bar --- */
        .timeline-footer {
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Progress Bar */
        .progress-bar-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .progress-label {
            font-size: 0.8em;
            color: #555;
            margin-right: 5px;
            white-space: nowrap;
        }

        .progress-bar-bg {
            flex-grow: 1;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background-color: #4CAF50;
            border-radius: 4px;
            transition: width 0.5s ease-out;
        }

        .progress-percentage {
            font-size: 0.8em;
            font-weight: bold;
            margin-left: 5px;
            color: #4CAF50;
        }

        /* Legend */
        .legend {
            display: flex;
            justify-content: space-around;
            padding-top: 5px;
        }

        .legend-item {
            font-size: 0.7em;
            color: #555;
            display: flex;
            align-items: center;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 4px;
        }

        /* --- Popover Styling --- */
        .task-detail-popover {
            /* Positioned relative to the main dashboard wrapper */
            position: absolute; 
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: none; /* Hidden by default */
            width: 250px;
        }

        .task-detail-popover h4 {
            color: #38761d;
            font-size: 1.1em;
            margin-top: 0;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        #popover-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #popover-list li {
            font-size: 0.9em;
            margin-bottom: 5px;
            padding: 2px 0;
            border-left: 3px solid transparent;
            padding-left: 5px;
        }

        #popover-list li.exam-task { border-left-color: #FF6347; }
        #popover-list li.project-task { border-left-color: #4682B4; }
        #popover-list li.quiz-task { border-left-color: #FFD700; }
    </style>
</head>
<body>

    <div style="padding: 20px; background-color: #f5f5f0; min-height: 450px;">
        <div class="dashboard-card-wrapper">
            
            <div class="academic-timeline-container">
                <div class="timeline-header">
                    <h3>Academic Timeline for <?php echo $user_name; ?> 🗓️</h3>
                </div>
                <div class="calendar-controls">
                    <button class="control-btn" id="prevMonth">←</button>
                    <span id="currentMonthYear"></span>
                    <button class="control-btn" id="nextMonth">→</button>
                </div>
                
                <div class="calendar-grid">
                    <div class="day-header">Sun</div>
                    <div class="day-header">Mon</div>
                    <div class="day-header">Tue</div>
                    <div class="day-header">Wed</div>
                    <div class="day-header">Thu</div>
                    <div class="day-header">Fri</div>
                    <div class="day-header">Sat</div>
                    
                    </div>
                
                <div class="timeline-footer">
                    <div class="progress-bar-container">
                        <div class="progress-label">Semester Progress:</div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: 0%;"></div>
                        </div>
                        <span class="progress-percentage">0%</span>
                    </div>
                    <div class="legend">
                        <span class="legend-item"><span class="legend-dot exam"></span> Exam</span>
                        <span class="legend-item"><span class="legend-dot project"></span> Project</span>
                        <span class="legend-item"><span class="legend-dot quiz"></span> Quiz/HW</span>
                    </div>
                </div>
            </div>

            <div id="task-detail-popover" class="task-detail-popover">
                <h4>Deadlines for <span id="popover-date"></span></h4>
                <ul id="popover-list">
                    </ul>
            </div>
            
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const calendarGrid = document.querySelector('.calendar-grid');
            const currentMonthYearSpan = document.getElementById('currentMonthYear');
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');
            const popover = document.getElementById('task-detail-popover');
            const popoverDateSpan = document.getElementById('popover-date');
            const popoverList = document.getElementById('popover-list');
            
            // Set the initial date to October 2025 (Month index 9)
            let currentDate = new Date(2025, 9, 1); 

            // --- Mock Deadline Data ---
            // NOTE: In a PHP implementation, this object would be generated by PHP and
            // inserted here using: const MOCK_DEADLINES = <?php echo $deadlines_json; ?>;
            const MOCK_DEADLINES = {
                '2025-09-29': [{ type: 'project', course: 'ITE 083', name: 'Entity Relationship Diagram' }],
                '2025-09-30': [{ type: 'project', course: 'ITE 083', name: 'Title Proposal Documentation' }],
                '2025-10-04': [{ type: 'quiz', course: 'PED 002', name: 'CPEEK' }],
                '2025-10-08': [{ type: 'project', course: 'ITE 083', name: 'Entity Diagram Draft' }],
                '2025-10-15': [{ type: 'exam', course: 'BSIT 2-5', name: 'Midterm Exam' }],
                '2025-10-22': [{ type: 'project', course: 'ITE 208', name: 'Final System Design' }],
                '2025-10-25': [
                    { type: 'exam', course: 'ITE 204', name: 'Networking Finals' },
                    { type: 'quiz', course: 'BSIT 2-5', name: 'Module 3 Quiz' },
                ],
            };

            // Helper to format date key (YYYY-MM-DD)
            const formatDateKey = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            // --- Calendar Generation Function ---
            function renderCalendar(date) {
                // Clear grid and re-add day headers (for navigation clarity)
                calendarGrid.innerHTML = ''; 
                const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                dayNames.forEach(day => {
                    const header = document.createElement('div');
                    header.className = 'day-header';
                    header.textContent = day;
                    calendarGrid.appendChild(header);
                });

                const year = date.getFullYear();
                const month = date.getMonth();
                const today = new Date();

                // Update header
                currentMonthYearSpan.textContent = date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

                const firstDayOfMonth = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                // 1. Add filler days from the previous month (inactive)
                const prevMonthDays = new Date(year, month, 0).getDate();
                for (let i = 0; i < firstDayOfMonth; i++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'calendar-day inactive';
                    dayDiv.innerHTML = `<span class="day-number">${prevMonthDays - firstDayOfMonth + i + 1}</span>`;
                    calendarGrid.appendChild(dayDiv);
                }

                // 2. Add days for the current month
                let dayCounter = 1;
                let dayCountInGrid = firstDayOfMonth;

                while (dayCounter <= daysInMonth) {
                    const dayDate = new Date(year, month, dayCounter);
                    const dateKey = formatDateKey(dayDate);
                    const deadlines = MOCK_DEADLINES[dateKey] || [];

                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'calendar-day';
                    dayDiv.dataset.date = dateKey;

                    // Check for 'Today'
                    if (dayDate.toDateString() === today.toDateString()) {
                        dayDiv.classList.add('today');
                    }

                    dayDiv.innerHTML = `<span class="day-number">${dayCounter}</span>`;

                    // Add deadline indicators
                    if (deadlines.length > 0) {
                        const indicatorsRow = document.createElement('div');
                        indicatorsRow.className = 'indicators-row';
                        
                        // Limit to 3 unique types for display compactness
                        const uniqueTypes = [...new Set(deadlines.map(d => d.type))]; 

                        uniqueTypes.forEach(type => {
                            const indicator = document.createElement('span');
                            indicator.className = `day-indicator ${type}`;
                            indicatorsRow.appendChild(indicator);
                        });
                        dayDiv.appendChild(indicatorsRow);

                        // Add mouse event listeners for the popover
                        dayDiv.addEventListener('mouseenter', showPopover);
                        dayDiv.addEventListener('mouseleave', hidePopover);
                    }
                    
                    calendarGrid.appendChild(dayDiv);
                    dayCounter++;
                    dayCountInGrid++;
                }

                // 3. Add filler days from the next month (inactive)
                // Ensure the grid has a full 6 rows (42 cells total) for consistent layout
                const totalCells = 42;
                const currentCells = dayCountInGrid; // Count of cells currently added (headers + previous month + current month)
                const remainingCells = totalCells - currentCells;

                for (let i = 1; i <= remainingCells; i++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'calendar-day inactive';
                    dayDiv.innerHTML = `<span class="day-number">${i}</span>`;
                    calendarGrid.appendChild(dayDiv);
                }

                updateSemesterProgress(date);
            }

            // --- Popover Functions ---
            function showPopover(event) {
                const dayDiv = event.currentTarget;
                const dateKey = dayDiv.dataset.date;
                const deadlines = MOCK_DEADLINES[dateKey];
                
                if (!deadlines || deadlines.length === 0) return;

                popoverList.innerHTML = '';
                const dayDate = new Date(dateKey);

                popoverDateSpan.textContent = dayDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

                deadlines.forEach(item => {
                    const listItem = document.createElement('li');
                    listItem.className = `${item.type}-task`;
                    listItem.innerHTML = `<strong>${item.course}</strong>: ${item.name}`;
                    popoverList.appendChild(listItem);
                });

                // Position the popover dynamically
                const containerRect = calendarGrid.parentElement.getBoundingClientRect();
                
                // Use a slight delay to allow popover content to render for accurate height calculation
                setTimeout(() => {
                    const rect = dayDiv.getBoundingClientRect();
                    const popoverHeight = popover.offsetHeight; 
                    
                    // Horizontal position: 10px to the right of the calendar container
                    popover.style.left = `${containerRect.width + 10}px`; 

                    // Vertical position: Aligned with the top of the hovered day
                    let topPos = rect.top - containerRect.top;
                    
                    // Keep popover within the container boundaries (bottom check)
                    if (topPos + popoverHeight > containerRect.height) {
                        topPos = containerRect.height - popoverHeight;
                    }
                    // Keep popover within the container boundaries (top check)
                    if (topPos < 0) {
                        topPos = 0;
                    }
                    
                    popover.style.top = `${topPos}px`; 
                    popover.style.display = 'block';
                }, 0);
            }

            function hidePopover() {
                popover.style.display = 'none';
            }


            // --- Navigation and Progress Bar ---
            prevMonthBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar(currentDate);
            });

            nextMonthBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar(currentDate);
            });

            function updateSemesterProgress(date) {
                // Mock Semester: Aug 15 to Dec 15 (122 days)
                const semesterStart = new Date(date.getFullYear(), 7, 15); // Month 7 is August
                const semesterEnd = new Date(date.getFullYear(), 11, 15); // Month 11 is December
                const today = new Date();
                
                // Calculate days passed in milliseconds
                const totalDurationMs = semesterEnd - semesterStart;
                const passedDurationMs = today - semesterStart;

                let percentage = 0;
                if (passedDurationMs > 0 && totalDurationMs > 0) {
                    percentage = Math.min(100, Math.floor((passedDurationMs / totalDurationMs) * 100));
                } else if (passedDurationMs >= totalDurationMs) {
                    percentage = 100;
                }

                const progressBarFill = document.querySelector('.progress-bar-fill');
                const progressPercentageSpan = document.querySelector('.progress-percentage');

                progressBarFill.style.width = `${percentage}%`;
                progressPercentageSpan.textContent = `${percentage}%`;
            }

            // Initial render
            renderCalendar(currentDate);
        });
    </script>
</body>
</html>