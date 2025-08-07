<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET))
{
    $errors = [];
    $cars = json_decode(file_get_contents('autok.json'), true);

    if ($_GET['action'] == 'del')
    {
        if (empty($_GET['azon'])) { $errors[] = "A művelethez azonosító megadása szükséges!"; }
        elseif ($_GET['azon'] < 1 || $_GET['azon'] > end($cars)['id']) { $errors[] = "Érvénytelen azonosító!"; }

        if (empty($errors))
        {
            $idToDelete = (int)$_GET['azon'];
            $cars = array_values(array_filter($cars, fn($car) => $car['id'] !== $idToDelete));
            foreach ($cars as $index => &$car) { $car['id'] = $index + 1; }
            file_put_contents('autok.json', json_encode($cars, JSON_PRETTY_PRINT));
        }
    }
    else
    {
        $fields = ['brand', 'model', 'year', 'fuel_type', 'transmission', 'passengers', 'daily_price_huf', 'image'];
    
        foreach ($fields as $field) { if (empty($_GET[$field])) { $errors[] = "$field hiányzik."; } }
        if (!empty($_GET['year']) && ($_GET['year'] < 1885 || $_GET['year'] > 2025)) { $errors[] = "Gyártási év 1885 és 2025 közötti lehet!"; }
        if (!empty($_GET['transmission']) && ($_GET['transmission'] != "Automatic" && $_GET['transmission'] != "Manual")) { $errors[] = "Érvénytelen váltó típus!"; }
        if (!empty($_GET['passengers']) && ($_GET['passengers'] < 1 || $_GET['passengers'] > 9)) { $errors[] = "Érvénytelen férőhely szám!"; }
        if ((!empty($_GET['daily_price_huf']) && $_GET['daily_price_huf'] < 1)) { $errors[] = "Érvénytelen napi díj!"; }

        if ($_GET['action'] == 'mod')
        {
            if (empty($_GET['azon'])) { $errors[] = "A művelethez azonosító megadása szükséges!"; }
            elseif ($_GET['azon'] < 1 || $_GET['azon'] > end($cars)['id']) { $errors[] = "Érvénytelen azonosító!"; }
        }
        if (empty($errors))
        {
            if ($_GET['action'] == 'add')
            {
                $newId = end($cars)['id'] + 1;
        
                $newCar =
                [
                    'id' => $newId,
                    'brand' => htmlspecialchars($_GET['brand']),
                    'model' => htmlspecialchars($_GET['model']),
                    'year' => (int)$_GET['year'],
                    'fuel_type' => htmlspecialchars($_GET['fuel_type']),
                    'transmission' => htmlspecialchars($_GET['transmission']),
                    'passengers' => (int)$_GET['passengers'],
                    'daily_price_huf' => (int)$_GET['daily_price_huf'],
                    'image' => htmlspecialchars($_GET['image']),
                ];
        
                $cars[] = $newCar;
                file_put_contents('autok.json', json_encode($cars, JSON_PRETTY_PRINT));
            }
            else
            {
                $newCar =
                [
                    'id' => (int)$_GET['azon'],
                    'brand' => htmlspecialchars($_GET['brand']),
                    'model' => htmlspecialchars($_GET['model']),
                    'year' => (int)$_GET['year'],
                    'fuel_type' => htmlspecialchars($_GET['fuel_type']),
                    'transmission' => htmlspecialchars($_GET['transmission']),
                    'passengers' => (int)$_GET['passengers'],
                    'daily_price_huf' => (int)$_GET['daily_price_huf'],
                    'image' => htmlspecialchars($_GET['image']),
                ];
                $idToUpdate = (int)$_GET['azon'];
                foreach ($cars as &$car)
                {
                    if ($car['id'] == $idToUpdate)
                    {
                        $car['brand'] = $_GET['brand'];
                        $car['model'] = $_GET['model'];
                        $car['year'] = (int)$_GET['year'];
                        $car['fuel_type'] = $_GET['fuel_type'];
                        $car['passengers'] = (int)$_GET['passengers'];
                        $car['daily_price_huf'] = (int)$_GET['daily_price_huf'];
                        $car['image'] = $_GET['image'];
                        break;
                    }
                }
                file_put_contents('autok.json', json_encode($cars, JSON_PRETTY_PRINT));
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_admin.css" rel="stylesheet">
</head>
<body>
    <header>
        <h1>iKarRental - Adatbázis szerkesztése</h1>
    </header>

    <div class="form-container">
        <h2>Autó adatok</h2>
        <?php
        if (!empty($_GET))
        {
            if (!empty($errors))
            {
                echo '<div class="alert alert-danger">Hibák:<ul>';
                foreach ($errors as $error)
                {
                    echo "<li>$error</li>";
                }
                echo '</ul></div>';
            }
            else { echo 'Sikeres művelet!'; }
        }
        ?>
        <form method="GET" action="./admin.php" novalidate>
            <div class="mb-3">
                <label for="brand" class="form-label">Márka</label>
                <input type="text" class="form-control" id="brand" name="brand">
            </div>
            <div class="mb-3">
                <label for="model" class="form-label">Típus</label>
                <input type="text" class="form-control" id="model" name="model">
            </div>
            <div class="mb-3">
                <label for="year" class="form-label">Gyártási év</label>
                <input type="number" class="form-control" id="year" name="year">
            </div>
            <div class="mb-3">
                <label for="fuel_type" class="form-label">Üzemanyag</label>
                <input type="text" class="form-control" id="fuel_type" name="fuel_type">
            </div>
            <div class="mb-3">
                <label for="transmission" class="form-label">Váltó típusa</label>
                <input type="text" class="form-control" id="transmission" name="transmission">
            </div>
            <div class="mb-3">
                <label for="passengers" class="form-label">Férőhelyek száma</label>
                <input type="number" class="form-control" id="passengers" name="passengers">
            </div>
            <div class="mb-3">
                <label for="daily_price_huf" class="form-label">Napidíj (Ft)</label>
                <input type="number" class="form-control" id="daily_price_huf" name="daily_price_huf">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Kép URL</label>
                <input type="text" class="form-control" id="image" name="image">
            </div>
            <div class="mb-3">
                <input type="radio" id="add" name="action" value="add" checked>
                <label for="add">Hozzáadás</label><br>
                <input type="radio" id="mod" name="action" value="mod">
                <label for="mod">Módosítás</label><br>
                <input type="radio" id="del" name="action" value="del">
                <label for="del">Törlés</label>
            </div>
            <div class="mb-3">
                <label for="azon" class="form-label">Érintett rekord azonosítója:</label>
                <input type="number" class="form-control" id="azon" name="azon">
            </div>
            <button type="submit" class="btn btn-primary">Művelet végrehajtása</button>
        </form>
        <a href="index.php"><button class="btn btn-primary">Vissza a főoldalra</button></a>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
