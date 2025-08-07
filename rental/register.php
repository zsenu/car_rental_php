<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (empty($_GET['fullname'])) { $errors[] = "Teljes név megadása kötelező!"; }
    elseif (strlen($_GET['fullname']) < 5) { $errors[] = 'A teljes név legalább 5 karakter hosszú legyen.'; }
    if (empty($_GET['email'])) { $errors[] = "E-mail cím megadása kötelező!"; }
    elseif (strlen($_GET['email']) < 5 || !strpos($_GET['email'], '@') || !strpos($_GET['email'], '.')) { $errors[] = 'Érvényes email-címet adjon meg!'; }
    if (empty($_GET['password'])) { $errors[] = "Jelszó megadása kötelező!"; }
    elseif (strlen($_GET['password']) < 5) { $errors[] = 'A jelszó legalább 5 karakter hosszú legyen.'; }

    if (empty($errors))
    {
        $user =
        [
            'fullname' => $_GET['fullname'],
            'email' => $_GET['email'],
            'password' => password_hash($_GET['password'], PASSWORD_DEFAULT),
        ];

        $usersFile = 'users.json';
        $users = json_decode(file_get_contents($usersFile), true);
        $users[] = $user;

        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style_register.css" rel="stylesheet">
</head>
<body>
    <header class="d-flex justify-content-between align-items-center">
        <h1>Regisztráció</h1>
        <a href="index.php" class="back-button">Vissza a főoldalra</a>
    </header>

    <main>
        <form method="GET" action="">
            <h2>Regisztrációs űrlap</h2>

            <?php if (!empty($_GET) && !empty($errors)): ?>
                <div class="errors">
                    <ul class="error">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="fullname" class="form-label">Teljes név</label>
                <input type="text" name="fullname" id="fullname" class="form-control" value="<?= htmlspecialchars($_GET['fullname'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail cím</label>
                <input type="text" name="email" id="email" class="form-control" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Jelszó</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Regisztráció</button>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>