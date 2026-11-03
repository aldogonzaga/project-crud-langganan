# SubTrack &bull; Pelacak Langganan & Uji Coba Gratis

A modern, responsive full-stack **Subscription & Free Trial Tracker** CRUD web application built with **Laravel 12** and **Bootstrap 5.3**. Designed based on the UI/UX concepts in `refference/KeikiSpace (2).html` but styled with a **clean, modern fintech palette** (Deep Indigo, Slate Navy, Emerald, and Amber — zero pink theme), dark/light mode toggle, and rich interactive data visualizations using **Chart.js**.

---

## 🌟 Key Features

### 1. 🔐 User Authentication & Profile Preferences
- **Registration & Login**: Secure authentication with password hashing and session management.
- **1-Click Demo Login**: Pre-seeded demo account (`demo@example.com` / `password123`) loaded with 13 realistic subscriptions (Spotify, Netflix, Prime, Adobe Creative Cloud, GitHub Copilot, Gym, JetBrains, Figma Free Trial, etc.).
- **Multi-Currency Support**: Choose preferred currency symbol (`$`, `Rp`, `€`, `£`, `¥`) in settings; all totals, monthly calculations, and charts adapt automatically.

### 2. 📊 Core Dashboard & Metrics
- **Total Monthly Spend**: Calculates normalized monthly cost (accurately converting Weekly `* 52 / 12`, Monthly, and Yearly `/ 12` cycles).
- **Projected Annual Spend**: Automatic 12-month expenditure projection.
- **Urgent Payment Alert Banner**: Triggered dynamically when any subscription is due within ≤ 3 days or overdue.
- **Specialized View: "Payments Due in Next 7 Days"**: Chronological timeline of upcoming renewals.
- **Free Trials Watchdog**: Dedicated countdown widget with visual progress bars.

### 3. 🔁 Full CRUD Management
- **Create**: Add new subscriptions with service name, cost, cycle, next billing date, category, status, icon/emoji, and notes. Includes live monthly/annual cost preview calculator and preset autofill buttons.
- **Read**:
  - **Card Grid View**: Modern squircle cards with icons, price, renewal schedule, and quick action dropdowns.
  - **Table View**: Comprehensive data table with sorting and filtering indicators.
- **Update**: Modify prices (for rate hikes), update billing cycles, change status from Free Trial to Active, or mark Cancelled.
- **Delete & Soft Deletes**:
  - **Status "Cancelled"**: Keeps the subscription in your history for reference but stops adding to monthly expenses.
  - **Soft Delete to Trash**: Move unwanted records to Trash with full **Restore** and **Permanent Delete** capabilities.

### 4. 🚀 Level-Up Portfolio Capabilities
- **Filtering & Sorting**:
  - Filter by **Category** (*Entertainment, Utilities, Health, Work, Education, Food, Shopping, Others*).
  - Filter by **Status** (*All, Active, Free Trial, Cancelled*).
  - Filter by **Billing Cycle** (*Monthly, Yearly, Weekly*).
  - Search by service name and notes.
  - Sort by: *Renewal Date (Soonest)*, *Highest Cost*, *Lowest Cost*, *Name (A-Z)*, *Recently Added*.
- **Visualizations (Chart.js)**:
  - **Spending by Category**: Interactive doughnut chart with legend, amounts, and budget percentages.
  - **Billing Cycle Distribution**: Frequency bar chart comparing Monthly vs Yearly vs Weekly commitments.
  - **Top 5 Expenses Ranking**: Identifies services with the highest impact on your monthly budget.
  - **Cancellation Savings Impact**: Calculates estimated monthly and annual money saved by cancelling inactive services.
- **Date Logic & Urgency Highlighting**:
  - **Red / Danger (`card-critical`)**: Subscriptions due within **≤ 3 days** or overdue.
  - **Yellow / Warning (`card-warning`)**: Subscriptions due within **4 to 7 days**.
  - **Normal**: Renewals due in > 7 days.
- **Light & Dark Mode**: Persistent theme switcher stored in `localStorage`.
- **Mobile Responsive**: Includes desktop sticky sidebar and mobile bottom navigation bar (`.mobile-bottom-nav`).

---

## 🛠️ Tech Stack
- **Backend**: Laravel 12.x, PHP 8.2+
- **Database**: SQLite (or MySQL)
- **Frontend**: Bootstrap 5.3.3, Bootstrap Icons 1.11.3, Google Fonts (*Plus Jakarta Sans*)
- **Data Visualizations**: Chart.js v4.4.2

---

## 🚀 Getting Started

### 1. Start Local Server
Run the Laravel development server:
```bash
php artisan serve
```
Open your browser at: `http://127.0.0.1:8000`

### 2. Demo Account Credentials
- **Email**: `demo@example.com`
- **Password**: `password123`
*(Or click the "1-Click Demo Login" button directly on the login page)*

### 3. Re-seed Database (Optional)
To reset and re-seed the sample subscriptions:
```bash
php artisan db:seed
```

### 4. Run Verification Tests
```bash
php tests/verify.php
```
