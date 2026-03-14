

<!-- LARAVEL-PRINSIPPER:START -->
## Generelle Laravel-prinsipper (synkronisert)

# Generelle Laravel-utviklingsprinsipper

Dette dokumentet inneholder generelle mønstre, pakker og beste praksis for Laravel-utvikling basert på Laravel Boost Guidelines. Disse prinsippene kan gjenbrukes på tvers av prosjekter.

## Laravel-first og Spatie-first (obligatorisk)

- Default: Velg innebygd Laravel/Eloquent før custom kode.
- Default: Velg Spatie-pakker før andre tredjepartspakker når behovet dekkes.

### Prioritetsrekkefølge for valg av løsning

1. Innebygd Laravel/Eloquent
2. Spatie-pakke
3. Annen etablert pakke
4. Egen implementasjon (kun når 1-3 ikke dekker behovet)

### Ikke lag custom hvis Laravel allerede har dette

- Collections: bruk Collection API (`wrap`, `map`, `filter`, `pluck`, `keyBy`, `groupBy`).
- Querying: bruk Eloquent/query builder (`when`, `whereRelation`, `withCount`, `withExists`, `exists`, `firstOrFail`, `paginate`).
- Validation/auth: Form Requests, Policies, Gates, middleware.
- Cache/queues/events/notifications/files: bruk Laravel facades og contracts.
- Routing/URLs: named routes + `route()`.
- Model behavior: bruk relationships, scopes, casts, accessors/mutators, observers.
- API output: API Resources før manuell array-bygging.

### Spatie-regel

- Sjekk alltid først om Spatie har en moden pakke for behovet.
- Eksempler:
  - permissions/roles -> `spatie/laravel-permission`
  - media/files -> `spatie/laravel-medialibrary`
  - activity/audit logs -> `spatie/laravel-activitylog`
  - settings/data objects -> relevante Spatie-pakker
- Ved valg av annen pakke enn Spatie skal det begrunnes kort.

### Controller/Service-regel

- Controller kan inneholde effektiv, tydelig Laravel-kode.
- Flytt kun ut logikk hvis den blir gjenbrukt eller øker tydelig domeneansvar.
- Unngå abstraksjoner uten tydelig verdi.

### Forbud mot unødvendige wrappers

- Ikke lag egne helper-metoder hvis Laravel har en direkte metode.
- Ikke lag egne datastrukturer når Collection/Eloquent dekker behovet.
- Ikke bruk rå SQL hvis samme kan uttrykkes tydelig med Eloquent.

### Kvalitetskrav i PR/commit

- Ved custom løsning: skriv kort hvorfor innebygd Laravel/Spatie ikke var nok.
- All ny adferd skal ha test (happy path + validering + authorization der relevant).

## Før du implementerer

- Finnes dette i Laravel core?
- Finnes dette i Eloquent API?
- Finnes dette i Spatie?
- Hvis nei: kan enkel custom kode forsvares?

## 📦 Anbefalt Teknologi-stack

### Backend

- **Laravel siste versjon** - Moderne Laravel-struktur
- **PHP 8.3+** - Constructor property promotion, type hints
- **Laravel Breeze** - Autentisering og grunnleggende UI
- **SQLite** (utvikling) / PostgreSQL/MySQL (produksjon)

### Frontend

- **Blade** templates med komponentbasert arkitektur
- **TailwindCSS v4** for styling
- **Alpine.js v3** for enkel interaktivitet
- **Vite** for asset bundling

### Testing & Kvalitet

- **Pest 4** - Modern PHP testing framework
- **Laravel Pint** - Code formatting (Laravel's opinionated PHP-CS-Fixer)
- Feature og unit tests med factories

### Komponentbibliotek (valgfritt)

- **ddfsn/blade-components** eller lignende - Forhåndsbyggede UI-komponenter
- **Spatie-pakker** - Foretrukket tredjeparts-leverandør (Media Library, Permissions, etc.)

## 🏗️ Arkitekturprinsipper

### Komponent-wrapper Mønster

**VIKTIG**: Wrap alltid tredjepartskomponenter i egne app-spesifikke komponenter.

```
resources/views/components/
├── app/              # Dine wrapper-komponenter
│   ├── card/         # Wrapper for x-card med app-spesifikk styling
│   ├── button/       # Wrapper for x-btn
│   └── ...
├── form/             # Egne form-komponenter
├── layouts/          # Layout-komponenter
└── [domain]/         # Domene-spesifikke komponenter
```

**Fordeler**:

- Sentral kontroll over styling (f.eks. `rounded-lg` som standard)
- Enkel endring av standardverdier uten å berøre vendor-kode
- Mulighet til å bytte ut underliggende bibliotek
- Konsistens på tvers av applikasjonen

### Laravel 12 Struktur

- Middleware i `bootstrap/app.php`, ikke `app/Http/Kernel.php`
- Service providers i `bootstrap/providers.php`
- Console commands auto-registreres fra `app/Console/Commands/`
- Ingen `app/Console/Kernel.php`

## 📝 Kodestandarder

### PHP Generelt

```php
// ✅ Bruk constructor property promotion
public function __construct(
    public GitHub $github,
    private string $apiKey,
) {}

// ✅ Alltid eksplisitte return types
public function isAccessible(User $user, ?string $path = null): bool
{
    // ...
}

// ✅ Curly braces selv for single-line
if ($condition) {
    return true;
}

// ✅ PHPDoc blocks for kompleks logikk
/**
 * @param array{name: string, email: string} $data
 * @return Collection<int, User>
 */
public function createUsers(array $data): Collection
{
    // ...
}
```

### Laravel Best Practices

```php
// ✅ Bruk Eloquent relationships, ikke raw queries
$company->pipelineStage()->first();
$tenant->pipelineStages()->orderBy('order')->get();

// ✅ Eager loading for N+1 prevention
$stages = $tenant->pipelineStages()
    ->withCount('companies')
    ->orderBy('order')
    ->get();

// ✅ Named routes
return redirect()->route('settings.stages.index');

// ✅ config() i stedet for env()
$apiKey = config('services.github.token');

// ❌ ALDRI bruk env() utenfor config-filer
$apiKey = env('GITHUB_TOKEN'); // FEIL!
```

### Controllers

```php
// ✅ Authorization i controller methods
public function index(Request $request)
{
    $this->authorize('viewAny', PipelineStage::class);
    // ...
}

// ✅ Bruk Form Requests for validering
public function store(PipelineStageRequest $request)
{
    // Validering og authorization håndteres i Request-klassen
}

// ✅ Hold controllers effektive og bruk laravel magic
public function update(Request $request, Model $model)
{
    $this->authorize('update', $model);

    $model->update($request->validated());

    return redirect()->route('resource.index')
        ->with('success', __('messages.updated'));
}
```

```php
// ✅ Unngå invokable controllers som standard
// Foretrekk standard controller-metoder: index/show/create/store/edit/update/destroy
// Unngå --invokable med mindre det er eksplisitt avtalt i oppgaven
```

### Form Requests

```php
class PipelineStageRequest extends FormRequest
{
    // ✅ Authorization i Request, ikke Controller
    public function authorize(): bool
    {
        return true; // Eller mer kompleks logikk
    }

    // ✅ Bruk array syntax
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('table')->ignore($this->route('model')),
            ],
            'color' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
        ];
    }

    // ✅ Custom error messages
    public function messages(): array
    {
        return [
            'name.required' => __('validation.name_required'),
        ];
    }
}
```

### Policies

```php
class ResourcePolicy
{
    // ✅ Enkel, tydelig authorization logic
    public function viewAny(User $user): bool
    {
        return $user->current_tenant_id !== null
            && $user->isAdminOfCurrentTenant();
    }

    public function update(User $user, Resource $resource): bool
    {
        return $user->current_tenant_id === $resource->tenant_id
            && $user->isAdminOfCurrentTenant();
    }
}
```

### Models

```php
class PipelineStage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'color',
        'order',
        'is_active',
    ];

    // ✅ Bruk casts() method, ikke $casts property
    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ✅ Type-hinted relationships
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}
```

## 🎨 Frontend Mønstre

### Blade Komponent Struktur

```blade
{{-- ✅ Konsistent layout pattern --}}
<x-layouts.app>
    <div class="py-6">
        <x-container class="space-y-6">
            {{-- Title --}}
            <div class="flex flex-col gap-2">
                <x-heading size="lg">{{ __('page.title') }}</x-heading>
                <x-paragraph style="muted">{{ __('page.subtitle') }}</x-paragraph>
            </div>

            {{-- Grid with sidebar --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <aside class="lg:col-span-1">
                    <x-settings.sidebar />
                </aside>

                <div class="lg:col-span-3 space-y-6">
                    {{-- Content --}}
                    <x-app.card>
                        <x-app.card.body>
                            @if($items->isEmpty())
                                <x-empty :title="..." :description="..." />
                            @else
                                {{-- Content here --}}
                            @endif
                        </x-app.card.body>
                    </x-app.card>
                </div>
            </div>
        </x-container>
    </div>
</x-layouts.app>
```

### Form Pattern

```blade
<form method="POST" action="{{ route('resource.store') }}" class="space-y-4">
    @csrf

    {{-- Input field --}}
    <div>
        <x-form.label for="name" :label="__('form.name')" />
        <x-form.input
            id="name"
            name="name"
            :value="old('name')"
            required
            class="@error('name') border-red-500 @enderror"
        />
        @error('name')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Checkbox --}}
    <div>
        <label for="is_active" class="flex items-center gap-2">
            <input
                type="checkbox"
                id="is_active"
                name="is_active"
                value="1"
                {{ old('is_active', true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
            />
            <span class="text-sm font-medium text-gray-700">{{ __('form.is_active') }}</span>
        </label>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3 pt-4">
        <x-btn type="submit">{{ __('form.save') }}</x-btn>
        <x-btn href="{{ route('resource.index') }}" style="ghost">{{ __('form.cancel') }}</x-btn>
    </div>
</form>
```

### Tailwind CSS Mønstre

- Bruk utility classes direkte i komponenter
- Konsistent spacing: `gap-2`, `gap-3`, `gap-4`, `space-y-4`, `space-y-6`
- Konsistent padding: `py-6`, `p-4`, `px-4`
- Responsive design: `grid-cols-1 lg:grid-cols-4`
- Consistent rounding: `rounded-lg` (8px)

## 🧪 Testing med Pest

### Test Struktur

```php
<?php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can perform action', function () {
    // Arrange
    $tenant = Tenant::factory()->create();
    $admin = User::factory()->create(['current_tenant_id' => $tenant->id]);
    $tenant->users()->attach($admin->id, ['role' => 'admin']);

    // Act
    $response = $this->actingAs($admin)
        ->post('/resource', ['name' => 'Test']);

    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('resources', ['name' => 'Test']);
});

test('non-admin cannot perform action', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['current_tenant_id' => $tenant->id]);
    $tenant->users()->attach($user->id, ['role' => 'user']);

    $this->actingAs($user)
        ->post('/resource', ['name' => 'Test'])
        ->assertForbidden();
});
```

### Test Best Practices

- ✅ Bruk `RefreshDatabase` trait
- ✅ Test både happy path og edge cases
- ✅ Test authorization (admin vs user)
- ✅ Test validation (required fields, unique constraints)
- ✅ Use factories for test data
- ✅ Kjør kun relevante tester: `php artisan test --filter=TestName`
- ✅ Bruk `--compact` for rask feedback

## 📚 Lokalisering

### Språkfil Struktur

```php
// lang/en/resource.php
return [
    'title' => 'Resources',
    'subtitle' => 'Manage your resources.',
    'create_new' => 'Create New Resource',
    'empty' => 'No resources yet.',

    'form' => [
        'name' => 'Name',
        'color' => 'Color',
        'save' => 'Save Changes',
        'cancel' => 'Cancel',
    ],

    'validation' => [
        'name_required' => 'Name is required.',
        'name_unique' => 'A resource with this name already exists.',
    ],

    'messages' => [
        'created' => 'Resource created successfully.',
        'updated' => 'Resource updated successfully.',
        'deleted' => 'Resource deleted successfully.',
    ],
];
```

### Bruk i Views

```blade
{{ __('resource.title') }}
{{ __('resource.form.name') }}
{{ __('resource.messages.created') }}

{{-- Med parametere --}}
{{ __('resource.count', ['count' => $items->count()]) }}
```

## 🛠️ Artisan Commands

### Opprett Nye Filer

```bash
# Controller
php artisan make:controller ResourceController --resource --no-interaction

# Model med factory og migration
php artisan make:model Resource -mf --no-interaction

# Form Request
php artisan make:request ResourceRequest --no-interaction

# Policy
php artisan make:policy ResourcePolicy --model=Resource --no-interaction

# Test
php artisan make:test ResourceTest --pest --no-interaction

# Generic PHP class
php artisan make:class Actions/DoSomethingAction --no-interaction
```

### Alltid Bruk --no-interaction

Dette sikrer at kommandoen kjører uten brukerinput, viktig for automatisering og CI/CD.

## 🎯 Workflow

### Utviklingsprosess

1. **Analyser** - Forstå eksisterende mønstre i codebase
2. **Plan** - Lag strukturert plan før implementering
3. **Implementer** - Backend først (controller, request, policy, routes)
4. **Views** - Følg etablerte UI-mønstre
5. **Lokaliser** - Legg til translation keys
6. **Test** - Skriv comprehensive feature tests
7. **Format** - Kjør `vendor/bin/pint --dirty --format agent`
8. **Verifiser** - Kjør alle tester: `php artisan test --compact`

### Database Migrations

```php
Schema::create('resources', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
    $table->string('name');
    $table->string('color');
    $table->integer('order');
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index(['tenant_id', 'order']);
});
```

### Route Organisering

```php
// Gruppert med middleware
Route::middleware(['auth', 'tenant'])->group(function () {
    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('/stages', [PipelineStageController::class, 'index'])
            ->name('settings.stages.index');
        Route::get('/stages/create', [PipelineStageController::class, 'create'])
            ->name('settings.stages.create');
        // etc...
    });

    // Resource routes
    Route::resource('resources', ResourceController::class);
});
```

## 📋 Sjekkliste for Nye Features

- [ ] Controller med authorization checks
- [ ] Form Request med validation og messages
- [ ] Policy med admin/user checks
- [ ] Routes med korrekt naming convention
- [ ] Views med wrapper components
- [ ] Translation keys for alle UI-tekster
- [ ] Feature tests (happy path + edge cases)
- [ ] Kjør Pint for formatering
- [ ] Kjør alle tester for å sikre ingen regresjoner
- [ ] Test manuelt i browser

## 🚀 Laravel Boost MCP Tools

Hvis du bruker Laravel Boost:

```bash
# Search dokumentasjon (VIKTIG!)
laravel-boost-mcp-search-docs --queries=["rate limiting", "validation"]

# Database queries
laravel-boost-mcp-database-query --query="SELECT * FROM users LIMIT 5"

# Tinker execution
laravel-boost-mcp-tinker --code="User::count()"

# List routes
laravel-boost-mcp-list-routes --path="settings"

# Application info
laravel-boost-mcp-application-info

# Get absolute URL
laravel-boost-mcp-get-absolute-url --path="/dashboard"
```

## 💡 Viktige Prinsipper

1. **Følg Laravel Conventions** - Bruk Laravels innebygde løsninger først
2. **Komponenter av Komponenter** - Wrap tredjepartsbiblioteker
3. **Test Everything** - Feature tests er påkrevd
4. **Type Hints Everywhere** - PHP 8.3+ features
5. **Lokalisering fra Start** - Ingen hardkodet tekst
6. **Keep Controllers Effective** - Large business logic i Actions/Services, but try use laravel magic and keep code in controllers.
7. **Authorization i Policies** - Ikke spredt rundt i koden
8. **Eager Loading** - Unngå N+1 queries
9. **Named Routes** - Aldri hardkodede URLs
10. **Format med Pint** - Konsistent kodestil
11. **Bruk MCP Tools** - For rask innsikt i codebase og debugging
12. **DRY Principles** - Ikke gjenta deg selv, bruk komponenter og tjenester
13. **Sikkerhet Først** - Alltid tenk på authorization og data validation
14. **Ytelse** - Optimaliser database queries og unngå unødvendige operasjoner
15. **Cache Strategisk** - Bruk caching for å forbedre ytelsen der det gir mening

## 🔗 Nyttige Ressurser

- Laravel Documentation: https://laravel.com/docs
- Pest Documentation: https://pestphp.com
- Tailwind CSS Documentation: https://tailwindcss.com
- Laravel Best Practices: https://github.com/alexeymezenin/laravel-best-practices
- Spatie Packages: https://spatie.be/open-source
<!-- LARAVEL-PRINSIPPER:END -->
===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.30
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan Commands

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`, `php artisan tinker --execute "..."`).
- Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Debugging

- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.
- To execute PHP code for debugging, run `php artisan tinker --execute "your code here"` directly.
- To read configuration values, read the config files directly or run `php artisan config:show [key]`.
- To inspect routes, run `php artisan route:list` directly.
- To check environment variables, read the `.env` file directly.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
