<?php
require_once 'config.php';

// Delete old admin if exists
$pdo->prepare("DELETE FROM utilisateurs WHERE email = 'admin@admin.com'")->execute();

// Create new admin account
$nom = "Super Admin";
$email = "admin@admin.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";
$photo = "default.png";

$stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, password, role, photo) VALUES (?, ?, ?, ?, ?)");

if ($stmt->execute([$nom, $email, $password, $role, $photo])) {
    echo "<h2 style='color:green;'>✅ Admin created successfully!</h2>";
    echo "<p><strong>Email:</strong> admin@admin.com</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<p><strong>Role:</strong> admin</p>";
    echo "<a href='login.php' style='background:#10b981;color:white;padding:10px 20px;text-decoration:none;border-radius:8px;'>🔐 Login Now</a>";
} else {
    echo "<h2 style='color:red;'>❌ Error creating admin</h2>";
}
?>