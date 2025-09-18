<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $stock = $_POST['stock'];
    
    // Handle file upload
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $image_path = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_file = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $mysqli->prepare("INSERT INTO products (name, price, description, type, image, stock) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsssi", $name, $price, $description, $type, $image_path, $stock);
    
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        $error = "Error adding product";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Add New Product</h1>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 mb-2">Product Name</label>
                        <input type="text" name="name" required class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Price</label>
                        <input type="number" name="price" step="0.01" required class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Type</label>
                        <select name="type" required class="w-full p-2 border rounded">
                            <option value="cpu">CPU</option>
                            <option value="gpu">GPU</option>
                            <option value="ram">RAM</option>
                            <option value="storage">Storage</option>
                            <option value="motherboard">Motherboard</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Stock</label>
                        <input type="number" name="stock" required class="w-full p-2 border rounded">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-700 mb-2">Description</label>
                        <textarea name="description" required class="w-full p-2 border rounded h-32"></textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-700 mb-2">Product Image</label>
                        <input type="file" name="image" accept="image/*" required class="w-full p-2 border rounded">
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Add Product
                    </button>
                    <a href="admin.php" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
