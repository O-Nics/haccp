<!doctype html>
<html lang="en" data-theme="{{ $config->get('ui.theme', 'light') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="{{ $config->get('ui.theme', 'light') }}">
    <title>{{ $config->get('ui.title', config('app.name') . ' - API Docs') }}</title>

    <script src="https://unpkg.com/@stoplight/elements@8.4.2/web-components.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements@8.4.2/styles.min.css">

    <script>
        const originalFetch = window.fetch;

        // intercept TryIt requests and add the XSRF-TOKEN header,
        // which is necessary for Sanctum cookie-based authentication to work correctly
        window.fetch = (url, options) => {
            const CSRF_TOKEN_COOKIE_KEY = "XSRF-TOKEN";
            const CSRF_TOKEN_HEADER_KEY = "X-XSRF-TOKEN";
            const getCookieValue = (key) => {
                const cookie = document.cookie.split(';').find((cookie) => cookie.trim().startsWith(key));
                return cookie?.split("=")[1];
            };

            const updateFetchHeaders = (
                headers,
                headerKey,
                headerValue,
            ) => {
                if (headers instanceof Headers) {
                    headers.set(headerKey, headerValue);
                } else if (Array.isArray(headers)) {
                    headers.push([headerKey, headerValue]);
                } else if (headers) {
                    headers[headerKey] = headerValue;
                }
            };
            const csrfToken = getCookieValue(CSRF_TOKEN_COOKIE_KEY);
            if (csrfToken) {
                const { headers = new Headers() } = options || {};
                updateFetchHeaders(headers, CSRF_TOKEN_HEADER_KEY, decodeURIComponent(csrfToken));
                return originalFetch(url, {
                    ...options,
                    headers,
                });
            }

            return originalFetch(url, options);
        };
    </script>

    <style>
        html, body { margin:0; height:100%; }
        body { background-color: var(--color-canvas); }
        /* issues about the dark theme of stoplight/mosaic-code-viewer using web component:
         * https://github.com/stoplightio/elements/issues/2188#issuecomment-1485461965
         */
        [data-theme="dark"] .token.property {
            color: rgb(128, 203, 196) !important;
        }
        [data-theme="dark"] .token.operator {
            color: rgb(255, 123, 114) !important;
        }
        [data-theme="dark"] .token.number {
            color: rgb(247, 140, 108) !important;
        }
        [data-theme="dark"] .token.string {
            color: rgb(165, 214, 255) !important;
        }
        [data-theme="dark"] .token.boolean {
            color: rgb(121, 192, 255) !important;
        }
        [data-theme="dark"] .token.punctuation {
            color: #dbdbdb !important;
        }
        .sl-bg-canvas-100 {
            background-color: #9272ef;
            /*background-color: #9272ef;*/
            /*font-weight: 600;*/
            color: white;
        }

        img {
            max-width: 100% !important;
            width: 100%;
        }

        .sl-text-paragraph.sl-leading-snug.sl-font-prose.sl-font-semibold.sl-text-heading {
            display: none !important;
        }
        .sl-overflow-y-auto.sl-w-full.sl-bg-canvas-100{
            border-top: solid 1px !important;
        }

        .sl-stack--vertical .sl-bg-canvas-100{
            background-color: #ebeef5;
            color: #171717;
            font-weight: 400;
        }

        .sl-inverted .sl-bg-canvas-100{
            background-color: #2d394e;
            font-weight: 500;
            color: white;
        }

        .sl-my-3 svg{
            color: white;
        }

        /* Icônes des schémas blanches uniquement si non sélectionnés */
        [id^="sl-toc-/schemas/"]:not(.sl-bg-primary-tint) .sl-icon {
            color: #fff !important;
            fill: #fff !important;
        }

        /* Si sélectionné : couleur violette */
        [id^="sl-toc-/schemas/"].sl-bg-primary-tint .sl-icon {
            color: #6c47ff !important; /* ou #9272ef selon ta charte */
            fill: #6c47ff !important;
        }
        .sl-flex.sl-items-center.sl-h-md.sl-bg-canvas-100:hover {
            background-color: #835aff !important;
            color: #fff !important;
            border-radius: 6px !important;
            margin: 4px 4px !important;
        }
        .sl-flex.sl-items-center.sl-h-md.sl-bg-canvas-100 {
            margin: 4px 4px !important;

        }

        .sl-flex.sl-items-center.sl-h-md.sl-bg-canvas-100:not(.sl-pl-8) {
            padding-left: 12px !important;
        }

        .sl-flex.sl-items-center.sl-h-md.sl-bg-canvas-100 {
            padding-left: 28px;
        }


        .sl-flex.sl-items-center.sl-text-xs
        .sl-text-danger,
        .sl-flex.sl-items-center.sl-text-xs
        .sl-text-success,
        .sl-flex.sl-items-center.sl-text-xs
        .sl-text-primary,
        .sl-flex.sl-items-center.sl-text-xs
        .sl-text-warning {
            background-color: #ffffff !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
            font-weight: 800 !important;
        }
        a[href*="stoplight.io"][target="_blank"][rel~="noopener"] {
            display: none !important;
        }

        [id^="sl-toc-"]:not(.sl-bg-primary-tint) .sl-icon {
            color: #fff !important;
            fill: #fff !important;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
            background: transparent;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            border: 2px solid transparent;
            background-clip: content-box;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 1);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.8) transparent;
        }

        .sl-flex.sl-items-center.sl-h-md.sl-pr-4.sl-pl-4.sl-bg-canvas-100:hover {
            background-color: #835aff !important; /* couleur personnalisée */
            color: #fff !important; /* texte blanc au survol */
            transition: background-color 0.2s ease;
        }

        .sl-bg-canvas-100 .sl-text-paragraph{
            color: white;
        }
        .sl-bg-primary-tint {
            background-color: white;
            font-weight: 600;
            color: #171717;
        }
        .sl-border-r{
            border-right-width : 0px;
        }

        /* Cmd+K modal */
        /* Cache tout élément portant l'attribut hidden */
        #kbar[hidden] { display: none !important; }
        .kbar-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.35);backdrop-filter:saturate(120%) blur(2px);z-index:9999;display:flex;align-items:flex-start;justify-content:center;padding-top:10vh}
        .kbar-modal{width:min(720px,92vw);background:var(--color-canvas,#fff);border:1px solid var(--color-canvas-200,#e5e7eb);border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.25);overflow:hidden}
        .kbar-header{padding:10px 12px;border-bottom:1px solid var(--color-canvas-200,#e5e7eb)}
        .kbar-header input{width:100%;padding:10px 12px;border:1px solid var(--color-canvas-300,#d1d5db);border-radius:8px;outline:none;background:var(--color-canvas-50,#fafafa)}
        .kbar-list{max-height:60vh;overflow:auto}
        .kbar-item{display:block;align-items:center;gap:10px;padding:10px 12px;border-bottom:1px solid var(--color-canvas-100,#f3f4f6);cursor:pointer}
        .kbar-item:hover,.kbar-item[aria-selected="true"]{background:var(--color-canvas-100,#f3f4f6)}
        .kbar-item .kbar-op{font-weight:600}
        .kbar-item .kbar-hash{opacity:.65;font-size:12px}
        [data-theme="dark"] .kbar-modal{background:#0b0b0b;border-color:#1f2937}
        [data-theme="dark"] .kbar-header{border-color:#1f2937}
        [data-theme="dark"] .kbar-header input{background:#0f172a;border-color:#1f2937;color:#e5e7eb}
        [data-theme="dark"] .kbar-item{border-color:#111827}
        [data-theme="dark"] .kbar-item:hover,[data-theme="dark"] .kbar-item[aria-selected="true"]{background:#0f172a}
        .kbar-item .kbar-container {display: block}
        .kbar-item .kbar-top { display:flex; justify-content: space-between; align-items:center; gap:8px; margin-bottom:2px; }
        .kbar-item .kbar-tag { display:inline-block; font-size:12px; font-weight:700; text-transform:uppercase; opacity:.9; }
        .kbar-item .kbar-title { display:inline-block; font-size:10px; font-weight:600; text-transform:uppercase; opacity:.9; }
        .kbar-item .kbar-path { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size:12px; opacity:.8; }

        .kbar-badges { display:flex; flex-wrap:wrap; gap:6px; margin-top:6px; }
        .badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:12px; font-weight:700; border:1px solid transparent; }
        .badge-op { background: var(--color-canvas-50,#f3f4f6); border-color: var(--color-canvas-200,#e5e7eb); }
        .badge-get { background:#dcfce7; color:#065f46; border-color:#34d399; }       /* success */
        .badge-post { background:#e0e7ff; color:#3730a3; border-color:#818cf8; }     /* primary */
        .badge-put { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }      /* treat like danger */
        .badge-patch { background:#fef3c7; color:#92400e; border-color:#fcd34d; }    /* warning */
        .badge-delete { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }   /* danger */
        .badge-schema { background:#f3e8ff; color:#6b21a8; border-color:#d8b4fe; }

        /* Loader under search bar */
        .kbar-loading { display:flex; align-items:center; gap:8px; padding-top:8px; font-size:12px; opacity:.8; }
        .kbar-spinner { width:14px; height:14px; border-radius:50%; border:2px solid currentColor; border-right-color: transparent; animation: kbar-spin .7s linear infinite; }
        @keyframes kbar-spin { to { transform: rotate(360deg); } }
        /* Ensure loader hides when [hidden] */
        #kbar-loading[hidden] { display: none !important; }
    </style>

</head>
<body style="height: 100vh; overflow-y: hidden">
<!-- Cmd/Ctrl+K search modal -->
<div id="kbar" class="kbar-backdrop" hidden>
    <div class="kbar-modal" role="dialog" aria-modal="true" aria-label="Search endpoints">
        <div class="kbar-header">
            <input id="kbar-input" type="search" placeholder="Search endpoints… (type to filter, ↑/↓ to navigate, Enter to open, Esc to close)">
            <div id="kbar-loading" class="kbar-loading" hidden>
                <span class="kbar-spinner" aria-hidden="true"></span>
                <span>Chargement de l’index…</span>
            </div>
        </div>
        <div id="kbar-list" class="kbar-list" role="listbox" aria-label="Endpoints"></div>
    </div>
</div>

<elements-api
    id="docs"
    tryItCredentialsPolicy="{{ $config->get('ui.try_it_credentials_policy', 'include') }}"
    router="hash"
    @if($config->get('ui.hide_try_it')) hideTryIt="true" @endif
    @if($config->get('ui.hide_schemas')) hideSchemas="true" @endif
    @if($config->get('ui.logo')) logo="{{ $config->get('ui.logo') }}" @endif
    @if($config->get('ui.layout')) layout="{{ $config->get('ui.layout') }}" @endif
/>
{{--script pour la modal de recherch :--}}
<script>
    (function(){
        const modal = document.getElementById('kbar');
        const input = document.getElementById('kbar-input');
        const list  = document.getElementById('kbar-list');
        const docsEl = document.getElementById('docs');
        const loading = document.getElementById('kbar-loading');
        const showLoading = (on) => {
            if (!loading) return;
            if (on) {
                loading.removeAttribute('hidden');
                loading.style.display = 'flex';
            } else {
                loading.setAttribute('hidden', '');
                loading.style.display = 'none';
            }
        };

        let SPEC = null; // cache
        let index = [];   // items: operation or schema
        let filtered = [];
        let active = 0;

        const NM = (s) => (s||'').toString().normalize('NFD').replace(/\p{Diacritic}/gu,'').toLowerCase();

        async function getSpec(){
            if (SPEC) return SPEC;
            const urlAttr = docsEl.getAttribute('apiDescriptionUrl');
            const url = urlAttr && urlAttr.length ? urlAttr : '{{ asset('docs/api.json') }}';
            try {
                const res = await fetch(url + '?v=' + Date.now(), { cache: 'no-store' });
                if (res.ok) {
                    SPEC = await res.json();
                    return SPEC;
                }
            } catch(e) {}
            // Fallback: inline spec from blade
            SPEC = @json($spec);
            return SPEC;
        }

        async function buildIndex(){
            const spec = await getSpec();
            index = [];

            // --- Operations ---
            const paths = spec.paths || {};
            for (const [path, methods] of Object.entries(paths)) {
                if (!methods || typeof methods !== 'object') continue;
                const availableMethods = Object.keys(methods).filter(m => typeof methods[m] === 'object');
                for (const method of availableMethods) {
                    const op = methods[method];
                    if (!op || typeof op !== 'object') continue;
                    const opId = op.operationId || `${method.toUpperCase()} ${path}`;
                    const tags = Array.isArray(op.tags) ? op.tags : [];
                    const description = op.description || '';

                    index.push({
                        kind: 'op',
                        title: opId,
                        opId,
                        method: method.toUpperCase(),
                        path,
                        tags,
                        description,
                        methodsOnPath: availableMethods.map(m=>m.toUpperCase()),
                        haystack: NM([opId, path, tags.join(' '), description].join(' \n ')),
                        hash: `#/operations/${opId}`,
                    });
                }
            }

            // --- Schemas ---
            const schemas = (spec.components && spec.components.schemas) ? spec.components.schemas : {};
            for (const name of Object.keys(schemas)) {
                index.push({
                    kind: 'schema',
                    title: name,
                    schema: name,
                    path: `#/components/schemas/${name}`,
                    tags: ['Schema'],
                    description: schemas[name]?.description || '',
                    haystack: NM([name, 'schema', schemas[name]?.description || ''].join(' \n ')),
                    hash: `#/schemas/${name}`,
                });
            }

            // stable order by title
            index.sort((a,b) => a.title.localeCompare(b.title));
        }

        const METHOD_BADGE_CLASS = (m) => {
            switch (m) {
                case 'GET': return 'badge-get';
                case 'POST': return 'badge-post';
                case 'PUT': return 'badge-put';
                case 'PATCH': return 'badge-patch';
                case 'DELETE': return 'badge-delete';
                default: return 'badge-op';
            }
        };

        const render = () => {
            list.innerHTML = '';
            filtered.forEach((item, i) => {
                const div = document.createElement('div');
                div.className = 'kbar-item';
                div.setAttribute('role','option');
                div.setAttribute('aria-selected', i===active ? 'true' : 'false');

                if (item.kind === 'op') {
                    // One item per method; show method badge on the right of the title
                    div.innerHTML = `
                        <div class="kbar-container">
                          <span class="kbar-op">${(item.tags && item.tags[0]) ? item.tags[0] : 'Operation'}</span>
                          <div class="kbar-top">
                              <span class="kbar-path">${item.title}</span>
                              <span class="badge ${METHOD_BADGE_CLASS(item.method)}">${item.method}</span>
                          </div>
                        </div>
                        <div class="kbar-path">${item.path}</div>
                    `;
                } else { // schema
                    div.innerHTML = `
                        <div class="kbar-top">
                          <span class="kbar-op">${item.title}</span>
                          <span class="kbar-tag badge badge-schema">Schema</span>
                        </div>
                        <div class="kbar-path">${item.path}</div>
                    `;
                }

                div.addEventListener('click', () => openResult(item));
                list.appendChild(div);
            });
        };

        const applyFilter = (q) => {
            const terms = NM(q||'').split(/\s+/).filter(Boolean);
            if (!terms.length) {
                filtered = index.slice(0, 200);
                active = 0; render(); return;
            }
            filtered = index.filter(it => terms.every(t => it.haystack.includes(t)));
            active = 0;
            render();
        };

        const openModal = async () => {
            if (modal.hidden) {
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
                input.value = '';
                showLoading(true);
                try {
                    await buildIndex();
                    applyFilter('');
                } finally {
                    showLoading(false);
                }
                setTimeout(()=>input.focus(), 0);
            }
        };

        const closeModal = () => {
            if (!modal.hidden) {
                modal.hidden = true;
                document.body.style.overflow = '';
                showLoading(false);
            }
        };

        const openResult = (item) => {
            window.location.hash = item.hash; // operations or schemas
            closeModal();
        };

        // Cmd/Ctrl+K
        window.addEventListener('keydown', async (e) => {
            const isMac = navigator.platform.toUpperCase().includes('MAC');
            const combo = (isMac && e.metaKey && e.key.toLowerCase()==='k') || (!isMac && e.ctrlKey && e.key.toLowerCase()==='k');
            if (combo) {
                e.preventDefault();
                if (modal.hidden) {
                    await openModal();
                } else {
                    closeModal();
                }
                return;
            }
            if (!modal.hidden) {
                if (e.key === 'Escape') { e.preventDefault(); closeModal(); }
                else if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active+1, filtered.length-1); render(); ensureActiveVisible(); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active-1, 0); render(); ensureActiveVisible(); }
                else if (e.key === 'Enter') { e.preventDefault(); if (filtered[active]) openResult(filtered[active]); }
            }
        });

        input.addEventListener('input', (e) => applyFilter(e.target.value));

        function ensureActiveVisible() {
            const el = list.children[active];
            if (el) el.scrollIntoView({ block: 'nearest' });
        }

        // Close on backdrop click
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    })();
</script>
{{--end script--}}
<script>
    (async () => {
        const docs = document.getElementById('docs');
        docs.apiDescriptionDocument = @json($spec);
    })();
</script>

@if($config->get('ui.theme', 'light') === 'system')
    <script>
        var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

        function updateTheme(e) {
            if (e.matches) {
                window.document.documentElement.setAttribute('data-theme', 'dark');
                window.document.getElementsByName('color-scheme')[0].setAttribute('content', 'dark');
            } else {
                window.document.documentElement.setAttribute('data-theme', 'light');
                window.document.getElementsByName('color-scheme')[0].setAttribute('content', 'light');
            }
        }

        mediaQuery.addEventListener('change', updateTheme);
        updateTheme(mediaQuery);
    </script>
@endif
</body>
</html>
