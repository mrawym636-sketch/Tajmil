# 🌸 Tajmil Lwajh

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-orange)](https://www.mysql.com/)
[![Responsive](https://img.shields.io/badge/Design-Responsive-purple)](#-fonctionnalités)
[![InfinityFree](https://img.shields.io/badge/Host-InfinityFree-purple)](https://infinityfree.com/)

**Tajmil Lwajh** est une plateforme e-commerce dédiée aux produits de beauté et aux parfums. Le projet comprend une boutique, un panier, une liste de souhaits, l’authentification des utilisateurs, les commandes, les avis clients, un chatbot et des outils de gestion.

---

## 🚀 Démo

- **Site client :** [https://amraoui2008.ifree.page/](https://amraoui2008.ifree.page/)
- **Page de connexion admin en ligne :** [https://amraoui2008.ifree.page/admin/login.php](https://amraoui2008.ifree.page/admin/login.php)

> Aucun mot de passe administrateur n’est publié dans ce README. Ne publiez pas d’identifiants dans un dépôt public. Si un mot de passe de démonstration partagé précédemment est encore actif, changez-le immédiatement.

## ✨ Fonctionnalités

- 📦 Catalogue de produits avec catégories et marques
- 🔍 Recherche et pages de détails produit
- 🛒 Panier avec ajout, modification des quantités et suppression
- ❤️ Liste de souhaits
- 👤 Inscription, connexion et profil utilisateur
- 📦 Passage de commande et historique des commandes
- ⭐ Avis et système de notation
- 📞 Formulaire de contact et newsletter
- 🤖 Chatbot d’assistance en français et en darija
- 📱 Mise en page responsive
- 🛠️ Outils de gestion des produits, stocks, commandes, utilisateurs et avis

## 🛠️ Technologies

- **Backend :** PHP avec PDO
- **Base de données :** MySQL ou MariaDB
- **Frontend :** HTML, CSS et JavaScript
- **Icônes et polices :** Font Awesome et Google Fonts
- **Interface :** CSS personnalisé responsive

## 📁 Structure du projet

```text
Tajmil/
├── assets/
│   ├── css/
│   ├── images/
│   ├── js/
│   └── uploads/
├── pages/                  # Pages de la boutique
│   ├── about.php
│   ├── cart.php
│   ├── checkout.php
│   ├── contact.php
│   ├── home.php
│   ├── login.php
│   ├── orders.php
│   ├── product-details.php
│   ├── products.php
│   ├── profile.php
│   ├── register.php
│   ├── review.php
│   ├── user_dashboard.php
│   ├── wishlist.php
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
├── auth.php
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

> Dans la version actuelle du dépôt, les scripts de gestion sont placés à la racine : aucun dossier `admin/` ni fichier `khalid.sql` n’est présent à la racine. Le site déployé peut avoir une configuration différente.

## ⚙️ Prérequis

- PHP 7.4 ou plus récent
- MySQL ou MariaDB
- Apache, Nginx ou un serveur local tel que XAMPP
- Extension PHP PDO MySQL
- Extension GD si vous utilisez les fonctions de génération d’images

Composer n’est actuellement pas requis.

## 🚀 Installation locale

### 1. Cloner le projet

```bash
git clone https://github.com/mrawym636-sketch/Tajmil.git
cd Tajmil
```

### 2. Placer le projet dans le dossier web

Avec XAMPP :

```text
C:/xampp/htdocs/Tajmil
```

Avec un serveur Linux :

```text
/var/www/html/Tajmil
```

### 3. Créer la base de données

La configuration actuelle de `php_files/config.php` utilise `khalid` comme nom de base de données :

```sql
CREATE DATABASE khalid CHARACTER SET utf8 COLLATE utf8_general_ci;
```

Le dépôt ne fournit pas de fichier SQL complet à la racine. Les tables principales requises par l’application, notamment `utilisateurs` et `produits`, doivent être préparées à partir de votre schéma de base de données. Le script `pages/install.php` crée certaines tables auxiliaires et ajoute quelques colonnes ; il ne constitue pas à lui seul une installation complète de la base.

### 4. Configurer l’accès à MySQL

Modifiez `php_files/config.php` pour correspondre à votre environnement :

```php
$host = 'localhost';
$dbname = 'khalid';
$username = 'root';
$password = '';
```

N’utilisez pas les identifiants de développement sur un serveur public et ne publiez pas de vrais mots de passe dans le dépôt.

### 5. Démarrer le site

Démarrez Apache et MySQL, puis ouvrez :

```text
http://localhost/Tajmil/
```

## ☁️ Déploiement sur InfinityFree

Le projet peut être déployé sur un hébergement compatible PHP et MySQL/MariaDB. Les noms d’hôte, de base de données et d’utilisateur dépendent de votre compte : récupérez les valeurs exactes dans le panneau de contrôle de l’hébergeur.

1. Créez une base de données MySQL depuis le panneau d’hébergement.
2. Notez le nom d’hôte MySQL, le nom de la base et l’utilisateur fournis pour votre compte.
3. Modifiez `php_files/config.php` avec ces valeurs. Gardez le mot de passe privé.
4. Importez votre schéma SQL dans phpMyAdmin si vous en disposez.
5. Téléversez les fichiers en conservant la structure des dossiers.
6. Vérifiez que `assets/uploads/` dispose des permissions nécessaires.
7. Après l’installation, désactivez ou supprimez les scripts temporaires qui ne doivent pas rester accessibles publiquement.

Site en ligne : [https://amraoui2008.ifree.page/](https://amraoui2008.ifree.page/)

## 🗄️ Base de données

Selon les fonctionnalités utilisées, le code s’appuie notamment sur les tables suivantes :

| Table | Description |
|---|---|
| `utilisateurs` | Comptes utilisateurs |
| `produits` | Catalogue de produits |
| `categories` | Catégories de produits |
| `brands` | Marques |
| `commandes` | Commandes clients |
| `commande_logs` | Historique des changements de statut |
| `avis` | Avis et notes |
| `coupons` | Codes promotionnels |
| `logs` | Journal d’activité |
| `messages` | Messages de contact |
| `newsletter_subscribers` | Abonnés à la newsletter |
| `pages` | Contenu des pages |
| `blacklist` | Numéros bloqués |
| `visits` | Statistiques de visites |
| `chat_logs` | Historique du chatbot |

La structure exacte dépend du schéma de base de données utilisé. Faites une sauvegarde avant toute migration ou modification.

## 🎮 Utilisation

- **Boutique locale :** `http://localhost/Tajmil/`
- **Boutique en ligne :** [https://amraoui2008.ifree.page/](https://amraoui2008.ifree.page/)
- **Connexion admin en ligne :** [https://amraoui2008.ifree.page/admin/login.php](https://amraoui2008.ifree.page/admin/login.php)

Les outils de gestion présents dans le dépôt incluent notamment la gestion des produits, catégories, marques, stocks, commandes, utilisateurs, avis, coupons et statistiques. Leurs scripts se trouvent à la racine du dépôt dans la version actuelle.

## 🔐 Sécurité

- Utilisez des mots de passe forts et uniques pour les comptes et la base de données.
- Changez les identifiants par défaut ou de démonstration avant le déploiement.
- Ne commitez ni secrets, ni sauvegardes contenant des données personnelles.
- Limitez l’accès aux scripts d’administration, d’installation, de diagnostic et de sauvegarde.
- Vérifiez les contrôles d’accès et la configuration du serveur avant toute mise en production.

## 🤝 Contribution

1. Forkez le dépôt.
2. Créez une branche : `git checkout -b feature/ma-fonctionnalite`.
3. Commitez vos changements.
4. Poussez votre branche et ouvrez une Pull Request.

## 📄 Licence

Aucun fichier de licence n’a été identifié dans le dépôt. Contactez le propriétaire du projet pour connaître les conditions de réutilisation.

## 📬 Contact

Pour toute question, ouvrez une issue dans ce dépôt ou contactez le mainteneur via GitHub.

⭐ N’oubliez pas de laisser une étoile si ce projet vous plaît ! ⭐
