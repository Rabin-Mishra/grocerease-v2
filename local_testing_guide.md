# GrocerEase v2 — Local Testing & Execution Guide

This document lists the exact chronological commands and URLs required to get **GrocerEase v2** running on your local machine, seed test data from the legacy SQL dump, and run the automated test suite.

---

## 🛠️ Chronological Execution Commands

Follow these steps in exact order to set up your environment:

### Step 1: Initialize Environment Config
Create your local environment file from the template:
```bash
cp .env.example .env
```

### Step 2: Install Project Dependencies
Run composer to install framework requirements, Intervention Image processing, and S3-compatible cloud storage SDKs:
```bash
composer install
```

### Step 3: Generate Encryption Key
```bash
php artisan key:generate
```

### Step 4: Run Fresh Migrations & Seed Database
This command drops all existing tables and re-seeds the application (Categories, Brands, Administrative accounts, Test users, and Products):
```bash
php artisan migrate:fresh --seed
```

### Step 5: Migrate Legacy Product Images
Import the 48 legacy product images from your previous website area to the current storage engine:
```bash
php artisan grocerease:migrate-images
```

### Step 6: Link Public Storage
Expose the uploaded/migrated product images to the web-server by symlinking public paths:
```bash
php artisan storage:link
```

### Step 7: Launch Local Web Server
Start Laravel's built-in web server:
```bash
php artisan serve
```
*The portal will be hosted at [http://127.0.0.1:8000](http://127.0.0.1:8000).*

### Step 8: Execute Automated Test Suite (Optional)
Ensure the full cart, payments, checkout, and session merging features are passing:
```bash
./vendor/bin/phpunit
```

---

## 🔑 Seeding Commands & Credentials

If you need to manually wipe or seed specific aspects of your application database, use the following Artisan commands:

### Full Re-Seed
```bash
php artisan db:seed
```

### Manually Seed Legacy Products Catalog
```bash
php artisan db:seed --class=ProductSeeder
```

### 🔐 Out-of-the-Box Test Credentials

| Role | Username / Email | Password | Access Rights |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@grocerease.com` | `Admin@1234` | All Admin features & Dashboard views |
| **Customer (Test)** | `test@grocerease.com` | `Test@1234` | General Storefront, Cart, Checkout |

---

## 📂 Logs & Error Resolution

If you encounter any server errors (such as `500 Internal Server Error` or migration failures) during testing, full trace logs are stored at:
📁 **`storage/logs/`**

- **Main Log File**: [laravel.log](file:///home/rabin/Documents/GrocerEase/grocerease-v2/storage/logs/laravel.log)
- **Tailing Logs Live**:
  ```bash
  tail -f storage/logs/laravel.log
  ```

---

## 🔗 Route Map & Direct URLs

### 👤 Customer Experience (Public)
- **Homepage Catalog**: [http://localhost:8000/](http://localhost:8000/)
- **Shopping Cart**: [http://localhost:8000/cart](http://localhost:8000/cart)
- **Checkout Flow**: [http://localhost:8000/checkout](http://localhost:8000/checkout)
- **Sign In**: [http://localhost:8000/login](http://localhost:8000/login)

### 👑 Business Control Panel (Admin)
- **Admin Dashboard**: [http://localhost:8000/admin](http://localhost:8000/admin)
- **Product Catalog Management**: [http://localhost:8000/admin/products](http://localhost:8000/admin/products)
- **Order Tracking Panel**: [http://localhost:8000/admin/orders](http://localhost:8000/admin/orders)
