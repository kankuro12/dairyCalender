# Route Validation System

## Overview
This system prevents route-related errors by validating that all `route()` calls in your codebase reference routes that actually exist.

## Usage

### Validate All Routes
```bash
php artisan route:validate
```

This command will:
- ✅ Scan all controllers and views
- ✅ Find all `route('...')` references
- ✅ Compare them against defined routes in `routes/web.php`
- ✅ Report any mismatches

### Show All Route References
```bash
php artisan route:validate --show-all
```

This will display every route reference found, including valid ones.

## When to Run

Run this command:
- ✅ Before committing code changes
- ✅ After modifying routes in `routes/web.php`
- ✅ After refactoring controllers
- ✅ When you get "Route not defined" errors
- ✅ As part of your CI/CD pipeline

## Example Output

### ✅ Success (All routes valid)
```
🔍 Validating route references...

✓ Found 20 defined routes
✓ Found 19 route references in code

✅ All route references are valid!
```

### ❌ Error (Invalid routes found)
```
🔍 Validating route references...

✓ Found 20 defined routes
✓ Found 19 route references in code

❌ Found 1 invalid route reference(s):

  Route: admin.events.logo
  File:  app\Http/Controllers\SettingController.php:144
  Code:  ->route('admin.events.logo')

💡 Tip: Run "php artisan route:list" to see all available routes
```

## Integration with Git Hooks

To automatically validate routes before each commit, add this to `.git/hooks/pre-commit`:

```bash
#!/bin/sh
php artisan route:validate

if [ $? -ne 0 ]; then
    echo ""
    echo "❌ Route validation failed. Please fix the invalid routes before committing."
    exit 1
fi
```

Make it executable:
```bash
chmod +x .git/hooks/pre-commit
```

## Best Practices

1. **Always use named routes**: Define route names in `routes/web.php`
   ```php
   Route::get('/admin', [Controller::class, 'index'])->name('admin.index');
   ```

2. **Use consistent naming**: Follow the pattern `{prefix}.{resource}.{action}`
   ```php
   admin.settings.logo
   admin.announcements.index
   admin.month.data.edit
   ```

3. **Run validation frequently**: Don't wait for production errors

4. **Keep route names in sync**: When renaming routes, update all references

## Files Scanned

The validator automatically scans:
- `app/Http/Controllers/**/*.php`
- `resources/views/**/*.blade.php`

## Route Naming Convention

This project follows this naming pattern:

```
{area}.{feature}.{action}
```

Examples:
- `admin.index` - Admin dashboard
- `admin.settings.logo` - Settings logo page
- `admin.announcements.index` - Announcements list
- `admin.announcements.store` - Create announcement
- `admin.announcements.update` - Update announcement
- `admin.announcements.destroy` - Delete announcement
- `calendar.index` - Public calendar
- `calendar.data` - Calendar AJAX data

## Troubleshooting

### "Route not defined" error
1. Run `php artisan route:validate` to find the issue
2. Check `php artisan route:list` to see all available routes
3. Update the code to use the correct route name

### Route exists but validator says it doesn't
1. Clear caches: `php artisan route:clear && php artisan config:clear`
2. Check if the route is defined in `routes/web.php`
3. Ensure the route has a `->name('...')` method call

## Fixed Issues

### 2026-01-16: admin.events.logo → admin.settings.logo
- **Problem**: SettingController was using non-existent route `admin.events.logo`
- **Solution**: Changed to correct route `admin.settings.logo` in 3 locations
- **Impact**: Settings form now works correctly without 404 errors
