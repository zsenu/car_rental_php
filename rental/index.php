<?php

$cars = json_decode(file_get_contents('autok.json'), true);

function filterCars($cars, $transmission = null, $seats = null, $minPrice = null, $maxPrice = null)
{
    $filteredCars = $cars;
    
    if ($transmission !== null && $transmission !== '')
    {
        $filteredCars = array_filter($filteredCars, function($car) use ($transmission)
        { return $car['transmission'] == $transmission; });
    }

    if ($seats !== null && $seats !== '')
    {
        $filteredCars = array_filter($filteredCars, function($car) use ($seats)
        { return $car['passengers'] >= $seats; });
    }

    if ($minPrice !== null && $minPrice !== '')
    {
        $filteredCars = array_filter($filteredCars, function($car) use ($minPrice)
        { return $car['daily_price_huf'] >= $minPrice; });
    }

    if ($maxPrice !== null && $maxPrice !== '')
    {
        $filteredCars = array_filter($filteredCars, function($car) use ($maxPrice)
        { return $car['daily_price_huf'] <= $maxPrice; });
    }

    return $filteredCars;
}

if ($_SERVER["REQUEST_METHOD"] == "GET")
{
    $transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
    $minPrice = isset($_GET['min_price']) && !empty($_GET['min_price']) ? intval($_GET['min_price']) : 1;
    $maxPrice = isset($_GET['max_price']) && !empty($_GET['max_price']) ? intval($_GET['max_price']) : PHP_INT_MAX;
    $seats = isset($_GET['passengers']) && !empty($_GET['passengers']) ? intval($_GET['passengers']) : 1;
    
    $errors = [];
    if ($seats <= 0) { $errors[] = "Férőhelyek száma csak pozitív szám lehet!"; $seats = 1; }
    if ($minPrice <= 0 || $maxPrice <= 0) { $errors[] = "Az ár pozitív szám lehet!"; $minPrice = 1; $maxPrice = PHP_INT_MAX; }
    if ($minPrice > $maxPrice) { $errors[] = "A minimum ár nem haladhatja meg a maximumot!"; $minPrice = 1; $maxPrice = PHP_INT_MAX; }

    $filteredCars = filterCars($cars, $transmission, $seats, $minPrice, $maxPrice);
    
}
else { $filteredCars = $cars; }
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autókölcsönző</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./style.css" rel="stylesheet">
</head>
<body>

    <header class="d-flex justify-content-between align-items-center">
        <h1>iKarRental</h1>
        <div class="buttons">
            <a href="#" class="btn">Bejelentkezés</a>
            <a href="register.php" class="btn">Regisztráció</a>
            <a href="admin.php" class="btn add-car-btn">Admin panel</a>
        </div>
    </header>

    <main class="container">
        <h1 class="text-center mb-4">Kölcsönözz autókat könnyedén!</h1>
        <form method="GET" action="/index.php" novalidate>
        <div class="filters row mb-4 g-2">
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <input type="date" name="start_date" placeholder="Kezdő dátum" class="form-control">
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <input type="date" name="end_date" placeholder="Befejező dátum" class="form-control">
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <select name="transmission" class="form-select">
                    <option value="">Váltó típusa</option>
                    <option value="Automatic">Automatic</option>
                    <option value="Manual">Manual</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <input type="number" name="passengers" placeholder="Férőhely" class="form-control">
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <input type="number" name="min_price" placeholder="Min. ár" class="form-control">
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                <input type="number" name="max_price" placeholder="Max. ár" class="form-control">
            </div>
            <div class="col-12 d-flex justify-content-center">
                <button class="btn">Szűrés</button>
            </div>
        </div>
        </form>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?php foreach ($errors as $error): ?>
                    <?= htmlspecialchars($error) ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="car-list row">
            <?php foreach ($filteredCars as $car): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="car-card">
                        <a href="auto.php?id=<?= $car['id'] ?>">
                        <img src="<?= $car['image'] ?>" alt="<?= $car['brand'] . ' ' . $car['model'] ?>" class="img-fluid car-image">
                        </a>
                        <h3><?= $car['brand'] . ' ' . $car['model'] ?></h3>
                        <p><?= $car['passengers'] ?> férőhely - <?= $car['transmission'] ?></p>
                        <div class="price"><?= number_format($car['daily_price_huf'], 0, '.', ' ') ?> Ft / nap</div>
                        <a href="auto.php?id=<?= $car['id'] ?>"><button class="btn mt-2">Foglalás</button></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>