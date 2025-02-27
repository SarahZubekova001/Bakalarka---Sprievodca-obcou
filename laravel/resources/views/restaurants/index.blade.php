<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoznam reštaurácií</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Zoznam reštaurácií</h1>

        <!-- Flash správy -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tlačidlo na pridanie novej reštaurácie -->
        <a href="{{ route('restaurants.create') }}" class="btn btn-primary mb-3">Pridať reštauráciu</a>

        <!-- Tabuľka reštaurácií -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Názov</th>
                    <th>Otváracie hodiny</th>
                    <th>Telefón</th>
                    <th>Adresa</th>
                    <th>Akcie</th>
                </tr>
            </thead>
            <tbody>
                @foreach($restaurants as $index => $restaurant)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $restaurant->name }}</td>
                        <td>{{ $restaurant->opening_hours ?? 'Nezadané' }}</td>
                        <td>{{ $restaurant->phone_number ?? 'Nezadané' }}</td>
                        <td>
                            {{ $restaurant->address->street ?? 'Nezadaná ulica' }},
                            {{ $restaurant->address->descriptive_number ?? 'č.?' }},
                            {{ $restaurant->address->postal_code ?? 'PSČ nezadané' }}
                        </td>
                        <td>
                            <!-- Tlačidlo na úpravu -->
                            <a href="{{ route('restaurants.edit', $restaurant->id) }}" class="btn btn-warning btn-sm">Upraviť</a>

                            <!-- Formulár na vymazanie -->
                            <form action="{{ route('restaurants.destroy', $restaurant->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Naozaj chcete vymazať túto reštauráciu?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Vymazať</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Ak nie sú žiadne reštaurácie -->
        @if($restaurants->isEmpty())
            <p class="text-muted">Zatiaľ neexistujú žiadne reštaurácie.</p>
        @endif

        <!-- Odkaz na hlavnú stránku -->
        <a href="{{ url('/') }}" class="btn btn-secondary mt-3">Späť na hlavnú stránku</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
