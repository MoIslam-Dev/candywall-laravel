<?php

// Database credentials
$servername = "localhost";
$username = "morijlqx_mintly2";
$password = "morijlqx_mintly2";
$dbname = "morijlqx_mintly2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




function get_balance($userid) {
    global $conn;
    $sql = "SELECT balance FROM users WHERE userid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['balance'];
    } else {
        return false;
    }
}
//

function insert_history($userid, $network, $is_lead, $is_custom, $offerid, $ip, $points, $note) {
    global $conn;
    $sql = "INSERT INTO hist_activities (userid, network, is_lead, is_custom, offerid, ip, points, note, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
  
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiiisds", $userid, $network, $is_lead, $is_custom, $offerid, $ip, $points, $note);
    return $stmt->execute();
}

// Update the balance for a specific user ID
function update_balance($userid, $new_balance) {
    global $conn;
    $sql = "UPDATE users SET balance = ? WHERE userid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $new_balance, $userid);
    return $stmt->execute();
}





 $undecode = urldecode($_GET['subId']) ;
  $delimiter = "@@@-";
  $get_user = explode($delimiter, $undecode);
  $coor = $get_user[0];
 $getbalance = get_balance($get_user[0]);
 $new_b = $getbalance + $_GET['reward'];
 update_balance($get_user[0],$new_b);

 
 

if (insert_history($undecode, "wannads", 0, 0, $_GET['transId'], $_GET['userIp'], $_GET['reward'], "good")) {
     echo 'OK';
} else {
    echo "DUP";
}




// Close the connection
$conn->close();
?>


