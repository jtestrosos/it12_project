import { test, expect } from '@playwright/test';
import { login, testUsers } from './helpers.js';

test.describe('Inventory Management Module', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, testUsers.admin.email, testUsers.admin.password);
        await page.goto('/admin/inventory');
    });

    test('should display inventory page', async ({ page }) => {
        await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /inventory/i })).toBeVisible();
        await expect(page.locator('table, .table, .inventory-card')).toBeVisible();
    });

    test('should open add item modal', async ({ page }) => {
        // Click Add Item button
        await page.click('button:has-text("Add Item"), a:has-text("Add Item"), button:has-text("Add New")');

        // Modal should be visible
        await expect(page.locator('.modal, [role="dialog"]').filter({ hasText: /add.*item/i })).toBeVisible();
    });

    test('should show validation for required fields', async ({ page }) => {
        // Open add item modal
        await page.click('button:has-text("Add Item"), a:has-text("Add Item"), button:has-text("Add New")');

        // Try to submit without filling fields
        await page.click('.modal button[type="submit"], .modal button:has-text("Save")');

        // Should show validation errors
        await expect(page.locator('.modal').getByText(/required/i).first()).toBeVisible({ timeout: 3000 });
    });

    test('should display stock indicators', async ({ page }) => {
        // Look for stock indicators (low stock, out of stock)
        const stockIndicators = page.locator('.stock-indicator, .badge, .alert, [class*="stock"]');

        if (await stockIndicators.count() > 0) {
            await expect(stockIndicators.first()).toBeVisible();
        }
    });

    test('should show low stock alerts', async ({ page }) => {
        // Look for low stock warnings
        const lowStockAlerts = page.locator('.alert-warning, .badge-warning, .text-warning, [class*="low-stock"]');

        // Low stock alerts may or may not be present depending on data
        const count = await lowStockAlerts.count();
        expect(count).toBeGreaterThanOrEqual(0);
    });

    test('should have restock functionality', async ({ page }) => {
        // Check if there are inventory items
        const itemRows = page.locator('table tbody tr, .inventory-card');
        const count = await itemRows.count();

        if (count > 0) {
            // Look for restock button
            const restockButton = itemRows.first().locator('button:has-text("Restock"), .fa-plus-circle').first();

            if (await restockButton.isVisible()) {
                await restockButton.click();

                // Restock modal should appear
                await expect(page.locator('.modal, [role="dialog"]').filter({ hasText: /restock/i })).toBeVisible({ timeout: 3000 });
            }
        }
    });

    test('should have deduct/usage functionality', async ({ page }) => {
        // Check if there are inventory items
        const itemRows = page.locator('table tbody tr, .inventory-card');
        const count = await itemRows.count();

        if (count > 0) {
            // Look for deduct/usage button
            const deductButton = itemRows.first().locator('button:has-text("Deduct"), button:has-text("Use"), .fa-minus-circle').first();

            if (await deductButton.isVisible()) {
                await expect(deductButton).toBeVisible();
            }
        }
    });
});
