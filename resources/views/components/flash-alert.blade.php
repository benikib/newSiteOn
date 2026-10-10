@props(['message' => null, 'type' => null])

@php
    $firstValidationError = isset($errors) ? $errors->first() : null;
    $alertMessage = $message
        ?? session('error')
        ?? ($firstValidationError ? 'Veuillez vérifier les informations indiquées.' : null)
        ?? session('success')
        ?? session('status');
    $alertType = $type ?? (session('error') || $firstValidationError ? 'error' : 'success');
@endphp

<style>
    .bisika-alert { position: relative; display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin: 1rem 0; padding: .85rem 1rem; border: 1px solid transparent; border-radius: 4px; transition: opacity .25s ease; }
    .bisika-alert-error { color: #842029; background: #f8d7da; border-color: #f5c2c7; }
    .bisika-alert-success { color: #0f5132; background: #d1e7dd; border-color: #badbcc; }
    .bisika-alert-close { border: 0; padding: 0; background: transparent; color: inherit; font-size: 1.25rem; line-height: 1; cursor: pointer; }
    .bisika-alert.is-hidden { opacity: 0; }
</style>

@if ($alertMessage)
    <div class="bisika-alert bisika-alert-{{ $alertType }}" role="alert" aria-live="polite">
        <span>{{ $alertMessage }}</span>
        <button type="button" class="bisika-alert-close" aria-label="Fermer" data-dismiss-alert>&times;</button>
    </div>
@endif

<script>
    (() => {
        const show = (message, type = 'error') => {
            document.querySelector('.bisika-alert')?.remove();
            const alert = document.createElement('div');
            alert.className = `bisika-alert bisika-alert-${type}`;
            alert.setAttribute('role', 'alert');
            alert.setAttribute('aria-live', 'polite');

            const text = document.createElement('span');
            text.textContent = message;
            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'bisika-alert-close';
            close.setAttribute('aria-label', 'Fermer');
            close.textContent = '×';
            alert.append(text, close);
            const target = document.querySelector('main') || document.body;
            target.prepend(alert);

            const dismiss = () => {
                alert.classList.add('is-hidden');
                window.setTimeout(() => alert.remove(), 250);
            };
            close.addEventListener('click', dismiss);
            window.setTimeout(dismiss, 8000);
            document.addEventListener('input', event => {
                if (event.target.closest('form')) dismiss();
            }, { once: true });
        };

        document.querySelectorAll('.bisika-alert').forEach(alert => {
            const dismiss = () => {
                alert.classList.add('is-hidden');
                window.setTimeout(() => alert.remove(), 250);
            };
            alert.querySelector('[data-dismiss-alert]')?.addEventListener('click', dismiss);
            window.setTimeout(dismiss, 8000);
            document.addEventListener('input', event => {
                if (event.target.closest('form')) dismiss();
            }, { once: true });
        });

        window.BisikaAlerts = {
            error: message => show(message),
            success: message => show(message, 'success'),
        };

        window.addEventListener('error', event => {
            console.error(event.error || event.message);
            show('Une erreur est survenue. Veuillez réessayer.');
        });
        window.addEventListener('unhandledrejection', event => {
            console.error(event.reason);
            show('Une erreur est survenue. Veuillez réessayer.');
        });
    })();
</script>