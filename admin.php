<?php
session_start();
// Check for both user login and admin status
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // Redirect with an error message
    $_SESSION['error'] = "You don't have permission to access the admin panel.";
    header("Location: index.php");
    exit();
}
include 'db_config.php';

$stmt = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Cupboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Product Management</h1>
            <a href="add_product.php" class="bg-green-500 text-white px-4 py-2 rounded">Add New Product</a>
        </div>

        <div class="bg-white rounded-lg shadow-md">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-4">Image</th>
                        <th class="p-4">Name</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Price</th>
                        <th class="p-4">Stock</th>
                        <th class="p-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr class="border-t">
                        <td class="p-4">
                            <img src="<?php echo $product['image']; ?>" class="w-20 h-20 object-cover">
                        </td>
                        <td class="p-4"><?php echo $product['name']; ?></td>
                        <td class="p-4"><?php echo $product['type']; ?></td>
                        <td class="p-4">$<?php echo $product['price']; ?></td>
                        <td class="p-4"><?php echo $product['stock']; ?></td>
                        <td class="p-4">
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                               class="bg-blue-500 text-white px-3 py-1 rounded mr-2">Edit</a>
                            <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                               class="bg-red-500 text-white px-3 py-1 rounded" 
                               onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
