<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Správa kategórií a sezón</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Sekcia pre Kategórie -->
        <div class="card mb-4">
            <div class="card-header">Kategórie</div>
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="category-name" class="form-label">Názov kategórie</label>
                        <input type="text" class="form-control" id="category-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category-image" class="form-label">Nahraj obrázok</label>
                        <input type="file" class="form-control" id="category-image" name="image" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Pridať kategóriu</button>
                </form>

                <h3 class="mt-4">Existujúce kategórie</h3>
                <ul class="list-group">
    @foreach($categories as $category)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>{{ $category->name }}</span>
            @if($category->image)
                <img src="{{ asset('storage/' . $category->image->path) }}" alt="Obrázok" width="50">
            @endif

            <div>
                <!-- Edit button -->
                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning btn-sm">Upraviť</a>

                <!-- Delete form -->
                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Naozaj chcete vymazať túto kategóriu?')">
                        Vymazať
                    </button>
                </form>
            </div>
        </li>
    @endforeach
</ul>

            </div>
        </div>

        <!-- Sekcia pre Sezóny -->
        <div class="card">
            <div class="card-header">Sezóny</div>
            <div class="card-body">
                <form action="{{ route('seasons.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="season-name" class="form-label">Názov sezóny</label>
                        <input type="text" class="form-control" id="season-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="season-image" class="form-label">Nahraj obrázok</label>
                        <input type="file" class="form-control" id="season-image" name="image" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Pridať sezónu</button>
                </form>

                <h3 class="mt-4">Existujúce sezóny</h3>
                <ul class="list-group">
    @foreach($seasons as $season)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>{{ $season->name }}</span>
            @if($season->image)
                <img src="{{ asset('storage/' . $season->image->path) }}" alt="Obrázok" width="50">
            @endif

            <div>
                <a href="{{ route('seasons.edit', $season->id) }}" class="btn btn-warning btn-sm">Upraviť</a>

                <form action="{{ route('seasons.destroy', $season->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Naozaj chcete vymazať túto sezónu?')">
                        Vymazať
                    </button>
                </form>
            </div>
        </li>
    @endforeach
</ul>

            </div>
        </div>
    </div>
</body>
</html>
