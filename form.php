<?php
   session_start();
   if (!isset($_SESSION['historique'])) {
       $_SESSION['historique'] = [];
   }
   
   function convertir(){
        if ($_POST['cfa'] !== "" && $_POST['cfa'] > 0) {
            $tauxDeChange = 0.001524;
            $montantCfa = $_POST['cfa'];
            $montantEuro = $montantCfa * $tauxDeChange;
            $montantEuroArrondi = round($montantEuro, 2);
            $conversion = [
                'montant' =>$_POST['cfa'],
                'date' => date('j F Y H:i:s'),
                'montant_convertit'=>$montantEuroArrondi
            ];
            $_SESSION['historique'][] = $conversion;
            
            return "Le montant en Euro est : " . $montantEuroArrondi . " Euro";
        }
   }
   if (isset($_POST['reset'])) {
    $_SESSION['historique'] = [];

}   
   if (isset($_POST['submit'])) {
       $resultat = convertir($_POST['cfa']);
   }

   ?>
   <!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Convertisseur cfa en euro</title>
        <link rel="stylesheet" href="form.css">
    </head>
    <body>
        <form action="form.php" method="post">
            <h1>Convertisseur</h1>
            <input type="number" required name="cfa" value="" placeholder=" CFA" autocomplete="off" class="cfa">
            <button type="submit" name="submit">Convertir</button>
            <input type="text" name="euro" readonly placeholder="EURO" class="euro" value="<?php if (isset($_POST['submit'])) echo $resultat ?>">
        </form>
            <div class="historique">
                <h2>Historique de conversions :</h2>
                <ol>
                    <?php foreach ($_SESSION['historique'] as $montant) : ?>
                        <li>
                            Montant : <?= $montant['montant'] ?> CFA, 
                            Date : <?= $montant['date'] ?>, 
                            Montant Converti : <?= $montant['montant_convertit'] ?> Euro
                        </li>
                    <?php endforeach; ?>
                </ol>
            <input type="submit" name="reset" value="<?php if (isset($_POST['reset'])) echo $_SESSION['histroque']=[]?>">
            </div>
        </form> 
    </body>
</html>