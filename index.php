<?php
session_start();
include 'db_config.php';
// Add error message display
if (isset($_SESSION['error'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">';
    echo '<span class="block sm:inline">' . htmlspecialchars($_SESSION['error']) . '</span>';
    echo '</div>';
    unset($_SESSION['error']);
}
$stmt = $mysqli->query("SELECT * FROM products WHERE stock > 0");
$products = $stmt->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cupboard - Computer Parts Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    
    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-blue-800 to-blue-600 text-white py-12">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl font-bold mb-4">Welcome to Cupboard</h1>
            <p class="text-xl">Your One-Stop Shop for Premium Computer Parts</p>
        </div>
    </div>

    <div class="container mx-auto p-6">
        <!-- Products Grid -->
        <h2 class="text-2xl font-bold mb-6">Featured Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <?php foreach($products as $product): ?>
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow">
                <a href="product.php?id=<?php echo $product['id']; ?>">
                    <img src="<?php echo $product['image']; ?>" class="w-full h-48 object-cover rounded-t-lg">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2"><?php echo $product['name']; ?></h3>
                        <div class="text-xl font-bold text-red-600 mb-2">
                            $<?php echo number_format($product['price'], 2); ?>
                        </div>
                        <?php if($product['stock'] > 0): ?>
                            <div class="text-sm text-green-600 mb-4">In Stock</div>
                        <?php else: ?>
                            <div class="text-sm text-red-600 mb-4">Out of Stock</div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                    class="w-full bg-yellow-400 hover:bg-yellow-500 text-black py-2 rounded">
                                Add to Cart
                            </button>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    function addToCart(productId) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Just update the cart count in the header
                updateCartCount(data.cart_count);
            }
        });
    }

    function updateCartCount(count) {
        const cartCountSpan = document.querySelector('.cart-count');
        if (cartCountSpan) {
            cartCountSpan.textContent = count;
        }
    }
    </script>
</body>
</html>
