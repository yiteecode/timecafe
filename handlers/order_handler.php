<?php
require_once '../db-config.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['menu_item_id', 'customer_name', 'email', 'phone', 'address', 'quantity'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Validate menu item exists
    $stmt = $connect->prepare("SELECT id, price FROM menu_items WHERE id = ? AND active = 1");
    $stmt->bind_param("i", $_POST['menu_item_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $menu_item = $result->fetch_assoc();

    if (!$menu_item) {
        throw new Exception("Invalid menu item");
    }

    // Create order
    $stmt = $connect->prepare("
        INSERT INTO orders (
            menu_item_id, customer_name, email, phone, address, 
            quantity, special_instructions
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issssis",
        $_POST['menu_item_id'],
        $_POST['customer_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['quantity'],
        $_POST['special_instructions']
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to place order");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 