<?php
// function to connect to database using PDO Mysql
function pdo_connect_mysql() {
    // Our Database login Data
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
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

// If the user clicked the add to cart button on the product page we can check for the form data
if (isset($_POST['product_id'], $_POST['quantity']) && is_numeric($_POST['product_id']) && is_numeric($_POST['quantity'])) {
    // Set the post variables so we easily identify them, also make sure they are integer
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    // Prepare the SQL statement, we basically are checking if the product exists in our database
    $stmt = pdo_connect_mysql()->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_POST['product_id']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if ($product && $quantity > 0) {
        // Product exists in database, now we can create/update the session variable for the cart
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            if (array_key_exists($product_id, $_SESSION['cart'])) {
                // Product exists in cart so just update the quanity
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                // Product is not in cart so add it
                $_SESSION['cart'][$product_id] = $quantity;
            }
        } else {
            // There are no products in cart, this will add the first product to cart
            $_SESSION['cart'] = array($product_id => $quantity);
        }
    }
    // Prevent form resubmission...
    header('location: cart.php');
    exit;
}

// Remove product from cart, check for the URL param "remove", this is the product id, make sure it's a number and check if it's in the cart
if (isset($_POST['remove']) && isset($_SESSION['cart'])) {
    // Remove the product from the shopping cart
    unset($_SESSION['cart'][$_POST['product_id']]);
    // Prevent form resubmission...
    header('location: cart.php');
    exit;
}


$shipping = 0;
//shipping cost calculations. Standard is free shipping with no cost.
if (isset($_POST['standard']) && isset($_SESSION['cart'])) {
    $shipping = 0;
}
if (isset($_POST['premium']) && isset($_SESSION['cart'])) {
    $shipping = 10;
}

// Update product quantities in cart if the user clicks the "Update" button on the shopping cart page
if (isset($_POST['update']) && isset($_SESSION['cart'])) {
    // Loop through the post data so we can update the quantities for every product in cart
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'quantity') !== false && is_numeric($v)) {
            $id = str_replace('quantity-', '', $k);
            $quantity = (int)$v;
            // Always do checks and validation
            if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                // Update new quantity
                $_SESSION['cart'][$id] = $quantity;
            }
        }
    }
    // Prevent form resubmission...
    header('location: cart.php');
    exit;
}

// Send the user to the place order page if they click the Place Order button, also the cart should not be empty
if (isset($_POST['placeorder']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: login.php");
        $_SESSION['notLogged'] = "Log in to place your order.";
        exit;
    }
    header('Location: placeorder.php');
    exit;
}

// if cart is empty, you cant place an order.
if (empty($_SESSION['cart']) && isset($_POST['placeorder']) ){
    $cartisempty = "Your cart is empty. Please fill your cart to place an order.";
}else{
    $cartisempty ="";
}

echo $cartisempty;

// Check the session variable for products in cart
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$products = array();
$subtotal = 0.00;
// If there are products in cart
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = pdo_connect_mysql()->prepare('SELECT * FROM products WHERE id IN (' . $array_to_question_marks . ')');
    // We only need the array keys, not the values, the keys are the id's of the products
    $stmt->execute(array_keys($products_in_cart));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Calculate the subtotal
    foreach ($products as $product) {
        $subtotal += (float)$product['price'] * (int)$products_in_cart[$product['id']];
    }
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

<div class="cart content-wrapper">
    <h1>Shopping Cart</h1>
    <form action="cart.php" method="post">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">You have no products added in your Shopping Cart</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="img">
                        <a href="pager.php?page=product&id=<?=$product['id']?>">
                            <img src="imgs/<?=$product['img']?>" width="50" height="50" alt="<?=$product['name']?>">
                        </a>
                    </td>
                    <td>
                        <a href="pager.php?page=product&id=<?=$product['id']?>"><?=$product['name']?></a>
                        <br>
                        <div class="buttons">
                            <input type="submit" value="remove" name="remove">
                            <input type="hidden" name="product_id" value="<?=$product['id']?>">
                        </div>
                    </td>
                    <td class="price">&dollar;<?=$product['price']?></td>
                    <td class="quantity">
                        <input type="number" name="quantity-<?=$product['id']?>" value="<?=$products_in_cart[$product['id']]?>" min="1" max="<?=$product['quantity']?>" placeholder="Quantity" required>
                    </td>
                    <td class="price">&dollar;<?=$product['price'] * $products_in_cart[$product['id']]?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <table>
            <tbody>
                <tr>
                    <td class="buttons" colspan="2">shipping options</td>
                    <td>
                        <div class="buttons">
                            <input type="submit" value="standard" name="standard">
                        </div>
                    </td>
                    <td>
                        <div style="text-align:center;" class="buttons">
                            delivery: 7-10 days.<br>
                            cost: free.
                        </div>
                    </td>
                    <td>
                        <div class="buttons">
                            <input type="submit" value="premium" name="premium">
                        </div>
                    </td>  
                    <td>
                        <div style="text-align:center;" class="buttons">
                            delivery: 2-3 days. <br>
                            cost: 10$
                        </div>
                    </td>                      
                </tr>
            </tbody>
        </table>
        <div class="subtotal">
            <span class="text">Subtotal</span>
            <span class="price">&dollar;<?=$subtotal + $shipping ?></span>
        </div>
        <div class="buttons">
            <input type="submit" value="Update" name="update">
            <input type="submit" value="Place Order" name="placeorder">
        </div>

    </form>
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