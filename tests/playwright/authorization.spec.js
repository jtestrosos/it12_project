import { test, expect } from '@playwright/test';
import { login, logout, testUsers } from './helpers.js';

test.describe('Authorization Module', () => {
    test('should prevent Patient from accessing Admin Dashboard', async ({ page }) => {
        await login(page, testUsers.patient.email, testUsers.patient.password);

        // Try to access admin dashboard
        await page.goto('/admin/dashboard');

        // Should be redirected away from admin dashboard
        await expect(page).not.toHaveURL(/\/admin\/dashboard/);
    });

    test('should prevent Guest from accessing protected pages', async ({ page }) => {
        // Try to access appointments without logging in
        await page.goto('/patient/appointments');

        // Should be redirected to login page
        await expect(page).toHaveURL(/\/login/);
    });

    test('should allow Admin to access Admin Dashboard', async ({ page }) => {
        await login(page, testUsers.admin.email, testUsers.admin.password);

        // Should be on admin dashboard
        await expect(page).toHaveURL(/\/admin\/dashboard/);
        await expect(page.locator('h1, h2, h3').filter({ hasText: /dashboard/i })).toBeVisible();
    });

    test('should prevent Admin from accessing Super Admin features', async ({ page }) => {
        await login(page, testUsers.admin.email, testUsers.admin.password);

        // Try to access super admin users page
        await page.goto('/superadmin/users');

        // Should be redirected or show unauthorized
        await expect(page).not.toHaveURL(/\/superadmin\/users/);
    });

    test('should allow Super Admin to access User Management', async ({ page }) => {
        await login(page, testUsers.superadmin.email, testUsers.superadmin.password);

        // Navigate to user management
        await page.goto('/superadmin/users');

        // Should be on user management page
        await expect(page).toHaveURL(/\/superadmin\/users/);
        await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /user.*management/i })).toBeVisible();
    });

    test('should prevent Patient from accessing Admin/Inventory pages', async ({ page }) => {
        await login(page, testUsers.patient.email, testUsers.patient.password);

        // Try to access inventory
        await page.goto('/admin/inventory');

        // Should be redirected away
        await expect(page).not.toHaveURL(/\/admin\/inventory/);
    });
});
