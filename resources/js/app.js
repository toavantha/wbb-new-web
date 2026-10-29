window.lucide?.createIcons();

document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
    document.getElementById('mobile-menu')?.classList.toggle('hidden');
});

document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
    document.getElementById('admin-sidebar')?.classList.toggle('-translate-x-full');
});

const passwordInput = document.getElementById('password');
const passwordVisibilityButton = document.getElementById('toggle-password-visibility');

passwordVisibilityButton?.addEventListener('click', () => {
    if (!passwordInput) {
        return;
    }

    const isPasswordVisible = passwordInput.type === 'password';
    passwordInput.type = isPasswordVisible ? 'text' : 'password';
    passwordVisibilityButton.textContent = isPasswordVisible ? 'Hide' : 'Show';
    passwordVisibilityButton.setAttribute('aria-label', isPasswordVisible ? 'Hide password' : 'Show password');
    passwordVisibilityButton.setAttribute('aria-pressed', String(isPasswordVisible));
});

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm ?? '')) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('input[data-digit-limit]').forEach((input) => {
    input.addEventListener('input', () => {
        const digitLimit = Number(input.dataset.digitLimit);
        input.value = input.value.replace(/\D/g, '').slice(0, digitLimit);
    });
});

document.querySelectorAll('[data-open-extend-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        const modal = document.getElementById('extend-modal');
        const form = document.getElementById('extend-form');
        const title = document.getElementById('extend-notice-title');
        const currentDeadline = document.getElementById('extend-current-deadline');
        const deadlineInput = document.getElementById('new_deadline_date');

        if (!(modal instanceof HTMLElement)
            || !(form instanceof HTMLFormElement)
            || !(title instanceof HTMLElement)
            || !(currentDeadline instanceof HTMLElement)
            || !(deadlineInput instanceof HTMLInputElement)) {
            return;
        }

        const procurementId = button.dataset.procurementId;
        const deadline = button.dataset.deadline ?? '';

        if (!procurementId) {
            return;
        }

        form.action = `/admin/procurements/${encodeURIComponent(procurementId)}/extend-deadline`;
        title.textContent = button.dataset.title ?? '';
        currentDeadline.textContent = deadline || 'None';
        deadlineInput.value = deadline;
        modal.classList.remove('hidden');
    });
});

document.querySelectorAll('[data-close-extend-modal]').forEach((button) => {
    button.addEventListener('click', () => {
        document.getElementById('extend-modal')?.classList.add('hidden');
    });
});

const contactMap = document.getElementById('contact-office-map');
const contactMapStatus = document.getElementById('contact-office-map-status');

if (contactMap instanceof HTMLElement && contactMapStatus instanceof HTMLElement) {
    window.publicContactMapLoadError = () => {
        contactMapStatus.textContent = 'Google Maps could not be loaded. Use the Google Maps link below to view the office location.';
    };

    window.gm_authFailure = window.publicContactMapLoadError;

    window.initPublicContactMap = () => {
        if (!window.google?.maps) {
            window.publicContactMapLoadError();
            return;
        }

        const position = {
            lat: Number(contactMap.dataset.latitude),
            lng: Number(contactMap.dataset.longitude),
        };

        if (!Number.isFinite(position.lat) || !Number.isFinite(position.lng)) {
            contactMapStatus.textContent = 'The office map location is unavailable.';
            return;
        }

        const map = new window.google.maps.Map(contactMap, {
            center: position,
            zoom: 16,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
        });

        new window.google.maps.Marker({
            map,
            position,
            title: 'Welfare Benefits Board headquarters',
        });

        contactMapStatus.textContent = '';
    };

    const googleMapsApiKey = contactMap.dataset.googleMapsApiKey;

    if (googleMapsApiKey) {
        const mapsScript = document.createElement('script');
        const mapsUrl = new URL('https://maps.googleapis.com/maps/api/js');

        mapsUrl.searchParams.set('key', googleMapsApiKey);
        mapsUrl.searchParams.set('callback', 'initPublicContactMap');
        mapsUrl.searchParams.set('loading', 'async');
        mapsScript.src = mapsUrl.toString();
        mapsScript.async = true;
        mapsScript.addEventListener('error', window.publicContactMapLoadError);
        document.head.append(mapsScript);
    }
}

const conferbotWidgetId = document.body.dataset.conferbotWidget;

if (conferbotWidgetId) {
    const conferbotScript = document.createElement('script');

    conferbotScript.id = 'conferbot-js';
    conferbotScript.charset = 'UTF-8';
    conferbotScript.src = 'https://cdn.conferbot.com/dist/v1/widget.min.js';
    conferbotScript.async = true;
    conferbotScript.addEventListener('load', () => {
        window.ConferbotWidget?.(conferbotWidgetId, 'live_chat');
    });
    document.head.append(conferbotScript);
}
