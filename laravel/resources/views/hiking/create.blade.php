<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pridať turistickú trasu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Pridať turistickú trasu</h1>

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

        <!-- Formulár na pridanie trasy -->
        <form action="{{ route('hiking.store') }}" method="POST">
            @csrf

            <!-- Názov -->
            <div class="mb-3">
                <label for="name" class="form-label">Názov trasy</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <!-- Dĺžka -->
            <div class="mb-3">
                <label for="length" class="form-label">Dĺžka (km)</label>
                <input type="number" class="form-control" id="length" name="length">
            </div>

            <!-- Trvanie - Hodiny -->
            <div class="mb-3">
                <label for="hours" class="form-label">Trvanie (hodiny)</label>
                <select class="form-control" id="hours" name="hours" >
                    @for($i = 0; $i <= 23; $i++)
                        <option value="{{ $i }}">{{ $i }} hod</option>
                    @endfor
                </select>
            </div>

            <!-- Trvanie - Minúty -->
            <div class="mb-3">
                <label for="minutes" class="form-label">Trvanie (minúty)</label>
                <select class="form-control" id="minutes" name="minutes" >
                    @for($i = 0; $i <= 59; $i += 5)
                        <option value="{{ $i }}">{{ $i }} min</option>
                    @endfor
                </select>
            </div>


            <!-- Obtiažnosť -->
            <div class="mb-3">
                <label for="difficulty" class="form-label">Obtiažnosť (1-5)</label>
                <select class="form-control" id="difficulty" name="difficulty" >
                    <option value="" selected>Neurčená</option>
                    <option value="1">1 - Veľmi ľahká</option>
                    <option value="2">2 - Ľahká</option>
                    <option value="3">3 - Stredná</option>
                    <option value="4">4 - Ťažká</option>
                    <option value="5">5 - Veľmi ťažká</option>
                </select>
            </div>

            <!-- Kategória -->
            <div class="mb-3">
                <label for="id_category" class="form-label">Kategória</label>
                <select class="form-control" id="id_category" name="id_category" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sezóna -->
            <div class="mb-3">
                <label for="id_season" class="form-label">Sezóna</label>
                <select class="form-control" id="id_season" name="id_season" required>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}">{{ $season->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Nahrať nové obrázky -->
            <div class="mb-3">
                <label for="images" class="form-label">Pridať obrázky</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple>
            </div>


            <!-- Tlačidlá -->
            <button type="submit" class="btn btn-success">Pridať trasu</button>
            <a href="{{ route('hiking.index') }}" class="btn btn-secondary">Späť</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
