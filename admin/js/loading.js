

const MINIMUM_LOADING_TIME = 1000;
const loadStartTime = Date.now();

function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (!loadingOverlay) return;

    const elapsedTime = Date.now() - loadStartTime;
    const remainingTime = Math.max(0, MINIMUM_LOADING_TIME - elapsedTime);

    setTimeout(function () {
        loadingOverlay.classList.add('fade-out');
        setTimeout(function () {
            loadingOverlay.remove();
        }, 500);
    }, remainingTime);
}
document.addEventListener('DOMContentLoaded', hideLoading);
window.addEventListener('load', function () {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay && !loadingOverlay.classList.contains('fade-out')) {
        hideLoading();
    }
});
