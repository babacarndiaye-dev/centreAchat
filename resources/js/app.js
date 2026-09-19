import './bootstrap';
import './pwa';
import Alpine from 'alpinejs';
import UIkit from 'uikit';
import UIkitIcons from 'uikit/dist/js/uikit-icons';

UIkit.use(UIkitIcons);
window.UIkit = UIkit;

window.Alpine = Alpine;
Alpine.start();

const pageLoader = document.getElementById('page-loader');
if (pageLoader && pageLoader.style.display !== 'none') {
    const started = Date.now();
    window.addEventListener('load', () => {
        const remaining = Math.max(0, 400 - (Date.now() - started));
        setTimeout(() => {
            pageLoader.classList.add('is-hidden');
            setTimeout(() => pageLoader.remove(), 500);
        }, remaining);
    });
    sessionStorage.setItem('caPageLoaderShown', '1');
}

document.addEventListener('DOMContentLoaded', () => {
    const revealEls = document.querySelectorAll('.reveal');

    if ('IntersectionObserver' in window && revealEls.length) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
        );

        revealEls.forEach((el) => observer.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('is-visible'));
    }

    let lastScroll = 0;
    const header = document.getElementById('site-header');

    if (header) {
        window.addEventListener('scroll', () => {
            const current = window.scrollY;
            header.classList.toggle('shadow-soft', current > 8);
            header.classList.toggle('bg-white/95', current > 8);
            header.classList.toggle('bg-white/60', current <= 8);
            lastScroll = current;
        });
    }

    if (document.getElementById('chart-revenue') && window.__analyticsData) {
        import('./analytics.js').then(({ initAnalyticsCharts }) => {
            initAnalyticsCharts(window.__analyticsData);
        });
    }
});
