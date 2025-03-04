
<?php
session_start();
if (!isset($_SESSION['uzivatel_id']) || $_SESSION['role'] !== 'administrator') {
    header("Location: index.php");
    exit();
}

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

// Přidání nového uživatele
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $jmeno = $_POST['jmeno'];
    $email = $_POST['email'];
    $heslo = password_hash($_POST['heslo'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO uzivatele (jmeno, email, heslo, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$jmeno, $email, $heslo, $role]);

    echo "<div class='alert alert-success'>Uživatel byl přidán!</div>";
}

// Úprava uživatele
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    $id = $_POST['id'];
    $jmeno = $_POST['jmeno'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE uzivatele SET jmeno = ?, email = ?, role = ? WHERE id = ?");
    $stmt->execute([$jmeno, $email, $role, $id]);

    echo "<div class='alert alert-success'>Uživatel byl upraven!</div>";
}

// Smazání uživatele
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM uzivatele WHERE id = ?");
    $stmt->execute([$id]);

    echo "<div class='alert alert-success'>Uživatel byl smazán!</div>";
}

// Získání seznamu uživatelů
$stmt = $conn->prepare("SELECT * FROM uzivatele");
$stmt->execute();
$uzivatele = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správa uživatelů</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Správa uživatelů</h1>

        <!-- Přidání nového uživatele -->
        <h2>Přidat nového uživatele</h2>
        <form method="POST" class="mb-4">
            <input type="hidden" name="action" value="add_user">
            <div class="mb-3">
                <label for="jmeno" class="form-label">Jméno</label>
                <input type="text" class="form-control" id="jmeno" name="jmeno" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="heslo" class="form-label">Heslo</label>
                <input type="password" class="form-control" id="heslo" name="heslo" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="uzivatel">Uživatel</option>
                    <option value="administrator">Administrátor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Přidat uživatele</button>
        </form>

        <!-- Seznam uživatelů -->
        <h2>Seznam uživatelů</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Jméno</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($uzivatele as $uzivatel): ?>
                <tr>
                    <td><?= htmlspecialchars($uzivatel['id']) ?></td>
                    <td><?= htmlspecialchars($uzivatel['jmeno']) ?></td>
                    <td><?= htmlspecialchars($uzivatel['email']) ?></td>
                    <td><?= htmlspecialchars($uzivatel['role']) ?></td>
                    <td>
                        <!-- Formulář pro úpravu uživatele -->
                        <form method="POST" class="d-inline-block">
                            <input type="hidden" name="action" value="edit_user">
                            <input type="hidden" name="id" value="<?= $uzivatel['id'] ?>">
                            <button type="submit" class="btn btn-warning">Upravit</button>
                        </form>
                        <!-- Formulář pro smazání uživatele -->
                        <form method="POST" class="d-inline-block">
                            <input type="hidden" name="action" value="delete_user">
                            <input type="hidden" name="id" value="<?= $uzivatel['id'] ?>">
                            <button type="submit" class="btn btn-danger">Smazat</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="odhlasit.php?action=logout" class="btn btn-danger mt-4">Odhlásit se</a>
    </div>
</body>
</html>
