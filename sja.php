<?php
session_start();

require_once 'core/forms/sja.class.php';
require_once 'core/security.class.php';
require_once 'core/user.class.php';
require_once 'core/core.class.php';
require_once 'core/station.class.php';
require_once 'core/riskevaluation.class.php';

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
                        <h1>Sikkert Job Analyse</h1>

                        <form name="sja" action="sja.php" method="post">                        
                            <div class="row">    
                                <div class="column column-4">
                                    <label for="names">Navn</label>

                                    <select name="names" id="names">
                                        <?php 
                                        $users = $core->getUsers(); 
                                        foreach($users as $user) {
                                            echo '<option value="'.$user->getFullName().'">'.$user->getFullName().'</option>';
                                        }
                                        ?>
                                    </select>
                                </div> 

                                <div class="column column-2">
                                    <button type="button" id="addName">Tilføj ></button>
                                </div>

                                <div class="column column-6">
                                    <label for="montorer">Montører</label>
                                    <input type="text" name="montorer" id="montorer" readonly>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="column column-6">
                                    <label for="majorActivity">Aktivitet</label>
                                    <input type="text" name="majorActivity" id="majorActivity">
                                </div>

                                 <div class="column column-6">
                                    <label for="datepicker">Dato</label>
                                    <input type="text" name="date" id="datepicker" placeholder="F.eks 17-05-2014" readonly>
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
                                <div class="column column-6">
                                    <label for="securityCoordinator">Sikkerhedskoordinator</label>
                                    <input type="text" name="securityCoordinator" id="securityCoordinator">
                                </div>           

                                <div class="column column-6">
                                    <label for="client">Opdragsgiver hos Circle K</label>
                                    <input type="text" name="client" id="client">
                                </div>
                            </div>

                            <div class="row">
                                <div class="column column-12">
                                    <label for="specialConcerns">Specielle hensyn på denne service station</label>
                                    <textarea name="specialConcerns" id="specialConcerns"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="column column-12">
                                    <label for="nearMiss">Nærved-hændelser på denne service station</label>
                                    <textarea name="nearMiss" id="nearMiss"></textarea>
                                </div>
                            </div>
                
                           
                            <fieldset id="riskEvalution">
                                <legend>Risikovurdering</legend>

                                <div class="row">
                                    <div class="column column-6">
                                        <label for="activity">Aktivitet</label>
                                        <textarea name="activity[]" id="activity" placeholder="Gennemgå alle arbejdstrinene"></textarea>
                                    </div>
     
                                    <div class="column column-6">
                                        <label for="risk">Risiko</label>
                                        <textarea name="risk[]" id="risk" placeholder="Analyser faremomenterne, og skriv dem ud for det aktuelle arbejdstrin."></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="column column-6">
                                        <label for="percausions">Sikkerhedsforanstaltning</label>
                                        <textarea name="percausions[]" id="percausions" placeholder="Hvilke sikkerhedsforanstaltninger tages der?"></textarea>
                                    </div>

                                    <div class="column column-6">
                                        <label for="responsible">Hvem er ansvarlig?</label>
                                        <textarea name="responsible[]" id="responsible" placeholder="Hvem er ansvarlig for at udføre sikkerhedsforanstaltningen?"></textarea>
                                    </div>
                                </div>
                            </fieldset>

                            <div class="row">
                                <div class="column column-12">
                                    <button type="button" id="addRow">Tilføj Række</button>
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

                        $riskEvalutions = array();

                        for ($i = 0; $i <= count($_POST['activity'])-1; $i++) {
                            $riskEvalutions[] = new RiskEvaluation($_POST['activity'][$i], $_POST['risk'][$i], $_POST['percausions'][$i], $_POST['responsible'][$i]);

                        }

                        $sja = new SJA($_POST['montorer'], $user, $_POST['majorActivity'], $_POST['date'], $station, $_POST['securityCoordinator'], $_POST['client'], $_POST['specialConcerns'], $_POST['nearMiss'], $riskEvalutions);

                        $result = $sja->create();
                        
                        if($result) {
                            ?>
                            <h1>Sikkert Job Analyse Sendt!</h1>
    
                            <div class="row">
                                <div class="column column-12">
                                    <label for="montorer">Montører</label>
                                    <input type="text" name="montorer" id="montorer" value="<?=$_POST['montorer']; ?>" disabled>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="column column-6">
                                    <label for="majorActivity">Aktivitet</label>
                                    <input type="text" name="majorActivity" id="majorActivity" value="<?=$_POST['majorActivity']; ?>" disabled>
                                </div>

                                 <div class="column column-6">
                                    <label for="datepicker">Dato</label>
                                    <input type="text" name="date" id="datepicker" placeholder="F.eks 17-05-2014" value="<?=$_POST['date']; ?>" disabled>
                                </div>
                            </div>

                            <div class="row">                                
                                <div class="column column-6">
                                    <label for="stationNumber">Stations Nummer</label>
                                    <input type="text" name="stationNumber" id="stationNumber" value="<?=$_POST['stationNumber']; ?>" disabled>
                                </div>

                                <div class="column column-6">
                                    <label for="stationName">Stations Navn</label>
                                    <input type="text" name="stationName" id="stationName" value="<?=$_POST['stationName']; ?>" disabled>
                                </div>
                            </div>

                            <div class="row">                                
                                <div class="column column-6">
                                    <label for="securityCoordinator">Sikkerhedskoordinator</label>
                                    <input type="text" name="securityCoordinator" id="securityCoordinator" value="<?=$_POST['securityCoordinator']; ?>" disabled>
                                </div>           

                                <div class="column column-6">
                                    <label for="client">Opdragsgiver hos Circle K</label>
                                    <input type="text" name="client" id="client" value="<?=$_POST['client']; ?>" disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="column column-12">
                                    <label for="specialConcerns">Specielle hensyn på denne service station</label>
                                    <textarea name="specialConcerns" id="specialConcerns" disabled><?=$_POST['specialConcerns']; ?></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="column column-12">
                                    <label for="nearMiss">Nærved-hændelser på denne service station</label>
                                    <textarea name="nearMiss" id="nearMiss" disabled><?=$_POST['nearMiss']; ?></textarea>
                                </div>
                            </div>
                            <?php                
                            for ($i = 0; $i <= count($_POST['activity'])-1; $i++) {                            
                            ?>
                            <fieldset class="">
                                <legend>Risikovurdering</legend>

                                <div class="row">
                                    <div class="column column-6">
                                        <label for="activity">Aktivitet</label>
                                        <textarea name="activity[]" id="activity" placeholder="Gennemgå alle arbejdstrinene" disabled><?=$_POST['activity'][$i];?></textarea>
                                    </div>
     
                                    <div class="column column-6">
                                        <label for="risk">Risiko</label>
                                        <textarea name="risk[]" id="risk" placeholder="Analyser faremomenterne, og skriv dem ud for det aktuelle arbejdstrin." disabled><?=$_POST['risk'][$i];?></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="column column-6">
                                        <label for="percausions">Sikkerhedsforanstaltning</label>
                                        <textarea name="percausions[]" id="percausions" placeholder="Hvilke sikkerhedsforanstaltninger tages der?" disabled><?=$_POST['percausions'][$i];?></textarea>
                                    </div>

                                    <div class="column column-6">
                                        <label for="responsible">Hvem er ansvarlig?</label>
                                        <textarea name="responsible[]" id="responsible" placeholder="Hvem er ansvarlig for at udføre sikkerhedsforanstaltningen?" disabled><?=$_POST['responsible'][$i];?></textarea>
                                    </div>
                                </div>
                            </fieldset>
                            <?php
                            }
                        } else {
                            foreach($sja->getErrors() as $error) {
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