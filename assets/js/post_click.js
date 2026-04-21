function togglePost(el) {
    document.querySelectorAll('.post').forEach(p => {
        if (p !== el) p.classList.remove('active');
    });

    el.classList.toggle('active');
}
function loadPosts(type = 'all') {
    fetch('/app/functions/get_posts.php?type=' + type)
        .then(res => res.text())
        .then(html => {
            document.getElementById('posts-container').innerHTML = html;
        });
}