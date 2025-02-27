<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galérie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Galérie</h1>

        <a href="{{ route('galleries.create') }}" class="btn btn-success mb-3">Vytvoriť novú galériu</a>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Priradené k</th>
                    <th>Akcie</th>
                </tr>
            </thead>
            <tbody>
                @foreach($galleries as $gallery)
                    <tr>
                        <td>{{ $gallery->id }}</td>
                        <td>
                            @if($gallery->id_post) Príspevok #{{ $gallery->id_post }}
                            @elseif($gallery->id_restaurant) Reštaurácia #{{ $gallery->id_restaurant }}
                            @elseif($gallery->id_hiking) Turistická trasa #{{ $gallery->id_hiking }}
                            @else Nepriradené
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('galleries.edit', $gallery->id) }}" class="btn btn-warning btn-sm">Upraviť</a>
                            <form action="{{ route('galleries.destroy', $gallery->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Naozaj chcete vymazať túto galériu?')">Vymazať</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
