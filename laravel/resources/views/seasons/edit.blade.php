<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upraviť sezonu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">

        <div class="container">
            <h1>Upraviť kategóriu</h1>
            
            <form action="{{ route('seasons.update', $season->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="seasons-name" class="form-label">Názov kategórie</label>
                    <input type="text" class="form-control" id="seasons-name" name="name" value="{{ $season->name }}" required>
                </div>

                <div class="mb-3">
                    <label for="seasons-image" class="form-label">Nahraj nový obrázok (voliteľné)</label>
                    <input type="file" class="form-control" id="seasons-image" name="image">
                </div>

                @if($season->image)
                    <p>Aktuálny obrázok:</p>
                    <img src="{{ asset('storage/' . $season->image->path) }}" alt="Obrázok" width="100">
                @endif

                <button type="submit" class="btn btn-success">Uložiť zmeny</button>
                <a href="{{ route('seasons.index') }}" class="btn btn-secondary">Späť</a>
            </form>
        </div>
    </div>
</body>
</html>

