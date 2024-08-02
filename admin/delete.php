<?php

require '../database/config.php';

?>

<?php

if(isset($_GET['id'])){
    $id = $_GET['id'];
}

$query = "DELETE FROM tbluser WHERE id = '$id' ";
$result = mysqli_query($conn, $query);

if($result){
    header('location: users.php');
}

?>