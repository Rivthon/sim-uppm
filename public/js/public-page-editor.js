(() => {
    const editorElement = document.querySelector('#public-page-editor');
    const contentInput = document.querySelector('#public-page-content');
    const form = editorElement?.closest('form');
    const status = document.querySelector('[data-editor-status]');

    if (!editorElement || !contentInput || !form || typeof Quill === 'undefined') return;

    contentInput.hidden = true;
    const quill = new Quill(editorElement, {
        theme: 'snow',
        placeholder: 'Mulai tulis isi halaman di sini…',
        modules: { toolbar: [
            [{ header: [2, 3, 4, false] }], ['bold', 'italic', 'underline', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
            [{ align: [] }], ['blockquote', 'code-block', 'link', 'image'], ['clean'],
        ] },
    });

    quill.clipboard.dangerouslyPasteHTML(contentInput.value);

    const showStatus = (message, isError = false) => {
        if (!status) return;
        status.textContent = message;
        status.classList.toggle('is-error', isError);
    };

    quill.getModule('toolbar').addHandler('image', () => {
        const picker = document.createElement('input');
        picker.type = 'file';
        picker.accept = 'image/jpeg,image/png,image/webp';
        picker.addEventListener('change', async () => {
            const image = picker.files?.[0];
            if (!image) return;
            if (image.size > 5 * 1024 * 1024) {
                showStatus('Ukuran gambar maksimal 5 MB.', true);
                return;
            }

            showStatus('Mengunggah gambar…');
            const body = new FormData();
            body.append('image', image);

            try {
                const response = await fetch(editorElement.dataset.uploadUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        Accept: 'application/json',
                    },
                    body,
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gambar gagal diunggah.');
                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'image', result.url, 'user');
                quill.setSelection(range.index + 1, 0, 'silent');
                showStatus('Gambar berhasil disisipkan.');
            } catch (error) {
                showStatus(error.message || 'Gambar gagal diunggah.', true);
            }
        });
        picker.click();
    });

    form.addEventListener('submit', () => {
        contentInput.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
    });
})();
