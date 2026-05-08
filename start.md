# HUAIN Thailand - Quick Start

## 1) Create database

Create database name:

```
huain
```

Then import:

- `huain_db.txt`

This file contains schema and seed data (admin, banners, products, news, contact info).

## 2) Configure database connection

Edit:

- `includes/config.php`

Default values:

- DB_HOST: localhost
- DB_NAME: huain
- DB_USER: root
- DB_PASS: (empty)

You can also set environment variables:

- HUAIN_DB_HOST
- HUAIN_DB_NAME
- HUAIN_DB_USER
- HUAIN_DB_PASS

## 3) Run project

Serve folder root with PHP/Apache (XAMPP, Laragon, or production web server).

Enable Apache `mod_rewrite` and allow `.htaccess` for this project (already included).

Public pages:

- `/`
- `/products`
- `/products/{slug}`
- `/news`
- `/news/{slug}`
- `/contact`

Admin panel:

- `/admin/login.php`
- `/admin`

Default admin login:

- Username: admin
- Password: Admin@123

## 4) Upload files

Admin can upload:

- Banner images
- Product images
- Product PDF TH/EN
- News images

All files are stored in:

- `uploads/`

## 5) Language

Website supports:

- EN (default)
- TH

Switch from top navigation language buttons.

## Notes

- The sample product HDMI Matrix uses file name `HDMIMatrixcatalog.pdf`.
- You can upload the real PDF from admin product form.
