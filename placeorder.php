<?php
// function to connect to database using PDO Mysql
function pdo_connect_mysql() {
    // Our Database login Data
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'newuser';
    $DATABASE_PASS = 'password';
    $DATABASE_NAME = 'users';
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	// If there is an error with the connection, stop the script and display the error.
    	exit('Failed to connect to database!');
    }
}

// Initialize the session
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
// Get the amount of items in the shopping cart, this will be displayed in the header.
$num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Check if user is logged in.
if(isset($_SESSION['username'])){ 
    $loggedin = true;
}else{
    $loggedin = false;
}
 
// Check if the user is already logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] != true ) {
    header("location: Login.php");
    exit;
}

// Unset all of the session variables
$_SESSION = array();
 
// Destroy the session.
session_destroy();


// Get the amount of items in the shopping cart, this will be displayed in the header.
$num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

?>
<!DOCTYPE html>
<!-- "Part of the css is from an external source. We also created an own stylesheet
      for to prove our knowledge on this topic. We combined it with the external code
      to get the best look possible." Jasper&Bashkim-->
<html>
	<head>
		<meta charset="utf-8">
		<title>$title</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
        <header>
            <div class="content-wrapper">
                <h1>Webshob System</h1>
                <nav>
                    <a href="index.php">Home</a>
                    <a href="products.php">Products</a>
                    <?php if ($loggedin == false): ?>
                        <a href="Login.php">Login</a>
                    <?php endif ?>
                    <?php if ($loggedin): ?>
                        <a href="User.php">User</a>
                    <?php endif ?>
                    <?php if ($loggedin): ?>
                        <a href="logout.php">Logout</a>
                    <?php endif ?>
                </nav>
                <div class="link-icons">
                </div>
                <div class="link-icons">
                    <a href="cart.php">
						<i class="fas fa-shopping-cart"></i>
                        <span><?php echo $num_items_in_cart; ?></span>
					</a>
                </div>
            </div>
        </header>
        <main>

<div class="placeorder content-wrapper">
    <h1>Your Order Has Been Placed</h1>
    <p>Thank you for ordering with us, we'll contact you by email with your order details.</p>
</div>

</main>
        <footer>
            <div class="content-wrapper">
                <p>Shopping Cart System</p>
            </div>
            <div class="content-wrapper">
                <p>EHRE EHRE EHRE EHRE EHRE by Jasper&Bashkim <3 </p>
            </div>
        </footer>
        <script src="script.js"></script>
    </body>
</html>