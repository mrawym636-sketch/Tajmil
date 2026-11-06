# 🌸 Tajmil Lwajh

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-orange)](https://www.mysql.com/)
[![Responsive](https://img.shields.io/badge/Design-Responsive-purple)](#-fonctionnalités)
[![InfinityFree](https://img.shields.io/badge/Host-InfinityFree-purple)](https://infinityfree.com)

**Tajmil Lwajh** est une plateforme e-commerce dédiée aux produits de beauté et aux parfums.
Le projet comprend une boutique, un panier, une liste de souhaits, l'authentification des utilisateurs, les commandes, les avis clients, un chatbot et des outils de gestion.

---

## 🚀 Démo

- **Site client :** [https://amraoui2008.ifree.page/](https://amraoui2008.ifree.page/)
- **Espace admin :** [https://amraoui2008.ifree.page/admin/login.php](https://amraoui2008.ifree.page/admin/login.php)
  - **Email :** `admin@parfumerie.ma`
  - **Mot de passe :** `admin123`

> ⚠️ **Sécurité :** Ces identifiants sont fournis pour la démonstration. Changez-les avant tout déploiement en production.

---

## ✨ Fonctionnalités

- 📦 Catalogue de produits avec catégories et marques
- 🔍 Recherche et pages de détails produit
- 🛒 Panier avec ajout, modification des quantités et suppression
- ❤️ Liste de souhaits
- 👤 Inscription, connexion et profil utilisateur
- 📦 Passage de commande et historique des commandes
- ⭐ Avis et système de notation
- 📞 Formulaire de contact et newsletter
- 🤖 Chatbot d'assistance en français et en darija
- 📱 Mise en page responsive
- 🛠️ Tableau de bord admin complet (produits, stocks, commandes, utilisateurs, avis, paramètres, analyses)

---

## 🛠️ Technologies

- **Backend :** PHP avec PDO
- **Base de données :** MySQL ou MariaDB
- **Frontend :** HTML, CSS et JavaScript
- **Interface :** CSS personnalisé responsive
- **Icônes et polices :** Font Awesome et Google Fonts
- **Graphiques :** Chart.js
- **Tableaux :** DataTables

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
│   ├── orders.php
│   ├── wishlist.php
│   ├── profile.php
│   ├── login.php
│   ├── register.php
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
├── admin/                  # Espace administrateur
│   ├── index.php
│   ├── login.php
│   ├── auth.php
│   ├── config.php
│   ├── header.php
│   ├── footer.php
│   ├── products.php
│   ├── product-add.php
│   ├── product-edit.php
│   ├── categories.php
│   ├── brands.php
│   ├── orders.php
│   ├── order-details.php
│   ├── users.php
│   ├── crud_users.php
│   ├── reviews.php
│   ├── coupons.php
│   ├── newsletter.php
│   ├── analytics.php
│   ├── inventory.php
│   ├── logs.php
│   ├── pages.php
│   ├── settings.php
│   ├── blacklist.php
│   ├── export_orders.php
│   ├── export_products.php
│   ├── export_users.php
│   ├── export_reviews.php
│   ├── backup.php
│   ├── logout.php
│   └── create_admin.php
├── index.php
├── header.php
├── footer.php
├── khalid.sql              # Script de création de la base de données
├── README.md
└── ...
```

---

## ⚙️ Prérequis

- PHP 7.4 ou plus récent
- MySQL ou MariaDB
- Apache, Nginx ou un serveur PHP local (par exemple XAMPP)
- Extension PHP PDO MySQL
- Extension GD si vous utilisez les fonctions de génération d'images

Composer n'est actuellement pas requis.

---

## 🚀 Installation locale

### 1. Cloner le projet

```bash
git clone https://github.com/mrawym636-sketch/Tajmil.git
cd Tajmil
```

### 2. Placer dans le dossier web

Avec XAMPP :
```text
C:/xampp/htdocs/Tajmil
```

Avec un serveur Linux :
```text
/var/www/html/Tajmil
```

### 3. Créer la base de données

```sql
CREATE DATABASE khalid CHARACTER SET utf8 COLLATE utf8_general_ci;
```

Puis importer le fichier `khalid.sql` via phpMyAdmin ou en ligne de commande :

```bash
mysql -u root -p khalid < khalid.sql
```

### 4. Configurer l'accès à MySQL

Modifier `php_files/config.php` :

```php
$host = 'localhost';
$dbname = 'khalid';
$username = 'root';
$password = '';
```

### 5. Démarrer le site

Démarrer Apache et MySQL, puis ouvrir :

```text
http://localhost/Tajmil/
```

---

## ☁️ Déploiement sur InfinityFree

### 1. Créer la base de données

- Aller dans **cPanel → MySQL Databases**.
- Créer une base (ex. `if0_43357943_khalid`).
- Créer un utilisateur (ex. `if0_43357943`) et un mot de passe.
- Ajouter l'utilisateur à la base avec tous les privilèges.

### 2. Configurer `php_files/config.php`

```php
$host = 'sql200.infinityfree.com';       // ou sqlXXX.infinityfree.com
$dbname = 'if0_43357943_khalid';
$username = 'if0_43357943';
$password = 'VOTRE_MOT_DE_PASSE';
```

### 3. Importer la base de données

- Aller dans **phpMyAdmin** d'InfinityFree.
- Sélectionner la base `if0_43357943_khalid`.
- Importer `khalid.sql`.

### 4. Téléverser les fichiers

- Uploader le contenu du projet dans `htdocs/`.
- Vérifier que `assets/uploads/` est accessible en écriture (755).

### 5. Accéder au site

```text
https://amraoui2008.ifree.page/
```

---

## 🗄️ Base de données

Le code utilise notamment les tables suivantes :

| Table | Description |
|---|---|
| `utilisateurs` | Comptes utilisateurs |
| `produits` | Catalogue de produits |
| `categories` | Catégories de produits |
| `brands` | Marques |
| `commandes` | Commandes clients |
| `avis` | Avis et notes |
| `messages` | Messages de contact |
| `newsletter_subscribers` | Abonnements à la newsletter |
| `commande_logs` | Historique des changements de statut |
| `chat_logs` | Historique du chatbot |
| `visits` | Statistiques de visites |
| `pages` | Contenu de pages |
| `logs` | Journal d'activité |
| `blacklist` | Numéros bloqués |
| `coupons` | Codes promo |

### Script de création

Voir `khalid.sql` à la racine du dépôt.

---

## 🔑 Compte administrateur

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| `admin@parfumerie.ma` | `admin123` | Admin |

### Créer un admin en SQL

```sql
INSERT INTO utilisateurs (nom, email, password, role, photo) 
VALUES (
    'Administrateur',
    'admin@parfumerie.ma',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    'default.png'
);
```

---

## 🎮 Utilisation

### Client

```text
http://localhost/Tajmil/          (local)
https://amraoui2008.ifree.page/   (production)
```

### Admin

```text
http://localhost/Tajmil/admin/login.php
https://amraoui2008.ifree.page/admin/login.php
```

| Page | URL |
|------|-----|
| Dashboard | `admin/index.php` |
| Produits | `admin/products.php` |
| Commandes | `admin/orders.php` |
| Utilisateurs | `admin/users.php` |
| Avis | `admin/reviews.php` |
| Paramètres | `admin/settings.php` |
| Analyses | `admin/analytics.php` |

---

## 🔐 Sécurité

- Utilisez des mots de passe forts et uniques pour les comptes et la base de données.
- Changez les identifiants par défaut ou de démonstration avant le déploiement.
- Ne commitez ni secrets, ni sauvegardes contenant des données personnelles.
- Limitez l'accès aux scripts d'administration, d'installation, de diagnostic et de sauvegarde.
- Vérifiez les contrôles d'accès et la configuration du serveur avant toute mise en production.

---

## 🤝 Contribution

1. Forkez le dépôt.
2. Créez une branche : `git checkout -b feature/ma-fonctionnalite`.
3. Commitez vos changements.
4. Poussez votre branche et ouvrez une Pull Request.

---

## 📄 Licence

Aucun fichier de licence n'a été identifié dans le dépôt.  
Contactez le propriétaire du projet pour connaître les conditions de réutilisation.

---

## 📬 Contact

- 📧 Email : mrawym636@gmail.com
- 📱 WhatsApp : +212 723-699031
- 📍 Casablanca, Maroc
- 🐙 GitHub : [@mrawym636-sketch](https://github.com/mrawym636-sketch)

Pour toute question, ouvrez une issue dans ce dépôt ou contactez le mainteneur via GitHub.

---

⭐ **N'oubliez pas de laisser une étoile si ce projet vous plaît !** ⭐
