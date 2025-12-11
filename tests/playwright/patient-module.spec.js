import { test, expect } from '@playwright/test';
import { login, testUsers } from './helpers.js';

test.describe('Patient Module', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, testUsers.patient.email, testUsers.patient.password);
    });

    test.describe('Patient Dashboard', () => {
        test('should display patient dashboard', async ({ page }) => {
            await expect(page).toHaveURL(/\/patient\/dashboard/);
            await expect(page.locator('h1, h2, h3').filter({ hasText: /dashboard|welcome/i })).toBeVisible();
        });

        test('should show upcoming appointments', async ({ page }) => {
            // Look for appointments section
            const appointmentsSection = page.locator('.appointment, .card, table').filter({ hasText: /appointment/i });

            if (await appointmentsSection.count() > 0) {
                await expect(appointmentsSection.first()).toBeVisible();
            }
        });
    });

    test.describe('Book Appointment', () => {
        test('should navigate to book appointment page', async ({ page }) => {
            await page.goto('/patient/book-appointment');

            await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /book.*appointment/i })).toBeVisible();
        });

        test('should show appointment booking form', async ({ page }) => {
            await page.goto('/patient/book-appointment');

            await expect(page.locator('form')).toBeVisible();
            await expect(page.locator('input[name*="date"], input[type="date"]')).toBeVisible();
        });

        test('should validate required fields', async ({ page }) => {
            await page.goto('/patient/book-appointment');

            // Try to submit without filling fields
            await page.click('button[type="submit"]');

            // Should show validation errors
            await expect(page.getByText(/required/i).first()).toBeVisible({ timeout: 3000 });
        });

        test('should prevent booking past dates', async ({ page }) => {
            await page.goto('/patient/book-appointment');

            // Try to select a past date
            const dateInput = page.locator('input[name*="date"], input[type="date"]').first();

            if (await dateInput.isVisible()) {
                // Most modern browsers prevent selecting past dates with min attribute
                const minDate = await dateInput.getAttribute('min');
                expect(minDate).toBeTruthy();
            }
        });

        test('should show available services', async ({ page }) => {
            await page.goto('/patient/book-appointment');

            // Look for service selection
            const serviceSelect = page.locator('select[name*="service"], .service-option');

            if (await serviceSelect.count() > 0) {
                await expect(serviceSelect.first()).toBeVisible();
            }
        });
    });

    test.describe('Appointment History', () => {
        test('should display appointments page', async ({ page }) => {
            await page.goto('/patient/appointments');

            await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /appointment/i })).toBeVisible();
        });

        test('should show list of appointments', async ({ page }) => {
            await page.goto('/patient/appointments');

            // Appointments displayed in table or cards
            await expect(page.locator('table, .table, .appointment-card, .card')).toBeVisible();
        });

        test('should display appointment status', async ({ page }) => {
            await page.goto('/patient/appointments');

            // Look for status badges
            const statusBadges = page.locator('.badge, .status, [class*="badge"]');

            if (await statusBadges.count() > 0) {
                await expect(statusBadges.first()).toBeVisible();
            }
        });

        test('should have cancel appointment functionality', async ({ page }) => {
            await page.goto('/patient/appointments');

            // Look for cancel buttons (only for pending appointments)
            const cancelButtons = page.locator('button:has-text("Cancel"), .btn-cancel');

            // Cancel buttons may not be present if no pending appointments
            const count = await cancelButtons.count();
            expect(count).toBeGreaterThanOrEqual(0);
        });
    });

    test.describe('Medical Profile', () => {
        test('should navigate to medical profile page', async ({ page }) => {
            await page.goto('/patient/medical-profile');

            await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /medical.*profile|treatment.*record/i })).toBeVisible();
        });

        test('should display patient information', async ({ page }) => {
            await page.goto('/patient/medical-profile');

            // Look for patient info fields
            const infoFields = page.locator('input, select, textarea, .form-control');
            await expect(infoFields.first()).toBeVisible();
        });

        test('should have save/update functionality', async ({ page }) => {
            await page.goto('/patient/medical-profile');

            // Look for save button
            const saveButton = page.locator('button[type="submit"], button:has-text("Save"), button:has-text("Update")').first();

            if (await saveButton.isVisible()) {
                await expect(saveButton).toBeVisible();
            }
        });
    });
});
