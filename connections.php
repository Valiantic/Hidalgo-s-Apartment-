

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
// $host = 'localhost';  // Database host 
// $dbname = 'db_apartment';  // Your database name
// $username = 'root';  // Your database username
// $password = '';  // Your database password 

// $conn = mysqli_connect($host, $username, $password, $dbname);

// if (!$conn) {
//     die("Database connection failed: " . mysqli_connect_error());
// }


// NEW VERSION 

$host = 'localhost';  // Database host 
$dbname = 'db_apartment';  // Your database name
$username = 'root';  // Your database username
$password = '';  // Your database password 

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// PDO CONNECTION
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}


?>



