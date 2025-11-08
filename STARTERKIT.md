I'll explore the codebase to understand the Laravel 12 React starter kit structure.























Perfect! I've completed my exploration of the Laravel 12 React starter kit. Here's a comprehensive understanding of the codebase:

# Laravel 12 React Starter Kit - Codebase Overview

## **Tech Stack**

### Backend
- **Laravel 12** - Latest framework version with streamlined structure
- **Laravel Fortify** - Authentication scaffolding (login, registration, password reset, 2FA)
- **Laravel Wayfinder** - Type-safe route generation for frontend
- **Inertia.js v2** - Server-driven SPA framework
- **Pest v4** - Testing framework
- **PHP 8.2+**

### Frontend
- **React 19** - Latest React with React Compiler enabled
- **TypeScript** - Full type safety
- **Tailwind CSS v4** - Latest styling framework
- **Radix UI** - Headless component primitives
- **shadcn/ui** - Pre-built accessible components
- **Lucide React** - Icon library
- **Vite 7** - Build tool with SSR support

## **Project Structure**

### Backend Architecture
```
app/
├── Actions/Fortify/          # Fortify action classes
├── Http/
│   ├── Controllers/Settings/ # Settings controllers (Profile, Password, 2FA)
│   ├── Middleware/          # HandleInertiaRequests, HandleAppearance
│   └── Requests/Settings/   # Form request validation
├── Models/                  # User model with 2FA
└── Providers/              # FortifyServiceProvider, AppServiceProvider
```

### Frontend Architecture
```
resources/js/
├── components/             # Reusable components
│   ├── ui/                # shadcn/ui components (25+ components)
│   └── app-*.tsx          # App-specific components (header, sidebar, etc.)
├── hooks/                 # Custom hooks (appearance, clipboard, 2FA, etc.)
├── layouts/               # Layout templates (app, auth, settings)
├── pages/                 # Inertia pages
│   ├── auth/             # Authentication pages
│   ├── settings/         # Settings pages
│   ├── dashboard.tsx
│   └── welcome.tsx
├── types/                # TypeScript definitions
└── routes/               # Wayfinder generated routes (gitignored)
```

## **Key Features Implemented**

### Authentication (via Fortify)
- ✅ Registration
- ✅ Login with rate limiting
- ✅ Password reset
- ✅ Email verification
- ✅ Two-factor authentication (TOTP)
- ✅ Password confirmation

### User Settings
- ✅ Profile management (name, email)
- ✅ Password updates
- ✅ Account deletion
- ✅ Two-factor authentication setup
- ✅ Appearance settings (light/dark/system theme)

### UI/UX Features
- ✅ Responsive sidebar layout with collapsible state
- ✅ Breadcrumb navigation
- ✅ User menu with avatar
- ✅ Theme switcher (light/dark/system)
- ✅ Modern, accessible UI components
- ✅ Mobile-responsive design

### Developer Experience
- ✅ Type-safe routing with Wayfinder
- ✅ Hot module replacement (HMR)
- ✅ React Compiler for automatic optimization
- ✅ ESLint + Prettier configured
- ✅ Comprehensive test suite (Pest)
- ✅ Laravel Pint for PHP formatting
- ✅ Concurrent dev script (server, queue, logs, vite)
- ✅ SSR support configured

## **Database**
- SQLite database with migrations for:
  - Users table (with 2FA columns)
  - Cache table
  - Jobs table
  - Password reset tokens

## **Configuration Highlights**
- Middleware: Custom `HandleAppearance` and [HandleInertiaRequests](cci:2://file:///Users/clarkesp/WebStorm/laravel-starter-react-clarke-sp/app/Http/Middleware/HandleInertiaRequests.php:8:0-50:1)
- Shared Inertia data: user auth, app name, inspirational quotes, sidebar state
- Rate limiting: Login (5/min), 2FA (5/min)
- Cookie encryption excludes: `appearance`, `sidebar_state`

## **Testing**
- Feature tests for authentication flows
- Feature tests for settings pages
- Tests use factories with custom states (unverified, withoutTwoFactor)

---

## **Multi-Tenancy** ✨ NEW
- ✅ **Multi-database tenancy** - Each tenant gets isolated SQLite database
- ✅ **Subdomain identification** - Tenants accessed via subdomains
- ✅ **Tenant model & migrations** - Central tenant management
- ✅ **Domain management** - Multiple domains per tenant
- ✅ **Automatic database creation** - Tenant DBs created on-the-fly
- ✅ **Tenant-aware middleware** - Context switching per request
- ✅ **Isolated storage & cache** - Per-tenant file storage and caching

## **Possibilities for Enhancement**
- **User roles & permissions** system (per tenant)
- **Team/organization** functionality within tenants
- **API authentication** (Sanctum) with tenant awareness
- **File uploads** with tenant-isolated media library
- **Activity logs** / audit trail per tenant
- **Notifications** system (tenant-scoped)
- **Admin panel** with tenant management
- **Email templates** customization per tenant
- **Social authentication** (OAuth)
- **Advanced search** functionality
- **Tenant billing & subscriptions**
- **Tenant usage analytics**
- **Tenant impersonation** for support
