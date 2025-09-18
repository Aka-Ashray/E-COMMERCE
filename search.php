<?php
session_start();
include 'db_config.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Base query
$sql = "SELECT * FROM products WHERE 1=1";

// Add search condition if search term exists
if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
}

// Add category condition if specific category selected
if ($category != 'all') {
    $sql .= " AND LOWER(type) = LOWER(?)";
}

$stmt = $mysqli->prepare($sql);

// Bind parameters based on conditions
if (!empty($search) && $category != 'all') {
    $search_term = "%$search%";
    $stmt->bind_param("sss", $search_term, $search_term, $category);
} elseif (!empty($search)) {
    $search_term = "%$search%";
    $stmt->bind_param("ss", $search_term, $search_term);
} elseif ($category != 'all') {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results - Cupboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">
            Search Results <?php echo !empty($search) ? "for \"" . htmlspecialchars($search) . "\"" : ""; ?>
            <?php if($category != 'all') echo " in " . ucfirst($category); ?>
        </h1>

        <?php if (empty($products)): ?>
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <p class="text-gray-600">No products found matching your search criteria.</p>
                <a href="index.php" class="text-blue-500 hover:text-blue-600 mt-4 inline-block">Return to Home</a>
            </div>
        <?php else: ?>
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
        <?php endif; ?>
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
                location.reload();
            } else {
                alert(data.message || 'Error adding to cart');
            }
        });
    }
    </script>
</body>
</html>
