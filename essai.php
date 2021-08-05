<?php
/*
// dans config
$nbParPage = 4 ;

// en paramètre
$pos = 3 ;

// nombre de news dans le mois
$nbNews = 15 ;

$nouvellePos = 0;

for($i = 0; $i < $nbNews; $i = $i + $nbParPage)
{
    if ($nouvellePos <> $pos)
    {
        //$nou
        echo " " . ($nouvellePos + 1) . " " ;
    }
    else
    {
        echo " [" . ($nouvellePos + 1) . "] " ;
    }

    $nouvellePos++;
}
*/
/*
$date = "24/04/2003" ;
$heure = "23:56:18" ;

$jour = substr($date, 0, 2) ;
$mois = substr($date, 3, 2) ;
$annee = substr($date, 6, 4) ;

$hour = substr($heure, 0, 2) ;
$minute = substr($heure, 3, 2) ;
$seconde = substr($heure, 6, 2) ;

$tab["0"] = "20030405235926" ;
$tab["1"] = "20050405235926" ;
$tab["2"] = "20030405235925" ;

arsort($tab) ;

while (list($k,$v) = each($tab))
{
    echo "$k => $v<br>" ;
}
*/
/*
// tableau contenant les noms des fichiers
$listeFichiers = array() ;

$fd = @opendir("data/news") ;

if ($fd)
{
    while ($fichier = @readdir($fd))
    {
        // On ignore les fichier commençant pas un .
        if (!ereg("^\.", $fichier))
        {
            // sélectionne uniquement les fichiers dont le nom est (6 chiffres).xml
            if (ereg("^[0-9]{6}\.xml\$", $fichier))
            {
                $listeFichiers[count($listeFichiers)] = substr($fichier, 0, 6) ;
            }
        }
    }

    rsort($listeFichiers) ;
}

// Vérifie le format de annee. S'il n'y a rien c pareil
if (ereg("^[0-9]{4}\$", $HTTP_GET_VARS["annee"]))
{
    $annee = $HTTP_GET_VARS["annee"] ;
}
else
{
    $annee = substr($listeFichiers[0], 0, 4) ;
}

// Nombre de fichier
$nbFichier = count($listeFichiers) ;

$listeAnnees = array() ;

for ($i = 0; $i < $nbFichier; $i++)
{
    if (substr($listeFichiers[$i], 0, 4) == $annee)
    {
        echo "<a href='index.php?date=" . $listeFichiers[$i] . "'>" . $listeFichiers[$i] . "</a><br>" ;
    }
    else
    {
        $annee2 = substr($listeFichiers[$i], 0, 4) ;

        if (!isset($listeAnnees[$annee2]))
        {
            echo "<a href='index.php?annee=$annee2&date=" . $listeFichiers[$i] . "'>$annee2</a><br>" ;
        }
    }
}*/

echo "<select>" ;
    $fd = opendir("data/news/") ;

    if ($fd)
    {
        while ($fichier = @readdir($fd))
        {
            // On ignore les fichier commençant pas un .
            if (!ereg("^\.", $fichier))
            {
                // sélectionne uniquement les fichiers dont le nom est (6 chiffres).xml
                if (ereg("^[0-9]{6}\.xml\$", $fichier))
                {
                    $fichier = htmlentities($fichier) ;

                    echo "<option value='$fichier'>$fichier</option>" ;

                    if ($fichier == $fichierSelectionne)
                    {
                        echo " selected" ;
                    }


                    echo ">$fichier</option>" ;
                }
            }
        }
    }
echo "</select>" ;
?>
