<?php
// Add these lines at the very top
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../db-config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

header('Content-Type: application/json');

try {
    // Add debug output
    error_log("Executing query for chefs");
    
    $query = "
        SELECT id, name, profession, description, image, active, sort_order, created_at 
        FROM chefs 
        WHERE active = true 
        ORDER BY sort_order ASC, created_at DESC
    ";
    
    $result = mysqli_query($connect, $query);
    
    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($connect));
        throw new Exception('Failed to fetch chefs: ' . mysqli_error($connect));
    }
    
    $chefs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Verify image exists
        $imagePath = '../uploads/chefs/' . $row['image'];
        error_log("Checking image path: " . $imagePath);
        
        if (file_exists($imagePath)) {
            $chefs[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'profession' => $row['profession'],
                'description' => $row['description'],
                'image' => $row['image'],
                'active' => $row['active'] == 1,
                'sort_order' => $row['sort_order'],
                'created_at' => $row['created_at']
            ];
        }
    }
    
    error_log("Found " . count($chefs) . " chefs");
    
    echo json_encode([
        'success' => true,
        'data' => $chefs,
        'message' => count($chefs) > 0 ? '' : 'No chefs found'
    ]);

} catch (Exception $e) {
    error_log("Error in get_chefs.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($connect);
?> 