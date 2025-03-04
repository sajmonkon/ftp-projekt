<?php
session_start();

// Připojení k databázi
$host = 'localhost';
$db_name = 'konecnys_filmy';
$username = 'konecnys';
$password = 'Simon2006';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Funkce pro odhlášení
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: filmova_databaze.php");
    exit;
}

// Registrace uživatele
if (isset($_POST['register'])) {
    $jmeno = $_POST['jmeno'];
    $email = $_POST['email'];
    $heslo = password_hash($_POST['heslo'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO uzivatele (jmeno, email, heslo) VALUES (?, ?, ?)");
    if ($stmt->execute([$jmeno, $email, $heslo])) {
        echo "<div class='alert alert-success'>Registrace úspěšná. <a href='filmova_databaze.php'>Přihlaste se</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Chyba při registraci.</div>";
    }
}

// Přihlášení uživatele
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $heslo = $_POST['heslo'];

    $stmt = $conn->prepare("SELECT * FROM uzivatele WHERE email = ?");
    $stmt->execute([$email]);
    $uzivatel = $stmt->fetch();

    if ($uzivatel && password_verify($heslo, $uzivatel['heslo'])) {
        $_SESSION['uzivatel_id'] = $uzivatel['id'];
        header("Location: filmy.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Nesprávný email nebo heslo.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmová databáze</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Filmová databáze</h1>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Registrace</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="jmeno" class="form-label">Jméno</label>
                        <input type="text" name="jmeno" id="jmeno" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="heslo" class="form-label">Heslo</label>
                        <input type="password" name="heslo" id="heslo" class="form-control" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-success">Registrovat</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-secondary text-white">Přihlášení</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="heslo" class="form-label">Heslo</label>
                        <input type="password" name="heslo" id="heslo" class="form-control" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Přihlásit</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

