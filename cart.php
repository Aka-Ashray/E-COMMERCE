<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_config.php';

// Get cart items
$stmt = $mysqli->prepare("
    SELECT c.*, p.name, p.price, p.image 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart - Cupboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Shopping Cart</h1>
        
        <?php if (empty($cart_items)): ?>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <p class="text-gray-600">Your cart is empty</p>
                <a href="index.php" class="text-blue-500 hover:text-blue-600 mt-4 inline-block">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <?php foreach ($cart_items as $item): ?>
                    <div class="flex items-center border-b py-4">
                        <img src="<?php echo $item['image']; ?>" class="w-24 h-24 object-cover rounded">
                        <div class="flex-1 ml-4">
                            <h3 class="font-semibold"><?php echo $item['name']; ?></h3>
                            <p class="text-gray-600">$<?php echo number_format($item['price'], 2); ?></p>
                            <div class="flex items-center mt-2">
                                <label class="mr-2">Quantity:</label>
                                <input type="number" value="<?php echo $item['quantity']; ?>" 
                                       min="1" 
                                       onchange="updateQuantity(<?php echo $item['id']; ?>, this.value)"
                                       class="w-16 p-1 border rounded">
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <p class="font-bold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            <button onclick="removeFromCart(<?php echo $item['id']; ?>)"
                                    class="text-red-500 hover:text-red-600 mt-2">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mt-6 text-right">
                    <p class="text-lg font-bold">Total: $<?php echo number_format($total, 2); ?></p>
                    <button class="bg-yellow-400 hover:bg-yellow-500 text-black px-6 py-2 rounded mt-4">
                        Proceed to Checkout
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function updateQuantity(cartId, quantity) {
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cart_id=${cartId}&quantity=${quantity}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        });
    }

    function removeFromCart(cartId) {
        if (confirm('Remove this item from cart?')) {
            fetch('remove_from_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `cart_id=${cartId}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    }
    </script>
</body>
</html>
