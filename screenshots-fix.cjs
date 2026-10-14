const { chromium } = require('playwright');

async function openWidget(page) {
  // Disable animations for this click
  await page.addStyleTag({
    content: `
      *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
      }
    `
  });
  
  const buttons = await page.locator('button[aria-label="Abrir asistente láser"]').all();
  console.log(`Found ${buttons.length} buttons with aria-label="Abrir asistente láser"`);
  
  // Click the last one (floating button should be last in DOM)
  if (buttons.length > 0) {
    const floatingBtn = buttons[buttons.length - 1];
    await floatingBtn.click({ force: true });
    // Wait for Livewire component to mount inside the widget
    await page.waitForTimeout(1500);
    return true;
  }
  
  return false;
}

async function sendMessage(page, message) {
  // Wait for textarea to be enabled and visible
  await page.waitForFunction(() => {
    const textarea = document.querySelector('textarea[aria-label="Tu mensaje"]');
    return textarea && !textarea.disabled && textarea.offsetParent !== null;
  }, { timeout: 15000 }).catch(() => console.log('Textarea not ready after timeout'));
  
  const textarea = await page.locator('textarea[aria-label="Tu mensaje"]').first();
  if (await textarea.count() > 0) {
    await textarea.fill(message);
    await page.waitForTimeout(200);
    const sendBtn = await page.locator('button[aria-label="Enviar mensaje"]').first();
    if (await sendBtn.count() > 0) {
      await sendBtn.click({ force: true });
      await page.waitForTimeout(3000); // Wait for response
      return true;
    }
  }
  return false;
}

(async () => {
  const browser = await chromium.launch();
  
  // 1. Landing desktop (light mode)
  const page1 = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page1.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page1.waitForTimeout(500);
  await page1.screenshot({ path: 'storage/app/diagnostico/fix/landing-desktop-light.png', fullPage: true });
  console.log('1. Landing desktop light saved');
  
  // 2. Landing mobile (light mode)
  const page2 = await browser.newPage({ viewport: { width: 390, height: 844 } });
  await page2.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page2.waitForTimeout(500);
  await page2.screenshot({ path: 'storage/app/diagnostico/fix/landing-mobile-light.png', fullPage: true });
  console.log('2. Landing mobile light saved');
  
  // 3. Landing desktop (dark mode)
  const page3 = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page3.emulateMedia({ colorScheme: 'dark' });
  await page3.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page3.waitForTimeout(500);
  await page3.screenshot({ path: 'storage/app/diagnostico/fix/landing-desktop-dark.png', fullPage: true });
  console.log('3. Landing desktop dark saved');
  
  // 4. Landing mobile (dark mode)
  const page4 = await browser.newPage({ viewport: { width: 390, height: 844 } });
  await page4.emulateMedia({ colorScheme: 'dark' });
  await page4.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page4.waitForTimeout(500);
  await page4.screenshot({ path: 'storage/app/diagnostico/fix/landing-mobile-dark.png', fullPage: true });
  console.log('4. Landing mobile dark saved');
  
  // 5. Widget closed (desktop light)
  const page5 = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page5.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page5.waitForTimeout(500);
  await page5.screenshot({ path: 'storage/app/diagnostico/fix/widget-closed-desktop-light.png', fullPage: true });
  console.log('5. Widget closed desktop light saved');
  
  // 6. Widget closed (mobile light)
  const page6 = await browser.newPage({ viewport: { width: 390, height: 844 } });
  await page6.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page6.waitForTimeout(500);
  await page6.screenshot({ path: 'storage/app/diagnostico/fix/widget-closed-mobile-light.png', fullPage: true });
  console.log('6. Widget closed mobile light saved');
  
  // 7. Widget open empty (desktop light)
  const page7 = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page7.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page7.waitForTimeout(500);
  await openWidget(page7);
  await page7.screenshot({ path: 'storage/app/diagnostico/fix/widget-open-empty-desktop-light.png', fullPage: true });
  console.log('7. Widget open empty desktop light saved');
  
  // 8. Widget open empty (mobile light)
  const page8 = await browser.newPage({ viewport: { width: 390, height: 844 } });
  await page8.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page8.waitForTimeout(500);
  await openWidget(page8);
  await page8.screenshot({ path: 'storage/app/diagnostico/fix/widget-open-empty-mobile-light.png', fullPage: true });
  console.log('8. Widget open empty mobile light saved');
  
  // 9. Widget open with messages (desktop light) - try to send a message
  const page9 = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page9.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page9.waitForTimeout(500);
  await openWidget(page9);
  // Try to send message, but don't fail if it doesn't work
  try {
    await sendMessage(page9, 'Hola, ¿velocidad para MDF 3mm?');
  } catch (e) {
    console.log('Could not send message, taking screenshot anyway');
  }
  await page9.screenshot({ path: 'storage/app/diagnostico/fix/widget-open-messages-desktop-light.png', fullPage: true });
  console.log('9. Widget open messages desktop light saved');
  
  // 10. Widget open with messages (mobile light)
  const page10 = await browser.newPage({ viewport: { width: 390, height: 844 } });
  await page10.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page10.waitForTimeout(500);
  await openWidget(page10);
  try {
    await sendMessage(page10, 'Hola, ¿velocidad para MDF 3mm?');
  } catch (e) {
    console.log('Could not send message, taking screenshot anyway');
  }
  await page10.screenshot({ path: 'storage/app/diagnostico/fix/widget-open-messages-mobile-light.png', fullPage: true });
  console.log('10. Widget open messages mobile light saved');
  
  // 11. Widget open empty (desktop dark)
  const page11 = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page11.emulateMedia({ colorScheme: 'dark' });
  await page11.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page11.waitForTimeout(500);
  await openWidget(page11);
  await page11.screenshot({ path: 'storage/app/diagnostico/fix/widget-open-empty-desktop-dark.png', fullPage: true });
  console.log('11. Widget open empty desktop dark saved');
  
  // 12. Widget open with messages (desktop dark)
  const page12 = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  await page12.emulateMedia({ colorScheme: 'dark' });
  await page12.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page12.waitForTimeout(500);
  await openWidget(page12);
  try {
    await sendMessage(page12, 'Hola, ¿velocidad para MDF 3mm?');
  } catch (e) {
    console.log('Could not send message, taking screenshot anyway');
  }
  await page12.screenshot({ path: 'storage/app/diagnostico/fix/widget-open-messages-desktop-dark.png', fullPage: true });
  console.log('12. Widget open messages desktop dark saved');
  
  await browser.close();
})();