# Playwright Test Suite

Comprehensive end-to-end tests for the Barangay Health Center Management System.

## Test Files

### Authentication & Security
- **`auth.spec.js`** - Login, registration, and logout tests
- **`authorization.spec.js`** - Role-based access control tests

### Admin Modules
- **`patient-management.spec.js`** - Patient CRUD operations
- **`appointment-management.spec.js`** - Appointment viewing, filtering, and status updates
- **`inventory-management.spec.js`** - Inventory tracking, restocking, and alerts
- **`reports-analytics.spec.js`** - Analytics dashboard, patient reports, inventory reports
- **`services-management.spec.js`** - Health center services management

### Super Admin Modules
- **`user-management.spec.js`** - User creation, editing, and archiving
- **`system-management.spec.js`** - System logs, backups, and analytics

### Patient Modules
- **`patient-module.spec.js`** - Patient dashboard, booking, appointment history, medical profile

### Public Pages
- **`public-pages.spec.js`** - Landing page, services, contact, and navigation
- **`landing.spec.js`** - Additional landing page tests

## Prerequisites

1. **Node.js** (v16 or higher)
2. **Playwright** installed
3. **Laravel application** running on `http://127.0.0.1:8000`

## Installation

```bash
# Install Playwright
npm install -D @playwright/test

# Install browsers
npx playwright install chrome
```

## Running Tests

### Run all tests
```bash
npx playwright test
```

### Run specific test file
```bash
npx playwright test auth.spec.js
npx playwright test patient-management.spec.js
```

### Run tests in headed mode (see browser)
```bash
npx playwright test --headed
```

### Run tests in debug mode
```bash
npx playwright test --debug
```

### Run tests with UI mode
```bash
npx playwright test --ui
```

## Test Users

The tests use predefined test users from `helpers.js`:

- **Patient**: `patient@malasakit.com` / `Password123@`
- **Admin**: `admin@malasakit.com` / `password`
- **Super Admin**: `superadmin@malasakit.com` / `password`

**Important**: Ensure these users exist in your database before running tests.

## Test Reports

After running tests, view the HTML report:

```bash
npx playwright show-report
```

## Configuration

Tests are configured in `playwright.config.js`:
- Base URL: `http://127.0.0.1:8000`
- Browser: Chrome only
- Auto-starts Laravel dev server (`php artisan serve`)
- Screenshots on failure
- Video recording on failure

## Writing New Tests

1. Create a new `.spec.js` file in `tests/playwright/`
2. Import test utilities:
   ```javascript
   import { test, expect } from '@playwright/test';
   import { login, logout, testUsers } from './helpers.js';
   ```
3. Use helper functions for common tasks:
   - `login(page, email, password)` - Log in a user
   - `logout(page)` - Log out current user
   - `navigateToSidebarDropdownItem(page, dropdownText, itemText)` - Navigate sidebar

## Test Coverage

These tests cover all modules from the comprehensive test case plan:

### ✅ Authentication & Security (Table 22, 27)
- Login validation
- Registration validation
- Authorization checks

### ✅ Patient & Public Modules (Table 23, 33)
- Patient registration
- Appointment booking
- Public pages access

### ✅ Admin Modules (Tables 24-26, 30-32)
- Patient management
- Appointment management
- Inventory management
- Reports & analytics
- Services management
- Medical profile

### ✅ Super Admin Modules (Tables 28-29)
- User management
- System logs
- Backup & restore
- Analytics

## Continuous Integration

To run tests in CI:

```bash
# Set CI environment variable
CI=true npx playwright test
```

This will:
- Run tests in parallel workers
- Retry failed tests twice
- Generate JSON results

## Troubleshooting

### Tests failing due to timeout
Increase timeout in test:
```javascript
test('my test', async ({ page }) => {
    test.setTimeout(60000); // 60 seconds
    // ... test code
});
```

### Element not found
Use more flexible selectors:
```javascript
// Instead of exact text
await page.click('button:has-text("Save")');

// Use regex for flexibility
await expect(page.locator('h1').filter({ hasText: /dashboard/i })).toBeVisible();
```

### Server not starting
Ensure Laravel is properly configured:
```bash
# Check if port 8000 is available
php artisan serve

# Or manually start server before tests
php artisan serve &
npx playwright test --grep-invert "webServer"
```

## Best Practices

1. **Use data-testid attributes** for stable selectors
2. **Wait for network idle** after navigation
3. **Use flexible text matching** with regex
4. **Clean up test data** after tests
5. **Use beforeEach** for common setup
6. **Group related tests** with describe blocks

## Additional Resources

- [Playwright Documentation](https://playwright.dev)
- [Test Case Plan](../../.gemini/antigravity/brain/cd71f9ba-4a23-40e1-89c9-dce625b5288e/all_test_cases.md)
- [Component Verification Report](../../.gemini/antigravity/brain/cd71f9ba-4a23-40e1-89c9-dce625b5288e/component_verification_report.md)
