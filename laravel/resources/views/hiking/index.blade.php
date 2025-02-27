<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoznam turistických trás</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Zoznam turistických trás</h1>

        <!-- Flash správa -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tlačidlo na pridanie novej trasy -->
        <a href="{{ route('hiking.create') }}" class="btn btn-primary mb-3">Pridať turistickú trasu</a>

        <!-- Tabuľka s trasami -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Názov</th>
                    <th>Dĺžka (km)</th>
                    <th>Trvanie (hod)</th>
                    <th>Obtiažnosť</th>
                    <th>Kategória</th>
                    <th>Sezóna</th>
                    <th>Akcie</th>
                </tr>
            </thead>
                <tbody>
                    @foreach($hikings as $index => $hiking)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $hiking->name }}</td>
                            <td>{{ $hiking->length }} km</td>

                            <td>
                                @if($hiking->hours > 0)
                                    {{ $hiking->hours }} hod
                                @endif
                                @if($hiking->minutes > 0)
                                    {{ $hiking->minutes }} min
                                @endif
                            </td>

                            
                            <td>{{ $hiking->difficulty }}/5</td>
                            <td>{{ $hiking->category->name ?? 'Neznáma kategória' }}</td>
                            <td>{{ $hiking->season->name ?? 'Neznáma sezóna' }}</td>
                            <td>
                                <a href="{{ route('hiking.edit', $hiking->id) }}" class="btn btn-warning btn-sm">Upraviť</a>
                                <form action="{{ route('hiking.destroy', $hiking->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Naozaj chcete vymazať túto turistickú trasu?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Vymazať</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

        </table>

        <!-- Ak nie sú žiadne trasy -->
        @if($hikings->isEmpty())
            <p class="text-muted">Zatiaľ nie sú pridané žiadne turistické trasy.</p>
        @endif

        <a href="{{ url('/') }}" class="btn btn-secondary mt-3">Späť na hlavnú stránku</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
