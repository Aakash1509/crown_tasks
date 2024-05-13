<?php
// error_reporting(0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once('function.php');

$request_method = $_SERVER["REQUEST_METHOD"];

//If request method is GET

if ($request_method === 'GET') {
    if (isset($_GET['id'])) {
        $customer = getCustomer($_GET);
        echo $customer;
    } else {
        $customer_list = getCustomerList();
        echo $customer_list;
    }

//If request method is POST

} elseif ($request_method === 'POST') {
    $input_data = json_decode(file_get_contents("php://input"), true);
    if (empty($input_data)) {
        $store_customer = storeCustomer($_POST);
    } else {
        $store_customer = storeCustomer($input_data);
    }
    echo $store_customer;

//If request method is PUT

} elseif ($request_method === 'PUT') {
    $input_data = json_decode(file_get_contents("php://input"), true);
    $update_customer = updateCustomer($input_data);
    echo $update_customer;

//If request method is DELETE

} elseif ($request_method === 'DELETE') {
    $input_data = json_decode(file_get_contents("php://input"), true);
    $delete_customer = deleteCustomer($input_data);
    echo $delete_customer;
} else {
    $data = [
        'status' => 405,
        'message' => $request_method . ' Method Not Allowed',
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}

?>
