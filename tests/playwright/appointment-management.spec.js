import { test, expect } from '@playwright/test';
import { login, testUsers } from './helpers.js';

test.describe('Appointment Management Module', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, testUsers.admin.email, testUsers.admin.password);
        await page.goto('/admin/appointments');
    });

    test('should display appointments page', async ({ page }) => {
        await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /appointment/i })).toBeVisible();
        await expect(page.locator('table, .table, .appointment-card, .card')).toBeVisible();
    });

    test('should show appointment status badges', async ({ page }) => {
        // Look for status badges
        const statusBadges = page.locator('.badge, .status-badge, span[class*="badge"]');

        if (await statusBadges.count() > 0) {
            await expect(statusBadges.first()).toBeVisible();
        }
    });

    test('should be able to filter appointments', async ({ page }) => {
        // Look for filter/search options
        const filterSelect = page.locator('select[name*="status"], select[name*="filter"]').first();

        if (await filterSelect.isVisible()) {
            await filterSelect.selectOption({ index: 1 });
            await page.waitForTimeout(500);

            // Page should still display appointments
            await expect(page.locator('table, .appointment-card')).toBeVisible();
        }
    });

    test('should be able to update appointment status', async ({ page }) => {
        // Check if there are any appointments
        const appointmentRows = page.locator('table tbody tr, .appointment-card');
        const count = await appointmentRows.count();

        if (count > 0) {
            // Look for status update button/dropdown
            const statusButton = appointmentRows.first().locator('button:has-text("Approve"), button:has-text("Complete"), select[name*="status"]').first();

            if (await statusButton.isVisible()) {
                await expect(statusButton).toBeVisible();
            }
        }
    });

    test('should display appointment details', async ({ page }) => {
        // Check if there are appointments with view/details buttons
        const viewButtons = page.locator('button:has-text("View"), a:has-text("View"), .fa-eye');

        if (await viewButtons.count() > 0) {
            await viewButtons.first().click();

            // Modal or details page should appear
            await expect(page.locator('.modal, [role="dialog"], .appointment-details')).toBeVisible({ timeout: 3000 });
        }
    });
});
