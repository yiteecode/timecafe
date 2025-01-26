<?php
session_start();
require_once '../db-config.php';

// Check for admin session
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

header('Content-Type: application/json');

try {
    // Get menu items with categories
    $query = "
        SELECT 
            m.id,
            m.category_id,
            m.name,
            m.description,
            m.price,
            m.image,
            m.active,
            m.sort_order,
            m.created_at,
            m.updated_at,
            c.name as category_name
        FROM menu_items m
        LEFT JOIN menu_categories c ON m.category_id = c.id
        WHERE m.active = 1
        ORDER BY m.sort_order ASC, m.category_id ASC, m.name ASC
    ";
    
    $result = mysqli_query($connect, $query);
    
    if (!$result) {
        throw new Exception(mysqli_error($connect));
    }
    
    $menu_items = [];
    while ($item = mysqli_fetch_assoc($result)) {
        // Verify image exists
        if (!empty($item['image'])) {
            $image_path = "../uploads/menu/{$item['image']}";
            if (!file_exists($image_path)) {
                $item['image'] = null;
            }
        }
        
        // Format price to 2 decimal places
        $item['price'] = number_format((float)$item['price'], 2, '.', '');
        
        // Format dates
        $item['created_at'] = date('Y-m-d H:i:s', strtotime($item['created_at']));
        $item['updated_at'] = date('Y-m-d H:i:s', strtotime($item['updated_at']));
        
        // Group by category_id
        if (!isset($menu_items[$item['category_id']])) {
            $menu_items[$item['category_id']] = [
                'category_id' => $item['category_id'],
                'category_name' => $item['category_name'],
                'items' => []
            ];
        }
        
        // Add item to its category
        $menu_items[$item['category_id']]['items'][] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'description' => $item['description'],
            'price' => $item['price'],
            'image' => $item['image'],
            'sort_order' => $item['sort_order'],
            'created_at' => $item['created_at'],
            'updated_at' => $item['updated_at']
        ];
    }
    
    // Convert to indexed array and count total items
    $categories = array_values($menu_items);
    $total_items = array_sum(array_map(function($category) {
        return count($category['items']);
    }, $categories));
    
    echo json_encode([
        'success' => true,
        'data' => [
            'categories' => $categories,
            'total_items' => $total_items
        ],
        'message' => $total_items > 0 ? '' : 'No menu items found'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($connect);
?> 