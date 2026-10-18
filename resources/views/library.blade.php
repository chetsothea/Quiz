<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management</title>
    <style>
        :root {
            --bg: #f4f6fb; --card: #ffffff; --text: #1f2937; --muted: #6b7280;
            --border: #e5e7eb; --primary: #2563eb; --primary-dark: #1d4ed8;
            --danger: #dc2626; --success: #16a34a; --warn: #d97706;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", "Khmer OS", sans-serif; background: var(--bg); color: var(--text); }
        header { background: var(--primary); color: #fff; padding: 18px 24px; }
        header h1 { margin: 0; font-size: 22px; }
        header p { margin: 4px 0 0; opacity: .85; font-size: 14px; }
        main { max-width: 1200px; margin: 0 auto; padding: 20px 16px 40px; }

        .tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
        .tab { border: 1px solid var(--border); background: var(--card); padding: 10px 18px; border-radius: 8px;
               cursor: pointer; font-size: 15px; font-weight: 600; color: var(--muted); }
        .tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        .layout { display: grid; grid-template-columns: 340px 1fr; gap: 16px; align-items: start; }
        @media (max-width: 900px) { .layout { grid-template-columns: 1fr; } }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 10px; padding: 18px; }
        .card h2 { margin: 0 0 14px; font-size: 17px; }

        label { display: block; font-size: 13px; font-weight: 600; margin: 10px 0 4px; }
        label .req { color: var(--danger); }
        input, select, textarea { width: 100%; padding: 8px 10px; border: 1px solid var(--border); border-radius: 6px;
                                  font-size: 14px; font-family: inherit; background: #fff; }
        input:focus, select:focus, textarea:focus { outline: 2px solid #bfdbfe; border-color: var(--primary); }
        input:disabled, select:disabled { background: #f3f4f6; color: var(--muted); }
        textarea { min-height: 60px; resize: vertical; }

        .actions { display: flex; gap: 8px; margin-top: 16px; }
        button { border: 0; border-radius: 6px; padding: 9px 16px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-light { background: #e5e7eb; color: var(--text); }
        .btn-edit { background: #fef3c7; color: #92400e; padding: 5px 10px; font-size: 13px; }
        .btn-del { background: #fee2e2; color: var(--danger); padding: 5px 10px; font-size: 13px; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 9px 10px; border-bottom: 1px solid var(--border); white-space: nowrap; }
        th { background: #f9fafb; font-size: 13px; color: var(--muted); }
        tr:hover td { background: #f9fafb; }
        td.row-actions { display: flex; gap: 6px; }
        .empty { text-align: center; color: var(--muted); padding: 24px; }
        .list-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .list-head h2 { margin: 0; }
        .count { color: var(--muted); font-size: 13px; }

        #toast { position: fixed; right: 16px; bottom: 16px; padding: 12px 18px; border-radius: 8px; color: #fff;
                 font-size: 14px; max-width: 380px; opacity: 0; transform: translateY(10px); transition: .25s; pointer-events: none; }
        #toast.show { opacity: 1; transform: translateY(0); }
        #toast.ok { background: var(--success); }
        #toast.err { background: var(--danger); }
    </style>
</head>
<body>
<header>
    <h1>📚 Library Management</h1>
    <p>បញ្ចូល · បង្ហាញ · កែប្រែ · លុប ទិន្នន័យ តាមរយៈ Laravel API</p>
</header>

<main>
    <div class="tabs" id="tabs"></div>

    <div class="layout">
        <section class="card">
            <h2 id="form-title">បញ្ចូលថ្មី</h2>
            <form id="form" novalidate>
                <div id="fields"></div>
                <div class="actions">
                    <button type="submit" class="btn-primary" id="btn-save">💾 រក្សាទុក</button>
                    <button type="button" class="btn-light" id="btn-cancel">សម្អាត</button>
                </div>
            </form>
        </section>

        <section class="card">
            <div class="list-head">
                <h2 id="list-title">បញ្ជី</h2>
                <span class="count" id="count"></span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead id="thead"></thead>
                    <tbody id="tbody"></tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<div id="toast"></div>

<script>
const API = '/api';

// Configuration of the 4 tables: API path, primary key and form fields
const RESOURCES = {
    'book-types': {
        label: 'Book Types',
        key: ['BookTypeID'],
        fields: [
            { name: 'BookTypeName', label: 'Book Type Name', required: true },
        ],
        columns: ['BookTypeID', 'BookTypeName'],
    },
    'authors': {
        label: 'Authors',
        key: ['AuthorID'],
        fields: [
            { name: 'AuthorName', label: 'Author Name', required: true },
            { name: 'Gender', label: 'Gender', type: 'select', options: [['', '-- ជ្រើសរើស --'], ['Male', 'Male'], ['Female', 'Female']] },
            { name: 'DOB', label: 'Date of Birth', type: 'date' },
            { name: 'POB', label: 'Place of Birth' },
            { name: 'Address', label: 'Address' },
            { name: 'Phone', label: 'Phone' },
            { name: 'Email', label: 'Email', type: 'email' },
            { name: 'Photo', label: 'Photo (URL)' },
        ],
        columns: ['AuthorID', 'AuthorName', 'Gender', 'DOB', 'Phone', 'Email'],
    },
    'books': {
        label: 'Books',
        key: ['BookID'],
        fields: [
            { name: 'BookTitle', label: 'Book Title', required: true },
            { name: 'BookTypeID', label: 'Book Type', type: 'select', required: true, source: 'book-types', optValue: 'BookTypeID', optText: 'BookTypeName' },
            { name: 'PublishDate', label: 'Publish Date', type: 'date' },
            { name: 'NumOfPages', label: 'Number of Pages', type: 'number' },
            { name: 'NumOfCopies', label: 'Number of Copies', type: 'number' },
            { name: 'Edition', label: 'Edition' },
            { name: 'Publisher', label: 'Publisher' },
            { name: 'BookSource', label: 'Book Source' },
            { name: 'Remark', label: 'Remark', type: 'textarea' },
        ],
        columns: ['BookID', 'BookTitle', r => r.book_type?.BookTypeName ?? '', 'PublishDate', 'NumOfPages', 'NumOfCopies', 'Publisher'],
        columnNames: ['BookID', 'BookTitle', 'BookType', 'PublishDate', 'Pages', 'Copies', 'Publisher'],
    },
    'book-authors': {
        label: 'Book Authors',
        key: ['BookID', 'AuthorID'],
        fields: [
            { name: 'BookID', label: 'Book', type: 'select', required: true, isKey: true, source: 'books', optValue: 'BookID', optText: 'BookTitle' },
            { name: 'AuthorID', label: 'Author', type: 'select', required: true, isKey: true, source: 'authors', optValue: 'AuthorID', optText: 'AuthorName' },
            { name: 'AuthorDate', label: 'Author Date', type: 'date' },
            { name: 'Remark', label: 'Remark', type: 'textarea' },
        ],
        columns: [r => r.book?.BookTitle ?? r.BookID, r => r.author?.AuthorName ?? r.AuthorID, 'AuthorDate', 'Remark'],
        columnNames: ['Book', 'Author', 'AuthorDate', 'Remark'],
    },
};

let current = 'book-types';
let rows = [];
let editing = null; // row being edited, or null when inserting

const $ = id => document.getElementById(id);

function toast(msg, ok = true) {
    const t = $('toast');
    t.textContent = msg;
    t.className = 'show ' + (ok ? 'ok' : 'err');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.className = '', 3500);
}

async function request(method, path, body) {
    const res = await fetch(API + path, {
        method,
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: body ? JSON.stringify(body) : undefined,
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        const details = data.errors ? Object.values(data.errors).flat().join('\n') : '';
        throw new Error(details || data.message || ('Error ' + res.status));
    }
    return data;
}

function keyPath(row) {
    return RESOURCES[current].key.map(k => row[k]).join('/');
}

function escapeHtml(v) {
    return String(v ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

function renderTabs() {
    $('tabs').innerHTML = Object.entries(RESOURCES).map(([k, r]) =>
        `<button class="tab ${k === current ? 'active' : ''}" data-tab="${k}">${r.label}</button>`).join('');
    document.querySelectorAll('.tab').forEach(b => b.onclick = () => switchTab(b.dataset.tab));
}

async function renderForm() {
    const res = RESOURCES[current];
    $('form-title').textContent = editing ? `✏️ កែប្រែ ${res.label}` : `➕ បញ្ចូល ${res.label} ថ្មី`;
    $('btn-save').textContent = editing ? '💾 រក្សាទុកការកែប្រែ' : '💾 រក្សាទុក';
    $('btn-cancel').textContent = editing ? 'បោះបង់' : 'សម្អាត';

    // Load dropdown options (e.g. Book Types for a Book)
    const options = {};
    for (const f of res.fields.filter(f => f.source)) {
        try { options[f.name] = await request('GET', '/' + f.source); } catch { options[f.name] = []; }
    }

    $('fields').innerHTML = res.fields.map(f => {
        const value = editing ? (editing[f.name] ?? '') : '';
        const req = f.required ? ' <span class="req">*</span>' : '';
        const disabled = editing && f.isKey ? 'disabled' : '';
        let input;
        if (f.type === 'select') {
            const opts = f.source
                ? [['', '-- ជ្រើសរើស --'], ...options[f.name].map(o => [o[f.optValue], `${o[f.optValue]} - ${o[f.optText]}`])]
                : f.options;
            input = `<select name="${f.name}" ${disabled}>${opts.map(([v, t]) =>
                `<option value="${escapeHtml(v)}" ${String(v) === String(value) ? 'selected' : ''}>${escapeHtml(t)}</option>`).join('')}</select>`;
        } else if (f.type === 'textarea') {
            input = `<textarea name="${f.name}">${escapeHtml(value)}</textarea>`;
        } else {
            input = `<input type="${f.type || 'text'}" name="${f.name}" value="${escapeHtml(value)}" ${disabled}>`;
        }
        return `<label>${f.label}${req}</label>${input}`;
    }).join('');
}

function renderTable() {
    const res = RESOURCES[current];
    const names = res.columnNames || res.columns;
    $('list-title').textContent = `📋 បញ្ជី ${res.label}`;
    $('count').textContent = `${rows.length} records`;
    $('thead').innerHTML = `<tr>${names.map(n => `<th>${n}</th>`).join('')}<th>Actions</th></tr>`;

    if (!rows.length) {
        $('tbody').innerHTML = `<tr><td class="empty" colspan="${names.length + 1}">មិនទាន់មានទិន្នន័យ</td></tr>`;
        return;
    }
    $('tbody').innerHTML = rows.map((r, i) => `<tr>
        ${res.columns.map(c => `<td>${escapeHtml(typeof c === 'function' ? c(r) : r[c])}</td>`).join('')}
        <td class="row-actions">
            <button class="btn-edit" data-edit="${i}">✏️ កែ</button>
            <button class="btn-del" data-del="${i}">🗑️ លុប</button>
        </td></tr>`).join('');

    document.querySelectorAll('[data-edit]').forEach(b => b.onclick = () => startEdit(rows[b.dataset.edit]));
    document.querySelectorAll('[data-del]').forEach(b => b.onclick = () => remove(rows[b.dataset.del]));
}

async function loadRows() {
    try {
        rows = await request('GET', '/' + current);
    } catch (e) {
        rows = [];
        toast('មិនអាចទាញទិន្នន័យ: ' + e.message, false);
    }
    renderTable();
}

async function switchTab(tab) {
    current = tab;
    editing = null;
    renderTabs();
    await renderForm();
    await loadRows();
}

async function startEdit(row) {
    editing = row;
    await renderForm();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function remove(row) {
    if (!confirm('តើអ្នកពិតជាចង់លុបទិន្នន័យនេះមែនទេ?')) return;
    try {
        const data = await request('DELETE', `/${current}/${keyPath(row)}`);
        toast('✅ ' + (data.message || 'Deleted'));
        if (editing && keyPath(editing) === keyPath(row)) { editing = null; await renderForm(); }
        await loadRows();
    } catch (e) {
        toast('❌ ' + e.message, false);
    }
}

$('form').onsubmit = async (e) => {
    e.preventDefault();
    const res = RESOURCES[current];
    const body = {};
    for (const f of res.fields) {
        if (editing && f.isKey) continue; // keys cannot be changed
        const v = e.target.elements[f.name].value.trim();
        body[f.name] = v === '' ? null : (f.type === 'number' || f.source ? Number(v) : v);
    }
    try {
        const data = editing
            ? await request('PUT', `/${current}/${keyPath(editing)}`, body)
            : await request('POST', `/${current}`, body);
        toast('✅ ' + (data.message || 'Saved'));
        editing = null;
        await renderForm();
        await loadRows();
    } catch (err) {
        toast('❌ ' + err.message, false);
    }
};

$('btn-cancel').onclick = async () => { editing = null; await renderForm(); };

switchTab(current);
</script>
</body>
</html>
