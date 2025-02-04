<?php
session_start();
require_once '../../db-config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'update':
            // Validate required fields
            if (empty($_POST['heading']) || empty($_POST['main_content'])) {
                throw new Exception('Heading and main content are required');
            }

            // Handle image upload if present
            $image_name = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                $max_size = 2 * 1024 * 1024; // 2MB

                if (!in_array($_FILES['image']['type'], $allowed_types)) {
                    throw new Exception('Invalid image format. Only JPG, PNG and WEBP are allowed');
                }

                if ($_FILES['image']['size'] > $max_size) {
                    throw new Exception('Image size must be less than 2MB');
                }

                // Get current image to delete later
                $stmt = $connect->prepare("SELECT image FROM about_section LIMIT 1");
                $stmt->execute();
                $current = $stmt->get_result()->fetch_assoc();

                $upload_dir = '../../uploads/about/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
                $upload_path = $upload_dir . $image_name;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    throw new Exception('Failed to upload image');
                }

                // Delete old image if exists
                if ($current && $current['image'] && file_exists($upload_dir . $current['image'])) {
                    unlink($upload_dir . $current['image']);
                }
            }

            // Check if record exists
            $check = $connect->query("SELECT id FROM about_section LIMIT 1");
            if ($check->num_rows > 0) {
                // Update existing record
                $sql = "UPDATE about_section SET 
                        heading = ?, 
                        subheading = ?, 
                        main_content = ?, 
                        mission = ?, 
                        vision = ?, 
                        video_url = ?";
                
                $params = [
                    $_POST['heading'],
                    $_POST['subheading'] ?? null,
                    $_POST['main_content'],
                    $_POST['mission'] ?? null,
                    $_POST['vision'] ?? null,
                    $_POST['video_url'] ?? null
                ];
                
                if ($image_name) {
                    $sql .= ", image = ?";
                    $params[] = $image_name;
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $check->fetch_assoc()['id'];
                
                $stmt = $connect->prepare($sql);
                $types = str_repeat('s', count($params) - 1) . 'i';
                
            } else {
                // Insert new record
                $sql = "INSERT INTO about_section (heading, subheading, main_content, mission, vision, video_url, image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $params = [
                    $_POST['heading'],
                    $_POST['subheading'] ?? null,
                    $_POST['main_content'],
                    $_POST['mission'] ?? null,
                    $_POST['vision'] ?? null,
                    $_POST['video_url'] ?? null,
                    $image_name
                ];
                
                $stmt = $connect->prepare($sql);
                $types = 'sssssss';
            }

            $stmt->bind_param($types, ...$params);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to save about section: ' . $stmt->error);
            }

            echo json_encode([
                'success' => true,
                'message' => 'About section updated successfully'
            ]);
            break;

        case 'add_feature':
            if (empty($_POST['title']) || empty($_POST['icon'])) {
                throw new Exception('Title and icon are required');
            }

            $stmt = $connect->prepare("INSERT INTO about_features (title, description, icon) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_POST['title'], $_POST['description'], $_POST['icon']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to add feature: ' . $stmt->error);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Feature added successfully'
            ]);
            break;

        case 'edit_feature':
            if (empty($_POST['id']) || empty($_POST['title']) || empty($_POST['icon'])) {
                throw new Exception('ID, title and icon are required');
            }

            $stmt = $connect->prepare("UPDATE about_features SET title = ?, description = ?, icon = ? WHERE id = ?");
            $stmt->bind_param("sssi", $_POST['title'], $_POST['description'], $_POST['icon'], $_POST['id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update feature: ' . $stmt->error);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Feature updated successfully'
            ]);
            break;

        case 'get_feature':
            if (empty($_POST['id'])) {
                throw new Exception('Feature ID is required');
            }

            $id = intval($_POST['id']);
            $stmt = $connect->prepare("SELECT * FROM about_features WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $feature = $result->fetch_assoc();
            
            if (!$feature) {
                throw new Exception('Feature not found');
            }
            
            echo json_encode([
                'success' => true,
                'feature' => $feature
            ]);
            break;

        case 'delete_feature':
            if (empty($_POST['id'])) {
                throw new Exception('Feature ID is required');
            }

            $stmt = $connect->prepare("DELETE FROM about_features WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete feature: ' . $stmt->error);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Feature deleted successfully'
            ]);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    error_log("About handler error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 