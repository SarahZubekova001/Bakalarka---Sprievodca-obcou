<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pridať novú reštauráciu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Pridať novú reštauráciu</h1>

        <!-- Zobrazenie chýb vo formulári -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulár na pridanie reštaurácie -->
        <form action="{{ route('restaurants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Názov -->
            <div class="mb-3">
                <label for="name" class="form-label">Názov reštaurácie</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <!-- Otváracie hodiny -->
            <div class="mb-3">
                <label for="opening_hours" class="form-label">Otváracie hodiny</label>
                <input type="text" class="form-control" id="opening_hours" name="opening_hours">
            </div>

            <!-- Telefónne číslo -->
            <div class="mb-3">
                <label for="phone_number" class="form-label">Telefónne číslo</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number">
            </div>

            <!-- Adresa -->
            <h4>Adresa</h4>
            <div class="mb-3">
                <label for="town" class="form-label">Obec/Mesto</label>
                <input type="text" class="form-control" id="town" name="town" required>
            </div>
            <div class="mb-3">
                <label for="street" class="form-label">Ulica</label>
                <input type="text" class="form-control" id="street" name="street" required>
            </div>
            <div class="mb-3">
                <label for="descriptive_number" class="form-label">Súpisné číslo</label>
                <input type="text" class="form-control" id="descriptive_number" name="descriptive_number" required>
            </div>
            <div class="mb-3">
                <label for="postal_code" class="form-label">PSČ</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" required>
            </div>

            <!-- URL adresa -->
            <div class="mb-3">
                <label for="url_address" class="form-label">Webová stránka</label>
                <input type="text" class="form-control" id="url_address" name="url_address">
            </div>

            <div class="mb-3">
                <label for="images" class="form-label">Nahraj obrázky (min. 1)</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple required>
            </div>

            <!-- Tlačidlá -->
            <button type="submit" class="btn btn-success">Pridať reštauráciu</button>
            <a href="{{ route('restaurants.index') }}" class="btn btn-secondary">Späť</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
