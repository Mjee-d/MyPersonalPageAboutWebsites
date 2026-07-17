<?php
include 'db.php';
header('Content-Type: application/json');
$action = $_GET['action'] ?? '';

if ($action === 'fetch') {
    $result = $conn->query("SELECT * FROM users ORDER BY id ASC");
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}

elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = trim($input['name'] ?? '');
    $age = intval($input['age'] ?? 0);

    if (!empty($name) && $age > 0) {
        $stmt = $conn->prepare("INSERT INTO users (name, age, status) VALUES (?, ?, 0)");
        $stmt->bind_param("si", $name, $age);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid inputs']);
    }
}

elseif ($action === 'toggle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);

    if ($id > 0) {
        $stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result) {
            $new_status = $result['status'] == 1 ? 0 : 1;
            $update_stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $new_status, $id);
            
            if ($update_stmt->execute()) {
                echo json_encode(['success' => true, 'new_status' => $new_status]);
            } else {
                echo json_encode(['success' => false, 'error' => $update_stmt->error]);
            }
            $update_stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'User not found']);
        }
    }
}
$conn->close();
?>