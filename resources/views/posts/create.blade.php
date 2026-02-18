@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 32px 16px;">
    <h2 style="font-size:20px;font-weight:700;margin:0 0 24px;">Crear nuevo post</h2>

    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
        <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:14px;font-weight:600;margin-bottom:8px;">Imagen *</label>
                <div id="drop-zone" style="border:2px dashed var(--border);border-radius:8px;padding:40px;text-align:center;cursor:pointer;transition:border-color 0.2s;"
                     onclick="document.getElementById('image-input').click()">
                    <div id="preview-container" style="display:none;">
                        <img id="preview-img" style="max-height:300px;max-width:100%;border-radius:8px;margin:0 auto;display:block;">
                        <p style="margin:8px 0 0;font-size:13px;color:var(--text-muted);">Haz clic para cambiar</p>
                    </div>
                    <div id="placeholder" style="color:var(--text-muted);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:48px;height:48px;margin:0 auto 12px;display:block;opacity:0.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <p style="margin:0;font-size:14px;font-weight:500;">Haz clic para seleccionar una imagen</p>
                        <p style="margin:4px 0 0;font-size:12px;">JPG, PNG, GIF — máx. 5MB</p>
                    </div>
                </div>
                <input type="file" id="image-input" name="image" accept="image/*" style="display:none;"
                       onchange="previewImage(this)">
                @error('image')
                    <p style="color:var(--danger);font-size:13px;margin:6px 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:14px;font-weight:600;margin-bottom:8px;">Caption</label>
                <textarea name="caption" rows="4" maxlength="2200"
                          style="width:100%;border:1px solid var(--border);border-radius:8px;padding:12px;font-size:14px;resize:vertical;box-sizing:border-box;font-family:inherit;"
                          placeholder="Escribe un caption...">{{ old('caption') }}</textarea>
                @error('caption')
                    <p style="color:var(--danger);font-size:13px;margin:6px 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn-primary">Publicar</button>
                <a href="{{ route('feed') }}" class="btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-container').style.display = 'block';
            document.getElementById('placeholder').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
