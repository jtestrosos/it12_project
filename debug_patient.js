import { chromium } from 'playwright';

(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage();

    console.log('--- Debugging Patient Login ---');
    try {
        await page.goto('http://127.0.0.1:8000/login');

        await page.fill('input[name="email"]', 'patient@malasakit.com');
        await page.fill('input[name="password"]', 'Password123@');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        console.log('After Login URL:', page.url());
        if (page.url().includes('dashboard')) {
            console.log('Login successful!');
            const content = await page.content();
            console.log('Dashboard Content Preview (first 1000 chars):', content.substring(0, 1000));

            // Check for headers
            const headers = await page.locator('h1, h2').allTextContents();
            console.log('Headers found:', headers);
        } else {
            console.log('Login failed or redirected elsewhere.');
        }
    } catch (e) {
        console.error('Login Error:', e);
    }

    console.log('\n--- Debugging Registration ---');
    try {
        await page.goto('http://127.0.0.1:8000/register');

        const inputs = await page.locator('input').all();
        console.log('Input names found:');
        for (const input of inputs) {
            const name = await input.getAttribute('name');
            const type = await input.getAttribute('type');
            console.log(`- Name: ${name}, Type: ${type} `);
        }

        const selects = await page.locator('select').all();
        console.log('Select names found:');
        for (const select of selects) {
            const name = await select.getAttribute('name');
            console.log(`- Name: ${name} `);
        }

    } catch (e) {
        console.error('Registration Error:', e);
    }

    await browser.close();
})();
