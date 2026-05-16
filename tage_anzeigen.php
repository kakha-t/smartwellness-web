<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

$host = "localhost";
$user = "root";
$pass = "";
$db = "smartwellness_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

$sql_user = "SELECT vorname, nachname FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result()->fetch_assoc();
$vorname = htmlspecialchars($result_user['vorname']);
$nachname = htmlspecialchars($result_user['nachname']);

$sql = "SELECT id, tag, daten, aktualisiert_am FROM ernaehrungsplaene WHERE user_id = ? ORDER BY FIELD(tag,'Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag','Sonntag')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>SmartWellness – Tagespläne</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
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

    <main class="konto-container" style="margin-top: 140px;">
        <a href="ernaehrungsplan.html" class="zurueck-icon" title="Zurück zum Ernährungsplan">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="green" viewBox="0 0 24 24">
                <path d="M15.5 5l-7 7 7 7" stroke="green" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
        <h2>Meine gespeicherten Tagespläne</h2>
        <div id="feedback-message" class="success-message" style="display: none;"></div>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<div class='plan-card'>";
            echo "<h3>" . htmlspecialchars($row['tag']) . "</h3>";

            $produkte = json_decode($row['daten'], true);
            $sum_kcal = $sum_fett = $sum_eiweiss = $sum_kh = $sum_gramm = 0;

            if ($produkte && is_array($produkte)) {
                echo "<table class='ernaehrungstabelle'>";
                echo "<thead><tr><th>Produkt</th><th>Gramm</th><th>Kalorien</th><th>Fett</th><th>Eiweiß</th><th>KH</th><th>GI</th><th>Verwalten</th></tr></thead><tbody>";

                foreach ($produkte as $p) {
                    $name = trim(str_replace([" ", "  "], " ", $p['produkt']));
                    $menge = floatval($p['menge']);
                    $gi = intval($p['glyk_index']);

                    $stmt2 = $conn->prepare("SELECT kalorien, fett, eiweiss, kohlenhydrate, glyk_index FROM lebensmittel WHERE produkt = ?");
                    $stmt2->bind_param("s", $name);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    $n = $result2->fetch_assoc();

                    $kcal = $n ? ($n['kalorien'] * $menge / 100) : 0;
                    $fett = $n ? ($n['fett'] * $menge / 100) : 0;
                    $eiw  = $n ? ($n['eiweiss'] * $menge / 100) : 0;
                    $kh   = $n ? ($n['kohlenhydrate'] * $menge / 100) : 0;

                    echo "<tr>
                        <td>$name</td>
                        <td>" . round($menge) . "</td>
                        <td>" . round($kcal) . "</td>
                        <td>" . round($fett, 1) . "</td>
                        <td>" . round($eiw, 1) . "</td>
                        <td>" . round($kh, 1) . "</td>
                        <td>$gi</td>
                        <td><button onclick=\"loescheProdukt('$name', '{$row['tag']}', this)\">–</button></td>
                    </tr>";

                    $sum_kcal += $kcal;
                    $sum_fett += $fett;
                    $sum_eiweiss += $eiw;
                    $sum_kh += $kh;
                    $sum_gramm += $menge;
                }

                echo "<tr class='summenzeile'>
                    <th>Summierte Werte</th>
                    <th>" . round($sum_gramm) . "</th>
                    <th>" . round($sum_kcal) . "</th>
                    <th>" . round($sum_fett, 1) . "</th>
                    <th>" . round($sum_eiweiss, 1) . "</th>
                    <th>" . round($sum_kh, 1) . "</th>
                    <th>-</th>
                    <th><a class='green-button' href='hinzufuegen_auswahl.php?tag={$row['tag']}'>+</a></th></tr>";
                echo "</tbody></table>";
            }

            echo "<div class='footer'>Letzte Änderung: " . $row['aktualisiert_am'] . "</div>";
            echo "</div>";
        }
        ?>
    </main>

    <script>
        function loescheProdukt(produkt, tag, buttonElement) {
            const formData = new FormData();
            formData.append("produkt", produkt);
            formData.append("tag", tag);

            fetch("produkt_loeschen.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    const feedback = document.getElementById("feedback-message");
                    if (data.success) {
                        const row = buttonElement.closest("tr");
                        const tabelle = row.closest("table");
                        row.remove();

                        let sum_kcal = 0,
                            sum_fett = 0,
                            sum_eiw = 0,
                            sum_kh = 0,
                            sum_gramm = 0;

                        // Schleife durch alle Datenzeilen außer der Summenzeile
                        const rows = tabelle.querySelectorAll("tbody tr");
                        rows.forEach(r => {
                            if (!r.classList.contains("summenzeile")) {
                                const zellen = r.querySelectorAll("td");
                                const gramm = parseFloat(zellen[1].textContent.replace("g", "").trim());
                                const kcal = parseFloat(zellen[2].textContent.trim());
                                const fett = parseFloat(zellen[3].textContent.trim());
                                const eiw = parseFloat(zellen[4].textContent.trim());
                                const kh = parseFloat(zellen[5].textContent.trim());

                                sum_gramm += isNaN(gramm) ? 0 : gramm;
                                sum_kcal += isNaN(kcal) ? 0 : kcal;
                                sum_fett += isNaN(fett) ? 0 : fett;
                                sum_eiw += isNaN(eiw) ? 0 : eiw;
                                sum_kh += isNaN(kh) ? 0 : kh;
                            }
                        });

                        // Summenzeile aktualisieren
                        const sumRow = tabelle.querySelector(".summenzeile");
                        if (sumRow) {
                            const zellen = sumRow.querySelectorAll("th");
                            zellen[1].textContent = `${Math.round(sum_gramm)}`;
                            zellen[2].textContent = `${Math.round(sum_kcal)}`;
                            zellen[3].textContent = `${sum_fett.toFixed(1)}`;
                            zellen[4].textContent = `${sum_eiw.toFixed(1)}`;
                            zellen[5].textContent = `${sum_kh.toFixed(1)}`;
                        }

                        // Feedback anzeigen
                        feedback.textContent = "✅ Produkt erfolgreich gelöscht.";
                        feedback.style.display = "block";
                        feedback.style.color = "green";
                        feedback.style.fontWeight = "normal";
                        feedback.style.margin = "5px 0";
                        feedback.style.fontSize = "14px";

                        setTimeout(() => {
                            feedback.textContent = "";
                        }, 3000);
                    } else {
                        alert("Fehler beim Löschen: " + data.message);
                    }
                });
        }
    </script>

    <script>
        document.getElementById("userDropdown").addEventListener("click", function() {
            const menu = document.getElementById("userMenu");
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        });

        function logout() {
            fetch("logout.php").then(() => window.location.href = "login.html");
        }
    </script>
    <footer class="footer">
        <p>&copy; 2025 SmartWellness – <a href="impressum.html">Impressum</a> | <a href="datenschutz.html">Datenschutz</a></p>
    </footer>

</body>

</html>