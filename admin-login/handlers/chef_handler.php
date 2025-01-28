<?php
// Prevent any output before headers
ob_start();

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once '../../db-config.php';

// Set JSON content type
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = '../../uploads/chefs/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

function handleEditChef($connect, $upload_dir) {
    // Clear any previous output
    while (ob_get_level()) {
        ob_end_clean();
    }

    try {
        $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid chef ID');
        }

        if (empty($_POST['name']) || empty($_POST['profession'])) {
            throw new Exception('Name and profession are required');
        }

        // Get current chef data
        $stmt = $connect->prepare("SELECT * FROM chefs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $current_chef = $stmt->get_result()->fetch_assoc();

        if (!$current_chef) {
            throw new Exception('Chef not found');
        }

        // Handle image upload if new image is provided
        $image_name = $current_chef['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (!in_array($file_extension, $allowed_types)) {
                throw new Exception('Invalid image type. Allowed: JPG, PNG, WEBP');
            }

            $image_name = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $image_name;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                throw new Exception('Failed to upload image');
            }

            // Delete old image
            if ($current_chef['image'] && file_exists($upload_dir . $current_chef['image'])) {
                unlink($upload_dir . $current_chef['image']);
            }
        }

        // Update chef
        $stmt = $connect->prepare("
            UPDATE chefs 
            SET name = ?, profession = ?, description = ?, image = ?,
                facebook = ?, instagram = ?, twitter = ?
            WHERE id = ?
        ");

        if (!$stmt) {
            throw new Exception("Database error: " . $connect->error);
        }

        $description = $_POST['description'] ?? '';
        $facebook = $_POST['facebook'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $twitter = $_POST['twitter'] ?? '';

        $stmt->bind_param("sssssssi", 
            $_POST['name'],
            $_POST['profession'],
            $description,
            $image_name,
            $facebook,
            $instagram,
            $twitter,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to update chef: " . $stmt->error);
        }

        $response = [
            'success' => true,
            'message' => 'Chef updated successfully!',
            'chef' => [
                'name' => $_POST['name'],
                'profession' => $_POST['profession']
            ]
        ];

        echo json_encode($response);
        return;

    } catch (Exception $e) {
        // Delete uploaded image if it exists
        if (isset($upload_path) && file_exists($upload_path)) {
            unlink($upload_path);
        }
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        return;
    }
}

// Main handler code
try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'add':
            handleAddChef($connect, $upload_dir);
            break;
        case 'edit':
            handleEditChef($connect, $upload_dir);
            break;
        case 'delete':
            handleDeleteChef($connect, $upload_dir);
            break;
        case 'get':
            handleGetChef($connect);
            break;
        default:
            throw new Exception('Invalid action specified');
    }

} catch (Exception $e) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($connect);
exit;

function handleGetChef($connect) {
    $id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception('Invalid chef ID');
    }

    $stmt = $connect->prepare("SELECT * FROM chefs WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to fetch chef details');
    }

    $result = $stmt->get_result();
    $chef = $result->fetch_assoc();

    if (!$chef) {
        throw new Exception('Chef not found');
    }

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'data' => $chef
    ]);
}

function handleDeleteChef($connect, $upload_dir) {
    $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
    if (!$id) {
        throw new Exception('Invalid chef ID');
    }

    // Get image filename before deleting
    $stmt = $connect->prepare("SELECT image FROM chefs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $chef = $stmt->get_result()->fetch_assoc();

    // Delete from database
    $stmt = $connect->prepare("DELETE FROM chefs WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete chef');
    }

    // Delete image file if exists
    if ($chef && $chef['image'] && file_exists($upload_dir . $chef['image'])) {
        unlink($upload_dir . $chef['image']);
    }

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Chef deleted successfully!'
    ]);
}

function handleAddChef($connect, $upload_dir) {
    // Validate required fields
    if (empty($_POST['name']) || empty($_POST['profession'])) {
        throw new Exception('Name and profession are required');
    }

    // Validate image
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        throw new Exception('Chef photo is required');
    }

    // Process image
    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (!in_array($file_extension, $allowed_types)) {
        throw new Exception('Invalid image type. Allowed: JPG, PNG, WEBP');
    }

    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload image');
    }

    // Get next sort order
    $sort_query = "SELECT COALESCE(MAX(sort_order), 0) + 1 as next_order FROM chefs";
    $result = mysqli_query($connect, $sort_query);
    $next_order = $result->fetch_assoc()['next_order'];

    // Insert chef
    $stmt = $connect->prepare("
        INSERT INTO chefs (name, profession, description, image, facebook, instagram, twitter, active, sort_order) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)
    ");

    if (!$stmt) {
        throw new Exception("Database error: " . $connect->error);
    }

    $description = $_POST['description'] ?? '';
    $facebook = $_POST['facebook'] ?? '';
    $instagram = $_POST['instagram'] ?? '';
    $twitter = $_POST['twitter'] ?? '';

    $stmt->bind_param("sssssssi", 
        $_POST['name'],
        $_POST['profession'],
        $description,
        $new_filename,
        $facebook,
        $instagram,
        $twitter,
        $next_order
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to add chef: " . $stmt->error);
    }

    ob_end_clean();
    echo json_encode([
        'success' => true, 
        'message' => 'Chef added successfully!',
        'chef' => [
            'name' => $_POST['name'],
            'profession' => $_POST['profession']
        ]
    ]);
}
?> 