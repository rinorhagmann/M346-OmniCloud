<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>OmniCloud Verwaltung - Über uns</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
  


    <nav>
        <a href="index.php"><img src="img/logo.png" alt="" class="logo"></a>     
        <ul class="nav-list">
            <li><a href="index.php">Home</a></li>
            <li><a href="überuns.php"><b>Über uns</b></a></li>
        </ul>
        
        <img src="img/burger-bar1.png" alt="" class="menu-btn">
    </nav>
    <div id="maincontent">
        <center>
        <section id="services">
        <div class="container">
            <div class="section-title">
                <h1>Über uns</h1>
                <div class="line">

                </div>
            </div>
        </div>
        </section>
    
    <main>
    <section>
            <h2>Wer wir sind</h2>
            <p style="padding: 0 15%;">
                OmniCloud ist ein innovatives Startup im Bereich Cloud-Infrastruktur. 
                Wir bieten modernste Infrastruktur-as-a-Service (IaaS) Lösungen, mit denen 
                Unternehmen virtuelle Maschinen einfach und effizient nutzen können. Unser Ziel ist es, leistungsstarke und zuverlässige Cloud-Dienste bereitzustellen, 
                die an die Bedürfnisse unserer Kunden angepasst sind. Unsere Plattform ist flexibel, 
                skalierbar und kosteneffizient.
            </p>
        </section>
        <section>
            <h2>Unsere Mission</h2>
            <p style="padding: 0 15%">
                Wir glauben an die Macht der Cloud, um Unternehmen zu transformieren.
                OmniCloud strebt danach, die besten IaaS-Lösungen bereitzustellen, die sowohl 
                Einsteiger als auch Experten begeistern.
            </p>
        </section>
        <hr>
        <section id="services">
        <div class="container">
            <div class="section-title">
                <h1>Unser Team</h1>
                <div class="line">

                </div><br>
                <p><b>Das ist eine artistische Wahl. Wir wissen, dass wir wie Gremlings aussehen. </b></p>
                <p>(Richtig wärs mit max-height)</p>
            </div>
            <div class="services">
                <div class="service">
                    <img src="img/Timmy.jpeg" alt="" width="350px" height="197px">
                    <br><br><h3>Tim Kluge</h3>
                    <p>
                   CEO & Gründer <br> Datenschutzspezialist
                  
                   </p><br>
                    <center><a href="mailto:tim.kluge@stud.edubs.ch"><button class="btn btn-primary" style="width: 168.48px">✉ E-Mail schreiben</button></a></center><br>
               </div>
                <div class="service">
                    <img src="img/Rinor.jpg" alt="" width="350px" height="197px"> <!-- 350px x 197px -->
                    <br><br><h3>Rinor Hagmann</h3>
                    <p>CTO <br> Graphic Designer
                    
                    </p><br>
                    <center><a href="mailto:rinor.hagmann@stud.edubs.ch"><button class="btn btn-primary" style="width: 168.48px">✉ E-Mail schreiben</button></a></center><br>
                </div>
                <div class="service">
                    <img src="img/Stefano.jpeg" alt="" width="350px" height="197px">
                    <br><br><h3>Stefano Milone</h3>
                    <p>Produktmanager <br> Senior Web-Developper
                    
                    </p><br>
                    <center><a href="mailto:stefano.milone@stud.edubs.ch"><button class="btn btn-primary" style="width: 168.48px">✉ E-Mail schreiben</button></a></center><br>
                </div>
            </div>
            <center>
                
        </div>
   </section>
    </main>
    </div>
    </center>
    <script>
        const navBtn = document.querySelector('.menu-btn')
        const navList = document.querySelector('.nav-list')

        navBtn.addEventListener('click', function(){
            navList.classList.toggle('active')
        })
</script>
<footer style="bottom: 0;">
        <div class="container">
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="überuns.php"></b>Über uns</b></a></li>
            </ul>
            <p>©OmniCloud Verwaltung - 2024</p>
        </div>
   </footer>
</body>
</html>
