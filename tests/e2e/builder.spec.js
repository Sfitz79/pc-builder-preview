import { test, expect } from '@playwright/test';

test.describe('PCTG Builder — Alpine interactions (real browser, real backend)', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'demo@pc-tg.co.uk');
    await page.fill('#password', 'password');
    await Promise.all([
      page.waitForURL('**/builder', { timeout: 20000 }),
      page.click('button[type="submit"]'),
    ]);
  });

  test('AI build -> compatibility -> FPS -> save -> load', async ({ page }) => {
    await page.waitForSelector('[x-data="builderState()"]');
    await expect(page.getByRole('button', { name: 'Generate AI Build' })).toBeVisible();

    await page.getByRole('button', { name: 'Gaming' }).click();
    await page.getByRole('button', { name: 'Generate AI Build' }).click();

    const fpsCS2 = page.getByText('CS2', { exact: true });
    await expect(fpsCS2).toBeVisible({ timeout: 20000 });
    await expect(page.getByText('340 FPS', { exact: true })).toBeVisible();

    await expect(page.getByText('Intel Core i5-14600K', { exact: true }).first()).toBeVisible();
    await expect(page.getByText('Gigabyte Z790 Aorus', { exact: true }).first()).toBeVisible();
    await expect(page.getByText('Compatibility Warning')).toBeHidden();

    await page.getByRole('button', { name: 'Save Build' }).click();

    const publicLink = page.locator('a[href*="/build/"]').last();
    await expect(publicLink).toBeVisible({ timeout: 20000 });
    const href = await publicLink.getAttribute('href');
    expect(href).toMatch(/\/build\/[A-Za-z0-9]+$/);

    const option = page.locator('select[x-model="selectedBuildId"] option', {
      hasText: 'Gaming Build',
    });
    await expect(option.first()).toHaveCount(1, { timeout: 20000 });
  });

  test('incompatible swap trips the compatibility warning', async ({ page }) => {
    await page.waitForSelector('[x-data="builderState()"]');
    await page.getByRole('button', { name: 'Generate AI Build' }).click();
    await expect(page.getByText('CS2', { exact: true })).toBeVisible({ timeout: 20000 });

    await page.getByRole('button', { name: 'Change Motherboard' }).click();
    await expect(page.getByText('Selector', { exact: false })).toBeVisible();

    await page.getByRole('button', { name: 'ASUS B650-A Gaming' }).click();

    const warning = page.getByText('Compatibility Warning');
    await expect(warning).toBeVisible({ timeout: 20000 });

    const cpuRow = page.getByText('CPU + Motherboard').locator('xpath=following-sibling::span');
    await expect(cpuRow).toHaveClass(/text-red-400/);
  });
});

test.describe('PCTG Builder — guest (no login)', () => {
  test('AI build -> save -> load works without authentication', async ({ page }) => {
    await page.goto('/builder');
    await page.waitForSelector('[x-data="builderState()"]');
    await expect(page.getByRole('button', { name: 'Generate AI Build' })).toBeVisible();

    await page.getByRole('button', { name: 'Gaming' }).click();
    await page.getByRole('button', { name: 'Generate AI Build' }).click();
    await expect(page.getByText('CS2', { exact: true })).toBeVisible({ timeout: 20000 });

    await page.getByRole('button', { name: 'Save Build' }).click();

    const publicLink = page.locator('a[href*="/build/"]').last();
    await expect(publicLink).toBeVisible({ timeout: 20000 });
    const href = await publicLink.getAttribute('href');
    expect(href).toMatch(/\/build\/[A-Za-z0-9]+$/);

    const option = page.locator('select[x-model="selectedBuildId"] option', {
      hasText: 'Gaming Build',
    });
    await expect(option.first()).toHaveCount(1, { timeout: 20000 });

    await page.reload();
    await page.waitForSelector('[x-data="builderState()"]');
    await expect(
      page.locator('select[x-model="selectedBuildId"] option', { hasText: 'Gaming Build' }).first()
    ).toHaveCount(1, { timeout: 20000 });
  });
});
