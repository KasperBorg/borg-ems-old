<?php
session_start();

require_once 'core/forms/shortagelist.class.php';
require_once 'core/security.class.php';
require_once 'core/user.class.php';
require_once 'core/core.class.php';
require_once 'core/station.class.php';

$security = new Security();
$isLoggedIn = $security->isLoggedIn();
if(!$isLoggedIn)
{
    header("LOCATION: login.php");
}

$user = new User($_SESSION['id']);
$core = new Core();
?>
<!DOCTYPE HTML>
<!--
BORG Employee Management System
-->
<html>
    <head>
        <title>BORG EMS</title>
        
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="stylesheet" href="assets/css/index.css" />
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">

        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="https://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

        <script>
            function goBack() {
                window.history.back()
            }
        </script>

        <script>
            $(function() {
                $( "#datepicker" ).datepicker();
                $.datepicker.regional['da'] = {
                    closeText: 'Luk',
                    prevText: '&#x3c;Forrige',
                    nextText: 'Næste&#x3e;',
                    currentText: 'Idag',
                    monthNames: ['Januar','Februar','Marts','April','Maj','Juni',
                    'Juli','August','September','Oktober','November','December'],
                    monthNamesShort: ['Jan','Feb','Mar','Apr','Maj','Jun',
                    'Jul','Aug','Sep','Okt','Nov','Dec'],
                    dayNames: ['Søndag','Mandag','Tirsdag','Onsdag','Torsdag','Fredag','Lørdag'],
                    dayNamesShort: ['Søn','Man','Tir','Ons','Tor','Fre','Lør'],
                    dayNamesMin: ['Sø','Ma','Ti','On','To','Fr','Lø'],
                    weekHeader: 'Uge',
                    dateFormat: 'dd-mm-yy',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: ''};
                    $.datepicker.setDefaults($.datepicker.regional['da']);
                });
        </script>
    </head>
    <body>
        
        <!-- Wrapper -->
        <div id="wrapper">
            
            <!-- Header -->
            <header id="header">
                <h1 class="headline"><a href="index.php">BORG EMS</a></h1>
                
                <?=$core->printTopMenu();?>
            </header>         
            
            <!-- Main Content -->
            <main id="main">
                <section id="form">
                    <?php
                    if(!isset($_POST['sent'])) {
                        ?> 
                        <h1>Mangelliste</h1>

                        <form name="mangelliste" action="mangelliste.php" method="post">                        
                            <div class="row">    
                                <div class="column column-6">
                                    <label for="name">Navn</label>
                                    <input type="text" name="name" id="name" value="<?=$user->getFullName();?>" disabled>
                                </div>        

                                <div class="column column-6">
                                    <label for="datepicker">Dato</label>
                                    <input type="text" name="date" id="datepicker" placeholder="F.eks 17-05-2014">
                                </div>
                            </div>

                            <div class="row">                                
                                <div class="column column-6">
                                    <label for="stationNumber">Stations Nummer</label>
                                    <input type="text" name="stationNumber" id="stationNumber" placeholder="Stationsnumre består af 5 cifre og begynder altid med 10">
                                </div>

                                <div class="column column-6">
                                    <label for="stationName">Stations Navn</label>
                                    <input type="text" name="stationName" id="stationName" readonly>
                                </div>
                            </div>
                           
                            <div class="row">
                                <div class="column column-12">
                                    <label for="materials">Mangler</label>
                                    <textarea name="materials" id="materials" placeholder="Hvilke materialer mangler der?"></textarea>
                                </div>
                            </div>

                            <input type="hidden" name="sent" value="">
                            
                            <div class="row">
                                <div class="column column-12">
                                    <input type="submit" value="Indsend">
                                </div>
                            </div>
                        </form>
                        <?php
                    } else {                                    
                        $station = new Station($_POST['stationNumber']);
                        $shortageList = new ShortageList($user, $_POST['date'], $station, $_POST['materials']);
                        $result = $shortageList->create();
                        if($result) {
                            ?>
                            <h1>Mangelliste Sendt!</h1>
                            <form name="shortageList" action="mangelliste.php" method="post">                        
                                <div class="row">                     
                                    <div class="column column-6">
                                        <label for="name">Navn</label>
                                        <input type="text" name="name" id="name" value="<?=$user->getFullName();?>" disabled>
                                    </div>        
           
                                    <div class="column column-6">
                                        <label for="datepicker">Dato</label>
                                        <input type="text" name="date" id="datepicker" value="<?=$_POST['date']?>" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="column column-6">
                                        <label for="stationNumber">Stations Nummer</label>
                                        <input type="text" name="stationNumber" id="stationNumber" value="<?=$_POST['stationNumber']?>" disabled>
                                    </div>

                                    <div class="column column-6">
                                        <label for="stationNameData">Stations Navn</label>
                                        <input type="text" name="stationNameData" id="stationNameData" value="<?=$_POST['stationName']?>" disabled>
                                    </div>
                                </div>
                            
                                <div class="row">
                                    <div class="column column-12">
                                        <label for="materials">Mangler</label>
                                        <textarea name="materials" id="materials" disabled><?=$_POST['materials']?></textarea>
                                    </div>
                                </div>
                            </form>
                            <?php
                        } else {
                            foreach($shortageList->getErrors() as $error) {
                                print '<span class="error">' . $error . '</span>';
                            }
                            ?>
                                <a href="javascript: history.go(-1)">Gå tilbage</a>
                            <?php
                        }
                    }
                    ?>
                </section>
            </main>
            
            <!-- Footer -->
            <footer id="footer">
                <small>
                    <p class="copyright">Copyright &copy; 2016 BORG Inventar. Alle rettigheder forbeholdes.</p>
                </small>
            </footer>
        </div>
                
        <!-- Scripts -->
        <script src="assets/js/init.js"></script>
    </body>
</html>