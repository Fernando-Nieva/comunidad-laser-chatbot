const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  
  const page = await browser.newPage({ viewport: { width: 1280, height: 720 } });
  
  // Capture console errors
  page.on('console', msg => {
    if (msg.type() === 'error') {
      console.log('CONSOLE ERROR:', msg.text());
    }
  });
  
  page.on('pageerror', err => {
    console.log('PAGE ERROR:', err.message);
  });
  
  await page.goto('http://127.0.0.1:8000', { waitUntil: 'networkidle' });
  await page.waitForTimeout(500);
  
  // Check if Alpine store exists
  const alpineStore = await page.evaluate(() => {
    return typeof window.ChatWidgetStore !== 'undefined';
  });
  console.log('Alpine store exists:', alpineStore);
  
  // Check if Livewire is loaded
  const livewireLoaded = await page.evaluate(() => {
    return typeof Livewire !== 'undefined';
  });
  console.log('Livewire loaded:', livewireLoaded);
  
  // Open widget via custom event
  await page.evaluate(() => {
    window.dispatchEvent(new CustomEvent('open-chat-widget'));
  });
  
  // Wait for Livewire to mount inside widget
  await page.waitForTimeout(4000);
  
  // Check if chat-box component exists
  const chatBox = await page.locator('[wire\\:id]').first();
  const chatBoxExists = await chatBox.count() > 0;
  console.log('Livewire chat-box exists:', chatBoxExists);
  
  if (chatBoxExists) {
    const wireId = await chatBox.getAttribute('wire:id');
    console.log('Wire ID:', wireId);
  }
  
  // Check textarea
  const textarea = await page.locator('textarea[aria-label="Tu mensaje"]').first();
  const isDisabled = await textarea.getAttribute('disabled');
  const isVisible = await textarea.isVisible();
  console.log('Textarea disabled attr:', isDisabled, 'visible:', isVisible);
  
  // Check panel
  const panel = await page.locator('#chat-panel').first();
  const panelVisible = await panel.isVisible();
  console.log('Chat panel visible:', panelVisible);
  
  // Get panel HTML if exists
  if (panelVisible) {
    const panelHtml = await panel.innerHTML();
    console.log('Panel HTML length:', panelHtml.length);
  }
  
  await page.screenshot({ path: 'storage/app/diagnostico/fix/textarea-test4.png', fullPage: true });
  console.log('Screenshot saved');
  
  await browser.close();
})();