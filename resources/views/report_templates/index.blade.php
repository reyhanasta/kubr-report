<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Template Laporan</title>
    <style>
        /* Basic styling - replace with your framework or custom CSS */
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .actions a, .actions button { margin-right: 5px; padding: 5px 10px; text-decoration: none; border: 1px solid #ccc; background-color: #eee; color: #333; cursor: pointer; font-size: 0.9em; border-radius: 3px; }
        .actions form { display: inline; } /* Make delete button form inline */
        .actions button { border: 1px solid #dc3545; background-color: #dc3545; color: white; }
        .create-link { display: inline-block; margin-bottom: 15px; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>

    <h1>Daftar Template Laporan</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('template.create') }}" class="create-link">Buat Template Baru</a>

    <table>
        <thead>
            <tr>
                <th>Nama Template</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($templates as $template)
                <tr>
                    <td>{{ $template->name }}</td>
                    <td class="actions">
                        <a href="{{ route('template.edit', $template) }}">Edit</a>
                        <form action="{{ route('template.destroy', $template) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Belum ada template yang dibuat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    </body>
</html>