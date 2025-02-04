<?php
session_start();
if (!isset($_SESSION['uzivatel_id'])) {
    header("Location: index.php");
    exit();
}

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

// Získání ID přihlášeného uživatele
$uzivatel_id = $_SESSION['uzivatel_id'];

// Přidání filmu do oblíbených
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['film_id'])) {
    $film_id = $_POST['film_id'];

    // Kontrola, zda film již není v oblíbených
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM oblibene_filmy WHERE id_uzivatel = ? AND id_film = ?");
    $check_stmt->execute([$uzivatel_id, $film_id]);
    $count = $check_stmt->fetchColumn();

    if ($count == 0) {
        $stmt = $conn->prepare("INSERT INTO oblibene_filmy (id_uzivatel, id_film) VALUES (?, ?)");
        $stmt->execute([$uzivatel_id, $film_id]);
        echo "<div class='alert alert-success'>Film byl přidán do oblíbených!</div>";
    } else {
        echo "<div class='alert alert-warning'>Tento film je již ve vašich oblíbených!</div>";
    }
}

// Získání oblíbených filmů uživatele
$stmt = $conn->prepare("
    SELECT f.id, f.nazev, f.popis, f.rok_vydani
    FROM oblibene_filmy AS ob
    JOIN filmy AS f ON ob.id_film = f.id
    WHERE ob.id_uzivatel = ?
");
$stmt->execute([$uzivatel_id]);
$oblibene_filmy = $stmt->fetchAll();

// Odebrání filmu z oblíbených
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_film_id'])) {
    $remove_film_id = $_POST['remove_film_id'];
    $stmt = $conn->prepare("DELETE FROM oblibene_filmy WHERE id_uzivatel = ? AND id_film = ?");
    $stmt->execute([$uzivatel_id, $remove_film_id]);

    header("Location: playlists.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moje oblíbené filmy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Moje oblíbené filmy</h1>

        <?php if (empty($oblibene_filmy)): ?>
            <p>Nemáte žádné oblíbené filmy.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Název</th>
                        <th>Popis</th>
                        <th>Rok vydání</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($oblibene_filmy as $film): ?>
                    <tr>
                        <td><?= htmlspecialchars($film['nazev']) ?></td>
                        <td><?= htmlspecialchars($film['popis']) ?></td>
                        <td><?= htmlspecialchars($film['rok_vydani']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="remove_film_id" value="<?= $film['id'] ?>">
                                <button type="submit" class="btn btn-danger">Odebrat</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="filmy.php" class="btn btn-secondary">Zpět na seznam filmů</a>
    </div>
</body>
</html>
