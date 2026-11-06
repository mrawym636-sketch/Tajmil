# 🌸 Tajmil Lwajh

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-orange)](https://www.mysql.com/)
[![Responsive](https://img.shields.io/badge/Design-Responsive-purple)](#-fonctionnalités)

**Tajmil Lwajh** est une plateforme e-commerce dédiée aux produits de beauté et aux parfums. Le projet comprend une boutique, un panier, une liste de souhaits, l’authentification des utilisateurs, les commandes, les avis clients, un chatbot et des outils de gestion.

---

## 🚀 Démo

- **Site client :** [https://amraoui2008.ifree.page/](https://amraoui2008.ifree.page/)

> L’URL d’administration et les identifiants de démonstration ne sont pas documentés ici : vérifiez les chemins et comptes actuellement configurés avant de les partager. Ne publiez jamais de mot de passe dans le dépôt. Si un mot de passe de démonstration faible ou déjà partagé est encore actif, changez-le immédiatement.

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
- 🤖 Chatbot d’assistance en français et en darija
- 📱 Mise en page responsive
- 🛠️ Scripts de gestion des produits, stocks, commandes, utilisateurs et avis

---

## 🛠️ Technologies

- **Backend :** PHP avec PDO
- **Base de données :** MySQL ou MariaDB
- **Frontend :** HTML, CSS et JavaScript
- **Interface :** CSS personnalisé responsive
- **Icônes et polices :** Font Awesome et Google Fonts

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
│   └── install.php         # Création de tables auxiliaires / colonnes
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

> Les scripts de gestion sont présents à la racine du dépôt. Le dépôt ne contient pas de dossier `admin/` dans la structure actuelle.

---

## ⚙️ Prérequis

- PHP 7.4 ou plus récent
- MySQL ou MariaDB
- Apache, Nginx ou un serveur PHP local (par exemple XAMPP)
- Extension PHP PDO MySQL
- Extension GD si vous utilisez les fonctions de génération d’images

Composer n’est actuellement pas requis.

---

## 🚀 Installation locale

### 1. Cloner le projet

```bash
git clone https://github.com/mrawym636-sketch/Tajmil.git
cd Tajmil
```

### 2. Créer la base de données

La configuration de `php_files/config.php` utilise actuellement `khalid` comme nom de base de données. Créez une base correspondant à votre configuration, par exemple :

```sql
CREATE DATABASE khalid CHARACTER SET utf8 COLLATE utf8_general_ci;
```

### 3. Configurer l’accès à MySQL

Modifiez les paramètres dans `php_files/config.php` pour correspondre à votre environnement :

```php
$host = 'localhost';
$dbname = 'khalid';
$username = 'root';
$password = '';
```

N’utilisez pas ces identifiants de développement sur un serveur public. Ne publiez pas de vrais mots de passe dans GitHub.

### 4. Préparer les tables

Assurez-vous que les tables principales attendues par l’application, notamment `utilisateurs` et `produits`, existent avant d’utiliser les pages qui en dépendent. `pages/install.php` crée certaines tables auxiliaires et ajoute quelques colonnes ; ce script ne constitue pas à lui seul une installation complète de la base.

Aucun fichier `khalid.sql` n’est présent à la racine du dépôt. Si vous disposez du schéma de base de données séparément, importez-le dans la base avant de lancer l’application.

### 5. Démarrer le site

Démarrez Apache et MySQL, puis ouvrez le projet dans votre navigateur. Avec XAMPP, l’adresse est généralement :

```text
http://localhost/Tajmil/
```

---

## ☁️ Déploiement sur un hébergeur

Le projet peut être déployé sur un hébergement compatible PHP et MySQL/MariaDB, sous réserve que l’hébergeur prenne en charge les versions et extensions requises.

1. Téléversez les fichiers du projet en conservant la structure des dossiers.
2. Créez une base de données et un utilisateur dans le panneau de l’hébergeur.
3. Configurez l’hôte, le nom de base, l’utilisateur et le mot de passe dans `php_files/config.php`.
4. Importez le schéma de base de données fourni avec votre installation, s’il est disponible.
5. Vérifiez les permissions nécessaires pour `assets/uploads/`.
6. Après toute opération d’installation, désactivez ou supprimez les scripts temporaires accessibles publiquement.

Ne publiez pas d’identifiants de base de données, mots de passe administrateur ou autres secrets dans ce README ou dans le dépôt.

---

## 🗄️ Base de données

Le code utilise notamment les tables suivantes, selon les fonctionnalités activées :

| Table | Description |
|---|---|
| `utilisateurs` | Comptes utilisateurs |
| `produits` | Catalogue de produits |
| `commandes` | Commandes clients |
| `avis` | Avis et notes |
| `messages` | Messages de contact |
| `newsletter_subscribers` | Abonnements à la newsletter |
| `commande_logs` | Historique des changements de statut |
| `chat_logs` | Historique du chatbot |
| `visits` | Statistiques de visites |
| `pages` | Contenu de pages |
| `logs` | Journal d’activité |
| `blacklist` | Numéros bloqués |

La structure exacte dépend de la version de la base de données utilisée. Faites une sauvegarde avant toute migration ou modification de schéma.

---

## 🔐 Sécurité

- Utilisez des mots de passe forts et uniques pour les comptes et la base de données.
- Changez les identifiants par défaut ou de démonstration avant le déploiement.
- Ne commitez ni secrets, ni sauvegardes contenant des données personnelles.
- Limitez l’accès aux scripts d’administration, d’installation, de diagnostic et de sauvegarde.
- Vérifiez les contrôles d’accès et la configuration du serveur avant toute mise en production.

---

## 🤝 Contribution

1. Forkez le dépôt.
2. Créez une branche : `git checkout -b feature/ma-fonctionnalite`.
3. Commitez vos changements.
4. Poussez votre branche et ouvrez une Pull Request.

## 📄 Licence

Aucun fichier de licence n’a été identifié dans le dépôt. Contactez le propriétaire du projet pour connaître les conditions de réutilisation.

## 📬 Contact

Pour toute question, ouvrez une issue dans ce dépôt ou contactez le mainteneur via GitHub.
