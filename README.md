# 🌸 Tajmil Lwajh

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-orange)](https://www.mysql.com/)
[![InfinityFree](https://img.shields.io/badge/Host-InfinityFree-purple)](https://infinityfree.com/)

**Tajmil Lwajh** est une plateforme e-commerce dédiée à la beauté et aux parfums. Le projet comprend une boutique, un panier, une liste de favoris, l’authentification utilisateur, les commandes, les avis, un chatbot et des outils de gestion.

---

## 🚀 Démo & accès

- **Site client :** [https://amraoui2008.ifree.page/](https://amraoui2008.ifree.page/)
- **Page de connexion admin en ligne :** [https://amraoui2008.ifree.page/admin/login.php](https://amraoui2008.ifree.page/admin/login.php)

> Aucun mot de passe administrateur n’est publié dans ce README. Ne publiez pas d’identifiants dans un dépôt public. Si le mot de passe de démonstration partagé précédemment est encore actif, changez-le immédiatement.

---

## ✨ Fonctionnalités

- 📦 Catalogue avec catégories, marques et prix
- 🔍 Recherche et détails des produits
- 🛒 Panier et liste de favoris
- 👤 Inscription, connexion et profil utilisateur
- 📦 Commandes et historique des commandes
- ⭐ Avis et système de notation
- 📞 Formulaire de contact et newsletter
- 🤖 Chatbot d’assistance en français et en darija
- 📱 Design responsive (ordinateur et mobile)
- 🛠️ Outils de gestion des stocks, produits et commandes

---

## 🛠️ Technologies

- **Backend :** PHP avec PDO
- **Base de données :** MySQL / MariaDB
- **Frontend :** HTML, CSS et JavaScript
- **Icônes et polices :** Font Awesome et Google Fonts
- **Design :** CSS personnalisé responsive

---

## 📁 Structure du projet

```text
Tajmil/
├── assets/
│   ├── css/
│   ├── images/
│   ├── js/
│   └── uploads/
├── pages/                  # Pages de la boutique
│   ├── home.php
│   ├── products.php
│   ├── product-details.php
│   ├── cart.php
│   ├── checkout.php
│   ├── login.php
│   ├── register.php
│   ├── orders.php
│   ├── wishlist.php
│   ├── profile.php
│   ├── about.php
│   ├── review.php
│   ├── contact.php
│   └── install.php
├── php_files/              # Configuration et traitements PHP
│   ├── config.php
│   ├── login.php
│   ├── register.php
│   ├── add_to_cart.php
│   ├── get_cart.php
│   ├── update_cart.php
│   ├── remove_cart.php
│   ├── checkout.php
│   ├── add_review.php
│   ├── get_reviews.php
│   ├── chatbot.php
│   └── contact.php
├── index.php
├── header.php
├── footer.php
├── analytics.php
├── brands.php
├── categories.php
├── coupons.php
├── inventory.php
├── orders.php
├── order-details.php
├── crud_users.php
├── newsletter.php
├── logs.php
├── backup.php
├── create_admin.php
└── export_*.php           # Scripts d’export
```

> Dans le dépôt actuel, les scripts de gestion sont placés à la racine ; aucun dossier `admin/` ni fichier `khalid.sql` n’est présent à la racine. Le site déployé peut avoir une configuration différente.

---

## ⚙️ Prérequis

- PHP 7.4 ou plus récent
- MySQL ou MariaDB
- Apache, Nginx ou un serveur local tel que XAMPP
- Extension PHP PDO MySQL
- Extension GD si vous utilisez les fonctions de génération d’images

Composer n’est pas requis actuellement.

---

## 🚀 Installation locale

### 1. Cloner le projet

```bash
git clone https://github.com/mrawym636-sketch/Tajmil.git
cd Tajmil
```

### 2. Placer le projet dans le dossier web

Exemples de dossiers utilisés par des serveurs locaux :

```text
C:/xampp/htdocs/Tajmil
```

ou :

```text
/var/www/html/Tajmil
```

### 3. Créer la base de données

La configuration actuelle de `php_files/config.php` utilise la base `khalid` par défaut. Vous pouvez la créer avec :

```sql
CREATE DATABASE khalid CHARACTER SET utf8 COLLATE utf8_general_ci;
```

### 4. Configurer la connexion

Modifiez `php_files/config.php` pour utiliser les paramètres de votre environnement :

```php
$host = 'localhost';
$dbname = 'khalid';
$username = 'root';
$password = '';
```

N’utilisez pas les valeurs de développement sur un serveur public. Ne placez pas de mots de passe réels dans le dépôt.

### 5. Préparer les tables

Les tables principales utilisées par l’application, notamment `utilisateurs` et `produits`, doivent exister. `pages/install.php` crée certaines tables auxiliaires et ajoute quelques colonnes, mais ne configure pas à lui seul l’ensemble de la base de données.

Aucun fichier SQL de base de données n’est présent à la racine du dépôt. Si vous avez le schéma SQL séparément, importez-le dans la base avant d’utiliser l’application.

### 6. Démarrer le site

Démarrez Apache et MySQL, puis ouvrez :

```text
http://localhost/Tajmil/
```

---

## ☁️ Déploiement sur InfinityFree

Le projet peut être déployé sur un hébergement compatible PHP et MySQL/MariaDB. Les noms d’hôte, de base de données et d’utilisateur varient selon le compte d’hébergement : récupérez-les dans le panneau de contrôle de l’hébergeur.

1. Créez une base de données depuis le panneau d’hébergement.
2. Notez le nom de l’hôte MySQL, le nom de la base et l’utilisateur fournis pour votre compte.
3. Configurez ces valeurs dans `php_files/config.php` et gardez le mot de passe privé.
4. Importez votre schéma SQL dans phpMyAdmin si vous en disposez.
5. Téléversez les fichiers en conservant la structure des dossiers.
6. Après l’installation, désactivez ou supprimez les scripts temporaires accessibles publiquement.

Site en ligne : [https://amraoui2008.ifree.page/](https://amraoui2008.ifree.page/)

---

## 🗄️ Base de données

Selon les fonctionnalités utilisées, le code s’appuie notamment sur les tables suivantes :

| Table | Description |
|---|---|
| `utilisateurs` | Comptes clients et administrateurs |
| `produits` | Catalogue de produits |
| `categories` | Catégories de produits |
| `brands` | Marques |
| `commandes` | Commandes clients |
| `commande_logs` | Historique des changements de statut |
| `avis` | Avis clients |
| `coupons` | Codes promotionnels |
| `logs` | Journal d’activité |
| `messages` | Messages de contact |
| `newsletter_subscribers` | Abonnés à la newsletter |
| `pages` | Contenu des pages |
| `blacklist` | Numéros bloqués |
| `visits` | Statistiques de visites |
| `chat_logs` | Historique du chatbot |

La structure exacte dépend du schéma de base de données utilisé. Faites une sauvegarde avant toute migration ou modification.

---

## 🔐 Sécurité

- Les mots de passe des utilisateurs sont vérifiés avec les fonctions PHP de hachage de mots de passe.
- Les accès à la base de données utilisent PDO ; vérifiez que les requêtes sont préparées lorsque des données utilisateur sont utilisées.
- Changez les identifiants par défaut ou de démonstration avant le déploiement.
- Ne commitez aucun mot de passe, secret, sauvegarde contenant des données personnelles ou identifiant de base de données.
- Limitez l’accès aux scripts d’administration, d’installation, de diagnostic et de sauvegarde.

---

## 📝 Notes importantes

- Placez les images de produits dans `assets/images/` et les photos utilisateur dans `assets/uploads/`, selon les chemins attendus par l’application.
- Gardez la structure des dossiers intacte : plusieurs pages utilisent des chemins relatifs.
- Vérifiez les permissions d’écriture nécessaires pour le dossier `assets/uploads/`.
- Testez les fonctionnalités dans un environnement local avant la mise en production.

---

## 👨‍💻 Auteur

**Tajmil Lwajh**

- GitHub : [@mrawym636-sketch](https://github.com/mrawym636-sketch)
- Contact : ouvrez une issue dans ce dépôt pour toute question sur le projet.

## 📄 Licence

Aucun fichier de licence n’a été identifié dans le dépôt. Contactez le propriétaire du projet avant toute réutilisation.

## 🙏 Remerciements

Merci à toutes les clientes et à tous les clients qui font confiance à Tajmil Lwajh.

⭐ Si ce projet vous plaît, pensez à lui laisser une étoile ! ⭐
