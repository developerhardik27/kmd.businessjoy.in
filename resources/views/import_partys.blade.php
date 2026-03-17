<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel Data</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #f0ede8;
            --surface:  #ffffff;
            --border:   #ddd9d2;
            --ink:      #1a1714;
            --ink-2:    #6b6560;
            --accent:   #c84b2f;
            --accent-h: #a83a20;
            --green:    #2e7d4f;
            --green-bg: #edf7f1;
            --red:      #c0392b;
            --red-bg:   #fdf2f1;
            --amber:    #b45309;
            --amber-bg: #fefce8;
            --radius:   12px;
            --shadow:   0 2px 16px rgba(0,0,0,0.07);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
            min-height: 100vh;
            padding: 40px 24px 60px;
        }

        /* ── Page shell ── */
        .page-wrap {
            max-width: 780px;
            margin: 0 auto;
        }

        /* ── Header ── */
        .page-header {
            margin-bottom: 36px;
        }
        .page-header .eyebrow {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 8px;
        }
        .page-header h1 {
            font-family: 'Syne', sans-serif;
            font-size: clamp(28px, 5vw, 42px);
            font-weight: 800;
            line-height: 1.1;
            color: var(--ink);
        }
        .page-header p {
            margin-top: 10px;
            font-size: 15px;
            color: var(--ink-2);
        }

        /* ── Alerts ── */
        .alert {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px 16px;
            border-radius: var(--radius);
            margin-bottom: 16px;
            font-size: 14px;
            line-height: 1.5;
            border-left: 4px solid transparent;
        }
        .alert-icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
        .alert.success { background: var(--green-bg); border-color: var(--green); color: var(--green); }
        .alert.error   { background: var(--red-bg);   border-color: var(--red);   color: var(--red); }
        .alert.warning { background: var(--amber-bg); border-color: var(--amber); color: var(--amber); }
        .alert strong { font-weight: 600; }

        .error-table-wrap { max-height: 320px; overflow-y: auto; margin-top: 12px; border-radius: 8px; border: 1px solid var(--border); }
        .error-table { border-collapse: collapse; width: 100%; font-size: 13px; }
        .error-table th, .error-table td { padding: 9px 12px; text-align: left; border-bottom: 1px solid var(--border); }
        .error-table th { background: #fff8f6; font-weight: 600; color: var(--ink); }
        .error-table tr:last-child td { border-bottom: none; }
        .error-table pre { margin: 0; font-size: 11px; white-space: pre-wrap; word-break: break-all; }

        /* ── Card ── */
        .card {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            padding: 32px;
            margin-bottom: 24px;
        }
        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Type toggle ── */
        .type-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 24px;
        }
        @media (max-width: 500px) { .type-grid { grid-template-columns: 1fr; } }

        .type-option { position: relative; }
        .type-option input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
        .type-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.18s ease;
            background: var(--bg);
        }
        .type-label:hover { border-color: #bbb; background: #e8e5e0; }
        .type-option input:checked + .type-label {
            border-color: var(--accent);
            background: #fff4f2;
            color: var(--accent);
        }
        .type-label .emoji { font-size: 22px; }
        .type-label .label-text { font-size: 14px; font-weight: 600; }
        .type-label .label-desc { font-size: 12px; color: var(--ink-2); font-weight: 400; margin-top: 1px; }
        .type-option input:checked + .type-label .label-desc { color: #c07060; }

        /* ── File upload zone ── */
        .upload-zone {
            border: 2px dashed var(--border);
            border-radius: 10px;
            padding: 28px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.18s ease;
            background: var(--bg);
            position: relative;
            margin-bottom: 24px;
        }
        .upload-zone:hover, .upload-zone.dragover {
            border-color: var(--accent);
            background: #fff4f2;
        }
        .upload-zone input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
        .upload-icon { font-size: 36px; margin-bottom: 8px; }
        .upload-zone p { font-size: 14px; color: var(--ink-2); }
        .upload-zone p strong { color: var(--ink); }
        .upload-zone .file-name {
            margin-top: 10px; font-size: 13px; font-weight: 600;
            color: var(--accent); display: none;
        }
        .upload-zone.has-file .file-name { display: block; }
        .upload-zone.has-file p.placeholder { display: none; }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--accent);
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.04em;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.18s, transform 0.12s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }
        .btn-submit:hover { background: var(--accent-h); }
        .btn-submit:active { transform: scale(0.99); }
        .btn-submit:disabled {
            background: #ccc; cursor: not-allowed; transform: none;
        }
        .btn-submit .btn-default-content,
        .btn-submit .btn-loading-content { display: flex; align-items: center; gap: 10px; }
        .btn-submit .btn-loading-content { display: none; }
        .btn-submit.loading .btn-default-content { display: none; }
        .btn-submit.loading .btn-loading-content { display: flex; }

        /* spinner */
        .spinner {
            width: 18px; height: 18px;
            border: 2.5px solid rgba(255,255,255,0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            flex-shrink: 0;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Full-page overlay loader ── */
        #page-loader {
            position: fixed;
            inset: 0;
            background: rgba(240, 237, 232, 0.88);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            z-index: 9999;
        }
        #page-loader.active { display: flex; }
        .loader-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 36px 48px;
            text-align: center;
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
        }
        .loader-ring {
            width: 56px; height: 56px;
            border: 4px solid #f0ede8;
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 18px;
        }
        .loader-card h3 {
            font-family: 'Syne', sans-serif;
            font-size: 18px; font-weight: 700;
            color: var(--ink); margin-bottom: 6px;
        }
        .loader-card p { font-size: 13px; color: var(--ink-2); }
        .loader-dots span {
            display: inline-block;
            animation: bounce 1.2s infinite;
        }
        .loader-dots span:nth-child(2) { animation-delay: 0.2s; }
        .loader-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce {
            0%,80%,100% { opacity: 0.3; transform: translateY(0); }
            40% { opacity: 1; transform: translateY(-3px); }
        }

        /* ── Column reference panel ── */
        .col-panel {
            display: none;
            animation: fadeIn 0.25s ease;
        }
        .col-panel.visible { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

        .col-panel h3 {
            font-family: 'Syne', sans-serif;
            font-size: 14px; font-weight: 700;
            color: var(--ink); margin-bottom: 14px;
            display: flex; align-items: center; gap: 8px;
        }
        .col-chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .col-chip {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 5px 11px;
            font-size: 12px; font-weight: 500;
            color: var(--ink);
            display: flex; align-items: center; gap: 5px;
        }
        .col-chip .num {
            background: var(--accent);
            color: #fff;
            border-radius: 4px;
            font-size: 10px; font-weight: 700;
            padding: 1px 5px;
            min-width: 20px; text-align: center;
        }
        .col-chip em { font-style: normal; color: var(--ink-2); font-size: 11px; }
        .col-note {
            margin-top: 12px;
            font-size: 12px; color: var(--ink-2);
            background: var(--bg);
            border-radius: 8px;
            padding: 10px 14px;
            line-height: 1.6;
        }
        .col-note code {
            background: #e8e5e0; border-radius: 4px;
            padding: 1px 5px; font-size: 11px;
        }
    </style>
</head>
<body>

{{-- ── Full-page loader overlay ── --}}
<div id="page-loader">
    <div class="loader-card">
        <div class="loader-ring"></div>
        <h3>Importing Data<span class="loader-dots"><span>.</span><span>.</span><span>.</span></span></h3>
        <p>Processing your file, please wait</p>
    </div>
</div>

<div class="page-wrap">

    {{-- ── Header ── --}}
    <div class="page-header">
        <div class="eyebrow">Data Management</div>
        <h1>Import Excel Data</h1>
        <p>Upload an .xlsx, .xls, or .csv file to bulk-import records into the system.</p>
    </div>

    {{-- ── Alerts ── --}}
    @if(session('success'))
        <div class="alert success">
            <span class="alert-icon">✅</span>
            <div><strong>Success</strong> — {{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert error">
            <span class="alert-icon">❌</span>
            <div><strong>Error</strong> — {{ session('error') }}</div>
        </div>
    @endif

    @if(session('errors') && count(session('errors')) > 0)
        <div class="alert warning">
            <span class="alert-icon">⚠️</span>
            <div style="width:100%">
                <strong>{{ count(session('errors')) }} row error(s) found</strong>
                <div class="error-table-wrap">
                    <table class="error-table">
                        <thead><tr><th>Row</th><th>Error</th><th>Data</th></tr></thead>
                        <tbody>
                            @foreach(session('errors') as $err)
                                <tr>
                                    <td>{{ $err['row'] }}</td>
                                    <td>{{ $err['error'] }}</td>
                                    <td><pre>{{ json_encode($err['data'], JSON_PRETTY_PRINT) }}</pre></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if(session('duplicates') && count(session('duplicates')) > 0)
        <div class="alert warning" style="background: #fff3e0; border-color: #f59e0b; color: #92400e;">
            <span class="alert-icon">🔄</span>
            <div style="width:100%">
                <strong>{{ count(session('duplicates')) }} duplicate record(s) skipped</strong>
                <div class="error-table-wrap">
                    <table class="error-table">
                        <thead><tr><th>Row</th><th>Type</th><th>Name</th></tr></thead>
                        <tbody>
                            @foreach(session('duplicates') as $dup)
                                <tr>
                                    <td>{{ $dup['row'] }}</td>
                                    <td>{{ $dup['type'] }}</td>
                                    <td>{{ $dup['name'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Main form card ── --}}
    <div class="card">
        <div class="card-title">Configure Import</div>

        <form id="import-form" action="{{ url('import-partys') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="token"      value="{{ session('api_token') }}">
            <input type="hidden" name="user_id"    value="{{ session('user_id') }}">
            <input type="hidden" name="company_id" value="{{ session('company_id') }}">

            {{-- Type selection --}}
            <p style="font-size:13px;font-weight:600;color:var(--ink-2);margin-bottom:12px;text-transform:uppercase;letter-spacing:.06em">Step 1 — Select import type</p>
            <div class="type-grid">
                <div class="type-option">
                    <input type="radio" name="import_type" id="t-party" value="party" checked onchange="switchType(this)">
                    <label class="type-label" for="t-party">
                        <span class="emoji">🧑‍💼</span>
                        <div>
                            <div class="label-text">Party</div>
                            <div class="label-desc">Individual / contact records</div>
                        </div>
                    </label>
                </div>
                <div class="type-option">
                    <input type="radio" name="import_type" id="t-company" value="company" onchange="switchType(this)">
                    <label class="type-label" for="t-company">
                        <span class="emoji">🏢</span>
                        <div>
                            <div class="label-text">Company</div>
                            <div class="label-desc">Organisation master records</div>
                        </div>
                    </label>
                </div>
                <div class="type-option">
                    <input type="radio" name="import_type" id="t-garden" value="garden" onchange="switchType(this)">
                    <label class="type-label" for="t-garden">
                        <span class="emoji">🌿</span>
                        <div>
                            <div class="label-text">Garden</div>
                            <div class="label-desc">Garden / mark-name entries</div>
                        </div>
                    </label>
                </div>
                <div class="type-option">
                    <input type="radio" name="import_type" id="t-company_garden" value="company_garden" onchange="switchType(this)">
                    <label class="type-label" for="t-company_garden">
                        <span class="emoji">🔗</span>
                        <div>
                            <div class="label-text">Company-Garden</div>
                            <div class="label-desc">Link companies to gardens</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- File upload --}}
            <p style="font-size:13px;font-weight:600;color:var(--ink-2);margin-bottom:12px;text-transform:uppercase;letter-spacing:.06em">Step 2 — Choose file</p>
            <div class="upload-zone" id="upload-zone">
                <input type="file" name="file" id="file-input" required accept=".xlsx,.xls,.csv">
                <div class="upload-icon">📂</div>
                <p class="placeholder"><strong>Click to browse</strong> or drag & drop your file here<br><span style="font-size:12px;color:var(--ink-2)">.xlsx · .xls · .csv accepted</span></p>
                <div class="file-name" id="file-name-display"></div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit" id="submit-btn">
                <span class="btn-default-content">
                    <span>⬆</span> Upload &amp; Import
                </span>
                <span class="btn-loading-content">
                    <span class="spinner"></span> Uploading file…
                </span>
            </button>
        </form>
    </div>

    {{-- ── Column reference card ── --}}
    <div class="card">
        <div class="card-title">Expected Columns</div>

        {{-- PARTY --}}
        <div id="cols-party" class="col-panel visible">
            <h3>🧑‍💼 Party columns</h3>
            <div class="col-chips">
                @php $partyFields = ['CODE','NAME','BILL To','GSTIN','TMCO','ADRESS1','ADRESS2','CITY','STATE','PIN','EMAIL','C%']; @endphp
                @foreach($partyFields as $i => $f)
                    <div class="col-chip"><span class="num">{{ $i+1 }}</span> {{ $f }}</div>
                @endforeach
            </div>
            <div class="col-note">ℹ️ <strong>BILL To</strong> is used as the display party name.</div>
        </div>

        {{-- COMPANY --}}
        <div id="cols-company" class="col-panel">
            <h3>🏢 Company columns</h3>
            <div class="col-chips">
                @php $companyFields = [['CODE','empty'],['NAME',''],['BILL To','empty'],['GSTIN',''],['TMCO',''],['ADRESS1',''],['ADRESS2',''],['CITY',''],['STATE',''],['PIN',''],['EMAIL-1',''],['EMAIL-2',''],['C%','→ brokerage']]; @endphp
                @foreach($companyFields as $i => $f)
                    <div class="col-chip">
                        <span class="num">{{ $i+1 }}</span>
                        {{ $f[0] }}
                        @if($f[1])<em>({{ $f[1] }})</em>@endif
                    </div>
                @endforeach
            </div>
            <div class="col-note">ℹ️ Inserts into <code>companymasters</code>. <strong>C%</strong> is mapped to the <code>brokerage</code> column.</div>
        </div>

        {{-- GARDEN --}}
        <div id="cols-garden" class="col-panel">
            <h3>🌿 Garden columns</h3>
            <div class="col-chips">
                <div class="col-chip"><span class="num">1</span> MARK NAME <em>(stored)</em></div>
            </div>
            <div class="col-note">ℹ️ Data starts from <strong>Row 6</strong> — rows 1–5 are skipped as title/header. Inserts into <code>garden</code> table.</div>
        </div>

        {{-- COMPANY-GARDEN --}}
        <div id="cols-company_garden" class="col-panel">
            <h3>🔗 Company-Garden link columns</h3>
            <div class="col-chips">
                <div class="col-chip"><span class="num">1</span> MARK NAME <em>(finds garden)</em></div>
                <div class="col-chip"><span class="num">2</span> SELLER <em>(finds company)</em></div>
            </div>
            <div class="col-note">ℹ️ Creates links in <code>company_garden</code> table. Both records must already exist. Duplicate links are automatically skipped.</div>
        </div>
    </div>

</div>

<script>
    // ── Type switch ──
    function switchType(radio) {
        ['party', 'company', 'garden', 'company_garden'].forEach(type => {
            document.getElementById('cols-' + type).classList.toggle('visible', radio.value === type);
        });
    }

    // ── File input display ──
    const fileInput  = document.getElementById('file-input');
    const uploadZone = document.getElementById('upload-zone');
    const fileNameEl = document.getElementById('file-name-display');

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            fileNameEl.textContent = '📄 ' + fileInput.files[0].name;
            uploadZone.classList.add('has-file');
        }
    });

    // drag-and-drop visual
    uploadZone.addEventListener('dragover',  e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
    uploadZone.addEventListener('dragleave', ()  => uploadZone.classList.remove('dragover'));
    uploadZone.addEventListener('drop',      e  => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            fileNameEl.textContent = '📄 ' + e.dataTransfer.files[0].name;
            uploadZone.classList.add('has-file');
        }
    });

    // ── Form submit → show loader ──
    const form      = document.getElementById('import-form');
    const submitBtn = document.getElementById('submit-btn');
    const pageLoader = document.getElementById('page-loader');

    form.addEventListener('submit', function (e) {
        if (!fileInput.files.length) return; // let HTML5 validation handle it

        // Show button spinner
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        // Show full-page overlay
        pageLoader.classList.add('active');

        // Safety net: hide loader after 60s if page doesn't redirect
        setTimeout(() => {
            pageLoader.classList.remove('active');
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }, 60000);
    });

    // ── Hide loader when page is shown again (back/forward cache, etc.) ──
    window.addEventListener('pageshow', function (e) {
        pageLoader.classList.remove('active');
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
    });
</script>
</body>
</html>
