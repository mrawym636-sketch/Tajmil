# Tajmil

Tajmil is a PHP/MySQL-based fragrance e-commerce website focused on premium perfumes and beauty products. The project includes a storefront, cart and wishlist flows, user authentication, checkout, reviews, chatbot support, and an admin-oriented product management interface.

## Features

- Product catalog with categories, brands, and pricing
- Product detail pages with quantity and fragrance size selection
- Shopping cart and wishlist management
- User registration and login
- Order checkout and order history
- Product reviews and rating system
- Contact form and newsletter signup
- Floating chatbot assistant for customer support
- Responsive design for desktop and mobile devices
- Admin or management pages for inventory and order monitoring

## Tech Stack

- PHP
- MySQL
- HTML5 / CSS3 / JavaScript
- AJAX for dynamic cart and review updates
- Font Awesome icons
- Responsive custom CSS

## Project Structure

```text
Tajmil/
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/
├── pages/
│   ├── home.php
│   ├── cart.php
│   ├── checkout.php
│   ├── login.php
│   ├── register.php
│   ├── orders.php
│   └── ...
├── php_files/
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
│   ├── chatbot_widget.php
│   └── contact.php
├── index.php
├── header.php
├── footer.php
├── auth.php
├── analytics.php
├── orders.php
├── categories.php
├── brands.php
├── coupons.php
├── newsletter.php
├── inventory.php
├── export_products.php
├── export_orders.php
├── export_reviews.php
├── export_users.php
├── README.md
└── ...
```

## Requirements

- PHP 7.4+ or newer
- MySQL / MariaDB
- Apache or Nginx web server
- Composer is not required for this project

## Local Setup

1. Clone the repository:

```bash
git clone https://github.com/mrawym636-sketch/Tajmil.git
```

2. Move the project into your local web server folder, for example:

```text
C:/xampp/htdocs/Tajmil
```

or

```text
/var/www/html/Tajmil
```

3. Create or configure your MySQL database.

4. Edit the database credentials in `php_files/config.php`:

```php
$host = 'localhost';
$dbname = 'khalid';
$username = 'root';
$password = '';
```

5. Start Apache and MySQL.

6. Open the application in a browser:

```text
http://localhost/Tajmil/
```

## Database Notes

The project uses a MySQL database named `khalid` by default. Some scripts create tables automatically if they do not exist, including tables for products, orders, reviews, and messages.

## Main Entry Points

- `index.php` - landing/entry page
- `pages/home.php` - storefront home page
- `pages/cart.php` - cart details
- `pages/checkout.php` - checkout form
- `pages/login.php` - login page
- `pages/register.php` - registration page
- `pages/orders.php` - order list

## Notes

- This repository contains both customer-facing storefront logic and admin/management utilities.
- Some scripts rely on a specific folder structure and relative paths; keep the project root intact when deploying.
- The design is highly customized and includes a premium, luxury fragrance aesthetic.

## License

This project does not currently include a license file. Please check with the repository owner before commercial reuse or redistribution.

## Contact

For questions about the project, please use the repository issue tracker or contact the project maintainer through the GitHub repository.
