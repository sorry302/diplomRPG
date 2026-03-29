document.querySelectorAll('.action-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const card = btn.nextElementSibling;

    document.querySelectorAll('.action-card').forEach(c => {
      if (c !== card) c.classList.remove('active');
    });

    card.classList.toggle('active');
  });
});
document.addEventListener('click', function(e) {
  if (!e.target.closest('.action-wrapper')) {
    document.querySelectorAll('.action-card').forEach(card => {
      card.classList.remove('active');
    });
  }
});