<?php
error_reporting(0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Define API endpoint
require_once('../includes/db_conn.php');

/* To check null values


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
            WHERE id = '$id';";

    $result = mysqli_query($conn, $query);

    if($result){
        $row = mysqli_fetch_assoc($result);
        $missing_fields = [];
        foreach($row as $key => $value) {
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
*/


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $customer = getCustomer($_GET);
        echo $customer;
    } else {
        $student_list = getStudentList();
        echo $student_list;
    }
}

// To fetch all entities
function getStudentList(){
    global $conn;
    $query = "SELECT * FROM student";
    $query_run = mysqli_query($conn,$query);

    if($query_run){
        if(mysqli_num_rows($query_run)>0){
            $response = mysqli_fetch_all($query_run,MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Student List Fetched Succesfully',
                'data' => $response
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        }
        else{
            $data = [
                'status' => 404,
                'message' => 'No Customer Found',
            ];
            header("HTTP/1.0 404 No Customer Found");
            return json_encode($data);
        }
    }
    else{
        $data = [
            'status' => 500,
            'message' => 'Internal Server Error',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
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
    $name = mysqli_real_escape_string($conn, $input['name']);
    $email = mysqli_real_escape_string($conn, $input['email']);
    $age = mysqli_real_escape_string($conn, $input['age']);
    $branch = mysqli_real_escape_string($conn, $input['branch']);
    $year = mysqli_real_escape_string($conn, $input['year']);
    $phone = mysqli_real_escape_string($conn, $input['phone']);

    if(empty(trim($name))){
        return error422('Enter your name');
    }elseif(empty(trim($email))){
        return error422('Enter your email');
    }elseif(empty(trim($age))){
        return error422('Enter your age');
    }elseif(empty(trim($branch))){
        return error422('Enter your branch');
    }elseif(empty(trim($year))){
        return error422('Enter your year');
    }elseif(empty(trim($phone))){
        return error422('Enter your phone');
    }else{
            // Update the database table
            $query = "UPDATE student SET 
                    name = IF(name IS NULL, '$name', name),
                    email = IF(email IS NULL, '$email', email),
                    age = IF(age IS NULL, '$age', age),
                    branch = IF(branch IS NULL, '$branch', branch),
                    year = IF(year IS NULL, '$year', year),
                    phone = IF(phone IS NULL, '$phone', phone)
                    WHERE id = '$id'";
            $result = mysqli_query($conn, $query);

            if ($result) {
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
    }
}

?>