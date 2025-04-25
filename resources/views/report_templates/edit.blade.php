<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Template Laporan</title>
    <style>
        /* Basic styling - replace with your framework or custom CSS */
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 150px; }
        .error-message { color: red; font-size: 0.9em; margin-top: 5px; }
         .hint { font-size: 0.9em; color: #666; margin-top: 5px; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .back-link { display: inline-block; margin-top: 15px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

    <h1>Edit Template Laporan: {{ $template->name }}</h1>

    <form action="{{ route('template.update', $template) }}" method="POST">
        @csrf @method('PUT') <div class="form-group">
            <label for="name">Nama Template:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $template->name) }}" required>
             @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="content">Isi Template:</label>
            <textarea id="content" name="content" required>{{ old('content', $template->content) }}</textarea>
             <div class="hint">Gunakan kurung kurawal ganda untuk placeholder, contoh: 'total_pasien' ,  'kamar_terisi' </div>
            @error('content')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">Update Template</button>
    </form>

    <a href="{{ route('template.index') }}" class="back-link">Kembali ke Daftar Template</a>

    </body>
</html>