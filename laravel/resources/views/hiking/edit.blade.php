<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upraviť turistickú trasu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Upraviť turistickú trasu</h1>

        <form action="{{ route('hiking.update', $hiking->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Názov trasy -->
            <div class="mb-3">
                <label for="name" class="form-label">Názov</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $hiking->name }}" required>
            </div>

            <!-- Dĺžka trasy -->
            <div class="mb-3">
                <label for="length" class="form-label">Dĺžka (km)</label>
                <input type="number" class="form-control" id="length" name="length" value="{{ $hiking->length }}">
            </div>

            <!-- Trvanie (hodiny a minúty) -->
            <div class="mb-3">
                <label for="hours" class="form-label">Trvanie (hodiny)</label>
                <select class="form-control" id="hours" name="hours">
                    @for($i = 0; $i <= 23; $i++)
                        <option value="{{ $i }}" {{ $hiking->hours == $i ? 'selected' : '' }}>{{ $i }} hod</option>
                    @endfor
                </select>
            </div>

            <div class="mb-3">
                <label for="minutes" class="form-label">Trvanie (minúty)</label>
                <select class="form-control" id="minutes" name="minutes">
                    @for($i = 0; $i <= 59; $i += 5)
                        <option value="{{ $i }}" {{ $hiking->minutes == $i ? 'selected' : '' }}>{{ $i }} min</option>
                    @endfor
                </select>
            </div>

            <!-- Obtiažnosť -->
            <div class="mb-3">
                <label for="difficulty" class="form-label">Obtiažnosť (1-5)</label>
                <select class="form-control" id="difficulty" name="difficulty">
                    <option value="" {{ is_null($hiking->difficulty) ? 'selected' : '' }}>Neurčená</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ $hiking->difficulty == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <!-- Kategória -->
            <div class="mb-3">
                <label for="id_category" class="form-label">Kategória</label>
                <select class="form-control" id="id_category" name="id_category">
                    <option value="" {{ is_null($hiking->id_category) ? 'selected' : '' }}>Nezaradené</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $hiking->id_category == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sezóna -->
            <div class="mb-3">
                <label for="id_season" class="form-label">Sezóna</label>
                <select class="form-control" id="id_season" name="id_season">
                    <option value="" {{ is_null($hiking->id_season) ? 'selected' : '' }}>Nezaradené</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}" {{ $hiking->id_season == $season->id ? 'selected' : '' }}>
                            {{ $season->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Obrázky -->
            <h3>Existujúce obrázky</h3>
            <div class="row">
                @if($hiking->gallery && count($hiking->gallery->images) > 0)
                    @foreach($hiking->gallery->images as $image)
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
            <button type="submit" class="btn btn-primary">Uložiť zmeny</button>
            <a href="{{ route('hiking.index') }}" class="btn btn-secondary">Späť</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
