import { test, expect } from '@playwright/test';
import { login, logout, testUsers } from './helpers.js';

test.describe('Authentication', () => {
    test.describe('Login', () => {
        test.beforeEach(async ({ page }) => {
            await page.goto('/login');
            await page.evaluate(() => document.querySelector('form').setAttribute('novalidate', 'true'));
        });

        test('should display login form', async ({ page }) => {
            await expect(page.locator('main .card-body input[name="email"]')).toBeVisible();
            await expect(page.locator('main .card-body input[name="password"]')).toBeVisible();
            await expect(page.locator('main .card-body button[type="submit"]')).toBeVisible();
        });

        test('should show validation error for empty fields', async ({ page }) => {
            await page.click('main .card-body button[type="submit"]');
            await expect(page.locator('main').getByText('The email field is required')).toBeVisible();
            await expect(page.locator('main').getByText('The password field is required')).toBeVisible();
        });

        test('should show error for invalid email format', async ({ page }) => {
            await page.fill('main .card-body input[name="email"]', 'invalid-email');
            await page.fill('main .card-body input[name="password"]', 'password123');
            await page.click('main .card-body button[type="submit"]');
            // Expecting "The email must be a valid email address." or similar
            await expect(page.locator('main').getByText(/valid.*email/i)).toBeVisible();
        });

        test('should show error for invalid credentials', async ({ page }) => {
            await page.fill('main .card-body input[name="email"]', 'wrong@example.com');
            await page.fill('main .card-body input[name="password"]', 'wrongpassword');
            await page.click('main .card-body button[type="submit"]');
            await expect(page.locator('main').getByText('Invalid Credentials.')).toBeVisible({ timeout: 5000 });
        });

        test('should login as admin successfully', async ({ page }) => {
            await login(page, testUsers.admin.email, testUsers.admin.password);
            await expect(page).toHaveURL(/\/admin\/dashboard/);
        });

        test('should login as super admin successfully', async ({ page }) => {
            await login(page, testUsers.superadmin.email, testUsers.superadmin.password);
            await expect(page).toHaveURL(/\/superadmin\/dashboard/);
        });

        test('should have forgot password link', async ({ page }) => {
            await expect(page.locator('a:has-text("Forgot Password")')).toBeVisible();
        });

        test('should navigate to register page', async ({ page }) => {
            await page.click('text=Register here');
            await expect(page).toHaveURL('/register');
        });
    });

    test.describe('Registration', () => {
        test.beforeEach(async ({ page }) => {
            await page.goto('/register');
            await page.evaluate(() => document.querySelector('form').setAttribute('novalidate', 'true'));
        });

        test('should show validation errors for empty required fields', async ({ page }) => {
            await page.click('button[type="submit"]');
            await expect(page.locator('text=Full name is required').first()).toBeVisible();
        });

        test('should validate name field (no numbers)', async ({ page }) => {
            await page.fill('input[name="name"]', 'John123');
            await page.fill('input[name="email"]', 'test@example.com');
            await page.fill('input[name="password"]', 'Password@123');
            await page.fill('input[name="password_confirmation"]', 'Password@123');
            await page.selectOption('select[name="gender"]', 'male');
            await page.fill('input[name="birth_date"]', '2000-01-01');
            await page.selectOption('select[name="barangay"]', 'Barangay 11');
            await page.click('button[type="submit"]');
            await expect(page.locator('text=/name.*should not contain numbers/i').first()).toBeVisible({ timeout: 5000 });
        });

        test('should validate password confirmation match', async ({ page }) => {
            await page.fill('input[name="name"]', 'John Doe');
            await page.fill('input[name="email"]', 'test@example.com');
            await page.fill('input[name="password"]', 'Password@123');
            await page.fill('input[name="password_confirmation"]', 'DifferentPassword@123');
            await page.selectOption('select[name="gender"]', 'male');
            await page.fill('input[name="birth_date"]', '2000-01-01');
            await page.selectOption('select[name="barangay"]', 'Barangay 11');
            await page.click('button[type="submit"]');
            await expect(page.locator('text=/Password and confirm password must match/i').first()).toBeVisible({ timeout: 5000 });
        });

        test('should validate phone number format (11 digits)', async ({ page }) => {
            await page.fill('input[name="phone"]', '123');
            await page.fill('input[name="name"]', 'John Doe');
            await page.fill('input[name="email"]', 'test@example.com');
            await page.fill('input[name="password"]', 'Password@123');
            await page.fill('input[name="password_confirmation"]', 'Password@123');
            await page.selectOption('select[name="gender"]', 'male');
            await page.fill('input[name="birth_date"]', '2000-01-01');
            await page.selectOption('select[name="barangay"]', 'Barangay 11');
            await page.click('button[type="submit"]');
            await expect(page.locator('text=/Phone number must be exactly 11 digits/i').first()).toBeVisible({ timeout: 5000 });
        });

        test('should navigate to login page', async ({ page }) => {
            await page.click('text=Login here');
            await expect(page).toHaveURL('/login');
        });
    });

    test.describe('Logout', () => {
        test('should logout patient successfully', async ({ page }) => {
            await login(page, testUsers.patient.email, testUsers.patient.password);
            await logout(page);
            await expect(page).toHaveURL('/');
        });

        test('should logout admin successfully', async ({ page }) => {
            await login(page, testUsers.admin.email, testUsers.admin.password);
            await logout(page);
            await expect(page).toHaveURL('/');
        });
    });
});
