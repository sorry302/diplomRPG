const postsBtn = document.getElementById('openPosts');
const notifBtn = document.getElementById('openNotifications');

const postsDrawer = document.getElementById('postsDrawer');
const notifDrawer = document.getElementById('notificationsDrawer');

const overlay = document.getElementById('overlay');

postsBtn.onclick = () => {
    postsDrawer.classList.add('active');
    overlay.classList.add('active');
};

notifBtn.onclick = () => {
    notifDrawer.classList.add('active');
    overlay.classList.add('active');
};

overlay.onclick = closeAll;

document.querySelectorAll('.close-btn').forEach(btn => {
    btn.onclick = closeAll;
});

function closeAll() {
    postsDrawer.classList.remove('active');
    notifDrawer.classList.remove('active');
    overlay.classList.remove('active');
}