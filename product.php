<?php
// Initialize the session
if(!isset($_SESSION)) 
{ 
    session_start(); 
}  

// Check if user is logged in.
if(isset($_SESSION['username'])){ 
    $loggedin = true;
}else{
    $loggedin = false;
}

// Get the amount of items in the shopping cart, this will be displayed in the header.
$num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// we get the ITEM ID from the pager.php code.
// Check to make sure the id parameter is specified in the URL
if (isset($_GET['id'])) {
    // Prepare statement and execute, prevents SQL injection
    $stmt = pdo_connect_mysql()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if (!$product) {
        // Simple error to display if the id for the product doesn't exists (array is empty)
        exit('Product does not exist!');
    }
} else {
    // Simple error to display if the id wasn't specified
    exit('Product does not exist!');
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

    <div class="product content-wrapper">
    <img src="imgs/<?=$product['img']?>" width="500" height="500" alt="<?=$product['name']?>">
    <div>
        <h1 class="name"><?=$product['name']?></h1>
        <span class="price">
            &dollar;<?=$product['price']?>
            <?php if ($product['rrp'] > 0): ?>
            <span class="rrp">&dollar;<?=$product['rrp']?></span>
            <?php endif; ?>
        </span>
        <form action="cart.php" method="post">
            <input type="number" name="quantity" value="1" min="1" max="<?=$product['quantity']?>" required>
            <input type="hidden" name="product_id" value="<?=$product['id']?>">
            <input type="submit" value="Add To Cart">
        </form>
        <div class="description">
            <?=$product['desc']?>
        </div>
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