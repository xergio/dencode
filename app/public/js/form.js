
(function() {
    const form = document.getElementById('form');
    const textarea = document.getElementById('str');

    // Change the form method between GET and POST based on the textarea length
    const updateFormMethod = () => {
        const maxLength = 1024;
        const textLength = textarea.value.length;

        if (textLength > maxLength) {
            form.method = 'post';
        } else {
            form.method = 'get';
        }
    }

    document.addEventListener('DOMContentLoaded', updateFormMethod);
    textarea.addEventListener('input', updateFormMethod);
})();
