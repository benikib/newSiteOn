<style>
    #bisika-cookie-consent {
        position: fixed;
        z-index: 1000;
        right: 16px;
        bottom: 16px;
        width: min(520px, calc(100% - 32px));
        padding: 16px;
        border: 1px solid #d8ded9;
        border-radius: 12px;
        background: #fff;
        color: #202923;
        box-shadow: 0 12px 36px rgba(20, 35, 27, .2);
        font: 14px/1.5 "Segoe UI", sans-serif;
    }

    #bisika-cookie-consent p { margin: 0 0 12px; }
    #bisika-cookie-consent-actions { display: flex; justify-content: flex-end; gap: 8px; }
    #bisika-cookie-consent button {
        min-height: 44px;
        padding: 0 16px;
        border: 1px solid #245c48;
        border-radius: 8px;
        background: #fff;
        color: #245c48;
        font: inherit;
        font-weight: 700;
        cursor: pointer;
    }
    #bisika-cookie-consent button:first-child { background: #245c48; color: #fff; }
    #bisika-cookie-consent button:focus-visible { outline: 3px solid #c45c36; outline-offset: 2px; }
    body.bisika-consent-open .shop-whatsapp-fab { bottom: calc(150px + env(safe-area-inset-bottom)); }

    @media (max-width: 600px) {
        #bisika-cookie-consent { right: 12px; bottom: calc(12px + env(safe-area-inset-bottom)); width: calc(100% - 24px); }
        #bisika-cookie-consent-actions button { flex: 1; }
    }

    @media (prefers-reduced-motion: reduce) {
        #bisika-cookie-consent, #bisika-cookie-consent * { scroll-behavior: auto !important; transition-duration: .01ms !important; }
    }
</style>
<script>
    (() => {
        const measurementId = @json(config('services.analytics.ga4_measurement_id'));
        const storageKey = 'bisika_ga4_consent';
        const acceptedValue = 'accepted';
        const getConsent = () => {
            try { return localStorage.getItem(storageKey); } catch { return null; }
        };
        const saveConsent = value => {
            try { localStorage.setItem(storageKey, value); } catch { }
        };

        const loadAnalytics = () => {
            if (!measurementId || window.__bisikaGa4Loaded || getConsent() !== acceptedValue) return;
            window.__bisikaGa4Loaded = true;
            window.dataLayer = window.dataLayer || [];
            window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
            window.gtag('js', new Date());
            window.gtag('config', measurementId);

            const script = document.createElement('script');
            script.async = true;
            script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(measurementId)}`;
            script.dataset.bisikaGa4 = 'true';
            document.head.insertBefore(script, document.head.firstChild);
        };

        window.BisikaAnalytics = {
            track(eventName, parameters = {}) {
                if (getConsent() !== acceptedValue || typeof window.gtag !== 'function') return false;
                window.gtag('event', eventName, parameters);
                return true;
            },
            trackAndSubmit(form, eventName) {
                if (getConsent() !== acceptedValue || typeof window.gtag !== 'function') return false;
                let completed = false;
                const continueSubmit = () => {
                    if (completed) return;
                    completed = true;
                    HTMLFormElement.prototype.submit.call(form);
                };

                window.gtag('event', eventName, {
                    event_callback: continueSubmit,
                    event_timeout: 1200,
                });
                window.setTimeout(continueSubmit, 1300);
                return true;
            },
        };

        const showConsent = () => {
            if (getConsent() === acceptedValue || getConsent() === 'refused') return;

            const banner = document.createElement('section');
            banner.id = 'bisika-cookie-consent';
            banner.setAttribute('role', 'region');
            banner.setAttribute('aria-label', 'Consentement aux cookies analytiques');

            const message = document.createElement('p');
            message.textContent = 'Acceptez-vous les cookies de mesure d’audience pour améliorer BISIKA ?';
            const actions = document.createElement('div');
            actions.id = 'bisika-cookie-consent-actions';

            const accept = document.createElement('button');
            accept.type = 'button';
            accept.textContent = 'Accepter';
            const refuse = document.createElement('button');
            refuse.type = 'button';
            refuse.textContent = 'Refuser';

            const decide = choice => {
                saveConsent(choice);
                document.body.classList.remove('bisika-consent-open');
                banner.remove();
                if (choice === acceptedValue) loadAnalytics();
            };

            accept.addEventListener('click', () => decide(acceptedValue));
            refuse.addEventListener('click', () => decide('refused'));
            actions.append(accept, refuse);
            banner.append(message, actions);
            document.body.appendChild(banner);
            document.body.classList.add('bisika-consent-open');
        };

        const isSensitiveSearch = value => /[\w.+-]+@[\w.-]+\.[a-z]{2,}/i.test(value)
            || /\+?\d[\d\s().-]{6,}\d/.test(value);

        document.addEventListener('DOMContentLoaded', () => {
            if (getConsent() === acceptedValue) loadAnalytics();
            showConsent();

            let searchTimer;
            let previousSearch = '';
            document.addEventListener('input', event => {
                if (!event.target.matches('#shop-search')) return;
                window.clearTimeout(searchTimer);
                searchTimer = window.setTimeout(() => {
                    const term = event.target.value.trim();
                    if (!term || term === previousSearch || isSensitiveSearch(term)) return;
                    const normalize = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('fr');
                    const normalizedTerm = normalize(term);
                    const hasProductMatch = [...document.querySelectorAll('.shop-product-card')].some(card =>
                        normalize(`${card.dataset.title} ${card.dataset.categoryName}`).includes(normalizedTerm)
                    );
                    if (!hasProductMatch) return;
                    previousSearch = term;
                    window.BisikaAnalytics.track('search', { search_term: term });
                }, 600);
            });

            document.addEventListener('change', event => {
                const choice = event.target.closest('.reservation-product-choice:checked, .delivery-product-choice:checked');
                if (!choice) return;
                const card = choice.closest('.reservation-product, .delivery-product');
                const quantity = card?.querySelector('.reservation-product-quantity, .delivery-product-quantity');
                window.BisikaAnalytics.track('add_to_cart', {
                    item_name: card?.querySelector('h4')?.textContent.trim() || 'Produit',
                    quantity: Number(quantity?.value) || 1,
                });
            });

            document.addEventListener('click', event => {
                const link = event.target.closest('a[href^="https://wa.me/"]');
                if (!link) return;
                if (link.id === 'shop-detail-whatsapp') {
                    window.BisikaAnalytics.track('whatsapp_click', {
                        product: document.getElementById('shop-detail-title')?.textContent || 'Produit',
                    });
                } else {
                    window.BisikaAnalytics.track('whatsapp_click', { page: window.location.pathname });
                }
            });

            document.addEventListener('submit', event => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement) || event.defaultPrevented) return;
                const eventName = form.matches('.article-reservation-form, .shop-cart-form')
                    ? 'reservation_submit'
                    : form.matches('.delivery-order-form') ? 'delivery_order_submit' : null;
                if (!eventName || !window.BisikaAnalytics.trackAndSubmit(form, eventName)) return;
                event.preventDefault();
            });
        });
    })();
</script>