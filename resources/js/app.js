
import Alpine from 'alpinejs';

window.Alpine = Alpine;

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

Alpine.directive('reveal', (el, { modifiers }) => {
    if (prefersReducedMotion) return;

    el.classList.add('x-reveal');
    if (modifiers.includes('delay')) el.classList.add('x-reveal-delay');

    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            el.classList.add('is-revealed');
            observer.disconnect();
        }
    }, { threshold: 0.12 });

    observer.observe(el);
});

Alpine.directive('counter', (el) => {
    const target = parseFloat(el.getAttribute('data-counter-value') || '0');
    const duration = parseInt(el.getAttribute('data-counter-duration') || '1600', 10);
    const suffix = el.getAttribute('data-counter-suffix') || '';

    const setFinal = () => {
        el.textContent = target.toLocaleString('id-ID') + suffix;
    };

    if (prefersReducedMotion) {
        setFinal();
        return;
    }

    const run = () => {
        const start = performance.now();
        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(target * eased).toLocaleString('id-ID') + suffix;
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            run();
            observer.disconnect();
        }
    }, { threshold: 0.4 });

    observer.observe(el);
});

// Overlay loading halus untuk form filter (x-loading)
Alpine.directive('loading', (el) => {
    el.addEventListener('submit', () => {
        const dark = document.documentElement.classList.contains('dark');
        const overlay = document.createElement('div');
        overlay.className = `fixed inset-0 z-[100] backdrop-blur-sm flex items-center justify-center page-enter ${dark ? 'bg-dbl-darker/70' : 'bg-white/70'}`;
        overlay.innerHTML = `
            <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 rounded-full border-4 border-dbl-green/30 border-t-dbl-green animate-spin"></div>
                <span class="text-sm font-bold ${dark ? 'text-gray-300' : 'text-gray-600'}">Memuat data...</span>
            </div>`;
        document.body.appendChild(overlay);
    });
});

// Default styling Chart.js agar konsisten di semua halaman & mengikuti tema
const isDark = () => document.documentElement.classList.contains('dark');

const applyChartTheme = (dark) => {
    if (!window.Chart) return;

    const tickColor = dark ? '#9CA3AF' : '#6B7280';
    const gridColor = dark ? 'rgba(255, 255, 255, 0.07)' : 'rgba(0, 0, 0, 0.04)';

    Chart.defaults.color = tickColor;

    Object.values(Chart.instances || {}).forEach((chart) => {
        try {
            chart.options.color = tickColor;
            Object.values(chart.options.scales || {}).forEach((scale) => {
                scale.ticks = scale.ticks || {};
                scale.ticks.color = tickColor;
                if (scale.grid && scale.grid.drawOnChartArea !== false) {
                    scale.grid.color = gridColor;
                }
            });
            if (chart.options.plugins?.legend?.labels) {
                chart.options.plugins.legend.labels.color = tickColor;
            }
            chart.update();
        } catch (e) {}
    });
};

if (window.Chart) {
    Chart.defaults.font.family = "'Figtree', ui-sans-serif, system-ui, sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.animation.duration = 900;
    Chart.defaults.animation.easing = 'easeOutQuart';
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(17, 24, 39, 0.92)';
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 10;
    Chart.defaults.plugins.tooltip.titleFont = { weight: '700' };
    Chart.defaults.plugins.tooltip.titleColor = '#FFFFFF';
    Chart.defaults.plugins.tooltip.bodyColor = '#E5E7EB';
    Chart.defaults.plugins.tooltip.borderColor = 'rgba(255, 255, 255, 0.08)';
    Chart.defaults.plugins.tooltip.borderWidth = 1;

    applyChartTheme(isDark());
}

// Sinkronkan chart yang sudah dirender saat tema diganti
window.addEventListener('theme-changed', (e) => applyChartTheme(!!e.detail?.dark));

Alpine.start();
