<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Define API endpoint
require_once('db_conn.php');

// To check null values


if($_SERVER['REQUEST_METHOD'] === 'GET'){
    // Get the id from the URL
    $id = $_GET['id'];
    $null_columns = getNull($id);
    echo $null_columns;
}

function getNull($id){
    global $conn;
    $id = mysqli_real_escape_string($conn, $id);

    // Query to fetch null columns
    $query = "SELECT 
                IF(name IS NULL, 'name', '') AS name,
                IF(email IS NULL, 'email', '') AS email,
                IF(age IS NULL, 'age', '') AS age,
                IF(branch IS NULL, 'branch', '') AS branch,
                IF(year IS NULL, 'year', '') AS year,
                IF(phone IS NULL, 'phone', '') AS phone
            FROM student
            WHERE id = ?";

    // Prepare the statement
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $query)) {
        return "SQL Error";
    } else {
        // Bind parameters to the statement
        mysqli_stmt_bind_param($stmt, "s", $id);
        // Execute the statement
        mysqli_stmt_execute($stmt);
        // Get the result
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $missing_fields = [];
            foreach ($row as $key => $value) {
                if (!empty($value)) {
                    $missing_fields[] = $key;
                }
            }
            if (!empty($missing_fields)) {
                $data = [
                    'message' => 'Missing Fields',
                    'data' => $missing_fields
                ];
                return json_encode($data);
            } else {
                return 'No missing fields';
            }
        } else {
            return "Internal Server Error";
        }
    }
}

function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

//To update null values
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $id = $_GET['id'];
    $input = json_decode(file_get_contents("php://input"), true);
    $result = updateRecord($conn, $id, $input);
    echo json_encode($result);
}

function updateRecord($conn, $id, $input) {
    $name = isset($input['name']) ? mysqli_real_escape_string($conn, $input['name']) : null;
    $email = isset($input['email']) ? mysqli_real_escape_string($conn, $input['email']) : null;
    $age = isset($input['age']) ? mysqli_real_escape_string($conn, $input['age']) : null;
    $branch = isset($input['branch']) ? mysqli_real_escape_string($conn, $input['branch']) : null;
    $year = isset($input['year']) ? mysqli_real_escape_string($conn, $input['year']) : null;
    $phone = isset($input['phone']) ? mysqli_real_escape_string($conn, $input['phone']) : null;

    // Update the database table
    $query = "UPDATE student SET 
            name = COALESCE(?, name),
            email = COALESCE(?, email),
            age = COALESCE(?, age),
            branch = COALESCE(?, branch),
            year = COALESCE(?, year),
            phone = COALESCE(?, phone)
            WHERE id = ?";

    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "ssssssi", $name, $email, $age, $branch, $year, $phone, $id);
        if (mysqli_stmt_execute($stmt)) {
            return [
                'status' => 'success',
                'message' => 'Record updated successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Error updating record: ' . mysqli_error($conn)
            ];
        }
    } else {
        return [
            'status' => 'error',
            'message' => 'Error preparing statement: ' . mysqli_stmt_error($stmt)
        ];
    }
}


?>