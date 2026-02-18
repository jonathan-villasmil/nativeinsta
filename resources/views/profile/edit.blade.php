@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 32px 16px;">
    <h2 style="font-size:20px;font-weight:700;margin:0 0 24px;">Editar perfil</h2>

    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- Avatar --}}
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--border);">
                <img src="{{ $user->avatar_url }}" alt="avatar"
                     id="avatar-preview"
                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid var(--border);">
                <div>
                    <label style="cursor:pointer;color:var(--primary);font-weight:600;font-size:14px;"
                           onclick="document.getElementById('avatar-input').click()">
                        Cambiar foto de perfil
                    </label>
                    <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none;"
                           onchange="previewAvatar(this)">
                    @error('avatar')
                        <p style="color:var(--danger);font-size:13px;margin:4px 0 0;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Name --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:600;margin-bottom:6px;">Nombre</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       style="width:100%;border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:14px;box-sizing:border-box;font-family:inherit;">
                @error('name')
                    <p style="color:var(--danger);font-size:13px;margin:4px 0 0;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Username --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:14px;font-weight:600;margin-bottom:6px;">Nombre de usuario</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                       style="width:100%;border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:14px;box-sizing:border-box;font-family:inherit;">
                @error('username')
                    <p style="color:var(--danger);font-size:13px;margin:4px 0 0;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bio --}}
            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:14px;font-weight:600;margin-bottom:6px;">Biografía</label>
                <textarea name="bio" rows="3" maxlength="150"
                          style="width:100%;border:1px solid var(--border);border-radius:8px;padding:10px 12px;font-size:14px;resize:vertical;box-sizing:border-box;font-family:inherit;"
                          placeholder="Cuéntanos algo sobre ti...">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <p style="color:var(--danger);font-size:13px;margin:4px 0 0;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn-primary">Guardar cambios</button>
                <a href="{{ route('profile.show', $user) }}" class="btn-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatar-preview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
