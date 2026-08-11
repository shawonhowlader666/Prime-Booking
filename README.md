# ✈️ Prime Aviation — Enterprise Travel & Air Mobility SaaS Platform

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Redis](https://img.shields.io/badge/Redis-Cache-DC382D?style=for-the-badge&logo=redis&logoColor=white)](https://redis.io)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docker.com)
[![License](https://img.shields.io/badge/License-MIT-blue.style=for-the-badge)](#)

**Prime Aviation** is an enterprise-grade Travel & Air Mobility SaaS platform engineered with Agoda-level feature parity, multi-tenant vendor property management, live domestic flight booking, multi-currency price conversion, and real-time AJAX search autocomplete.

---

## 🌟 Key Platform Features

### 🏨 Agoda-Grade Hotel & Resort Booking System
- **10-Point Agoda Rating Scale**: Standardized review score calculations (9.4 Excellent, 8.8 Very Good).
- **Dynamic Popular Area Pills**: Automatic area recommendations per searched city (Cox's Bazar, Kuakata, Sylhet, Dhaka).
- **Agoda Photo Collage Grid**: Interactive 5-grid hero photo galleries with lightbox modal previews.
- **Filter Badge Counters**: Live count calculation matching search criteria across stars, prices, and amenities.

### ✈️ Domestic Flight Ticket Booking Engine
- **Routes & Airlines**: Instant booking for US-Bangla, NOVOAIR, Air Astra, and Biman Bangladesh across all domestic airports (DAC, CXB, ZYL, CGP, SPD, JSR).
- **e-Ticket Boarding Pass**: Instant PNR generation (`PNR-BD8801`), seat assignment, and printable PDF flight vouchers.

### 💱 Multi-Currency Converter Engine
- **Active Currencies**: **BDT (৳)**, **USD ($)**, **EUR (€)**, and **GBP (£)**.
- **Site-Wide Conversion**: Real-time price conversion across property search cards, room lists, checkout totals, and PDF invoices.

### 🔍 Live AJAX Search Autocomplete
- **Pop-over Suggestions**: Real-time destination and hotel property suggestions popover while typing in any search box.
- **Endpoint**: `/api/search/autocomplete?q={keyword}`.

### 🏢 Multi-Tenant SaaS Vendor & Admin Panels
- **Vendor Portal**: Property listing management, room inventory, price rules, promotion campaigns, and payout requests.
- **Admin Control Center**: Commission rates, vendor approvals, tenant domain management, master reservation ledger, CSV export, and PDF printable financial reports.

---

## 🛠️ Infrastructure & Tech Stack

| Layer | Technology |
| :--- | :--- |
| **Framework** | Laravel 11.x (PHP 8.2+) |
| **Database** | MySQL 8.0 (Port 3307 / XAMPP Compatible) |
| **Cache & Queue** | Redis & Supervisord Workers |
| **Search Engine** | Elasticsearch 7.17 / Mysql Fulltext |
| **Containerization** | Docker, Docker Compose, Nginx, PHP-FPM |
| **CI/CD Pipelines** | GitHub Actions (`ci.yml`, `deploy-production.yml`, `code-quality.yml`) |

---

## 🚀 Quick Setup Guide

### Local Development (XAMPP MySQL Port 3307)

```bash
# 1. Clone repository
git clone https://github.com/prime-aviation/prime-aviation.git
cd prime-aviation

# 2. Install Composer Dependencies
composer install

# 3. Environment Setup
cp .env.example .env
php artisan key:generate

# 4. Run Migrations & Seeder (MySQL Port 3307)
php artisan migrate:fresh --seed

# 5. Link Public Storage
php artisan storage:link

# 6. Start Development Server
php artisan serve
```

Access the application locally at `http://127.0.0.1:8000`.

---

### Docker Multi-Container Orchestration

```bash
# Launch full multi-container stack (App, Web, MySQL 3307, Redis, Elasticsearch, Queue Worker)
docker compose up -d

# Perform System Health Check
php artisan prime:health-check
```

---

## 🔑 Default Admin & Vendor Credentials

- **Admin Portal**: `http://127.0.0.1:8000/admin/login`
  - Email: `admin@primeavn.com`
  - Password: `password`

- **Vendor Portal**: `http://127.0.0.1:8000/vendor/login`
  - Email: `vendor@primeavn.com`
  - Password: `password`

---

## 🧪 Automated Testing

```bash
# Run PHPUnit Automated Test Suite
php artisan test

# Run Platform Health Audit Command
php artisan prime:health-check
```

---

## 📄 License
The Prime Aviation SaaS Platform is open-sourced software licensed under the [MIT License](LICENSE).
