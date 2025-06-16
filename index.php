<?php

// für den Teil musste ich KI fragen, dieser command erlaubt es die session auf 2 stunden zu strecken
ini_set('session.gc_maxlifetime', 7200);

/*startet eine session, die session ermöglicht es, die informationen wie die serverbelastung zu speichern, oder zumindes so
lange bis der Browser geschlossen wird. Die session ist gemacht damit, nachdem eine vm erstellt wurde, die daten
auch für spätere generierungen noch die korrekte anzahl an ressourcenabzüge haben*/
session_start();



// Initialize variables
$wertCPU = $wertRAM = $wertSSD = '';

/*checkt ob die session am laufen ist, falls sie läuft, werden die server generiert*/
if (!isset($_SESSION['hostServer'])) {
    $_SESSION ['hostServer'] = [
    'small'  => ['cpu_cores' => 4, 'ram' => 32768, 'ssd' => 4000],
    'medium' => ['cpu_cores' => 8, 'ram' => 65536, 'ssd' => 8000],
    'big'    => ['cpu_cores' => 16, 'ram' => 131072, 'ssd' => 16000]
    ];
}
//erstelld die variable $hostserver aus der session
$hostServer = &$_SESSION['hostServer'];

// array zu darstellung  von auswahlressourcen mit preisen
$preise = [
    'cpu' => [1 => 5, 2 => 10, 4 => 18, 8 => 30, 16 => 45],
    'ram' => [512 => 5, 1024 => 10, 2048 => 20, 4096 => 40, 8192 => 80, 16384 => 160, 32768 => 320],
    'ssd' => [10 => 5, 20 => 10, 40 => 20, 80 => 40, 240 => 120, 500 => 250, 1000 => 500],
];

/*funktion zu berechnung der severleistung $hostserver ist die eingabe von einer der 3 server.*/
function serverNutzung($hostServer) {
    $smallAuslastung = (($hostServer['small']['cpu_cores']/4*100) + ($hostServer['small']['ram']/32768*100) + ($hostServer['small']['ssd']/4000*100))/3; 
    $mediumAuslastung = (($hostServer['medium']['cpu_cores']/8*100) + ($hostServer['medium']['ram']/65536*100) + ($hostServer['medium']['ssd']/8000*100))/3;
    $bigAuslastung = (($hostServer['big']['cpu_cores']/16*100) + ($hostServer['big']['ram']/131072*100) + ($hostServer['big']['ssd']/16000*100))/3;
 
    // Nutzt andere Funktion, um den am wenigsten belasteten Server zu bestimmen
    return minimumBelasteterServer($smallAuslastung, $mediumAuslastung, $bigAuslastung);
}

// Basierend auf das Resultat der servernutzung Funktion, gibt es den wenigsten genutzten server aus
$selectedServer = serverNutzung($hostServer);

function minimumBelasteterServer($small, $medium, $big) {
    $nutzung = [
        'small' => $small,
        'medium' => $medium,
        'big' => $big
    ];

    // Bei gleicher Auslastung wird die Reihenfolge erzwungen: small -> medium -> big
    //
    asort($nutzung); // Sortiert die Werte aufsteigend und bewahrt die Schlüssel
    
    $minValue = reset($nutzung); // Kleinster Wert der Auslastung
    
    // Filtert Server mit der minimalen Auslastung
    $minKeys = array_keys($nutzung, $minValue);

    // Gibt den ersten Schlüssel in der bevorzugten Reihenfolge zurück
    foreach (['small', 'medium', 'big'] as $server) {
        if (in_array($server, $minKeys)) {
            return $server;
        }
    }

    // Fallback (falls etwas unerwartet schiefgeht, sollte nie eintreten)
    return 'small';
}


session_start();

// Initialize the VM counter if it doesn't exist
if (!isset($_SESSION['vm_counter'])) {
    $_SESSION['vm_counter'] = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $wertCPU = intval($_POST['cpu']);
    $wertRAM = intval($_POST['ram']);
    $wertSSD = intval($_POST['ssd']);
    $nameKunde = htmlspecialchars($_POST['name']);
    $emailKunde = htmlspecialchars($_POST['email']);

    $resourceFound = false;
    $serverOrder = ['small', 'medium', 'big'];

    foreach ($serverOrder as $server) {
        if ($hostServer[$server]['cpu_cores'] >= $wertCPU &&
            $hostServer[$server]['ram'] >= $wertRAM &&
            $hostServer[$server]['ssd'] >= $wertSSD) {
            
            // Allocate resources from this server
            $hostServer[$server]['cpu_cores'] -= $wertCPU;
            $hostServer[$server]['ram'] -= $wertRAM;
            $hostServer[$server]['ssd'] -= $wertSSD;

            $resourceFound = true;
            $selectedServer = $server;
            break;
        }
    }

    if ($resourceFound) {
        // Berechnung Preis
        $totalPrice = $preise['cpu'][$wertCPU] + $preise['ram'][$wertRAM] + $preise['ssd'][$wertSSD];

        // Gibt Id für Vm, wichtig dann beim erstellen und Löschen
        $vm_id = 'vm_' . $_SESSION['vm_counter']++;

        // erstellt neue Vm mit diesen Werten
        $_SESSION['vms'][$vm_id] = [
            'name' => $nameKunde,
            'email' => $emailKunde,
            'cpu' => $wertCPU,
            'ram' => $wertRAM,
            'ssd' => $wertSSD,
            'price' => $totalPrice,
            'server' => $selectedServer
        ];

        //gibt erfolgsnachricht bei erstellung aus
        $successMessage = "VM created successfully!";
        
        // nachdem vm erstellt riechtet daten nach DeineVm.php
        header("Location: DeineVm.php");
        exit();
    } else {
        $errorMessage = "Error: Not enough resources available.";
    }
}

//ist dazu dar, die werte an den globalen variablen zugänglich zu machen
$smallAuslastung = (($hostServer['small']['cpu_cores']/4*100) + ($hostServer['small']['ram']/32768*100) + ($hostServer['small']['ssd']/4000*100))/3; 
$mediumAuslastung = (($hostServer['medium']['cpu_cores']/8*100) + ($hostServer['medium']['ram']/65536*100) + ($hostServer['medium']['ssd']/8000*100))/3;
$bigAuslastung = (($hostServer['big']['cpu_cores']/16*100) + ($hostServer['big']['ram']/131072*100) + ($hostServer['big']['ssd']/16000*100))/3;




?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>OmniCloud Verwaltung</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <a href="index.php"><img src="img/logo.png" alt="" class="logo"></a>
        <ul class="nav-list">
            <li><a href="index.php"><b>Home</b></a></li>
            <li><a href="ueberuns.php">Über uns</a></li>
        </ul>
        
        <img src="img/burger-bar1.png" alt="hamburger" class="menu-btn">
    </nav>
   <div id="maincontent">
    <center>
    
    <section id="services">
    <div class="container">
    <div class="section-title">
        <h1>OmniCloud VM - Konfiguration</h1>
        <div class="line"></div>
    </div>

    
    <form id="vmForm" method="POST" action="DeineVm.php" target="blank">
        <div class="services">
            <div class="service">
                <h1><b>CPU Cores</b></h1><br>
                <select name="cpu" id="CPU" onchange="updatePrice()">
                    <option value="1">1 Core</option>
                    <option value="2">2 Cores</option>
                    <option value="4">4 Cores</option>
                    <option value="8">8 Cores</option>
                    <option value="16">16 Cores</option>
                </select>
                <br><br>
                <h1><b>Preise</b></h1><br>
                <p>
                    <b>1 Core:</b> 5 CHF <br>
                    <b>2 Cores:</b> 10 CHF <br>
                    <b>4 Cores:</b> 18 CHF <br>
                    <b>8 Cores:</b> 30 CHF <br>
                    <b>16 Cores:</b> 45 CHF <br>
                </p>
            </div>
        
            <div class="service">
                <h1><b>RAM (MB)</b></h1><br>
                <select name="ram" id="RAM" onchange="updatePrice()">
                    <option value="512">512 MB</option>
                    <option value="1024">1024 MB</option>
                    <option value="2048">2048 MB</option>
                    <option value="4096">4096 MB</option>
                    <option value="8192">8192 MB</option>
                    <option value="16384">16384 MB</option>
                    <option value="32768">32768 MB</option>
                </select>
                <br><br>
                <h1><b>Preise</b></h1><br>
                <p>
                    <b>512 MB:</b> 5 CHF <br>
                    <b>1024 MB:</b> 10 CHF <br>
                    <b>2048 MB:</b> 20 CHF <br>
                    <b>4096 MB:</b> 40 CHF <br>
                    <b>8192 MB:</b> 80 CHF <br>
                    <b>16384 MB:</b> 160 CHF <br>
                    <b>32768 MB:</b> 320 CHF <br>
                </p>
            </div>
            <div class="service">
                <h1><b>SSD (GB)</b></h1><br>
                <select name="ssd" id="SSD" onchange="updatePrice()">
                    <option value="10">10 GB</option>
                    <option value="20">20 GB</option>
                    <option value="40">40 GB</option>
                    <option value="80">80 GB</option>
                    <option value="240">240 GB</option>
                    <option value="500">500 GB</option>
                    <option value="1000">1000 GB</option>
                </select>
                <br><br>
                <h1><b>Preise</b></h1><br>
                <p>
                    <b>10 GB:</b> 5 CHF <br>
                    <b>20 GB:</b> 10 CHF <br>
                    <b>40 GB: </b> 20 CHF <br>
                    <b>80 GB:</b> 40 CHF <br>
                    <b>240 GB:</b> 120 CHF <br>
                    <b>500 GB:</b> 250 CHF <br>
                    <b>1000 GB:</b> 500 CHF <br>
                </p>
            </div><br>
            <center>
            <div class="service" style="margin-right: 42px; width: ">
                <h1><b>Name</b></h1><br>
                <input type="text" id="name" name="name" placeholder="Ihr Name" style="padding: 10px; border-radius: 10px;" required><br><br><br>
                <h1><b>E-Mail</b></h1><br>
                <input type="email" id="email" name="email" placeholder="Ihre E-Mail Adresse" style="padding: 10px; border-radius: 10px;" required><br>
            </div>
            </center>
        </div>
        
        <center><br><br>
    
    <h2 id="livePrice">Total Price: 0 CHF</h2>
    <br>
    <input type="hidden" name="form_submitted" value="1">
    <input type="submit" value="🖥️ VM erstellen" class="btn btn-primary"></center><br>
    <a href="DeineVM.php"><button class="btn btn-primary">➔ VM's anschauen</button></a>

    <?php
    //falls nachricht vorhanden gibt es die aus sonst sagt nicht genug ressourcen 

    if (isset($successMessage)) {
        echo "<p class='success'>$successMessage</p>";
    } elseif (isset($errorMessage)) {
        echo "<p class='error'>$errorMessage</p>";
    }   
    ?>
    </form>


   </section>   
    

    
    <script>
    /* Das ganze wurde mit Chatgpt gemacht ich kann nicht wirklich javascript  */

    const navBtn = document.querySelector('.menu-btn')
    const navList = document.querySelector('.nav-list')

    navBtn.addEventListener('click', function(){
        navList.classList.toggle('active')
    })

    // Preis Daten
    const prices = {
        cpu: {1: 5, 2: 10, 4: 18, 8: 30, 16: 45},
        ram: {512: 5, 1024: 10, 2048: 20, 4096: 40, 8192: 80, 16384: 160, 32768: 320},
        ssd: {10: 5, 20: 10, 40: 20, 80: 40, 240: 120, 500: 250, 1000: 500}
    };

    function updatePrice() {
        const cpu = document.getElementById('CPU').value;
        const ram = document.getElementById('RAM').value;
        const ssd = document.getElementById('SSD').value;

        const totalPrice = prices.cpu[cpu] + prices.ram[ram] + prices.ssd[ssd];
        
        document.getElementById('livePrice').innerText = `Total Price: ${totalPrice} CHF`;
    }

   //udated den Preis nach eingabe im formular
    updatePrice();
</script>


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

 
 
 
