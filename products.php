<?php
// function to connect to database using PDO Mysql
function pdo_connect_mysql() {
    // Our Database login Data
    $DATABASE_HOST = 'db';
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

// The amounts of products to show on each page
$num_products_on_each_page = 8;
// The current page, in the URL this will appear as index.php?page=products&p=1, index.php?page=products&p=2, etc...
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
// Select products ordered by the date added
$stmt = pdo_connect_mysql()->prepare('SELECT * FROM products ORDER BY date_added DESC LIMIT ?,?');
// bindValue will allow us to use integer in the SQL statement, we need to use for LIMIT
$stmt->bindValue(1, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_products_on_each_page, PDO::PARAM_INT);
$stmt->execute();
// Fetch the products from the database and return the result as an Array
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the total number of products
$total_products = pdo_connect_mysql()->query('SELECT * FROM products')->rowCount();

if(isset($_SESSION['username'])){ 
    $loggedin = true;
}else{
    $loggedin = false;
}
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

        <div class="products content-wrapper">
    <h1>Products</h1>
    <p><?=$total_products?> Products</p>
    <div class="products-wrapper">
        <?php foreach ($products as $product): 
            $phpVariable = $product['id'];
            ?>
            <!-- Send product ID to Pager which sends it to product.php to know which Item was clicked. -->
            <a href="pager.php?page=product&id=<?=$product['id']?>" class="product">
            <img src="imgs/<?=$product['img']?>" width="200" height="200" alt="<?=$product['name']?>">
            <span class="name"><?=$product['name']?></span>
            <span class="price">
                &dollar;<?=$product['price']?>
                <?php if ($product['rrp'] > 0): ?>
                <span class="rrp">&dollar;<?=$product['rrp']?></span>
                <?php endif; ?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="buttons">
        <?php if ($current_page > 1): ?>
        <a href="index.php?page=products&p=<?=$current_page-1?>">Prev</a>
        <?php endif; ?>
        <?php if ($total_products > ($current_page * $num_products_on_each_page) - $num_products_on_each_page + count($products)): ?>
        <a href="index.php?page=products&p=<?=$current_page+1?>">Next</a>
        <?php endif; ?>
    </div>
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