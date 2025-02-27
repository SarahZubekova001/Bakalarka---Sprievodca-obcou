<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správa príspevkov</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Správa príspevkov</h1>

        <!-- Úspešná správa -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tlačidlo na vytvorenie nového príspevku -->
        <a href="{{ route('posts.create') }}" class="btn btn-primary mb-3">Pridať nový príspevok</a>

        <!-- Tabuľka s príspevkami -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Názov</th>
                    <th>Popis</th>
                    <th>Kategória</th>
                    <th>Sezóna</th>
                    <th>Adresa</th>
                    <th>Akcie</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                    <tr>
                        <td>{{ $post->name }}</td>
                        <td>{{ $post->description ?? 'Bez popisu' }}</td>
                        <td>{{ $post->category->name ?? 'Bez kategórie' }}</td>
                        <td>{{ $post->season->name ?? 'Celoročné' }}</td>
                        <td>{{ $post->address->name ?? 'Bez adresy' }}</td>
                        <td>
                            <a href="{{ route('posts.show', $post->id) }}" class="btn btn-info btn-sm">Detail</a>
                            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning btn-sm">Upraviť</a>
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Naozaj chcete vymazať tento príspevok?')">
                                    Vymazať
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Ak nie sú žiadne príspevky -->
        @if($posts->isEmpty())
            <p class="text-center">Žiadne príspevky neboli nájdené.</p>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
