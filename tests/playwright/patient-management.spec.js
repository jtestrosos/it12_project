import { test, expect } from '@playwright/test';
import { login, testUsers } from './helpers.js';

test.describe('Patient Management Module', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, testUsers.admin.email, testUsers.admin.password);
        await page.goto('/admin/patients');
    });

    test('should display patients page', async ({ page }) => {
        await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /patient.*management/i })).toBeVisible();
        await expect(page.locator('table, .table')).toBeVisible();
    });

    test('should open add patient modal', async ({ page }) => {
        // Click Add Patient button
        await page.click('button:has-text("Add Patient"), a:has-text("Add Patient")');

        // Modal should be visible
        await expect(page.locator('.modal, [role="dialog"]').filter({ hasText: /add.*patient/i })).toBeVisible();
    });

    test('should show validation errors for missing required fields', async ({ page }) => {
        // Open add patient modal
        await page.click('button:has-text("Add Patient"), a:has-text("Add Patient")');

        // Try to submit without filling fields
        await page.click('.modal button[type="submit"], .modal button:has-text("Save")');

        // Should show validation errors
        await expect(page.locator('.modal').getByText(/required/i).first()).toBeVisible({ timeout: 3000 });
    });

    test('should be able to search/filter patients', async ({ page }) => {
        // Look for search input
        const searchInput = page.locator('input[type="search"], input[placeholder*="Search"]').first();

        if (await searchInput.isVisible()) {
            await searchInput.fill('Test');
            await page.waitForTimeout(500); // Wait for search to process

            // Table should still be visible
            await expect(page.locator('table, .table')).toBeVisible();
        }
    });

    test('should display patient actions (edit, archive)', async ({ page }) => {
        // Check if there are any patients in the table
        const tableRows = page.locator('table tbody tr, .table tbody tr');
        const rowCount = await tableRows.count();

        if (rowCount > 0) {
            // Check for action buttons in first row
            const firstRow = tableRows.first();
            const hasEditButton = await firstRow.locator('button:has-text("Edit"), a:has-text("Edit"), .fa-edit, .fa-pen').count() > 0;
            const hasArchiveButton = await firstRow.locator('button:has-text("Archive"), .fa-archive').count() > 0;

            expect(hasEditButton || hasArchiveButton).toBeTruthy();
        }
    });
});
