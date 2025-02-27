<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vytvoriť galériu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Vytvoriť galériu</h1>

        <form action="{{ route('galleries.store') }}" method="POST">
            @csrf

            <!-- Prepojenie s objektom -->
            <div class="mb-3">
                <label for="id_post" class="form-label">Príspevok</label>
                <select class="form-control" id="id_post" name="id_post">
                    <option value="">Nevybrané</option>
                    @foreach($posts as $post)
                        <option value="{{ $post->id }}">{{ $post->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="id_restaurant" class="form-label">Reštaurácia</label>
                <select class="form-control" id="id_restaurant" name="id_restaurant">
                    <option value="">Nevybrané</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="id_hiking" class="form-label">Turistická trasa</label>
                <select class="form-control" id="id_hiking" name="id_hiking">
                    <option value="">Nevybrané</option>
                    @foreach($hikings as $hiking)
                        <option value="{{ $hiking->id }}">{{ $hiking->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Vytvoriť</button>
            <a href="{{ route('galleries.index') }}" class="btn btn-secondary">Späť</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
