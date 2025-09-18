<?php
session_start();
include 'db_config.php';

$type = isset($_GET['type']) ? strtolower($_GET['type']) : 'all';
$sql = "SELECT * FROM products WHERE 1=1";
if ($type != 'all') {
    $sql .= " AND LOWER(type) = LOWER(?)";
}

$stmt = $mysqli->prepare($sql);
if ($type != 'all') {
    $stmt->bind_param("s", $type);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Category: <?php echo ucfirst($type); ?> - Cupboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6"><?php echo ucfirst($type); ?> Products</h1>
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
</body>
</html>
