document.addEventListener('DOMContentLoaded', () => {

    console.log('APP_USER:', window.APP_USER);

    const modal = document.getElementById('profileModal');

    if (!window.APP_USER) {
        console.log('Нет данных пользователя');
        return;
    }

    if (Number(window.APP_USER.role) === 1) {
        modal.style.display = 'block';
        console.log('Модалка открыта для role = 1');
    }
});
