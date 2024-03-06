<?
include("php/database.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <link rel="stylesheet" href="css/attendance.css">
    <link rel="stylesheet" href="css/topsidenavbars.css">   
</head>
<body>
    <header class="header">
        <nav class="topnav">
            <a class="active" href="index.php">Logout</a>
            <a href="#about">About</a>
            <a href="">Contact</a>
            <a class="logout-btn" href="index.php">Home</a>
        </nav>  
    </header>
    <section class="sidebar">
        <div class="logo-sidebar">ADMIN</div>
        <ul>
            <li><a href="dashboard.html"><i class="fas fa-box"></i>Dashboard</a></li>
            <li><a href="employeeform.html"><i class="fas fa-paperclip"></i>Employee Registration</a></li>
            <li><a href="attendance.php" class="btn-active"><i class="fas fa-check"></i>Attendance</a></li>
            <!--  <li><a href="employeelist.html"><i class="fas fa-users"></i>Employee List</a></li> -->
            <li><a href="positionlist.php"><i class="fas fa-user-tie"></i>Position List</a></li>
            <li><a href="schedule.html"><i class="fas fa-credit-card"></i>Schedule</a></li>
            <li><a href="DailyTimeRecord.html"><i class="fas fa-equals"></i>DTR</a></li>
            <li><a href="admin_user.php"><i class="fas fa-user"></i>Admin Users</a></li> 
        </ul>
    </div>
    </section> 
    <main class="main">  
        <div class="card-body">
            <div class="logo-main">Attendance List</div>
            <div class="attendance">
                <div class="attendance-list">  
                    <table id="attendanceTable" class="table">
                            <div class="reset-button-container">
                    <button id="resetAttendance" class="btn-reset">Reset Table</button>
                </div>
                        <thead>
                            <tr>
                                
                                <th>Name</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Total Hours</th>
                                <th>Status</th>
                                <th>OverTime In</th>
                                <th>OverTime Out</th>
                                <th>Total Hours</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dito ilalagay ng script ang attendance data -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </main>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/camera.js"></script> <!-- Include camera.js -->
    <script>
       $(document).ready(function() {
    console.log("Document is ready.");

    function fetchAttendanceData() {
        console.log("Fetching Attendance data...")
        $.ajax({
            url: 'attendance_employee.php',
            type: 'POST', // Change to POST method
            dataType: 'json',
            data: {
                action: 'getAttendance' // Send an action to indicate what data you want
            },
            success: function(data) {
                console.log("Received data:", data);
                populateAttendanceTable(data);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Function to populate the table with fetched data 
    function populateAttendanceTable(data) {
        console.log("Populating table with data:", data);
        var table = $('#attendanceTable tbody');
        table.empty();

        $.each(data, function(index, record) {
            var row = '<tr>' +
                
                '<td>' + record.name + '</td>' +
                '<td>' + record.time_in + '</td>' +
                '<td>' + record.time_out + '</td>' +
                '<td>' + record.total_hours + '</td>' +
                '<td>' + record.status + '</td>' +
                '<td>' + record.overtime_time_in + '</td>' +
                '<td>' + record.overtime_time_out + '</td>' +
                '<td>' + record.overtime_total_hours + '</td>' +
                //'<td><a href="#" class="download-link" data-image="' + record.image + '">Download Image</a></td>'+
                '</tr>';

            table.append(row);
        });
            
        // Attach click event to delete buttons
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            var recordId = $(this).data('id');
            deleteAttendanceRecord(recordId);
        });

        // Attach click event to reset button
        $('#resetAttendance').on('click', function() {
            console.log("Resetting Attendance Table...");
            resetAttendance();
        });

        // Function to reset the attendance table
        function resetAttendance() {
            $.ajax({
                url: 'delete_attendance.php',
                type: 'POST',
                data: {
                    action: 'resetAttendance'
                },
                success: function(response) {
                    console.log(response);
                    // Clear the table after successful reset
                    $('#attendanceTable tbody').empty();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        // Attach click event to download links
        $('.download-link').on('click', function(e) {
            e.preventDefault();
            var base64Image = $(this).data('image');
            downloadImage(base64Image);
        });
    }

    // Function to convert base64 string to a Blob object and download it
    function downloadImage(base64Image) {
        // Your download image function here
    }

    fetchAttendanceData();
});

    </script>
</body>
</html>