<?php
// Use __DIR__ to make the path to config/db.php more robust.
// This assumes config/db.php is in /opt/lampp/htdocs/tkt/config/
require_once __DIR__ . '/../config/db.php';

echo "Starting database setup...\n";

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Create companies table
    $conn->exec("CREATE TABLE IF NOT EXISTS companies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'companies' checked/created.\n";

    // Create users table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies(id)
    )");
    echo "Table 'users' checked/created.\n";

    // Create devices table
    $conn->exec("CREATE TABLE IF NOT EXISTS devices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT,
        user_id INT,
        device_uuid VARCHAR(100) UNIQUE NOT NULL,
        device_name VARCHAR(100),
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    echo "Table 'devices' checked/created.\n";

    // Create test company and admin user if they don't exist
    $stmt = $conn->prepare("SELECT id FROM companies WHERE name = 'Test Company'");
    $stmt->execute();
    $company = $stmt->fetch();

    if (!$company) {
        $conn->exec("INSERT INTO companies (name) VALUES ('Test Company')");
        $company_id = $conn->lastInsertId();
        echo "Test Company created with ID: $company_id.\n";
    } else {
        $company_id = $company['id'];
        echo "Test Company already exists with ID: $company_id.\n";
    }

    // Check if admin user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = 'admin@example.com'");
    $stmt->execute();
    if (!$stmt->fetch()) {
        // Create admin user with password 'admin123'
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $userStmt = $conn->prepare("INSERT INTO users (company_id, name, email, password, role) 
                                   VALUES (:company_id, :name, :email, :password, :role)");
        $userStmt->execute([
            ':company_id' => $company_id,
            ':name' => 'Admin User',
            ':email' => 'admin@example.com',
            ':password' => $password_hash,
            ':role' => 'admin'
        ]);
        echo "Admin user 'admin@example.com' created.\n";
    } else {
        echo "Admin user 'admin@example.com' already exists.\n";
    }

    echo "Database setup completed successfully!\n";
    echo "You can now login with:\n";
    echo "Email: admin@example.com\n";
    echo "Password: admin123\n";

} catch(PDOException $e) {
    echo "Database Setup Error: " . $e->getMessage() . "\n";
    echo "Please ensure your database server is running and 'config/db.php' (or environment variables) are correctly configured.\n";
}
?> 