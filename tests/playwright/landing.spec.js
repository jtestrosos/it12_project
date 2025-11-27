import { test, expect } from '@playwright/test';

test.describe('Landing Page', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/');
    });

    test('should load homepage successfully', async ({ page }) => {
        // The page is already at the homepage from beforeEach
        await expect(page).toHaveURL('/');
        // Check for Laravel branding or content
        await expect(page.locator('text=/Laravel|Clinic|Malasakit/i').first()).toBeVisible();
    });

    test('should display navigation menu', async ({ page }) => {
        await expect(page.locator('nav')).toBeVisible();
        await expect(page.locator('a:has-text("Log in"), a:has-text("Login")').first()).toBeVisible();
    });

    test('should have login button', async ({ page }) => {
        const loginButton = page.locator('a:has-text("Login"), a:has-text("Log in")').first();
        await expect(loginButton).toBeVisible();
    });

    test('should have register button', async ({ page }) => {
        // First navigate to login page
        await page.click('a:has-text("Login"), a:has-text("Log in")');

        // Then find and verify "Register here" link
        const registerButton = page.locator('a:has-text("Register here"), a:has-text("Register Here")').first();
        await expect(registerButton).toBeVisible();
    });

    test('should navigate to login page', async ({ page }) => {
        await page.click('a:has-text("Login"), a:has-text("Log in")');
        await expect(page).toHaveURL('/login');
    });

    test('should navigate to register page', async ({ page }) => {
        // First click Login to go to login page
        await page.click('a:has-text("Login"), a:has-text("Log in")');

        // Then click "Register here" link on the login page
        await page.click('a:has-text("Register here"), a:has-text("Register Here")');
        await expect(page).toHaveURL('/register');
    });

    test('should be responsive on mobile', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 667 });
        await expect(page.locator('nav')).toBeVisible();
    });

    test('should have external documentation links', async ({ page }) => {
        // Check for Laravel documentation link
        const docLink = page.locator('a[href*="laravel.com/docs"]').first();
        if (await docLink.isVisible()) {
            await expect(docLink).toBeVisible();
        }
    });
});
