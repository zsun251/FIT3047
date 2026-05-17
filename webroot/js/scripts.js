/**
 * Automatically hide flash messages after a short delay.
 */
document.addEventListener('DOMContentLoaded', () => {
    const flashMessages = document.querySelectorAll('.flash-overlay .alert, .flash-overlay .message');

    flashMessages.forEach((message) => {
        window.setTimeout(() => {
            message.classList.add('flash-hiding');

            window.setTimeout(() => {
                message.remove();
            }, 300);
        }, 12000);
    });
});
