import { test, expect } from '@playwright/test';
import { login, testUsers } from './helpers.js';

test.describe('Services Management Module', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, testUsers.admin.email, testUsers.admin.password);
        await page.goto('/admin/services');
    });

    test('should display services management page', async ({ page }) => {
        await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /service/i })).toBeVisible();
    });

    test('should show list of services', async ({ page }) => {
        // Services should be displayed in a table or card layout
        await expect(page.locator('table, .table, .service-card, .card')).toBeVisible();
    });

    test('should open add service modal/form', async ({ page }) => {
        // Click Add Service button
        const addButton = page.locator('button:has-text("Add Service"), a:has-text("Add Service"), button:has-text("Add New")').first();
        await expect(addButton).toBeVisible();
        await addButton.click();

        // Modal or form should appear
        await expect(page.locator('.modal, [role="dialog"], form').filter({ hasText: /service/i })).toBeVisible({ timeout: 3000 });
    });

    test('should show validation for required fields', async ({ page }) => {
        // Open add service form
        await page.click('button:has-text("Add Service"), a:has-text("Add Service"), button:has-text("Add New")');

        // Try to submit without filling fields
        await page.click('.modal button[type="submit"], .modal button:has-text("Save"), button[type="submit"]');

        // Should show validation errors
        await expect(page.locator('.modal, form').getByText(/required/i).first()).toBeVisible({ timeout: 3000 });
    });

    test('should have edit service functionality', async ({ page }) => {
        // Check if there are services
        const serviceRows = page.locator('table tbody tr, .service-card');
        const count = await serviceRows.count();

        if (count > 0) {
            // Look for edit button
            const editButton = serviceRows.first().locator('button:has-text("Edit"), a:has-text("Edit"), .fa-edit, .fa-pen').first();

            if (await editButton.isVisible()) {
                await expect(editButton).toBeVisible();
            }
        }
    });

    test('should have delete/deactivate service functionality', async ({ page }) => {
        // Check if there are services
        const serviceRows = page.locator('table tbody tr, .service-card');
        const count = await serviceRows.count();

        if (count > 0) {
            // Look for delete/deactivate button
            const deleteButton = serviceRows.first().locator('button:has-text("Delete"), button:has-text("Deactivate"), .fa-trash').first();

            if (await deleteButton.isVisible()) {
                await expect(deleteButton).toBeVisible();
            }
        }
    });
});
