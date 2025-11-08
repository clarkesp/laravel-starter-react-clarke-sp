# Tenancy for Laravel - Configuration & Reference

## 📦 Package Information

- **Package:** `stancl/tenancy`
- **Version:** v3.x
- **Documentation:** https://tenancyforlaravel.com/docs/v3
- **Repository:** https://github.com/stancl/tenancy

## 🎯 Our Tenancy Setup

### Architecture Type
**Multi-Database Tenancy** with **Subdomain Identification**

- Each tenant has a separate SQLite database file
- Tenants are identified by subdomain (e.g., `acme.localhost`)
- Central database manages tenant metadata
- Complete data isolation between tenants

### Database Structure

```
database/
├── central.sqlite          # Central/landlord database
│   ├── users              # Central users (optional)
│   ├── tenants            # Tenant registry
│   ├── domains            # Domain mappings
│   └── migrations         # Migration history
│
└── tenants/               # Tenant databases directory
    ├── acme.sqlite        # Tenant: Acme Corp
    ├── demo.sqlite        # Tenant: Demo Inc
    └── corp.sqlite        # Tenant: Corp Ltd
```

## ⚙️ Configuration Files

### 1. `config/tenancy.php`

**Key Settings:**

```php
'tenant_model' => Tenant::class,
'id_generator' => Stancl\Tenancy\UUIDGenerator::class,
'domain_model' => Domain::class,

'central_domains' => [
    '127.0.0.1',
    'localhost',
],

'database' => [
    'central_connection' => 'central',
    'template_tenant_connection' => 'tenant',
    'prefix' => database_path('tenants') . '/',
    'suffix' => '.sqlite',
],
```

**Bootstrappers Enabled:**
- `DatabaseTenancyBootstrapper` - Switches database connection
- `CacheTenancyBootstrapper` - Isolates cache per tenant
- `FilesystemTenancyBootstrapper` - Isolates file storage
- `QueueTenancyBootstrapper` - Makes queues tenant-aware

### 2. `config/database.php`

**Central Connection:**
```php
'central' => [
    'driver' => 'sqlite',
    'database' => env('DB_CENTRAL_DATABASE', database_path('central.sqlite')),
    'prefix' => '',
    'foreign_key_constraints' => true,
],
```

**Tenant Connection Template:**
```php
'tenant' => [
    'driver' => 'sqlite',
    'database' => null, // Set dynamically by tenancy
    'prefix' => '',
    'foreign_key_constraints' => true,
],
```

### 3. `.env` Configuration

```env
DB_CONNECTION=central
DB_CENTRAL_DATABASE=database/central.sqlite

CENTRAL_DOMAIN=localhost
TENANCY_DATABASE_PATH=database/tenants
```

## 🗄️ Database Schema

### Central Database Tables

#### `tenants` Table
```php
Schema::create('tenants', function (Blueprint $table) {
    $table->string('id')->primary();      // Tenant identifier
    $table->string('name');               // Tenant name
    $table->timestamps();
    $table->json('data')->nullable();     // Additional metadata
});
```

#### `domains` Table
```php
Schema::create('domains', function (Blueprint $table) {
    $table->increments('id');
    $table->string('domain', 255)->unique();  // e.g., acme.localhost
    $table->string('tenant_id');
    $table->timestamps();
    $table->foreign('tenant_id')
          ->references('id')
          ->on('tenants')
          ->onUpdate('cascade')
          ->onDelete('cascade');
});
```

### Tenant Database Tables

Tenant databases contain your application-specific tables:
- `users` - Tenant users
- `posts`, `products`, etc. - Your app tables
- Any tenant-specific data

**Location:** `database/migrations/tenant/`

## 🏗️ Models

### Tenant Model

**File:** `app/Models/Tenant.php`

```php
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
        ];
    }
}
```

**Key Methods:**
- `domains()` - Relationship to domains
- `run(callable $callback)` - Run code in tenant context
- `database()` - Get tenant database connection

## 🛣️ Routing

### Central Routes
**File:** `routes/web.php`

```php
// These routes run in central context
Route::get('/', function () {
    return Inertia::render('welcome');
});

// Tenant management routes (central)
Route::middleware(['auth'])->group(function () {
    Route::get('/tenants', [TenantController::class, 'index']);
    Route::post('/tenants', [TenantController::class, 'store']);
});
```

### Tenant Routes
**File:** `routes/tenant.php`

```php
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // These routes run in tenant context
    Route::get('/', function () {
        return Inertia::render('tenant/dashboard', [
            'tenant' => tenant(),
        ]);
    });
    
    // All your tenant-specific routes
});
```

## 🔧 Middleware

### Tenancy Initialization Middleware

1. **`InitializeTenancyByDomain`** - Identifies tenant by full domain
2. **`InitializeTenancyBySubdomain`** - Identifies tenant by subdomain only
3. **`InitializeTenancyByDomainOrSubdomain`** - Tries both methods
4. **`InitializeTenancyByPath`** - Identifies tenant by URL path
5. **`InitializeTenancyByRequestData`** - Identifies tenant by request data

### Protection Middleware

- **`PreventAccessFromCentralDomains`** - Blocks central domains from tenant routes
- **`PreventAccessFromTenantDomains`** - Blocks tenant domains from central routes (if needed)

## 📝 Common Operations

### Creating a Tenant

```php
use App\Models\Tenant;

$tenant = Tenant::create([
    'id' => 'acme',
    'name' => 'Acme Corporation',
]);

$tenant->domains()->create([
    'domain' => 'acme.localhost',
]);
```

### Running Tenant Migrations

```bash
# Migrate all tenants
php artisan tenants:migrate

# Migrate specific tenant
php artisan tenants:migrate --tenants=acme

# Migrate specific tenants
php artisan tenants:migrate --tenants=acme,demo,corp
```

### Running Code in Tenant Context

```php
// Method 1: Using run()
$tenant = Tenant::find('acme');
$tenant->run(function () {
    $users = User::all(); // Queries tenant database
});

// Method 2: Using tenancy()
tenancy()->initialize($tenant);
$users = User::all(); // Queries tenant database
tenancy()->end();

// Method 3: In controller (automatic via middleware)
public function index()
{
    // Already in tenant context
    $users = User::all();
}
```

### Getting Current Tenant

```php
// Get current tenant
$tenant = tenant();

// Get tenant ID
$tenantId = tenant('id');

// Get tenant attribute
$tenantName = tenant('name');

// Check if in tenant context
if (tenancy()->initialized) {
    // In tenant context
}
```

### Seeding Tenant Databases

```bash
# Seed all tenants
php artisan tenants:seed

# Seed specific tenant
php artisan tenants:seed --tenants=acme

# Seed with specific seeder
php artisan tenants:seed --class=ProductSeeder
```

### Running Artisan Commands for Tenants

```bash
# Run any artisan command for tenant(s)
php artisan tenants:artisan "db:seed" --tenant=acme
php artisan tenants:artisan "cache:clear" --tenants=acme,demo
php artisan tenants:artisan "queue:work" --tenant=acme
```

### Listing Tenants

```bash
php artisan tenants:list
```

## 🎨 Helper Functions

### Global Helpers

```php
// Get current tenant
tenancy()->tenant
tenant()
tenant('id')
tenant('name')

// Check tenancy state
tenancy()->initialized
tenancy()->tenant

// Initialize tenancy
tenancy()->initialize($tenant)

// End tenancy
tenancy()->end()
```

### Tenant-Aware Asset URLs

```php
// Tenant-specific asset
tenant_asset('images/logo.png')

// Global asset (non-tenant)
global_asset('images/logo.png')
```

## 🔄 Event System

### Tenant Lifecycle Events

**Defined in:** `app/Providers/TenancyServiceProvider.php`

```php
Events\TenantCreated::class => [
    JobPipeline::make([
        Jobs\CreateDatabase::class,
        Jobs\MigrateDatabase::class,
        // Jobs\SeedDatabase::class,
    ])
],

Events\TenantDeleted::class => [
    JobPipeline::make([
        Jobs\DeleteDatabase::class,
    ])
],
```

### Available Events

**Tenant Events:**
- `CreatingTenant` - Before tenant is created
- `TenantCreated` - After tenant is created
- `SavingTenant` - Before tenant is saved
- `TenantSaved` - After tenant is saved
- `UpdatingTenant` - Before tenant is updated
- `TenantUpdated` - After tenant is updated
- `DeletingTenant` - Before tenant is deleted
- `TenantDeleted` - After tenant is deleted

**Domain Events:**
- `CreatingDomain` - Before domain is created
- `DomainCreated` - After domain is created
- `DeletingDomain` - Before domain is deleted
- `DomainDeleted` - After domain is deleted

**Database Events:**
- `DatabaseCreated` - After tenant database is created
- `DatabaseMigrated` - After tenant database is migrated
- `DatabaseSeeded` - After tenant database is seeded
- `DatabaseDeleted` - After tenant database is deleted

**Tenancy Events:**
- `InitializingTenancy` - Before tenancy is initialized
- `TenancyInitialized` - After tenancy is initialized
- `EndingTenancy` - Before tenancy ends
- `TenancyEnded` - After tenancy ends

## 🧪 Testing

### Testing Tenant Features

```php
use Stancl\Tenancy\Features\Testing\TenancyTestCase;

class TenantTest extends TenancyTestCase
{
    public function test_tenant_can_create_users()
    {
        $tenant = Tenant::create(['id' => 'test']);
        $tenant->domains()->create(['domain' => 'test.localhost']);
        
        $tenant->run(function () {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@test.com',
                'password' => 'password',
            ]);
            
            $this->assertDatabaseHas('users', [
                'email' => 'test@test.com',
            ]);
        });
    }
}
```

## 🚨 Important Considerations

### Data Isolation

✅ **Isolated:**
- Database queries
- File storage (when using FilesystemBootstrapper)
- Cache (when using CacheBootstrapper)
- Queued jobs (when using QueueBootstrapper)

⚠️ **Not Automatically Isolated:**
- Redis (requires RedisTenancyBootstrapper)
- External API calls
- Third-party services
- Static variables

### Performance Tips

1. **Database Connections:** SQLite creates a new connection per tenant
2. **Caching:** Use tenant-aware caching to avoid cross-tenant data leaks
3. **Queue Workers:** Consider dedicated queue workers per tenant for high-traffic apps
4. **File Storage:** Monitor disk usage per tenant

### Security Best Practices

1. ✅ Always use middleware for tenant identification
2. ✅ Never trust user input for tenant identification
3. ✅ Validate tenant access in controllers
4. ✅ Use tenant-aware policies and gates
5. ✅ Log tenant actions for audit trails
6. ✅ Implement rate limiting per tenant

## 📚 Quick Reference

### Artisan Commands

```bash
# Tenant Management
php artisan tenants:list
php artisan tenants:migrate
php artisan tenants:migrate --tenants=acme
php artisan tenants:seed
php artisan tenants:seed --tenants=acme
php artisan tenants:artisan "command" --tenant=acme

# Database
php artisan migrate --database=central
php artisan migrate:status --database=central
```

### File Locations

```
config/tenancy.php                    # Tenancy configuration
config/database.php                   # Database connections
app/Models/Tenant.php                 # Tenant model
app/Providers/TenancyServiceProvider.php  # Tenancy events
routes/tenant.php                     # Tenant routes
database/migrations/                  # Central migrations
database/migrations/tenant/           # Tenant migrations
database/central.sqlite               # Central database
database/tenants/*.sqlite             # Tenant databases
```

### Environment Variables

```env
DB_CONNECTION=central
DB_CENTRAL_DATABASE=database/central.sqlite
CENTRAL_DOMAIN=localhost
TENANCY_DATABASE_PATH=database/tenants
```

## 🔗 Useful Links

- **Official Documentation:** https://tenancyforlaravel.com/docs/v3
- **GitHub Repository:** https://github.com/stancl/tenancy
- **Discord Community:** https://discord.gg/tenancy
- **Video Tutorials:** https://laracasts.com/series/multi-tenancy-in-laravel

---

**Last Updated:** November 8, 2025
**Package Version:** v3.x
**Laravel Version:** 12.x
