<?php
require_once 'db/Database.php';

$db = new Database();
$conn = $db->getConnection();

// Check if rooms table has data
$checkQuery = "SELECT COUNT(*) as count FROM rooms";
$stmt = $conn->prepare($checkQuery);
$stmt->execute();
$roomCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($roomCount == 0) {
    echo "Adding sample rooms...\n";
    
    $sampleRooms = [
        ['101', 'Standard Single', 'Single room with basic amenities', 89.99, 1, 'Available'],
        ['102', 'Standard Double', 'Double room with ocean view', 129.99, 2, 'Available'],
        ['103', 'Deluxe Suite', 'Spacious suite with balcony', 199.99, 4, 'Available'],
        ['201', 'Ocean View Premium', 'Premium room with direct ocean view', 249.99, 2, 'Available'],
        ['202', 'Family Suite', 'Large suite perfect for families', 299.99, 6, 'Occupied'],
        ['203', 'Penthouse', 'Luxury penthouse with private terrace', 499.99, 4, 'Available'],
        ['301', 'Standard Single', 'Single room with basic amenities', 89.99, 1, 'Maintenance'],
        ['302', 'Standard Double', 'Double room with garden view', 119.99, 2, 'Available'],
        ['303', 'Deluxe Ocean View', 'Deluxe room with premium ocean view', 179.99, 3, 'Available'],
        ['304', 'Executive Suite', 'Executive level suite with workspace', 349.99, 2, 'Available']
    ];
    
    $insertQuery = "INSERT INTO rooms (room_number, type, description, price, capacity, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    
    foreach ($sampleRooms as $room) {
        $stmt->execute($room);
    }
    
    echo "Sample rooms added successfully!\n";
} else {
    echo "Rooms table already has data ($roomCount rooms).\n";
}

// Check if users table has an admin user
$checkAdminQuery = "SELECT COUNT(*) as count FROM users WHERE role = 'admin'";
$stmt = $conn->prepare($checkAdminQuery);
$stmt->execute();
$adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

if ($adminCount == 0) {
    echo "Adding admin user...\n";
    
    $adminData = [
        'name' => 'Administrator',
        'email' => 'admin@hotel.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin'
    ];
    
    $insertAdminQuery = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertAdminQuery);
    $stmt->execute([$adminData['name'], $adminData['email'], $adminData['password'], $adminData['role']]);
    
    echo "Admin user added successfully!\n";
    echo "Email: admin@hotel.com\n";
    echo "Password: admin123\n";
} else {
    echo "Admin user already exists.\n";
}

echo "Setup complete!\n";
?>
