<?php
include("php/database.php");

// Handle Time In, Time Out, Overtime Time In, and Overtime Time Out actions
if (
    isset($_POST['action']) &&
    isset($_POST['employeeName']) &&
    isset($_POST['time'])   
) {
    $action = $_POST['action'];
    $employeeName = $_POST['employeeName'];
    $time = $_POST['time']; 

    date_default_timezone_set('Asia/Manila');
    $currentDateTime = date('Y-m-d H:i:s');
    echo "Employee Name: " . $employeeName;
    echo "         
";
    echo "Current Time: " . $currentDateTime;
    echo "         
";

    $sqlEmployee = "SELECT id FROM employees WHERE name = '$employeeName'";
    $resultEmployee = mysqli_query($connection, $sqlEmployee);

    if (mysqli_num_rows($resultEmployee) > 0) {
        $rowEmployee = mysqli_fetch_assoc($resultEmployee);
        $employeeId = $rowEmployee['id'];

        if ($action === 'Time In') {
            $status = 'Time In';
            $sqlInsert = "INSERT INTO attendance (employee_id, name, time_in, status) VALUES ('$employeeId', '$employeeName', '$currentDateTime', '$status')";
            
            if (mysqli_query($connection, $sqlInsert)) {
                echo "   TIME - IN ✔️ ";
            } else {
                echo "Error: " . $sqlInsert . "<br>" . mysqli_error($connection);
            }
        } else if ($action === 'Time Out') {
            $status = 'Time Out';
            $sqlUpdate = "UPDATE attendance SET time_out = '$currentDateTime', total_hours = TIMEDIFF('$currentDateTime', time_in), status = '$status' WHERE employee_id = '$employeeId' AND time_out IS NULL";
            
            if (mysqli_query($connection, $sqlUpdate)) {
                echo "  TIME - OUT ✔️ ";
            } else {
                echo "Error updating attendance: " . mysqli_error($connection);
            }
        } else if ($action === 'Overtime Time In') {
            $status = 'Overtime Time In';
            $sqlUpdate = "UPDATE attendance SET overtime_time_in = '$currentDateTime', status = '$status' WHERE employee_id = '$employeeId' AND time_out IS NOT NULL AND overtime_time_in IS NULL";
            if (mysqli_query($connection, $sqlUpdate)) {
                echo "   OVERTIME TIME - IN ✔️ ";
            } else {
                echo "Error updating overtime time in: " . mysqli_error($connection);

            }
        } else if ($action === 'Overtime Time Out') {
            $status = 'Overtime Time Out';
            $sqlUpdate = "UPDATE attendance SET overtime_time_out = '$currentDateTime', overtime_total_hours = TIMEDIFF('$currentDateTime', overtime_time_in), status = '$status' WHERE employee_id = '$employeeId' AND overtime_time_out IS NULL";
            
            if (mysqli_query($connection, $sqlUpdate)) {
                echo "  OVERTIME TIME - OUT ✔️ ";
            } else {
                echo "Error updating overtime: " . mysqli_error($connection);
            }
        }
    } else {
        echo "Employee not found";
    }
} 
else if (isset($_POST['action']) && $_POST['action'] === 'getAttendance') {
    $sql = "SELECT *, 
            TIME_FORMAT(time_in, '%h:%i %p') AS formatted_time_in, 
            TIME_FORMAT(time_out, '%h:%i %p') AS formatted_time_out, 
            TIME_FORMAT(overtime_time_in, '%h:%i %p') AS formatted_overtime_time_in, 
            TIME_FORMAT(overtime_time_out, '%h:%i %p') AS formatted_overtime_time_out,
            TIME_TO_SEC(TIMEDIFF(time_out, time_in)) AS total_seconds,
            TIME_TO_SEC(TIMEDIFF(overtime_time_out, overtime_time_in)) AS overtime_total_seconds
            FROM attendance";
    $result = mysqli_query($connection, $sql);

    $attendanceData = array(); // Initialize an empty array

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['total_hours'] = formatTimeFromSeconds($row['total_seconds']); // Format total seconds to minutes and seconds
            $row['overtime_total_hours'] = formatTimeFromSeconds($row['overtime_total_seconds']); // Format overtime total seconds to minutes and seconds
            $row['time_in'] = $row['formatted_time_in']; // Format time in to 12-hour format
            $row['time_out'] = $row['formatted_time_out']; // Format time out to 12-hour format
            $row['overtime_time_in'] = $row['formatted_overtime_time_in']; // Format overtime time in to 12-hour format
            $row['overtime_time_out'] = $row['formatted_overtime_time_out']; // Format overtime time out to 12-hour format
            unset(
                $row['formatted_time_in'],
                $row['formatted_time_out'],
                $row['formatted_overtime_time_in'],
                $row['formatted_overtime_time_out'],
                $row['total_seconds'],
                $row['overtime_total_seconds']
            ); // Remove extra fields
            $attendanceData[] = $row;
        }
        // Send the data back as JSON
        echo json_encode($attendanceData);
    } else {
        echo "No attendance records found.";
    }
} else {
    echo "Invalid request";
}

// Function to format time from seconds to minutes and seconds
function formatTimeFromSeconds($seconds) {
    $minutes = floor($seconds / 60);
    $seconds %= 60;
    return sprintf('%02d:%02d', $minutes, $seconds);
}

mysqli_close($connection);
?>
