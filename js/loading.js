// Loading Screen Auto-Hide (USER VERSION)
// Ensures loading screen displays for minimum 1 second

const MINIMUM_LOADING_TIME = 1000; // 1 second minimum
const loadStartTime = Date.now();

function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (!loadingOverlay) return;

    const elapsedTime = Date.now() - loadStartTime;
    const remainingTime = Math.max(0, MINIMUM_LOADING_TIME - elapsedTime);

    setTimeout(function () {
        loadingOverlay.classList.add('fade-out');

        // Remove from DOM after fade animation completes
        setTimeout(function () {
            loadingOverlay.remove();
        }, 500);
    }, remainingTime);
}

// Hide when DOM ready
document.addEventListener('DOMContentLoaded', hideLoading);

// Fallback: Hide when fully loaded (if DOMContentLoaded already fired)
window.addEventListener('load', function () {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay && !loadingOverlay.classList.contains('fade-out')) {
        hideLoading();
    }
});
