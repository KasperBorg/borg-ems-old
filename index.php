<?php
session_start();

require_once 'core/forms/dailydocket.class.php';
require_once 'core/security.class.php';
require_once 'core/user.class.php';
require_once 'core/core.class.php';

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
                        <h1>Dagsseddel</h1>

                        <form name="dagsseddel" action="index.php" method="post">                        
                            <div class="row">
                                <div class="column column-6">
                                    <label for="nameShow">Navn</label>
                                    <input type="text" name="name" value="<?=$user->getFirstName();?> <?=$user->getLastName();?>" disabled>
                                </div>
                                
                                <div class="column column-6">
                                    <label for="datepicker">Dato</label>
                                    <input type="text" name="date" id="datepicker" placeholder="F.eks 17-05-2014">
                                </div>
                            </div>

                            <div class="row">
                                <div class="column column-2">
                                    <label for="timeStart">Start kl.</label>
                                    <input type="text" name="timeStart" id="startTime" placeholder="F.eks 12.00">
                                </div>
                            
                                <div class="column column-2">
                                    <label for="timeEnd">Slut kl.</label>
                                    <input type="text" name="timeEnd" id="endTime" placeholder="F.eks 15.30">
                                </div>

                                <div class="column column-2">
                                    <label for="timeStartStore">Start Butik kl.</label>
                                    <input type="text" name="timeStartStore" id="timeStartStore" placeholder="F.eks 12.00">
                                </div>
                            
                                <div class="column column-2">
                                    <label for="timeEndStore">Slut Butik kl.</label>
                                    <input type="text" name="timeEndStore" id="timeEndStore" placeholder="F.eks 15.30">
                                </div>
                              
                                <div class="column column-4">   
                                    <label for="quant">Quant</label>
                                    <input type="text" name="quant" id="quant" placeholder="Quant / Tekst">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="column column-12">
                                    <label for="">Arbejdssted</label>
                                    <textarea name="location" placeholder="Hvor er arbejdet blevet udført?"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="column column-6">
                                    <label for="ownCar">Egen bil</label>
                                    <select name="ownCar" id="ownCar">
                                        <option value="Nej">Nej</option>
                                        <option value="Ja">Ja</option>
                                    </select>
                                </div>
                                
                                <div class="column column-6">
                                    <label for="kilometers">Kilometer</label>
                                    <input type="text" name="kilometers" id="kilometer" placeholder="F.eks 512">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="column column-12">
                                    <label for="workDescription">Opgavens Art</label>
                                    <textarea name="workDescription" id="workDescription" placeholder="Hvilket arbejde er der blevet udført?"></textarea>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="column column-12">
                                    <label for="materials">Materialer</label>
                                    <textarea name="materials" id="materials" placeholder="Hvilke materialer er der blevet brugt?"></textarea>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="column column-6">
                                    <label for="bridges">Broafgift, antal</label>
                                    <select name="bridges" id="bridges">
                                        <option value="0">0</option>
                                        <option value="1 Lille">1 Lille</option>
                                        <option value="2 Lille">2 Lille</option>
                                        <option value="1 Stor">1 Stor</option>
                                        <option value="2 Stor">2 Stor</option>
                                    </select>
                                </div>
                                
                                <div class="column column-6">
                                    <label for="hotels">Hotel Ophold</label>
                                    <select name="hotels" id="hotels">
                                        <option value="0">Ingen overnatninger</option>
                                        <option value="1">Hel overnatning</option>
                                        <option value="0,5">Halv overnatning</option>
                                    </select>                   
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
                        $dailyDocket = new DailyDocket($user, $_POST['date'], $_POST['location'], $_POST['timeStart'], $_POST['timeEnd'], $_POST['timeStartStore'], $_POST['timeEndStore'], $_POST['quant'], $_POST['ownCar'], $_POST['kilometers'], $_POST['workDescription'], $_POST['materials'], $_POST['bridges'], $_POST['hotels']);

                        $result = $dailyDocket->create();
                        $errors = $dailyDocket->getErrors();
                        if($result != False) {
                            ?>
                            <h1>Dagsseddel Sendt!</h1>

                            <form name="dagsseddel" action="index.php" method="post">                        
                                <div class="row">
                                    <div class="column column-6">
                                        <label for="nameShow">Navn</label>
                                        <input type="text" name="nameShow" value="<?=$dailyDocket->name;?>" disabled>
                                    </div>
                                    
                                    <div class="column column-6">
                                        <label for="datepicker">Dato</label>
                                        <input type="text" name="date" id="datepicker" value="<?=$dailyDocket->date?>" disabled>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="column column-12">
                                        <label for="">Arbejdssted</label>
                                        <textarea name="location" disabled><?=$dailyDocket->location;?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="column column-4">
                                        <label for="timeStart">Start kl.</label>
                                        <input type="text" name="timeStart" id="startTime" value="<?=$dailyDocket->timeStart;?>" disabled>
                                    </div>
                                
                                    <div class="column column-4">
                                        <label for="timeEnd">Slut kl.</label>
                                        <input type="text" name="timeEnd" id="endTime" value="<?=$dailyDocket->timeEnd;?>" disabled>
                                    </div>

                                    <div class="column column-4">
                                        <label for="timeTotal">Samlet Tid</label>
                                        <input type="text" name="timeTotal" id="totalTime" value="<?=$dailyDocket->timeTotal;?>" disabled>
                                    </div>
                                </div>

                               <div class="row">
                                    <div class="column column-4">
                                        <label for="timeStartStore">Start Butik kl.</label>
                                        <input type="text" name="timeStartStore" id="timeStartStore" value="<?=$dailyDocket->timeStartStore;?>" disabled>
                                    </div>
                                
                                    <div class="column column-4">
                                        <label for="timeEndStore">Slut Butik kl.</label>
                                        <input type="text" name="timeEndStore" id="endTimeStore" value="<?=$dailyDocket->timeEndStore;?>" disabled>
                                    </div>

                                    <div class="column column-4">
                                        <label for="timeTotalStore">Samlet Butik Tid</label>
                                        <input type="text" name="timeTotalStore" id="totalTimeStore" value="<?=$dailyDocket->timeTotalStore;?>" disabled>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="column column-12">   
                                        <label for="quant">Quant</label>
                                        <input type="text" name="quant" id="quant" value="<?=$dailyDocket->quant;?>" disabled
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="column column-6">
                                        <label for="ownCar">Egen bil</label>
                                        <input type="text" name="ownCar" id="ownCar" value="<?=$dailyDocket->ownCar;?>" disabled>
                                    </div>
                                    
                                    <div class="column column-6">
                                        <label for="kilometers">Kilometer</label>
                                        <input type="text" name="kilometers" id="kilometers" value="<?=$dailyDocket->kilometers;?>" disabled>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="column column-12">
                                        <label for="workDescription">Opgavens Art</label>
                                        <textarea name="workDescription" id="workDescription" disabled><?=$dailyDocket->workDescription;?></textarea>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="column column-12">
                                        <label for="materials">Materialer</label>
                                        <textarea name="materials" id="materials" disabled><?=$dailyDocket->materials;?></textarea>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="column column-6">
                                        <label for="bridges">Broafgift, antal</label>
                                        <input name="bridges" id="bridges" value="<?=$dailyDocket->bridges;?>" disabled>
                                    </div>
                                    
                                    <div class="column column-6">
                                        <label for="hotels">Hotel Ophold</label>
                                        <input name="hotels" id="hotels" value="<?=$dailyDocket->hotels;?>" disabled>
                                    </div>
                                </div>
                            </form>
                            <?php
                        } else {
                            foreach($errors as $error) {
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
                    <?=$core->printFooter();?>
                </small>
            </footer>
        </div>
                
        <!-- Scripts -->
        <script src="assets/js/init.js"></script>
    </body>
</html>