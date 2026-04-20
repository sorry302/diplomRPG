function togglePost(el) {
    document.querySelectorAll('.post').forEach(p => {
        if (p !== el) p.classList.remove('active');
    });

    el.classList.toggle('active');
}