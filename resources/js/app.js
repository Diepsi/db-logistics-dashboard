

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

Alpine.data('contactForm', () => ({
    name: '',
    email: '',
    phone: '',
    message: '',
    errors: {},
    sending: false,
    success: false,
    serverError: '',

    submit(event) {
        this.sending = true;
        this.errors = {};
        this.success = false;
        this.serverError = '';

        const form = event.target;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(form),
        })
            .then(async (response) => {
                const data = await response.json();
                if (response.ok) {
                    this.success = true;
                    form.reset();
                    this.name = '';
                    this.email = '';
                    this.phone = '';
                    this.message = '';
                } else if (data.errors) {
                    this.errors = data.errors;
                } else {
                    this.serverError = data.message || 'Terjadi kesalahan, silakan coba lagi.';
                }
            })
            .catch(() => {
                this.serverError = 'Terjadi kesalahan jaringan, silakan coba lagi.';
            })
            .finally(() => {
                this.sending = false;
            });
    },
}));

Alpine.start();
