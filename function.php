<?php

require_once('db_conn.php');

function error422($message){
    $data = [
        'status' => 422,
        'message' => $message,
    ];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

// To store data
function storeCustomer($customer_input){

    /*
    global $conn;

    $name = mysqli_real_escape_string($conn,$customer_input['name']);
    $email = mysqli_real_escape_string($conn,$customer_input['email']);
    $phone = mysqli_real_escape_string($conn,$customer_input['phone']);

    if(empty(trim($name))){
        return error422('Enter your name');

    }elseif(empty(trim($email))){
        return error422('Enter your email');

    }elseif(empty(trim($phone))){
        return error422('Enter your phone');
    }
    else{
        // $query = "INSERT INTO customers (name,email,phone) VALUES ('$name','$email','$phone')";
        // $result = mysqli_query($conn,$query);
        // if($result){
        //     $data = [
        //         'status' => 201, //Something is created
        //         'message' => 'Customer Created Succesfully',
        //     ];
        //     header("HTTP/1.0 201 Created");
        //     return json_encode($data);
        // }
        // else{
        //     $data = [
        //         'status' => 500,
        //         'message' => 'Internal Server Error',
        //     ];
        //     header("HTTP/1.0 500 Internal Server Error");
        //     return json_encode($data);
        // }

        //Using prepared statements
        $query = "INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $phone);
        
            if (mysqli_stmt_execute($stmt)) {
                $data = [
                    'status' => 201, // Something is created
                    'message' => 'Customer Created Successfully',
                ];
                header("HTTP/1.0 201 Created");
                echo json_encode($data);
            } else {
                $data = [
                    'status' => 500, // Internal Server Error
                    'message' => 'Failed to create customer',
                ];
                header("HTTP/1.0 500 Internal Server Error");
                echo json_encode($data);
            }
        } else {
            $data = [
                'status' => 500, // Internal Server Error
                'message' => 'Failed to prepare statement',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    }*/

    global $conn;

    $name = mysqli_real_escape_string($conn, $customer_input['name']);
    $email = mysqli_real_escape_string($conn, $customer_input['email']);
    $phone = mysqli_real_escape_string($conn, $customer_input['phone']);

    if (empty(trim($name))) {
        return error422('Enter your name');
    } elseif (empty(trim($email))) {
        return error422('Enter your email');
    } elseif (empty(trim($phone))) {
        return error422('Enter your phone');
    } elseif (!isset($_FILES['image'])) {
        return error422('Please upload an image');
    } else {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_error = $_FILES['image']['error'];

        // Check if file is uploaded successfully
        if ($image_error !== UPLOAD_ERR_OK) {
            return error422('Failed to upload image');
        }

        // Validate file size
        if ($image_size > 5000000) {
            return error422('File size too large. Max size is 5MB');
        }

        // Move the uploaded file to a folder on your local device
        $upload_dir = 'images/';
        $image_path = $upload_dir . $image_name;
        if (!move_uploaded_file($image_tmp_name, $image_path)) {
            return error500('Failed to move uploaded file');
        }

        // Using prepared statements
        $query = "INSERT INTO customers (name, email, phone, image) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $phone, $image_name);

            if (mysqli_stmt_execute($stmt)) {
                $data = [
                    'status' => 201, // Something is created
                    'message' => 'Customer Created Successfully',
                ];
                header("HTTP/1.0 201 Created");
                echo json_encode($data);
            } else {
                $data = [
                    'status' => 500, // Internal Server Error
                    'message' => 'Failed to create customer',
                ];
                header("HTTP/1.0 500 Internal Server Error");
                echo json_encode($data);
            }
        } else {
            $data = [
                'status' => 500, // Internal Server Error
                'message' => 'Failed to prepare statement',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
        }
    }
}

// To fetch all entities
// function getCustomerList(){
//     global $conn;
//     $query = "SELECT * FROM customers";
//     $query_run = mysqli_query($conn,$query);

//     if($query_run){
//         if(mysqli_num_rows($query_run)>0){
//             $response = mysqli_fetch_all($query_run,MYSQLI_ASSOC);

//             $data = [
//                 'status' => 200,
//                 'message' => 'Customer List Fetched Succesfully',
//                 'data' => $response
//             ];
//             header("HTTP/1.0 200 OK");
//             return json_encode($data);
//         }
//         else{
//             $data = [
//                 'status' => 404,
//                 'message' => 'No Customer Found',
//             ];
//             header("HTTP/1.0 404 No Customer Found");
//             return json_encode($data);
//         }
//     }
//     else{
//         $data = [
//             'status' => 500,
//             'message' => 'Internal Server Error',
//         ];
//         header("HTTP/1.0 500 Internal Server Error");
//         return json_encode($data);
//     }
// }

// To fetch all entities
function getCustomerList(){
    global $conn;

    $query = "SELECT id, name, email, phone, image FROM customers";
    $stmt = mysqli_stmt_init($conn);
    
    if(mysqli_stmt_prepare($stmt, $query)){
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0){
            $response = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $data = [
                'status' => 200,
                'message' => 'Customer List Fetched Succesfully',
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
            header("HTTP/1.0 404 Not Found");
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



// To fetch only one entity by id
function getCustomer($customer_params){
    global $conn;
    if($customer_params['id']==null){
        return error422('Enter your customer id');
    }

    $customer_id = mysqli_real_escape_string($conn, $customer_params['id']);
    $query = "SELECT * FROM customers WHERE id=?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "s", $customer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) == 1) {
                $res = mysqli_fetch_assoc($result);
                $data = [
                    'status' => 200,
                    'message' => 'Customer Fetched Succesfully',
                    'data' => $res
                ];
                header("HTTP/1.0 200 OK");
                return json_encode($data);
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'No Customer Found',
                ];
                header("HTTP/1.0 404 Not Found");
                return json_encode($data);
            }
        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    } else {
        $data = [
            'status' => 500,
            'message' => 'Failed to prepare statement',
        ];
        header("HTTP/1.0 500 Internal Server Error");
        return json_encode($data);
    }
}


// To update customer
// function updateCustomer($customer_input){
//     global $conn;

//     // if(!isset($customer_params['id'])){
//     //     return error422('customer id is not found in URL');

//     // }elseif($customer_params['id'] == null){
//     //     return error422('Enter the customer id');
//     // }
//     $customer_id = $customer_input['id'];

//     $name = $customer_input['name'];
//     $email =$customer_input['email'];
//     $phone =$customer_input['phone'];

//     if(empty(trim($name))){
//         return error422('Enter your name');

//     }elseif(empty(trim($email))){
//         return error422('Enter your email');

//     }elseif(empty(trim($phone))){
//         return error422('Enter your phone');
//     }
//     else{
//         $query = "UPDATE customers SET name='$name',email='$email',phone='$phone' WHERE id='$customer_id'";
//         $result = mysqli_query($conn,$query);
//         if($result){
//             $data = [
//                 'status' => 200, //Update
//                 'message' => 'Customer Updated Succesfully',
//             ];
//             header("HTTP/1.0 200 Success");
//             return json_encode($data);
//         }
//         else{
//             $data = [
//                 'status' => 500,
//                 'message' => 'Internal Server Error',
//             ];
//             header("HTTP/1.0 500 Internal Server Error");
//             return json_encode($data);
//         }
//     }
// }

//To update using prepared statements

function updateCustomer($customer_input){
    global $conn;
    $customer_id = $customer_input['id'];

    $name = $customer_input['name'];
    $email = $customer_input['email'];
    $phone = $customer_input['phone'];

    if(empty(trim($name))){
        return error422('Enter your name');
    } elseif(empty(trim($email))){
        return error422('Enter your email');
    } elseif(empty(trim($phone))){
        return error422('Enter your phone');
    } else {
        $query = "UPDATE customers SET name=?, email=?, phone=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sssi', $name, $email, $phone, $customer_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if($result){
            $data = [
                'status' => 200, //Update
                'message' => 'Customer Updated Successfully',
            ];
            header("HTTP/1.0 200 Success");
            return json_encode($data);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            header("HTTP/1.0 500 Internal Server Error");
            return json_encode($data);
        }
    }
}


//Delete Customer

// function deleteCustomer($input_data){
//     global $conn;

//     if(!isset($input_data['id'])){
//         return error422('Customer ID is not found in request data');
//     } elseif(empty($input_data['id'])){
//         return error422('Enter the customer ID');
//     }

//     $customer_id = mysqli_real_escape_string($conn, $input_data['id']);
//     $query = "DELETE FROM customers WHERE id='$customer_id'";
//     $result = mysqli_query($conn, $query);

//     if($result){
//         $data = [
//             'status' => 200,
//             'message' => 'Customer Deleted Successfully',
//         ];
//         header("HTTP/1.0 200 OK");
//         return json_encode($data);

//     } else {
//         $data = [
//             'status' => 404,
//             'message' => 'Customer Not Found',
//         ];
//         header("HTTP/1.0 404 Not Found");
//         return json_encode($data);
//     }
// }  
function deleteCustomer($input_data){
    global $conn;

    if(!isset($input_data['id'])){
        return error422('Customer ID is not found in request data');
    } elseif(empty($input_data['id'])){
        return error422('Enter the customer ID');
    }

    $customer_id = mysqli_real_escape_string($conn, $input_data['id']);

    // Prepare the DELETE statement
    $query = "DELETE FROM customers WHERE id=?";

    // Bind the customer_id parameter
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $customer_id);

    // Execute the statement
    $result = mysqli_stmt_execute($stmt);

    if($result){
        $data = [
            'status' => 200,
            'message' => 'Customer Deleted Successfully',
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);

    } else {
        $data = [
            'status' => 404,
            'message' => 'Customer Not Found',
        ];
        header("HTTP/1.0 404 Not Found");
        return json_encode($data);
    }
}

?>