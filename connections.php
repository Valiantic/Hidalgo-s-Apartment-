

<?php  

// STEVEN CONNECTION
// $sName = "localhost";
// $uName = "root";
// $pass  = "";
// $db_name = "db_apartment";

// try {
// 	$conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
// 	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// }catch(PDOExeption $e){
// 	echo "Connection failed: ". $e->getMessage();
// 	exit;
// }

// SAM CONNECTION
$host = 'localhost';  // Database host 
$dbname = 'db_apartment';  // Your database name
$username = 'root';  // Your database username
$password = '';  // Your database password 

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


?>