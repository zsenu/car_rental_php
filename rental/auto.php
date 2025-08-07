<?php
        $cars = json_decode(file_get_contents('autok.json'), true);
        $carId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $car = array_filter($cars, fn($c) => $c['id'] == $carId);
        $car = array_shift($car);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autó részletei</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_auto.css" rel="stylesheet">
</head>
<body>
    
    <header class="d-flex justify-content-between align-items-center">
        <h1>iKarRental</h1>
        <div class="buttons">
            <a href="#" class="btn">Bejelentkezés</a>
            <a href="register.php" class="btn">Regisztráció</a>
        </div>
    </header>

    <main>
        <?php if ($car): ?>
            <div class="car-details">
                <div class="car-image">
                    <img src="<?= $car['image'] ?>" alt="<?= $car['brand'] . ' ' . $car['model'] ?>">
                </div>
                <div class="car-info">
                    <h2><?= $car['brand'] . ' ' . $car['model'] ?></h2>
                    <p>Üzemanyag: <?= $car['fuel_type'] ?></p>
                    <p>Gyártási év: <?= $car['year'] ?></p>
                    <p>Váltó típusa: <?= $car['transmission'] ?></p>
                    <p>Férőhelyek száma: <?= $car['passengers'] ?></p>
                    <div class="price">Napi díj: <?= number_format($car['daily_price_huf'], 0, '.', ' ') ?> Ft</div>
                    <div class="car-buttons">
                        <button class="date-btn">Dátum kiválasztása</button>
                        <button class="book-btn">Lefoglalom</button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center">Az autó adatai nem találhatók.</p>
        <?php endif; ?>

        <div class="back-button">
            <a href="index.php">Vissza a főoldalra</a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
