/**
 * Test helper utilities for Playwright tests
 */

/**
 * Login helper function
 * @param {import('@playwright/test').Page} page
 * @param {string} email
 * @param {string} password
 */
export async function login(page, email, password) {
    await page.goto('/login');
    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', password);
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');
}

/**
 * Logout helper function
 * @param {import('@playwright/test').Page} page
 */
export async function logout(page) {
    const logoutSelector = 'button:has-text("Logout"), a:has-text("Logout")';
    const logoutButton = page.locator(logoutSelector).first();

    // Check if logout button is directly visible (e.g., Patient view)
    if (await logoutButton.isVisible()) {
        await logoutButton.click();
    } else {
        // If not visible, try opening the user dropdown (Admin/SuperAdmin view)
        // IDs found: dropdownUser1 (admin layout), dropdownUserApp (app layout)
        const dropdownToggle = page.locator('#dropdownUser1, #dropdownUserApp, .dropdown-toggle img');

        if (await dropdownToggle.first().isVisible()) {
            await dropdownToggle.first().click();
            // Wait for logout button to become visible in dropdown
            await logoutButton.first().waitFor({ state: 'visible', timeout: 5000 });
            await logoutButton.first().click();
        } else {
            // Fallback: try forcing a click on the logout selector if it exists in DOM but hidden
            // This handles cases where hover might be required or other UI states
            if (await logoutButton.count() > 0) {
                await logoutButton.first().click({ force: true });
            }
        }
    }

    // Wait for redirection to homepage
    await page.waitForURL('/', { timeout: 60000 }); // Increased timeout for slow server
}

/**
 * Test user credentials
 */
export const testUsers = {
    patient: {
        email: 'patient@malasakit.com',
        password: 'Password123@',
        name: 'Test Patient'
    },
    admin: {
        email: 'admin@malasakit.com',
        password: 'password',
        name: 'Test Admin'
    },
    superadmin: {
        email: 'superadmin@malasakit.com',
        password: 'password',
        name: 'Test Super Admin'
    }
};

/**
 * Wait for toast notification
 * @param {import('@playwright/test').Page} page
 * @param {string} message
 */
export async function waitForToast(page, message) {
    await page.waitForSelector(`.toast:has-text("${message}")`, { timeout: 5000 });
}

/**
 * Fill form field
 * @param {import('@playwright/test').Page} page
 * @param {string} selector
 * @param {string} value
 */
export async function fillField(page, selector, value) {
    await page.fill(selector, value);
}

/**
 * Select dropdown option
 * @param {import('@playwright/test').Page} page
 * @param {string} selector
 * @param {string} value
 */
export async function selectOption(page, selector, value) {
    await page.selectOption(selector, value);
}

/**
 * Check if element is visible
 * @param {import('@playwright/test').Page} page
 * @param {string} selector
 */
export async function isVisible(page, selector) {
    return await page.isVisible(selector);
}

/**
 * Wait for navigation
 * @param {import('@playwright/test').Page} page
 * @param {string} url
 */
export async function waitForNavigation(page, url) {
    await page.waitForURL(url);
}

/**
 * Navigate to profile page via dropdown
 * @param {import('@playwright/test').Page} page
 */
export async function navigateToProfile(page) {
    const dropdownToggle = page.locator('#dropdownUserApp, #dropdownUser1, .dropdown-toggle').first();
    await dropdownToggle.click();
    await page.waitForSelector('.dropdown-menu', { state: 'visible', timeout: 3000 });
    await page.click('a:has-text("Profile")');
}

/**
 * Navigate to a sidebar dropdown item (e.g., Services, Reports)
 * @param {import('@playwright/test').Page} page
 * @param {string} dropdownText - Text of the dropdown toggle (e.g., "Services & Reports")
 * @param {string} itemText - Text of the item to click (e.g., "Manage Services")
 */
export async function navigateToSidebarDropdownItem(page, dropdownText, itemText) {
    // Find and click the dropdown toggle
    const dropdownToggle = page.locator(`.nav-link:has-text("${dropdownText}")`).first();

    // Check if dropdown is already open by looking for the submenu
    const submenu = page.locator('.submenu').first();
    const isVisible = await submenu.isVisible().catch(() => false);

    if (!isVisible) {
        await dropdownToggle.click();
        // Wait for submenu to appear
        await page.waitForSelector('.submenu', { state: 'visible', timeout: 3000 });
    }

    // Click the item in the submenu
    await page.click(`.submenu a:has-text("${itemText}")`);
}
