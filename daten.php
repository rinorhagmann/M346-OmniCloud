<?php

// Erstellt eine Session welche 7200 Sekunden lang ist
ini_set('session.gc_maxlifetime', 7200);
session_start();

if (!isset($_SESSION['vms'])) {
    session_regenerate_id(true);
    $_SESSION['vms'] = [];
}

// Nimmt die Daten aus dem Formular und speichert sie in Variablen
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cpu'], $_POST['ram'], $_POST['ssd'])) {
        $nameKunde = htmlspecialchars($_POST['name']);
        $emailKunde = htmlspecialchars($_POST['email']);
        $wertCPU = intval($_POST['cpu']);
        $wertRAM = intval($_POST['ram']);
        $wertSSD = intval($_POST['ssd']);
    };
};            

// Falls probleme gibt gehe von diese Daten aus
if (!isset($_SESSION['hostServer'])) {
    $_SESSION['hostServer'] = [
        'small'  => ['cpu_cores' => 4, 'ram' => 32768, 'ssd' => 4000],
        'medium' => ['cpu_cores' => 8, 'ram' => 65536, 'ssd' => 8000],
        'big'    => ['cpu_cores' => 16, 'ram' => 131072, 'ssd' => 16000]
    ];
}

// Überprüft ob die vms Session existiert
if (!isset($_SESSION['vms'])) {
    $_SESSION['vms'] = [];
}

// Check von deineVM ob die Vm gelöscht wurde

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_vm'])) {
    $vmId = $_POST['delete_vm'];
    if (isset($_SESSION['vms'][$vmId])) {
        $vm = $_SESSION['vms'][$vmId];
        
        // Gibt die Ressourcen frei nach löschung
        $serverType = $vm['server'];
        $_SESSION['hostServer'][$serverType]['cpu_cores'] += (int)$vm['cpu'];
        $_SESSION['hostServer'][$serverType]['ram'] += (int)$vm['ram'];
        $_SESSION['hostServer'][$serverType]['ssd'] += (int)$vm['ssd'];

        // Löscht die Vm aus der Session
        unset($_SESSION['vms'][$vmId]);

       
    }
}

// Rechnet den Totalpreis der VMs aus


$totalPrice = 0;
if (isset($_SESSION['vms']) && is_array($_SESSION['vms'])) {
    foreach ($_SESSION['vms'] as $vm) {
        $totalPrice += $vm['price'];
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>OmniCloud - VMs Übersicht</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php"><img src="img/logo.png" alt="OmniCloud Logo" class="logo"></a>
    <ul class="nav-list">
        <li><a href="index.php">Home</a></li>
        <li><a href="ueberuns.php">Über uns</a></li>
    </ul>
    <img src="img/burger-bar1.png" alt="hamburger" class="menu-btn">
</nav>

<div class="container" style="margin-top: 100px;">
    <center>
    <h1>OmniCloud VMs Übersicht</h1>
    <div class="line"></div>

    <!-- Zeigt die Server Ressourcen -->
    <section id="server-resources">
        <h2>Verfügbare Ressourcen</h2><br>
        <table style="border: 1px solid #ccc;">
            <thead>
            <tr>
                <th>Server</th>
                <th>CPU Cores</th>
                <th>RAM (MB)</th>
                <th>SSD (GB)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($_SESSION['hostServer'] as $serverName => $resources): ?>
                <tr>
                    <td><?= ucfirst($serverName) ?></td>
                    <td><?= $resources['cpu_cores'] ?></td>
                    <td><?= $resources['ram'] ?></td>
                    <td><?= $resources['ssd'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table><br>
        <p><strong>Erwarteter Umsatz: <?= htmlspecialchars($totalPrice) ?> CHF</strong></p>
        
    </section>

    </section>
                    <!-- Zeigt die erstellten VMs -->
    <section id="created-vms">
        <h2>Erstellte VMs</h2>
        <?php if (empty($_SESSION['vms'])): ?>
            <p>Es wurden noch keine VMs erstellt.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>CPU</th>
                        <th>RAM (MB)</th>
                        <th>SSD (GB)</th>
                        
                    </tr>
                </thead>
                <tbody>
        <?php foreach ($_SESSION['vms'] as $vmId => $vm): ?>
            <tr>
                <td><?= htmlspecialchars($vmId) ?></td>
                <td><?= htmlspecialchars($vm['name']) ?></td>
                <td><?= htmlspecialchars($vm['email']) ?></td>
                <td><?= $vm['cpu'] ?></td>
                <td><?= $vm['ram'] ?></td>
                <td><?= $vm['ssd'] ?></td>
            </tr>
<?php endforeach; ?>    
                    
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</div>


<footer style="margin-top: 80px; ">
    <div class="container">
        <ul class="footer-links">
            <li><a href="index.php">Home</a></li>
            <li><a href=""></a></li>
            <li><a href="ueberuns.php">Über uns</a></li>
        </ul>
        <p>©OmniCloud Verwaltung - 2024</p>
    </div>
</footer>
</center>
</body>
</html>
