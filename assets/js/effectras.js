function showActionFeedback(items) {
  const container = document.getElementById('action-feedback') 
    || document.body.appendChild(Object.assign(document.createElement('div'), { id: 'action-feedback' }));

  items.forEach(item => {
    if (!item.value) return;

    const el = document.createElement('div');
    el.className = 'feedback-item ' + (item.value > 0 ? 'positive' : 'negative');
    el.textContent = `${item.value > 0 ? '+' : ''}${item.value} ${item.label}`;

    container.appendChild(el);

    // время отображения
    setTimeout(() => {
      el.classList.add('fade-out');

      setTimeout(() => el.remove(), 500); // время fade-out
    }, 4000);
  });
}
