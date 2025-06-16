<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>OmniCloud Verwaltung</title>
    <!-- Verbindet Google Fonts für die Verwendung der DM Sans-Schriftart -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <!-- Verlinkt die externe CSS-Datei -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation der Seite -->
    <nav>
        <!-- Logo- und Navigationslinks -->
        <a href="index.php"><img src="img/logo.png" alt="" class="logo"></a>
        <ul class="nav-list">
            <li><a href="index.php">Home</a></li>
            <li><a href="ueberuns.php">Über uns</a></li>
        </ul>
        <!-- Hamburger-Menü für mobile Darstellung -->
        <img src="img/burger-bar1.png" alt="hamburger" class="menu-btn">
    </nav>
   <div id="maincontent">
    <center>
    
    <section id="services">
    <div class="container">
    <div class="section-title">
        <h1>OmniCloud VM - Deine VM</h1>
        <div class="line"></div>
    </div><br><br>
<!-- PHP-Teil: Serverlogik für VMs -->
<?php
// Setzt die maximale Lebenszeit der PHP-Session
ini_set("session.gc_maxlifetime", 7200);
session_start();

// Funktion zur Berechnung des Preises basierend auf Ressourcen
function rechnenPreis($cpu, $ram, $ssd) {
    // Definiert Preise pro Einheit für CPU, RAM und SSD
    $pricePerCPU = [
        1 => 5, 2 => 10, 4 => 18, 8 => 30, 16 => 45
    ];
    $pricePerRAM = [
        512 => 5, 1024 => 10, 2048 => 20, 4096 => 40,
        8192 => 80, 16384 => 160, 32768 => 320
    ];
    $pricePerSSD = [
        10 => 5, 20 => 10, 40 => 20, 80 => 40,
        240 => 120, 500 => 250, 1000 => 500
    ];
    // Berechnet den Gesamtpreis
    $cpuPrice = $pricePerCPU[$cpu] ?? 0;
    $ramPrice = $pricePerRAM[$ram] ?? 0;
    $ssdPrice = $pricePerSSD[$ssd] ?? 0;

    $totalPreis = $cpuPrice + $ramPrice + $ssdPrice;

    return $totalPreis;
}

// Initialisiert den VM-Zähler, falls noch nicht gesetzt
if (!isset($_SESSION['vm_counter'])) {
    $_SESSION['vm_counter'] = 0;
}

// Generiert eine eindeutige ID für jede neue VM
$vm_id = 'vm_' . $_SESSION['vm_counter']++;

// Initialisiert die VM-Liste in der Session, falls sie nicht existiert
if (!isset($_SESSION['vms'])) {
    $_SESSION['vms'] = [];
}

$totalPreis = 0; // Initialisiert den Gesamtpreis

// Prüft, ob ein Formular abgesendet wurde, um eine neue VM zu erstellen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_submitted'])) {
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cpu'], $_POST['ram'], $_POST['ssd'])) {
    $nameKunde = htmlspecialchars($_POST['name']);
    $emailKunde = htmlspecialchars($_POST['email']);
    //Intval verändert den Wert von Strings in INT
    $wertCPU = intval($_POST['cpu']);
    $wertRAM = intval($_POST['ram']);
    $wertSSD = intval($_POST['ssd']);
}  

    // Berechnet den Gesamtpreis
    $totalPreis = rechnenPreis($wertCPU, $wertRAM, $wertSSD);

    // Speichert die neue VM in der Session
    $_SESSION['vms'][$vm_id] = [
        'name' => $nameKunde,
        'email' => $emailKunde,
        'cpu' => $wertCPU,
        'ram' => $wertRAM,
        'ssd' => $wertSSD,
        'price' => $totalPreis
    ];
}

// Logik für das Löschen einer VM
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_vm'])) {
    $vmId = $_POST['delete_vm'];
    if (isset($_SESSION['vms'][$vmId])) {
        $vm = $_SESSION['vms'][$vmId];

        // Ressourcen zurückgeben, die die VM genutzt hat
        $serverType = $vm['server'];
        $_SESSION['hostServer'][$serverType]['cpu_cores'] += (int)$vm['cpu'];
        $_SESSION['hostServer'][$serverType]['ram'] += (int)$vm['ram'];
        $_SESSION['hostServer'][$serverType]['ssd'] += (int)$vm['ssd'];

        // Entfernt die VM aus der Session
        unset($_SESSION['vms'][$vmId]);

       
    }
}

$wertCPU = $wertRAM = $wertSSD = '';

// Initialisiert verfügbare Serverkapazitäten
if (!isset($_SESSION['hostServer'])) {
    $_SESSION['hostServer'] = [
        'small'  => ['cpu_cores' => 4, 'ram' => 32768, 'ssd' => 4000],
        'medium' => ['cpu_cores' => 8, 'ram' => 65536, 'ssd' => 8000],
        'big'    => ['cpu_cores' => 16, 'ram' => 131072, 'ssd' => 16000]
    ];
}

$hostServer = &$_SESSION['hostServer'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cpu'], $_POST['ram'], $_POST['ssd'])) {
        $nameKunde = htmlspecialchars($_POST['name']);
        $emailKunde = htmlspecialchars($_POST['email']);
        $wertCPU = intval($_POST['cpu']);
        $wertRAM = intval($_POST['ram']);
        $wertSSD = intval($_POST['ssd']);
    } else {
        echo "<p class='error'>Keine Virtuellen Maschinen vorhanden</p>";
        exit;
    }

    }

    $resourceFound = false;

    // Ressourcenverteilung: Prüft, ob die gewünschte VM-Konfiguration auf einem Server verfügbar ist
    foreach ($hostServer as $serverName => &$server) {
        if ($server['cpu_cores'] >= $wertCPU &&
            $server['ram'] >= $wertRAM &&
            $server['ssd'] >= $wertSSD) {
    
            // Ressourcen werden abgezogen
            $server['cpu_cores'] -= intval($wertCPU);
            $server['ram'] -= intval($wertRAM);
            $server['ssd'] -= intval($wertSSD);
    
            $resourceFound = true;
            $selectedServer = $serverName;
            break; // Schleife beenden, wenn Ressourcen gefunden wurden
        }
    }
    

    
    foreach ($_SESSION['vms'] as $id => $vm);
    htmlspecialchars($id); 
    htmlspecialchars($vm['name']); 
    htmlspecialchars($vm['email']); 
    $vm['cpu']; 
    $vm['ram']; 
    $vm['ssd'];
    ?> 
    
    <?php
    if ($resourceFound): 
    // Calculate the total price
    $totalPreis = rechnenPreis($wertCPU, $wertRAM, $wertSSD);

    // Store the VM data in the session
    $_SESSION['vms'][$vm_id] = [
        'name' => $nameKunde,
        'email' => $emailKunde,
        'cpu' => $wertCPU,
        'ram' => $wertRAM,
        'ssd' => $wertSSD,
        'server' => $selectedServer,
        'price' => $totalPreis
    ];
        
    ?>
    <?php foreach ($_SESSION['vms'] as $vmid => $vm): ?>
        <!-- HTML-Abschnitt: Zeigt die erstellten VMs an -->
        <div class="service">
            <p><b>Kunde:</b> <?= htmlspecialchars($vm['name']) ?></p>
            <p><b>E-Mail:</b> <?= htmlspecialchars($vm['email']) ?></p>
            <p><b>CPU-Kerne:</b> <?= htmlspecialchars($vm['cpu']) ?></p>
            <p><b>RAM:</b> <?= htmlspecialchars($vm['ram']) ?> MB</p>
            <p><b>SSD:</b> <?= htmlspecialchars($vm['ssd']) ?> GB</p>
            <p><b>Preis:</b> <?= htmlspecialchars($vm['price']) ?> CHF</p><br>
    
            <!-- Löschbutton -->
            <form method="POST" action="DeineVm.php">
                <button type="submit" class="btn btn-danger">Delete VM</button>
                <input type="hidden" name="delete_vm" value="<?= htmlspecialchars($id) ?>">
            </form>
        </div>
        <br><br>
    <?php endforeach; ?>    
    <?php else: ?>
        <p>No VMs have been created yet.</p>
    <?php endif; ?>
    
    

   </section>   
    
<!-- JavaScript für das Hamburger-Menü -->
    <script>
    const navBtn = document.querySelector('.menu-btn')
    const navList = document.querySelector('.nav-list')

    navBtn.addEventListener('click', function(){
        navList.classList.toggle('active')
    })

    
</script>
<!-- Footer der Seite -->
<footer>
        <div class="container">
            <ul class="footer-links">
                <li><a href="index.php"><b>Home</b></a></li>
                <li><a href="überuns.php">Über uns</a></li>
            </ul>
            <p>©OmniCloud Verwaltung - 2024</p>
        </div>
   </footer>

</body>
</html>