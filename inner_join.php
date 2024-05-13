<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Define API endpoint
require_once('db_conn.php');
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Perform inner join query
    $sql = "SELECT 
    COALESCE(p.id, '') AS id, 
    COALESCE(p.name, '') AS name, 
    COALESCE(p.percentage, '') AS percentage, 
    COALESCE(p.age, '') AS age, 
    COALESCE(p.gender, '') AS gender, 
    COALESCE(p.city, '') AS city, 
    COALESCE(c.cid, '') AS cid,
    COALESCE(c.cityname, '') AS cityname
    FROM personal p
    INNER JOIN city c ON p.city = c.cid;
";

    $result = $conn -> query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo "0 results";
    }
}

?>