<?php
// Script d'installation automatique du chatbot
// Ouvre ce fichier dans le navigateur UNE SEULE FOIS : http://localhost/ton-projet/pages/add_chatbot.php

$pages = [
    'home.php', 'products.php', 'product-details.php', 'cart.php',
    'about.php', 'contact.php', 'review.php', 'wishlist.php',
    'orders.php', 'profile.php', 'login.php', 'register.php',
    'checkout.php', 'user_dashboard.php', 'profile_user.php'
];

$widget_code = "\n<?php require_once '../php_files/chatbot_widget.php'; ?>\n";

echo "<html><head><title>Installation Chatbot</title>";
echo "<style>body{font-family:Arial;padding:40px;background:#f8f9fa;} .ok{color:#28a745;} .skip{color:#ffc107;} .err{color:#dc3545;} h1{color:#e84393;} .box{background:white;padding:25px;border-radius:15px;box-shadow:0 5px 20px rgba(0,0,0,0.1);max-width:800px;margin:0 auto;} li{margin:10px 0;font-size:15px;}</style>";
echo "</head><body><div class='box'>";
echo "<h1>🤖 Installation du Chatbot</h1><ul>";

foreach ($pages as $page) {
    $filepath = __DIR__ . '/' . $page;
    
    if (!file_exists($filepath)) {
        echo "<li class='skip'>⚠️ <strong>$page</strong> - Fichier non trouvé (ignoré)</li>";
        continue;
    }
    
    $content = file_get_contents($filepath);
    
    // Vérifier si déjà installé
    if (strpos($content, 'chatbot_widget.php') !== false) {
        echo "<li class='skip'>✅ <strong>$page</strong> - Déjà installé (ignoré)</li>";
        continue;
    }
    
    // Chercher </body>
    $pos = strrpos($content, '</body>');
    
    if ($pos === false) {
        echo "<li class='err'>❌ <strong>$page</strong> - Pas de &lt;/body&gt; trouvé</li>";
        continue;
    }
    
    // Insérer AVANT </body>
    $new_content = substr($content, 0, $pos) . $widget_code . substr($content, $pos);
    
    if (file_put_contents($filepath, $new_content)) {
        echo "<li class='ok'>✅ <strong>$page</strong> - Chatbot ajouté !</li>";
    } else {
        echo "<li class='err'>❌ <strong>$page</strong> - Erreur d'écriture</li>";
    }
}

echo "</ul>";
echo "<hr style='margin:25px 0;border:none;border-top:2px solid #eee;'>";
echo "<h2 style='color:#1a1a2e;'>✨ Installation terminée !</h2>";
echo "<p>Le chatbot est maintenant actif sur toutes tes pages.</p>";
echo "<p style='margin-top:20px;'><a href='home.php' style='background:#e84393;color:white;padding:12px 25px;border-radius:50px;text-decoration:none;font-weight:600;'>🏠 Voir le site</a></p>";
echo "</div></body></html>";