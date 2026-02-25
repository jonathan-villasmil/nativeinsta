@extends('layouts.app')

@section('content')
<div style="max-width:600px;margin:0 auto;padding:32px 16px;">
    <h2 style="font-size:20px;font-weight:700;margin:0 0 20px;">Buscar personas</h2>

    {{-- Search input --}}
    <div style="position:relative;margin-bottom:24px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2"
             style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;pointer-events:none;">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="search-input"
               value="{{ $query }}"
               placeholder="Buscar por nombre o usuario..."
               autocomplete="off"
               style="width:100%;background:#efefef;border:none;border-radius:10px;
                      padding:11px 14px 11px 42px;font-size:15px;outline:none;
                      box-sizing:border-box;font-family:inherit;color:var(--text);">
        {{-- Spinner --}}
        <div id="search-spinner" style="display:none;position:absolute;right:14px;top:50%;transform:translateY(-50%);">
            <svg width="18" height="18" viewBox="0 0 50 50" style="animation:spin 0.8s linear infinite;">
                <circle cx="25" cy="25" r="20" fill="none" stroke="var(--primary)" stroke-width="5"
                        stroke-dasharray="80" stroke-dashoffset="60"/>
            </svg>
        </div>
    </div>

    {{-- Results container --}}
    <div id="search-results">
        @include('search._results', ['users' => $users, 'query' => $query])
    </div>
</div>

{{-- Self-contained fetch helper — no Vite module dependency --}}
<script>
function csrfFetch(url, opts) {
    opts = opts || {};
    var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    opts.headers = Object.assign({
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': token
    }, opts.headers || {});
    return fetch(url, opts);
}

var _searchTimer = null;

function doSearch(q) {
    clearTimeout(_searchTimer);
    var input   = document.getElementById('search-input');
    var results = document.getElementById('search-results');
    var spinner = document.getElementById('search-spinner');

    if (!q.trim()) {
        _searchTimer = setTimeout(function() {
            spinner.style.display = 'block';
            csrfFetch('/search?q=', { headers: { 'X-Search-Partial': '1' } })
                .then(function(r) { return r.text(); })
                .then(function(html) { results.innerHTML = html; })
                .catch(function(){})
                .finally(function() { spinner.style.display = 'none'; });
        }, 200);
        return;
    }

    spinner.style.display = 'block';
    _searchTimer = setTimeout(function() {
        csrfFetch('/search?q=' + encodeURIComponent(q), { headers: { 'X-Search-Partial': '1' } })
            .then(function(r) { return r.text(); })
            .then(function(html) { results.innerHTML = html; })
            .catch(function(){})
            .finally(function() { spinner.style.display = 'none'; });
    }, 350);
}

document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('search-input');
    if (input) {
        input.addEventListener('input', function() { doSearch(this.value); });
        input.focus();
        input.setSelectionRange(input.value.length, input.value.length);
    }
});
</script>

@endsection
