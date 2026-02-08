# HRIS - Copilot Instructions

## Project Overview
Laravel 12 Human Resource Information System for FortiTech, handling employee management, work schedules, and attendance tracking.

**Stack:** PHP 8.2+, Laravel 12, Pest, Tailwind CSS, Alpine.js, Vite, SQLite/MySQL

## Quick Commands
```bash
composer setup     # Full setup: install, migrate, build assets
composer dev       # Start all dev servers (web, queue, pail logs, vite)
composer test      # Run Pest tests
```

## Architecture

### Domain Model
- **User** → holds auth/identity (firstName, lastName, email)
- **Employee** → HR data linked 1:1 to User via `userID`
- **WorkSchedule** → shift definitions, one assigned per employee via `workScheduleID`
- **AttendanceRecord** → daily time records linked to Employee

### Naming Conventions (Critical)
| Context | Convention | Examples |
|---------|------------|----------|
| Primary keys | camelCase + ID suffix | `employeeID`, `id` |
| Foreign keys | camelCase + ID suffix | `userID`, `workScheduleID` |
| System/audit fields | PascalCase | `CreatedDateTime`, `CreatedByID`, `LastModifiedDateTime`, `LastModifiedByID` |
| Other columns | camelCase | `dateOfBirth`, `basicMonthlySalary`, `phoneNbr` |

## Key Patterns

### Enums (app/Enums/)
Use PHP 8.4 string-backed enums for all dropdown/categorical values:
```php
enum Department: string {
    case OPERATIONS = 'Operations';
    case HR = 'Human Resources';
}
```
Reference in models via casts: `'department' => Department::class`

### System Fields Trait
All models with audit tracking use `HasSystemFields` trait which auto-populates:
- `CreatedDateTime`, `CreatedByID` on create
- `LastModifiedDateTime`, `LastModifiedByID` on update

### Migrations
Use `MigrationHelper::addSystemFields($table)` at end of schema:
```php
Schema::create('table_name', function (Blueprint $table) {
    // ... columns
    MigrationHelper::addSystemFields($table);
});
```

### Factories
Use enums directly with Faker:
```php
'department' => fake()->randomElement(Department::cases()),
'gender' => fake()->randomElement(Gender::cases()),
```

### Controllers
- Inline validation in store/update methods (not Form Request classes)
- Use `DB::transaction()` for multi-model operations (e.g., creating User + Employee together)
- Pass enum cases to views: `'departments' => Department::cases()`

## Testing
- Framework: **Pest** (not PHPUnit directly)
- Feature tests use `RefreshDatabase` trait (configured in `tests/Pest.php`)
- Run: `composer test` or `php artisan test`

## Views
- Blade components: `<x-app-layout>`, `<x-text-input>`, etc.
- Layout: Sidebar navigation + sticky header
- Forms: Use `@selected()` for enum dropdowns, `@if(session('success'))` for flash messages

## File Locations
| Purpose | Location |
|---------|----------|
| Enums | `app/Enums/` |
| System trait | `app/Traits/HasSystemFields.php` |
| Migration helper | `app/Support/MigrationHelper.php` |
| Factories | `database/factories/` |
| Blade components | `resources/views/components/` |
