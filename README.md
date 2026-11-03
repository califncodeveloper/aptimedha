# AptiMedha — MCQ Practice & Placement Preparation Platform

> **Practise Smart. Get Placement Ready.**

AptiMedha is a comprehensive educational content and MCQ practice website designed to help students, graduates, and job seekers sharpen their aptitude, reasoning, verbal, general knowledge, programming, and engineering skills for campus placement assessments and competitive exams.

---

## 🚀 Key Features

- **Flagship MCQ Practice Engine**: Instant option feedback (green for correct, red for incorrect), step-by-step explanations, bookmarking, and community discussion threads.
- **Placement Preparation Roadmap**: Structured 4-stage preparation guide from profile/resume building to technical coding, GDs, and HR interviews.
- **Categorized Learning**: 8 core tracks covering Arithmetic Aptitude, Data Interpretation, Logical Reasoning, Verbal Ability, English Grammar, General Knowledge, Programming, and Placement Prep.
- **Interactive Student Dashboard**: Track practice volume, accuracy rate, saved bookmarks, discussion replies, and report statuses.
- **Full Administrative Panel**: Single MCQ authoring, bulk Excel/CSV import conforming to canonical 10-column schema, taxonomy management, moderation queues, and editorial blogs.

---

## 🎨 Brand Design Tokens

Designed in strict compliance with the client-approved theme:

| Token | Hex Value | Primary Purpose |
| :--- | :--- | :--- |
| **Primary Navy** | `#153B70` | Brand identity, main headers, primary navigation |
| **Secondary Teal** | `#087F82` | Highlights, active navigation states, primary action buttons |
| **Page Background** | `#F7F9FC` | Clean neutral background for extended study sessions |
| **Surface White** | `#FFFFFF` | Cards, forms, question containers, and modals |
| **Main Text** | `#243247` | Body text, question typography, and navigation links |

---

## 🛠️ Technology Stack

- **Backend Framework**: [Laravel 13](https://laravel.com) (PHP 8.5)
- **Frontend Styling**: [Tailwind CSS 4](https://tailwindcss.com)
- **Frontend Interactivity**: [Alpine.js](https://alpinejs.dev) & JavaScript
- **Icons**: [Lucide Icons](https://lucide.dev)
- **Asset Bundler**: [Vite](https://vitejs.dev)
- **Database**: MySQL 8.x
- **Testing**: PHPUnit / Laravel Test Suite

---

## 📦 Getting Started

### Prerequisites

- PHP >= 8.2 (Recommended: PHP 8.5)
- Composer
- Node.js & npm
- MySQL Server

### Local Installation

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd aptimedha
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**:
   ```bash
   npm install
   ```

4. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure your MySQL credentials in `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).*

5. **Run Database Migrations**:
   ```bash
   php artisan migrate
   ```

6. **Build Frontend Assets**:
   ```bash
   npm run build
   ```
   *(Or run `npm run dev` for hot module reloading during development)*

7. **Start the Development Server**:
   ```bash
   php artisan serve
   ```
   Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser.

---

## 🧪 Running Tests

Verify all application routes, authorization policies, and architecture tests:

```bash
php artisan test
```

---

## 📂 Project Structure

```text
aptimedha/
├── app/
│   ├── Enums/            # Enum classes (UserRole, ReportStatus, etc.)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/    # Admin panel management controllers
│   │   │   ├── Auth/     # Authentication & password reset controllers
│   │   │   ├── Frontend/ # Public website controllers (Home, MCQ, Categories, Blog, Pages)
│   │   │   └── User/     # Authenticated learner dashboard controllers
│   │   └── Middleware/   # Role-based authorization & security middleware
│   └── Models/           # Eloquent data models
├── database/
│   ├── migrations/       # Database schema migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── css/              # Tailwind CSS styles & theme tokens
│   ├── js/               # Application scripts & Alpine bootstrap
│   └── views/            # Blade templates (layouts, components, admin, user, public)
├── routes/
│   ├── web.php           # Web application routes
│   └── console.php       # Artisan console routes
└── tests/
    └── Feature/          # Architecture and feature verification tests
```

---

## 🔒 Security & Privacy

- Standard Laravel CSRF verification is enabled on all forms.
- Sensitive environment variables are excluded from source control (`.gitignore`).
- Admin routes are protected with strict server-side middleware (`auth` and `admin`).

---

## 📄 License

Proprietary — All rights reserved by AptiMedha.
