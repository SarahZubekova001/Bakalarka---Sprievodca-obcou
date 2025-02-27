<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upraviť kategóriu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">

        <div class="container">
            <h1>Upraviť kategóriu</h1>
            
            <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="category-name" class="form-label">Názov kategórie</label>
                    <input type="text" class="form-control" id="category-name" name="name" value="{{ $category->name }}" required>
                </div>

                <div class="mb-3">
                    <label for="category-image" class="form-label">Nahraj nový obrázok (voliteľné)</label>
                    <input type="file" class="form-control" id="category-image" name="image">
                </div>

                @if($category->image)
                    <p>Aktuálny obrázok:</p>
                    <img src="{{ asset('storage/' . $category->image->path) }}" alt="Obrázok" width="100">
                @endif

                <button type="submit" class="btn btn-success">Uložiť zmeny</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Späť</a>
            </form>
        </div>
    </div>
</body>
</html>

