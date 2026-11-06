<?php
echo "<h2>🔍 Vérification de l'extension GD</h2>";

if (extension_loaded('gd')) {
    echo "<span style='color:green;'>✅ L'extension GD est activée</span><br>";
    echo "Version: " . gd_info()['GD Version'] . "<br>";
} else {
    echo "<span style='color:red;'>❌ L'extension GD n'est pas activée</span><br>";
    echo "<p>Pour l'activer sur XAMPP:</p>";
    echo "<ol>";
    echo "<li>Ouvrez <strong>C:\\xampp\\php\\php.ini</strong></li>";
    echo "<li>Cherchez <strong>;extension=gd</strong></li>";
    echo "<li>Supprimez le point-virgule <strong>;</strong></li>";
    echo "<li>Redémarrez Apache</li>";
    echo "</ol>";
}
?>