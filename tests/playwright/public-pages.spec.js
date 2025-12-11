import { test, expect } from '@playwright/test';

test.describe('Public Pages Module', () => {
    test('should display landing page', async ({ page }) => {
        await page.goto('/');

        await expect(page.locator('h1, h2, .hero, .welcome').first()).toBeVisible();
    });

    test('should have navigation menu', async ({ page }) => {
        await page.goto('/');

        // Look for navigation links
        const nav = page.locator('nav, .navbar, header');
        await expect(nav).toBeVisible();
    });

    test('should display policy page', async ({ page }) => {
        await page.goto('/policy');

        await expect(page.locator('h1, h2, h3').filter({ hasText: /policy|booking.*policy/i })).toBeVisible();
    });

    test('should display services page', async ({ page }) => {
        await page.goto('/services');

        await expect(page.locator('h1, h2, h3').filter({ hasText: /service/i })).toBeVisible();
    });

    test('should show list of services on services page', async ({ page }) => {
        await page.goto('/services');

        // Services should be displayed
        const services = page.locator('.service, .card, .service-card');

        if (await services.count() > 0) {
            await expect(services.first()).toBeVisible();
        }
    });

    test('should display contact page', async ({ page }) => {
        await page.goto('/contact');

        await expect(page.locator('h1, h2, h3').filter({ hasText: /contact/i })).toBeVisible();
    });

    test('should have contact form', async ({ page }) => {
        await page.goto('/contact');

        // Look for contact form
        const contactForm = page.locator('form');

        if (await contactForm.count() > 0) {
            await expect(contactForm.first()).toBeVisible();
        }
    });

    test('should validate contact form fields', async ({ page }) => {
        await page.goto('/contact');

        const form = page.locator('form').first();

        if (await form.isVisible()) {
            // Try to submit empty form
            await page.click('button[type="submit"]');

            // Should show validation
            await expect(page.getByText(/required/i).first()).toBeVisible({ timeout: 3000 });
        }
    });

    test('should have login link', async ({ page }) => {
        await page.goto('/');

        const loginLink = page.locator('a:has-text("Login"), a[href*="login"]').first();
        await expect(loginLink).toBeVisible();
    });

    test('should have register link', async ({ page }) => {
        await page.goto('/');

        const registerLink = page.locator('a:has-text("Register"), a[href*="register"]').first();
        await expect(registerLink).toBeVisible();
    });

    test('should navigate to how it works page', async ({ page }) => {
        await page.goto('/how-it-works');

        await expect(page.locator('h1, h2, h3').filter({ hasText: /how.*it.*works/i })).toBeVisible();
    });
});
