<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

include 'db_config.php';

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $stock = $_POST['stock'];
    
    // Handle file upload if new image is provided
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image if exists
            if (!empty($product['image']) && file_exists($product['image'])) {
                unlink($product['image']);
            }
            $image_path = $target_file;
            
            $stmt = $mysqli->prepare("UPDATE products SET name=?, price=?, description=?, type=?, stock=?, image=? WHERE id=?");
            $stmt->bind_param("sdssisi", $name, $price, $description, $type, $stock, $image_path, $id);
        }
    } else {
        $stmt = $mysqli->prepare("UPDATE products SET name=?, price=?, description=?, type=?, stock=? WHERE id=?");
        $stmt->bind_param("sdssii", $name, $price, $description, $type, $stock, $id);
    }
    
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        $error = "Error updating product";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    
    <div class="container mx-auto p-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6">Edit Product</h1>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 mb-2">Product Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" 
                               required class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Price</label>
                        <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" 
                               required class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Type</label>
                        <select name="type" required class="w-full p-2 border rounded">
                            <?php
                            $types = ['cpu', 'gpu', 'ram', 'storage', 'motherboard'];
                            foreach ($types as $type) {
                                $selected = ($product['type'] == $type) ? 'selected' : '';
                                echo "<option value=\"$type\" $selected>" . strtoupper($type) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Stock</label>
                        <input type="number" name="stock" value="<?php echo $product['stock']; ?>" 
                               required class="w-full p-2 border rounded">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-700 mb-2">Description</label>
                        <textarea name="description" required 
                                  class="w-full p-2 border rounded h-32"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-gray-700 mb-2">Current Image</label>
                        <img src="<?php echo $product['image']; ?>" class="w-40 h-40 object-cover mb-2">
                        <input type="file" name="image" accept="image/*" class="w-full p-2 border rounded">
                        <p class="text-sm text-gray-500">Leave empty to keep current image</p>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Update Product
                    </button>
                    <a href="admin.php" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
