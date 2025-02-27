<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upraviť príspevok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Upraviť príspevok</h1>
           
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

        <!-- Formulár na úpravu príspevku -->
        <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Názov -->
            <div class="mb-3">
                <label for="name" class="form-label">Názov</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $post->name }}" required>
            </div>

            <!-- Popis -->
            <div class="mb-3">
                <label for="description" class="form-label">Popis</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $post->description }}</textarea>
            </div>

            <!-- Kategória -->
            <div class="mb-3">
                <label for="id_category" class="form-label">Kategória</label>
                <select class="form-select" id="id_category" name="id_category" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $category->id == $post->id_category ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sezóna -->
            <div class="mb-3">
                <label for="id_season" class="form-label">Sezóna</label>
                <select class="form-select" id="id_season" name="id_season">
                    <option value="">Celoročné</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}" {{ $season->id == $post->id_season ? 'selected' : '' }}>
                            {{ $season->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Adresa -->
            <h4>Adresa</h4>
            <div class="mb-3">
                <label for="town" class="form-label">Obec/Mesto</label>
                <input type="text" class="form-control" id="town" name="town" value="{{ $post->address->town->name ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label for="street" class="form-label">Ulica</label>
                <input type="text" class="form-control" id="street" name="street" value="{{ $post->address->street ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label for="descriptive_number" class="form-label">Súpisné číslo</label>
                <input type="text" class="form-control" id="descriptive_number" name="descriptive_number" value="{{ $post->address->descriptive_number ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label for="postal_code" class="form-label">PSČ</label>
                <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ $post->address->postal_code ?? '' }}" required>
            </div>

            <!-- Otváracie hodiny -->
            <div class="mb-3">
                <label for="opening_hours" class="form-label">Otváracie hodiny</label>
                <input type="text" class="form-control" id="opening_hours" name="opening_hours" value="{{ $post->opening_hours }}">
            </div>

            <!-- URL adresa -->
            <div class="mb-3">
                <label for="url_address" class="form-label">URL adresa</label>
                <input type="text" class="form-control" id="url_address" name="url_address" value="{{ $post->url_address }}">
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
            <button type="submit" class="btn btn-success" onclick="console.log('Form submitted')">Uložiť zmeny</button>

            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Späť</a>
        </form>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            console.log('Form submit event fired');
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
