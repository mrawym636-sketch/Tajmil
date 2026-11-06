# 🌸 Tajmil Lwajh — Boutique beauté en ligne

Tajmil Lwajh est un site e-commerce développé en PHP et MySQL pour présenter et vendre des produits de beauté. Les visiteurs peuvent parcourir le catalogue, créer un compte, gérer un panier et une liste de souhaits, passer commande et consulter les avis. Le projet comprend également des outils de gestion et un chatbot d’assistance.

## 🚀 Démo

- **Boutique :** [amraoui2008.ifree.page](https://amraoui2008.ifree.page/)
- **Accès d’administration :** [amraoui2008.ifree.page/admin/login.php](https://amraoui2008.ifree.page/admin/login.php)

Utilisez uniquement des identifiants qui vous ont été fournis de façon sécurisée. Ne publiez pas de mots de passe dans le README. Si les identifiants de démonstration mentionnés précédemment sont actifs, changez-les immédiatement.

## ✨ Fonctionnalités

### Boutique

- Catalogue, recherche et navigation par catégories et marques
- Pages de produits et choix de quantité/format
- Panier et liste de souhaits
- Inscription, connexion et profil utilisateur
- Passage de commande et historique des commandes
- Avis et notes des clients
- Formulaire de contact et newsletter
- Chatbot d’assistance en français et en darija
- Interface responsive

### Gestion

Le dépôt contient des outils de gestion pour les produits, catégories, marques, stocks, commandes, utilisateurs, avis, coupons, statistiques, exports et sauvegardes. Plusieurs de ces scripts se trouvent à la racine du dépôt, et non dans un dossier `admin/`.

## 🧰 Technologies

- PHP avec PDO
- MySQL ou MariaDB
- HTML, CSS et JavaScript
- Font Awesome et Google Fonts

## 📁 Structure du projet

```text
Tajmil/
├── assets/
│   ├── css/
│   ├── images/
│   ├── js/
│   └── uploads/
├── pages/                 # Pages de la boutique
├── php_files/             # Configuration et traitements PHP
├── index.php              # Point d’entrée du site
├── header.php
├── footer.php
├── analytics.php           # Outils de gestion et statistiques
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
└── export_*.php            # Outils d’export
```

Les pages client disponibles incluent notamment `home.php`, `products.php`, `product-details.php`, `cart.php`, `checkout.php`, `orders.php`, `wishlist.php`, `profile.php`, `login.php`, `register.php`, `about.php`, `review.php` et `contact.php` sous `pages/`.

## ⚙️ Installation locale

### Prérequis

- PHP 7.4 ou plus récent
- MySQL ou MariaDB
- Apache, Nginx ou un environnement local tel que XAMPP
- L’extension PHP PDO MySQL

L’extension GD peut être nécessaire pour certains outils de génération d’images.

### Étapes

1. Clonez le dépôt et placez-le dans le répertoire servi par votre serveur web :

   ```bash
   git clone https://github.com/mrawym636-sketch/Tajmil.git
   cd Tajmil
   ```

2. Créez une base de données MySQL pour l’application. La configuration actuelle de `php_files/config.php` utilise `khalid` comme nom de base de données.

3. Configurez les paramètres de connexion dans `php_files/config.php` selon votre environnement :

   ```php
   $host = 'localhost';
   $dbname = 'khalid';
   $username = 'root';
   $password = '';
   ```

   Remplacez ces valeurs par vos propres identifiants. Pour un hébergement public, n’utilisez pas les identifiants de développement et ne rendez pas le fichier de configuration accessible au public.

4. Configurez les tables nécessaires à partir du schéma SQL de votre installation, s’il est disponible. Aucun fichier `khalid.sql` n’est présent à la racine du dépôt ; ne supposez donc pas qu’une base de données vide sera entièrement initialisée automatiquement.

5. Démarrez Apache et MySQL, puis ouvrez le site, par exemple :

   ```text
   http://localhost/Tajmil/
   ```

## 🔐 Sécurité et déploiement

- Utilisez des mots de passe uniques et forts pour les comptes administrateur et base de données.
- Changez ou désactivez tout compte de démonstration avant le déploiement.
- Ne commitez pas de secrets, de sauvegardes de base de données ou de données personnelles.
- Vérifiez les permissions d’écriture nécessaires pour `assets/uploads/`.
- Les scripts d’installation, de diagnostic, de sauvegarde et de création d’administrateur ne devraient pas rester accessibles publiquement après leur utilisation.
- Testez les parcours de connexion, commande et administration dans un environnement de développement avant toute mise en production.

## 🤝 Contribution

1. Forkez le dépôt.
2. Créez une branche pour vos changements : `git checkout -b feature/ma-fonctionnalite`.
3. Commitez vos changements.
4. Poussez la branche et ouvrez une Pull Request.

## 📄 Licence

Aucun fichier de licence n’a été identifié dans le dépôt. En l’absence de licence, les droits d’utilisation et de redistribution ne sont pas accordés automatiquement. Contactez le propriétaire du projet pour toute réutilisation.

## 📬 Contact

Pour les questions relatives au projet, ouvrez une issue dans ce dépôt ou contactez son mainteneur via GitHub.
