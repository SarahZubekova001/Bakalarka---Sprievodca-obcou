<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upraviť reštauráciu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Upraviť reštauráciu</h1>

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

        <!-- Formulár na úpravu reštaurácie -->
        <form action="{{ route('restaurants.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Názov -->
            <div class="mb-3">
                <label for="name" class="form-label">Názov reštaurácie</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $restaurant->name }}" required>
            </div>

            <!-- Otváracie hodiny -->
            <div class="mb-3">
                <label for="opening_hours" class="form-label">Otváracie hodiny</label>
                <input type="text" class="form-control" id="opening_hours" name="opening_hours" value="{{ $restaurant->opening_hours }}">
            </div>

            <!-- Telefónne číslo -->
            <div class="mb-3">
                <label for="phone_number" class="form-label">Telefónne číslo</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $restaurant->phone_number }}">
            </div>

            <!-- Adresa -->
            <h4>Adresa</h4>
            <div class="mb-3">
                <label for="town" class="form-label">Obec/Mesto</label>
                <input type="text" class="form-control" id="town" name="town" value="{{ $restaurant->address->town->name ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label for="street" class="form-label">Ulica</label>
                <input type="text" class="form-control" id="street" name="street" value="{{ $restaurant->address->street ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label for="descriptive_number" class="form-label">Súpisné číslo</label>
                <input type="text" class="form-control" id="descriptive_number" name="descriptive_number" value="{{ $restaurant->address->descriptive_number ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label for="postal_code" class="form-label">PSČ</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ $restaurant->address->postal_code ?? '' }}" required>
            </div>

            <!-- URL adresa -->
            <div class="mb-3">
                <label for="url_address" class="form-label">Webová stránka</label>
                <input type="text" class="form-control" id="url_address" name="url_address" value="{{ $restaurant->url_address }}">
            </div>

             <!-- Obrázky -->
             <h3>Existujúce obrázky</h3>
            <div class="row">
                @if(isset($images) && count($images) > 0)
                    @foreach($images as $image)
                    <div class="col-md-3 text-center">
                        <img src="{{ asset('storage/' . $image->path) }}" class="img-fluid" alt="Obrázok">
                        <button type="button" class="btn btn-danger mt-2" onclick="deleteImage({{ $image->id }})">Odstrániť</button>
                    </div>
                    @endforeach
                @else
                    <p>Žiadne obrázky neboli nahraté.</p>
                @endif
            </div>

            <!-- Nahrať nové obrázky -->
            <div class="mb-3">
                <label for="images" class="form-label">Pridať nové obrázky</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple>
            </div>


            <!-- Tlačidlá -->
            <button type="submit" class="btn btn-success">Uložiť zmeny</button>
            <a href="{{ route('restaurants.index') }}" class="btn btn-secondary">Späť</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
