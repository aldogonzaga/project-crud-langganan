# 💳 SubTrack &bull; Subscription & Free Trial Tracker

A modern, responsive full-stack **Subscription & Free Trial Tracker** CRUD web application built with **Laravel 12** and **Bootstrap 5.3**. 

SubTrack solves a real-world financial problem: keeping track of recurring subscriptions (Netflix, Spotify, cloud storage, gym memberships, dev tools) and managing free trials before they auto-renew and charge your card. Designed with a clean, modern fintech palette (**Deep Indigo, Slate Navy, Emerald, and Amber** — zero pink theme), dark/light mode toggle, and rich interactive data visualizations using **Chart.js**.

---

## 🌟 Key Features

### 1. 🔐 Authentication & User Functions
- **Registration & Sign In**: Full auth pipeline with input validation, bcrypt password hashing, and remember-me session management.
- **1-Click Demo Login**: Pre-seeded demo account (`demo@example.com` / `password123`) loaded with 13 realistic services.
- **Multi-Currency Support**: Switch between `$`, `Rp`, `€`, `£`, and `¥` in user settings; all totals, monthly calculations, and charts adapt automatically.
- **Budget Goal Tracking**: Set a monthly subscription budget ceiling and monitor budget usage in real time.
- **Strict Data Privacy**: Full user isolation — each user only accesses their own subscriptions.

### 2. 📊 Dashboard & Financial Intelligence
- **Total Monthly Spend**: Calculates normalized monthly spend across mixed billing cycles (Weekly `× 52 ÷ 12`, Monthly, and Yearly `÷ 12`).
- **Projected Annual Spend**: Automatic 12-month expenditure projection.
- **Urgent Payment Alert Banner**: Triggered dynamically when any subscription is due within **≤ 3 days** or overdue.
- **Specialized View: "Payments Due in Next 7 Days"**: Chronological timeline of upcoming renewal deadlines.
- **Free Trials Watchdog Widget**: Countdown tracker with elapsed visual progress bars.
- **Budget Threshold Meter**: Visual indicator showing percent of monthly budget used (`✓ On track`, `⚡ Warning`, or `⚠ Over budget`).

### 3. 🔁 Full CRUD & Life-Cycle Management
- **Create**: Add new subscriptions with service name, price, cycle, next billing date, category, status, icon/emoji, and notes. Includes live monthly/annual cost preview calculator and 1-click popular presets (Spotify, Netflix, Copilot, Gym, etc.).
- **Read**:
  - **Card Grid View**: Modern squircle cards with icons, price, renewal schedule, and quick action dropdowns.
  - **Table View**: Comprehensive data table with status pills, sorting, and filter indicators.
- **Update**: Modify rates when services announce price hikes, change billing cycles, or convert status.
- **1-Click Renewal ("Mark as Paid")**: Advances the billing date by +1 cycle (Weekly `+1 week`, Monthly `+1 month`, Yearly `+1 year`) with a single click.
- **Delete & Soft Deletes**:
  - **Status "Cancelled"**: Stops adding to monthly expenses while preserving full history for reference.
  - **Soft Delete (Trash)**: Move records to Trash with full **Restore** and **Permanent Delete** capabilities.

### 4. 🚀 Level-Up Portfolio Capabilities
- **Filtering & Sorting**:
  - Filter by **Category** (*Entertainment, Utilities, Health, Work, Education, Food, Shopping, Others*).
  - Filter by **Status** (*All, Active, Free Trial, Cancelled*).
  - Filter by **Billing Cycle** (*Monthly, Yearly, Weekly*).
  - Search by service name and notes.
  - Sort by: *Renewal Date (Soonest)*, *Highest Cost*, *Lowest Cost*, *Name (A-Z)*, *Recently Added*.
- **Interactive Visualizations (Chart.js)**:
  - **Spending by Category**: Interactive doughnut chart with legend, amounts, and budget percentages.
  - **Billing Cycle Distribution**: Frequency bar chart comparing Monthly vs Yearly vs Weekly commitments.
  - **Top 5 Expenses Ranking**: Identifies the biggest drivers of monthly subscription spending.
  - **Cancellation Savings Impact**: Calculates estimated monthly and annual savings from cancelled services.
- **Date Logic & Urgency Highlights**:
  - **🔴 Red / Danger (`card-critical`)**: Subscriptions due within **≤ 3 days** or overdue.
  - **🟡 Yellow / Warning (`card-warning`)**: Subscriptions due within **4 to 7 days**.
  - **Normal**: Renewals due in > 7 days.
- **Data Export**: 1-click **Export to CSV** for spreadsheets (Excel / Google Sheets).
- **Light & Dark Mode**: Persistent theme switcher stored in `localStorage`.
- **Keyboard Shortcuts**: Press `N` for Quick Add, `/` to focus search, and `D` to toggle dark mode.
- **Responsive Design**: Desktop sticky sidebar + mobile bottom navigation bar (`.mobile-bottom-nav`).

---

## 🛠️ Tech Stack & Requirements

- **PHP**: `^8.2` or higher (tested on PHP 8.2 - 8.5)
- **Framework**: [Laravel 12.x](https://laravel.com)
- **Database**: MySQL / MariaDB (e.g. XAMPP) or SQLite
- **Frontend**: Bootstrap 5.3.3, Bootstrap Icons 1.11.3, Google Fonts (*Plus Jakarta Sans*)
- **Charts**: Chart.js 4.4.2
- **Composer**: `^2.0`

---

## 📥 Installation & Setup Guide

Follow these steps to clone, configure, and run the project locally.

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/crud-langganan.git
cd crud-langganan
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Configure Environment File
Copy the example `.env` file:
```bash
# On Linux/macOS
cp .env.example .env

# On Windows (PowerShell)
copy .env.example .env
```

Generate the application encryption key:
```bash
php artisan key:generate
```

### 4. Database Setup

Choose either **MySQL** or **SQLite**:

#### Option A: MySQL (e.g. XAMPP)
1. Start Apache & MySQL in XAMPP.
2. Create an empty database in phpMyAdmin or terminal:
   ```sql
   CREATE DATABASE `crud-langganan`;
   ```
3. Update `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crud-langganan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

#### Option B: SQLite (Zero configuration)
1. Create the SQLite file:
   ```bash
   # On Windows (PowerShell)
   New-Item -ItemType File -Force -Path database\database.sqlite

   # On Linux/macOS
   touch database/database.sqlite
   ```
2. Update `.env`:
   ```env
   DB_CONNECTION=sqlite
   # Comment out DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
   ```

### 5. Run Migrations & Seed Demo Data
Run the database migrations and seed the demo account with realistic sample subscriptions:
```bash
php artisan migrate --seed
```

### 6. Start the Development Server
```bash
php artisan serve
```

Visit the application in your browser at:
👉 **`http://127.0.0.1:8000`**

---

## 🔑 Demo Account Credentials

Use these preloaded credentials or click the **"1-Click Demo Login"** button on the login page:

| Role | Email | Password | Preloaded Data |
|---|---|---|---|
| **Demo User** | `demo@example.com` | `password123` | 13 Subscriptions (Active, Free Trials, Cancelled, Weekly, Yearly) |
| **Test User** | `test@example.com` | `password` | Clean account for custom testing |

---

## 🧪 Running Automated Tests

The repository includes comprehensive verification test suites covering authentication, CRUD, date logic, calculations, and multi-tenant security:

```bash
# 1. Run full HTTP authentication & session workflow test
php tests/test_http_auth.php

# 2. Run user functions, password hashing & data isolation test
php tests/test_auth_and_user.php

# 3. Run comprehensive 8-point system verification suite
php tests/verify.php
```

All suites should pass with `0 failures`.

---

## 📂 Project Architecture

```
crud-langganan/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AnalyticsController.php     # Chart.js metrics & savings calculations
│   │       ├── AuthController.php          # Login, Register, 1-Click Demo, Logout
│   │       ├── DashboardController.php      # Metrics, upcoming payments, budget tracking
│   │       ├── ProfileController.php        # Name, email, currency, budget, password
│   │       ├── SubscriptionController.php   # Full CRUD, CSV export, 1-click renewal
│   │       └── TrialController.php          # Dedicated free trials watchdog & cancel actions
│   └── Models/
│       ├── Subscription.php                 # Scopes, monthly normalization, date urgency logic
│       └── User.php                         # Auth, currency formatter, budget casts, relationships
├── database/
│   ├── migrations/                          # Users, currency, subscriptions, monthly budget
│   └── seeders/DatabaseSeeder.php           # Pre-seeds Demo user with 13 realistic services
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php                # Master layout with sidebar, theme toggle, quick add modal
│       │   └── auth.blade.php               # Clean authentication layout
│       ├── auth/                            # Login & Register views
│       ├── dashboard/index.blade.php        # Dashboard overview & summary charts
│       ├── subscriptions/                   # Index (Cards/Table), Create, Edit, Show, Trash
│       ├── trials/index.blade.php           # Free Trials Watchdog manager
│       ├── analytics/index.blade.php        # Deep visual analytics & spending distribution
│       └── profile/edit.blade.php           # Settings, currency selection & password update
├── routes/
│   └── web.php                              # Web routes with auth middleware & RESTful resources
└── tests/                                   # Standalone automated test runners
```

---

## 🧭 Key Routes Reference

| Method | URI | Action / Controller | Description |
|---|---|---|---|
| `GET` | `/login` | `AuthController@showLogin` | Sign in form |
| `POST` | `/login` | `AuthController@login` | Authenticate user credentials |
| `GET` | `/demo-login` | `AuthController@quickDemoLogin` | 1-Click test login |
| `GET` | `/register` | `AuthController@showRegister` | Registration form |
| `POST` | `/logout` | `AuthController@logout` | Sign out current user |
| `GET` | `/dashboard` | `DashboardController@index` | Main dashboard & metrics |
| `GET` | `/subscriptions` | `SubscriptionController@index` | Browse subscriptions (table/cards) |
| `POST` | `/subscriptions` | `SubscriptionController@store` | Create new subscription |
| `GET` | `/subscriptions/{id}` | `SubscriptionController@show` | View subscription details |
| `PUT` | `/subscriptions/{id}` | `SubscriptionController@update` | Update price/dates/status |
| `DELETE` | `/subscriptions/{id}` | `SubscriptionController@destroy` | Soft-delete to Trash |
| `POST` | `/subscriptions/{id}/renew`| `SubscriptionController@renew` | Mark as paid & advance 1 cycle |
| `GET` | `/subscriptions/export/csv` | `SubscriptionController@exportCsv`| Download CSV file |
| `GET` | `/subscriptions/trash` | `SubscriptionController@trash` | Trashed records view |
| `POST` | `/subscriptions/{id}/restore`| `SubscriptionController@restore`| Restore from Trash |
| `GET` | `/trials` | `TrialController@index` | Dedicated Free Trials Watchdog |
| `PATCH`| `/trials/{id}/cancel` | `TrialController@cancel` | Cancel trial immediately |
| `GET` | `/analytics` | `AnalyticsController@index` | Interactive Chart.js analytics |
| `GET` | `/profile` | `ProfileController@edit` | Account settings & currency |
| `PUT` | `/profile/budget` | `ProfileController@updateBudget` | Update monthly budget limit |

---

## 🤝 Contributing & Extending

Contributions are welcome! If you'd like to work on this project or use it as a foundation for your own portfolio:

### Development Workflow
1. **Fork** the repository.
2. Create a feature branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "feat: add reminder notification via email"
   ```
4. Run tests to ensure everything remains green:
   ```bash
   php tests/verify.php
   ```
5. Push to your branch and open a **Pull Request**.

### Ideas for Future Enhancements
- [ ] **Email Notifications**: Scheduled automated email alerts 3 days before renewal dates using Laravel Queues & Mailables.
- [ ] **Live Currency Exchange Rates**: Integration with an exchange rate API to convert multi-currency subscriptions into a single base currency.
- [ ] **PDF Statement Export**: Generate printable monthly spending statements using DomPDF.
- [ ] **PWA / Web Push Notifications**: Service worker notifications when opening the app near due dates.
- [ ] **Custom Category Management**: Allow users to add, edit, or customize their own categories and icon colors.

---

## 📄 License

This open-source project is licensed under the [MIT License](LICENSE).
Feel free to use it for learning, portfolio showcases, or personal finance tracking!
