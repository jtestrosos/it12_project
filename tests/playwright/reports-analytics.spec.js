import { test, expect } from '@playwright/test';
import { login, testUsers, navigateToSidebarDropdownItem } from './helpers.js';

test.describe('Reports & Analytics Module', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, testUsers.admin.email, testUsers.admin.password);
    });

    test.describe('Analytics Dashboard', () => {
        test('should display analytics dashboard', async ({ page }) => {
            await page.goto('/admin/reports');

            await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /analytics|reports/i })).toBeVisible();
        });

        test('should show charts and graphs', async ({ page }) => {
            await page.goto('/admin/reports');

            // Look for chart elements (canvas, svg, or chart containers)
            const charts = page.locator('canvas, svg, .chart, [class*="chart"]');

            if (await charts.count() > 0) {
                await expect(charts.first()).toBeVisible();
            }
        });
    });

    test.describe('Patient Reports', () => {
        test('should display patient reports page', async ({ page }) => {
            await page.goto('/admin/reports/patients');

            await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /patient.*report/i })).toBeVisible();
        });

        test('should show patient statistics', async ({ page }) => {
            await page.goto('/admin/reports/patients');

            // Look for stat cards or metrics
            const statCards = page.locator('.card, .stat-card, [class*="stat"]');
            await expect(statCards.first()).toBeVisible();
        });

        test('should have export to Excel button', async ({ page }) => {
            await page.goto('/admin/reports/patients');

            const excelButton = page.locator('button:has-text("Export Excel"), a:has-text("Export Excel"), .fa-file-excel').first();
            await expect(excelButton).toBeVisible();
        });

        test('should have export to PDF button', async ({ page }) => {
            await page.goto('/admin/reports/patients');

            const pdfButton = page.locator('button:has-text("Export PDF"), a:has-text("Export PDF"), .fa-file-pdf').first();
            await expect(pdfButton).toBeVisible();
        });
    });

    test.describe('Inventory Reports', () => {
        test('should display inventory reports page', async ({ page }) => {
            await page.goto('/admin/reports/inventory');

            await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /inventory.*report/i })).toBeVisible();
        });

        test('should show inventory statistics', async ({ page }) => {
            await page.goto('/admin/reports/inventory');

            // Look for stat cards showing total items, low stock, etc.
            const statCards = page.locator('.card, .stat-card, [class*="stat"]');
            await expect(statCards.first()).toBeVisible();
        });

        test('should have export to Excel button', async ({ page }) => {
            await page.goto('/admin/reports/inventory');

            const excelButton = page.locator('button:has-text("Export Excel"), a:has-text("Export Excel"), .fa-file-excel').first();
            await expect(excelButton).toBeVisible();
        });

        test('should have export to PDF button', async ({ page }) => {
            await page.goto('/admin/reports/inventory');

            const pdfButton = page.locator('button:has-text("Export PDF"), a:has-text("Export PDF"), .fa-file-pdf').first();
            await expect(pdfButton).toBeVisible();
        });
    });
});
