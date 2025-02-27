<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správa adries</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

    <h1 class="mb-4">Pridať novú adresu</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('addresses.store') }}" method="POST" class="mb-4">
    @csrf
    <div class="mb-3">
        <label for="street" class="form-label">Ulica</label>
        <input type="text" id="street" name="street" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="descriptive_number" class="form-label">Číslo domu</label>
        <input type="number" id="descriptive_number" name="descriptive_number" class="form-control" required>
    </div>

    <div class="mb-3">
    <label for="postal_code" class="form-label">Poštové smerovacie číslo</label>
    <input type="text" id="postal_code" name="postal_code" class="form-control" required pattern="\d{3}\s?\d{2}">
</div>


    <div class="mb-3">
        <label for="town_name" class="form-label">Mesto</label>
        <input type="text" id="town_name" name="town_name" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Pridať adresu</button>
</form>


    <h2>Zoznam adries</h2>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Ulica</th>
                <th>Číslo domu</th>
                <th>Mesto</th>
                <th>PSČ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($addresses as $address)
                <tr>
                    <td>{{ $address->id }}</td>
                    <td>{{ $address->street }}</td>
                    <td>{{ $address->descriptive_number }}</td>
                    <td>{{ $address->town->name ?? 'Neznáme mesto' }}</td>
                    <td>{{ $address->postal_code }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
