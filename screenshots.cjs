const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  
  // Chat page desktop
  const page = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page.goto('http://127.0.0.1:8000/chat', { waitUntil: 'networkidle' });
  await page.waitForTimeout(1000);
  await page.screenshot({ path: 'storage/app/diagnostico/chat-desktop.png', fullPage: true });
  console.log('Desktop chat screenshot saved');
  
  // Chat page mobile
  const page2 = await browser.newPage({ viewport: { width: 390, height: 844 } });
  await page2.goto('http://127.0.0.1:8000/chat', { waitUntil: 'networkidle' });
  await page2.waitForTimeout(1000);
  await page2.screenshot({ path: 'storage/app/diagnostico/chat-mobile.png', fullPage: true });
  console.log('Mobile chat screenshot saved');
  
  // Chat page with widget open (landing page)
  const page3 = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page3.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page3.waitForTimeout(500);
  // Click the chat widget button if exists
  const widgetBtn = await page3.locator('button[aria-label*="Abrir"]').first();
  if (await widgetBtn.count() > 0) {
    await widgetBtn.click();
    await page3.waitForTimeout(500);
  }
  await page3.screenshot({ path: 'storage/app/diagnostico/widget-open-desktop.png', fullPage: true });
  console.log('Widget open desktop screenshot saved');
  
  await browser.close();
})();