<?php
session_start();
if (!isset($_SESSION['historique'])) {
    $_SESSION['historique'] = [];
}
if (!isset($_SESSION['filtrage'])) {
    $_SESSION['filtrage'] = [];
}

function convertir()
{
    if ($_POST['cfa'] !== "" && $_POST['cfa'] > 0) {
        $tauxDeChange = 0.001524;
        $montantCfa = $_POST['cfa'];
        $montantEuro = $montantCfa * $tauxDeChange;
        $montantEuroArrondi = round($montantEuro, 2);
        $conversion = [
            'montant' => $_POST['cfa'],
            'date' => date('j F Y'),
            'heure_conversion' => date('H:i:s'),
            'montant_convertit' => $montantEuroArrondi
        ];
        $_SESSION['historique'][] = $conversion;
        return "Le montant en Euro est : " . $montantEuroArrondi . " Euro";
    } elseif ($_POST['cfa'] <= 0) {
        echo "Veuillez entrer un nombre positif";
    }
}
if (isset($_POST['submit'])) {
    $resultat = convertir($_POST['cfa']);
}
if (isset($_POST['filtre']) && isset($_POST['date'])) {
    // Utilisez strtotime pour convertir la date en timestamp
    $datefiltrer = date('j F Y', strtotime($_POST['date']));
    $_SESSION['filtrage'] = [];
    foreach ($_SESSION['historique'] as $montant) {
        if ($datefiltrer == $montant['date']) {
            $_SESSION['filtrage'][] = $montant;
        }
    }
}
if (isset($_POST['supprimer'])) {
    $dateASupprimer = $_POST['date_a_supprimer'];
    // Parcourir l'historique et supprimer les éléments avec la date correspondante
    foreach ($_SESSION['historique'] as $key => $montant) {
        if ($montant['date'] === $dateASupprimer) {
            unset($_SESSION['historique'][$key]);
        }
    }
    // Réindexez le tableau après la suppression
    $_SESSION['historique'] = array_values($_SESSION['historique']);
}
if (isset($_POST['reset'])) {
    session_destroy();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convertisseur cfa en euro</title>
    <link rel="stylesheet" href="currency_converter/form.css">
</head>
<body>
    <form action="form.php" method="POST" class="left">
        <h1>Convertisseur</h1>
        <input type="number" required name="cfa" value="" placeholder=" CFA" autocomplete="off" class="cfa">
        <button type="submit" name="submit" class="convertir">Convertir</button>
        <input type="text" name="euro" readonly placeholder="EURO" class="euro" value="<?php if (isset($_POST['submit'])) echo $resultat ?>">
    </form>
    <form action="form.php" method="POST" class="right">
    <button type="submit" name="reset" class="reset">Réinitialiser</button>
    <div class="historique">
            <h2>Historique de conversions :</h2>
            <ol>
                <?php
                $dateAffichee = '';
                foreach ($_SESSION['historique'] as $montant) :
                    if ($dateAffichee !== $montant['date']) {
                        echo "<h3>Date : " . $montant['date'] . "</h3>";
                        $dateAffichee = $montant['date'];
                        echo '<form action="form.php" method="POST">';
                        echo '<input type="hidden" name="date_a_supprimer" value="' . $montant['date'] . '">';
                        echo ' <button type="submit" name="supprimer" class="supprimer">Supprimer</button>';
                        echo '</form>';
                    }
                    ?>
                    <li>
                        Montant : <?= $montant['montant'] ?> CFA,
                        Montant_Converti : <?= $montant['montant_convertit'] ?> Euro 
                        Heure: <?php echo $montant['heure_conversion'] ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </form>

    <form action="form.php" method="POST" class="filter">
        <input type="date" name="date" class="filtre" required>
        <button type="submit" name="filtre" class="filtre">FILTRER</button>
        <div class="historique_filtrer">
            <ol>
            <?php
           $historique = isset($_SESSION['filtrage']) ? $_SESSION['filtrage'] : $_SESSION['historique'];
           if (!empty($historique)) {
               echo "<h3>Date : " . $historique[0]['date'] . "</h3>";
               
               echo "<ul>";
               foreach ($historique as $montant) :
                   ?>
                   <li>
                      <?php 
                      echo "Montant : " . $montant['montant'] . " CFA, ";
                      echo "Montant_Converti : " . $montant['montant_convertit'] . " Euro ";
                      echo "Heure: " . $montant['heure_conversion'];
                      ?>
                   </li>
               <?php endforeach;
               echo "</ul>";
           } else {
               echo "Aucun résultat trouvé pour la date sélectionnée.";
           }
            ?>
            </ol>
        </div>
    </form>
</body>
</html>
