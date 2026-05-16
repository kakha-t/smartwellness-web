<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$tag = $_GET['tag'] ?? '';
if (!$tag) {
    echo "Kein Tag angegeben.";
    exit;
}

$conn = new mysqli("localhost", "root", "", "smartwellness_db");
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Alle Produktnamen laden
$result = $conn->query("SELECT produkt FROM lebensmittel ORDER BY produkt ASC");
$produkte = [];
while ($row = $result->fetch_assoc()) {
    $produkte[] = $row['produkt'];
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Produkt hinzufügen – <?= htmlspecialchars($tag) ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="header">
        <h1 class="logo">
            <a href="index.html" style="text-decoration: none; color: inherit;">SmartWellness</a>
        </h1>
        <div class="nav-user-container">
            <div class="nav-user-combined">
                <nav class="navigation">
                    <a href="gesunde_ernaehrung.html">Ernährung & Rezepte</a>
                    <a href="fitness_bewegung.html">Fitness & Bewegung</a>
                </nav>

                <div class="user-dropdown" id="userDropdown">
                    <span id="userName">👤 <?php echo $vorname . " " . $nachname; ?></span>
                    <div class="dropdown-content" id="userMenu" style="display: none;">
                        <a href="meinkonto.html">Mein Konto</a>
                        <a onclick="logout()">Ausloggen</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="hinzufuegen-box">
            <h2>Produkt zum Plan hinzufügen – <?= htmlspecialchars($tag) ?></h2>
            <form id="hinzufuegenForm">
                <label for="produkt">Produkt:</label>
                <select name="produkt" id="produkt">
                    <option value="">-- bitte wählen --</option>
                    <?php
                    $res = $conn->query("SELECT produkt FROM lebensmittel ORDER BY produkt");
                    while ($r = $res->fetch_assoc()) {
                        echo "<option value=\"" . htmlspecialchars($r['produkt']) . "\">" . htmlspecialchars($r['produkt']) . "</option>";
                    }
                    ?>
                </select><br><br>

                <label for="menge">Menge (in Gramm):</label>
                <input type="number" name="menge" id="menge" min="1" required><br><br>

                <input type="hidden" name="tag" id="tag" value="<?php echo htmlspecialchars($_GET['tag']); ?>">
                <div class="button-row">
                    <button type="submit" class="green-button">Hinzufügen</button>
                    <a href="tage_anzeigen.php" class="green-button">← Zurück zur Übersicht</a>
                </div>
                <p id="feedback" class="success-message" style="display: none;"></p>
            </form>
        </div>

    </main>
    <script>
        document.getElementById("hinzufuegenForm").addEventListener("submit", function(e) {
            e.preventDefault();

            const form = document.getElementById("hinzufuegenForm");
            const formData = new FormData(form); // Holt automatisch alle Felder

            fetch("produkt_hinzufuegen.php", {
                    method: "POST",
                    body: formData // Formulardaten anhängen
                })
                .then(res => res.json())
                .then(data => {
                    const feedback = document.getElementById("feedback");
                    if (data.success) {
                        feedback.textContent = "✅ Produkt erfolgreich hinzugefügt.";
                    } else {
                        feedback.textContent = "❌ Fehler: " + data.message;
                    }
                    feedback.style.display = "block";
                });
        });
    </script>
    </main>

    <script>
        // Nutzername anzeigen
        document.addEventListener("DOMContentLoaded", () => {
            fetch("session_user.php")
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("userName").textContent = "👤 " + data.vorname + " " + data.nachname;
                    } else {
                        window.location.href = "login.html";
                    }
                });
        });

        function logout() {
            fetch("logout.php").then(() => {
                window.location.href = "login.html";
            });
        }

        // Dropdown-Menü sichtbar machen
        document.getElementById("userDropdown").addEventListener("click", function() {
            const menu = document.getElementById("userMenu");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        });
    </script>
    <footer class="footer">
        <p>&copy; 2025 SmartWellness – <a href="impressum.html">Impressum</a> | <a href="datenschutz.html">Datenschutz</a></p>
    </footer>
</body>

</html>