document.querySelector('[data-password-toggle]')?.addEventListener('click', (event) => {
    const input = document.querySelector('#password');
    const showing = input.type === 'password';
    input.type = showing ? 'text' : 'password';
    event.currentTarget.setAttribute('aria-label', showing ? 'Sembunyikan password' : 'Tampilkan password');
});

document.querySelector('[data-menu]')?.addEventListener('click', () => {
    document.querySelector('[data-sidebar]')?.classList.toggle('open');
});
