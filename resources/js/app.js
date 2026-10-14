// Chat widget store definition (registered via alpine:init in welcome.blade.php)
// This file is kept for Vite bundling but does not import/start Alpine
// to avoid "multiple instances" warning since Livewire manages Alpine

// Make store config globally accessible for the inline script
window.ChatWidgetStoreConfig = {
    isOpen: false,
    wasOpen: false,
    
    init() {
        const saved = sessionStorage.getItem('chatWidgetOpen');
        if (saved === 'true') {
            this.isOpen = true;
            this.wasOpen = true;
        }
        
        window.addEventListener('open-chat-widget', () => this.open());
        window.addEventListener('close-chat-widget', () => this.close());
    },
    
    open() {
        this.isOpen = true;
    },
    
    close() {
        this.isOpen = false;
    },
    
    toggle() {
        this.isOpen = !this.isOpen;
    }
};