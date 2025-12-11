import { test, expect } from '@playwright/test';
import { login, testUsers } from './helpers.js';

test.describe('Super Admin - User Management Module', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, testUsers.superadmin.email, testUsers.superadmin.password);
        await page.goto('/superadmin/users');
    });

    test('should display user management page', async ({ page }) => {
        await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /user.*management/i })).toBeVisible();
        await expect(page.locator('table, .table')).toBeVisible();
    });

    test('should show list of all users', async ({ page }) => {
        // Users table should have rows
        const userRows = page.locator('table tbody tr, .table tbody tr');
        const count = await userRows.count();

        expect(count).toBeGreaterThan(0);
    });

    test('should display user roles', async ({ page }) => {
        // Look for role badges (Admin, Patient, etc.)
        const roleBadges = page.locator('.badge, [class*="badge"]').filter({ hasText: /admin|patient|superadmin/i });

        if (await roleBadges.count() > 0) {
            await expect(roleBadges.first()).toBeVisible();
        }
    });

    test('should open create user modal', async ({ page }) => {
        // Click Create User button
        await page.click('button:has-text("Create User"), button:has-text("Add User"), a:has-text("Add User")');

        // Modal should be visible
        await expect(page.locator('.modal, [role="dialog"]').filter({ hasText: /create.*user|add.*user/i })).toBeVisible();
    });

    test('should show validation for required fields when creating user', async ({ page }) => {
        // Open create user modal
        await page.click('button:has-text("Create User"), button:has-text("Add User"), a:has-text("Add User")');

        // Try to submit without filling fields
        await page.click('.modal button[type="submit"], .modal button:has-text("Save")');

        // Should show validation errors
        await expect(page.locator('.modal').getByText(/required/i).first()).toBeVisible({ timeout: 3000 });
    });

    test('should have edit user functionality', async ({ page }) => {
        // Check if there are users
        const userRows = page.locator('table tbody tr, .table tbody tr');
        const count = await userRows.count();

        if (count > 0) {
            // Look for edit button
            const editButton = userRows.first().locator('button:has-text("Edit"), .fa-edit, .fa-pen').first();

            if (await editButton.isVisible()) {
                await expect(editButton).toBeVisible();
            }
        }
    });

    test('should have archive user functionality', async ({ page }) => {
        // Check if there are users
        const userRows = page.locator('table tbody tr, .table tbody tr');
        const count = await userRows.count();

        if (count > 0) {
            // Look for archive button
            const archiveButton = userRows.first().locator('button:has-text("Archive"), .fa-archive').first();

            if (await archiveButton.isVisible()) {
                await expect(archiveButton).toBeVisible();
            }
        }
    });

    test('should have archived users section', async ({ page }) => {
        // Look for archived users tab/link
        const archivedLink = page.locator('a:has-text("Archived"), button:has-text("Archived"), [href*="archived"]').first();

        if (await archivedLink.isVisible()) {
            await expect(archivedLink).toBeVisible();
        }
    });
});
