<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telephone = trim($_POST['telephone']);
    $photo = 'default.png';
    
    $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        header('Location: ../pages/register.php?error=exists');
        exit();
    }
    
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, password, telephone, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $email, $password, $telephone, $photo]);
    
    header('Location: ../pages/login.php?registered=1');
    exit();
}
header('Location: ../pages/register.php');
exit();
?>