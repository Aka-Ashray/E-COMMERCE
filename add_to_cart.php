<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    
    // Check if product already in cart
    $stmt = $mysqli->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + 1;
        $stmt = $mysqli->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
    } else {
        // Add new item
        $stmt = $mysqli->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $product_id);
    }
    
    if ($stmt->execute()) {
        // Get updated cart count
        $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $count_result = $stmt->get_result()->fetch_assoc();
        
        echo json_encode(['success' => true, 'cart_count' => $count_result['count']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding to cart']);
    }
}
?>
