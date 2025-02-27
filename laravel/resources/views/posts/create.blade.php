<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vytvoriť nový príspevok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Vytvoriť nový príspevok</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Názov</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Popis</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>

            <!-- Kategória -->
            <div class="mb-3">
                <label for="id_category" class="form-label">Kategória</label>
                <select class="form-select" id="id_category" name="id_category" required>
                    <option value="" selected disabled>Vyberte kategóriu</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sezóna -->
            <div class="mb-3">
                <label for="id_season" class="form-label">Sezóna</label>
                <select class="form-select" id="id_season" name="id_season">
                    <option value="">Celoročné</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}">{{ $season->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Vstupy pre novú adresu -->
            <h4>Adresa</h4>
            <div class="mb-3">
                <label for="town" class="form-label">Obec/Mesto</label>
                <input type="text" class="form-control" id="town" name="town">
            </div>
            <div class="mb-3">
                <label for="street" class="form-label">Ulica</label>
                <input type="text" class="form-control" id="street" name="street">
            </div>
            <div class="mb-3">
                <label for="descriptive_number" class="form-label">Súpisné číslo</label>
                <input type="text" class="form-control" id="descriptive_number" name="descriptive_number" required>
            </div>
            <div class="mb-3">
                <label for="postal_code" class="form-label">PSČ</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code">
            </div>

            <div class="mb-3">
                <label for="opening_hours" class="form-label">Otváracie hodiny</label>
                <input type="text" class="form-control" id="opening_hours" name="opening_hours">
            </div>

            <div class="mb-3">
                <label for="url_address" class="form-label">URL adresa</label>
                <input type="text" class="form-control" id="url_address" name="url_address">
            </div>
            <div class="mb-3">
                <label for="images" class="form-label">Nahraj obrázky (min. 1)</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple required>
            </div>


            <button type="submit" class="btn btn-success">Uložiť príspevok</button>
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Späť</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
