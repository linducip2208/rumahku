@if(config('app.debug'))
    <div style="margin-top:1rem;padding:.75rem .9rem;border:1px dashed #d97706;border-radius:.5rem;background:#fffbeb;font-size:.8rem;line-height:1.5">
        <strong>Demo lokal</strong><br>
        Admin: <code>admin@rumah123.local</code> / <code>password</code><br>
        Agen: <code>agen1@rumah123.local</code> / <code>password</code>
        <span style="opacity:.7">(hanya tampil saat APP_DEBUG=true)</span>
    </div>
@endif
