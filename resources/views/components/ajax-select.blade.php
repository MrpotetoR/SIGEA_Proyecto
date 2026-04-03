@props([
    'name',
    'url',
    'placeholder' => 'Buscar...',
    'label' => '',
    'value' => null,
    'display' => '',
    'required' => false,
    'minChars' => 2,
])

@php $uid = 'ajax_' . str_replace(['[', ']', '.'], '_', $name) . '_' . uniqid(); @endphp

<div class="relative" id="wrap-{{ $uid }}">
    @if($label)
        <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">{{ $label }}</label>
    @endif

    <input type="text" id="input-{{ $uid }}" placeholder="{{ $placeholder }}" autocomplete="off"
           class="w-full text-sm border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 dark:placeholder-gray-400 {{ $value ? 'hidden' : '' }}">
    <input type="hidden" name="{{ $name }}" id="hidden-{{ $uid }}" value="{{ $value }}" {{ $required ? 'required' : '' }}>

    {{-- Dropdown resultados --}}
    <div id="results-{{ $uid }}" class="hidden absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg dark:shadow-gray-900/30 max-h-48 overflow-y-auto"></div>

    {{-- Seleccionado --}}
    <div id="selected-{{ $uid }}" class="{{ $value ? '' : 'hidden' }} mt-1 flex items-center justify-between bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-xl px-3 py-2">
        <span id="text-{{ $uid }}" class="text-sm text-blue-700 dark:text-blue-300 font-medium">{{ $display }}</span>
        <button type="button" id="clear-{{ $uid }}" class="text-blue-400 hover:text-[#0606F0] dark:hover:text-blue-300 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <p id="hint-{{ $uid }}" class="text-[10px] text-gray-400 mt-1 {{ $value ? 'hidden' : '' }}">Escribe al menos {{ $minChars }} caracteres.</p>
</div>

@pushOnce('scripts')
<script>
function initAjaxSelect(uid, url, minChars) {
    // Convertir URL absoluta a relativa para evitar problemas cross-origin
    try { url = new URL(url, location.origin).pathname; } catch(e) {}

    const input    = document.getElementById('input-' + uid);
    const hidden   = document.getElementById('hidden-' + uid);
    const results  = document.getElementById('results-' + uid);
    const selected = document.getElementById('selected-' + uid);
    const text     = document.getElementById('text-' + uid);
    const clear    = document.getElementById('clear-' + uid);
    const hint     = document.getElementById('hint-' + uid);
    const isDark   = () => document.documentElement.classList.contains('dark');
    let timer = null;

    function search(q) {
        if (q.length < minChars) { results.classList.add('hidden'); return; }

        const params = new URLSearchParams({ q });
        const form = input.closest('form') || input.closest('.bg-white') || input.closest('.dark\\:bg-gray-800');
        if (form) {
            const carrera = form.querySelector('[id*="filtro-carrera"], [name="carrera_filter"]');
            if (carrera && carrera.value) params.append('carrera', carrera.value);
        }

        fetch(`${url}?${params}`)
            .then(r => r.json())
            .then(data => {
                const dk = isDark();
                if (!data.length) {
                    results.innerHTML = '<div class="px-3 py-3 text-sm text-gray-400 text-center">Sin resultados</div>';
                } else {
                    results.innerHTML = data.map(item =>
                        `<div class="px-3 py-2.5 text-sm ${dk ? 'text-gray-200 hover:bg-gray-700' : 'text-gray-700 hover:bg-blue-50'} cursor-pointer transition-colors" data-id="${item.id}" data-texto="${item.texto}">
                            <span class="font-medium">${item.texto}</span>
                            ${item.extra ? `<span class="text-[10px] text-gray-400 ml-1">${item.extra}</span>` : ''}
                        </div>`
                    ).join('');
                    results.querySelectorAll('[data-id]').forEach(el => {
                        el.addEventListener('click', () => pick(el.dataset.id, el.dataset.texto));
                    });
                }
                results.classList.remove('hidden');
            })
            .catch(() => {
                results.innerHTML = '<div class="px-3 py-3 text-sm text-red-400 text-center">Error</div>';
                results.classList.remove('hidden');
            });
    }

    function pick(id, label) {
        hidden.value = id;
        text.textContent = label;
        selected.classList.remove('hidden');
        input.classList.add('hidden');
        results.classList.add('hidden');
        hint.classList.add('hidden');
    }

    function reset() {
        hidden.value = '';
        input.value = '';
        input.classList.remove('hidden');
        selected.classList.add('hidden');
        hint.classList.remove('hidden');
        input.focus();
    }

    input.addEventListener('input', e => {
        clearTimeout(timer);
        timer = setTimeout(() => search(e.target.value.trim()), 300);
    });
    input.addEventListener('keydown', e => { if (e.key === 'Escape') results.classList.add('hidden'); });
    clear.addEventListener('click', reset);

    document.addEventListener('click', e => {
        if (!e.target.closest('#wrap-' + uid)) results.classList.add('hidden');
    });

    // Validar antes de submit si es required
    if (hidden.hasAttribute('required')) {
        const form = hidden.closest('form');
        if (form) {
            form.addEventListener('submit', e => {
                if (!hidden.value) {
                    e.preventDefault();
                    input.classList.remove('hidden');
                    input.focus();
                    input.style.borderColor = '#f87171';
                    setTimeout(() => input.style.borderColor = '', 2000);
                }
            });
        }
    }

    // Exponer reset para filtros externos
    window['ajaxSelectReset_' + uid] = reset;
}
</script>
@endPushOnce

@push('scripts')
<script>initAjaxSelect('{{ $uid }}', '{{ $url }}', {{ $minChars }});</script>
@endpush
