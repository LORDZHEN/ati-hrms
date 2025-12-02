import './bootstrap';

Livewire.on('profileUpdated', () => {
    const avatar = document.querySelector('[data-user-avatar]');
    if (avatar) {
        // Force reload by appending a timestamp
        const currentSrc = avatar.src.split('?')[0];
        avatar.src = currentSrc + '?t=' + new Date().getTime();
    }
});
