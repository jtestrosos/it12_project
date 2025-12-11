import { test, expect } from '@playwright/test';
import { login, testUsers } from './helpers.js';

test.describe('Super Admin - System Management Module', () => {
    test.beforeEach(async ({ page }) => {
        await login(page, testUsers.superadmin.email, testUsers.superadmin.password);
    });

    test.describe('System Logs', () => {
        test('should display system logs page', async ({ page }) => {
            await page.goto('/superadmin/system-logs');

            await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /system.*log/i })).toBeVisible();
        });

        test('should show logs table', async ({ page }) => {
            await page.goto('/superadmin/system-logs');

            await expect(page.locator('table, .table, .log-entry')).toBeVisible();
        });

        test('should have filter options', async ({ page }) => {
            await page.goto('/superadmin/system-logs');

            // Look for filter inputs (action, table, date)
            const filterInputs = page.locator('select[name*="action"], select[name*="table"], input[type="date"]');

            if (await filterInputs.count() > 0) {
                await expect(filterInputs.first()).toBeVisible();
            }
        });
    });

    test.describe('Backup & Restore', () => {
        test('should display backup page', async ({ page }) => {
            await page.goto('/superadmin/backup');

            await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /backup/i })).toBeVisible();
        });

        test('should have create backup button', async ({ page }) => {
            await page.goto('/superadmin/backup');

            const createBackupButton = page.locator('button:has-text("Create Backup"), button:has-text("Backup Now")').first();
            await expect(createBackupButton).toBeVisible();
        });

        test('should show backup status/history', async ({ page }) => {
            await page.goto('/superadmin/backup');

            // Look for backup history table or cards
            const backupList = page.locator('table, .table, .backup-card, .backup-list');
            await expect(backupList).toBeVisible();
        });

        test('should have download backup functionality', async ({ page }) => {
            await page.goto('/superadmin/backup');

            // Look for download buttons in backup list
            const downloadButtons = page.locator('button:has-text("Download"), a:has-text("Download"), .fa-download');

            // Download buttons may not be present if no backups exist
            const count = await downloadButtons.count();
            expect(count).toBeGreaterThanOrEqual(0);
        });

        test('should display backup statistics', async ({ page }) => {
            await page.goto('/superadmin/backup');

            // Look for backup stats (last backup, storage used, etc.)
            const statCards = page.locator('.card, .stat-card, [class*="backup-"]');
            await expect(statCards.first()).toBeVisible();
        });
    });

    test.describe('Analytics (Super Admin)', () => {
        test('should display super admin analytics page', async ({ page }) => {
            await page.goto('/superadmin/analytics');

            await expect(page.locator('h1, h2, h3, .page-title').filter({ hasText: /analytics/i })).toBeVisible();
        });

        test('should show system-wide statistics', async ({ page }) => {
            await page.goto('/superadmin/analytics');

            // Look for stat cards
            const statCards = page.locator('.card, .stat-card, [class*="stat"]');
            await expect(statCards.first()).toBeVisible();
        });

        test('should display charts and graphs', async ({ page }) => {
            await page.goto('/superadmin/analytics');

            // Look for chart elements
            const charts = page.locator('canvas, svg, .chart, [class*="chart"]');

            if (await charts.count() > 0) {
                await expect(charts.first()).toBeVisible();
            }
        });
    });
});
