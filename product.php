<?php
session_start();
include 'db_config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product['name']; ?> - Cupboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                <!-- Product Image -->
                <div>
                    <img src="<?php echo $product['image']; ?>" 
                         class="w-full h-96 object-cover rounded-lg">
                </div>
                
                <!-- Product Details -->
                <div>
                    <nav class="text-sm mb-4">
                        <a href="index.php" class="text-blue-500">Home</a>
                        <span class="mx-2">/</span>
                        <a href="category.php?type=<?php echo $product['type']; ?>" 
                           class="text-blue-500"><?php echo ucfirst($product['type']); ?></a>
                    </nav>

                    <h1 class="text-3xl font-bold mb-4"><?php echo $product['name']; ?></h1>
                    
                    <div class="text-2xl font-bold text-red-600 mb-4">
                        $<?php echo number_format($product['price'], 2); ?>
                    </div>

                    <?php if($product['stock'] > 0): ?>
                        <div class="text-green-600 mb-4">
                            In Stock (<?php echo $product['stock']; ?> available)
                        </div>
                    <?php else: ?>
                        <div class="text-red-600 mb-4">Out of Stock</div>
                    <?php endif; ?>

                    <div class="prose max-w-none mb-6">
                        <h3 class="text-lg font-semibold mb-2">Description:</h3>
                        <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>

                    <div class="space-y-4">
                        <div class="text-gray-700">
                            <strong>Category:</strong> <?php echo ucfirst($product['type']); ?>
                        </div>

                        <?php if(isset($_SESSION['user_id']) && $product['stock'] > 0): ?>
                            <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                    class="w-full bg-yellow-400 hover:bg-yellow-500 text-black py-3 rounded-lg font-semibold">
                                Add to Cart
                            </button>
                        <?php elseif(!isset($_SESSION['user_id'])): ?>
                            <a href="login.php" 
                               class="block text-center bg-gray-500 text-white py-3 rounded-lg">
                                Login to Purchase
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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
