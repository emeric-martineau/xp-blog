<?php
/******************************************************************************
 * XP-Blog - Le blog personnel en XML
 *
 * Copyright 2003 (c) Bubule
 *
 * Fichier gérant les catégorie
 *
 * ajout, suppression, modification
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 ******************************************************************************/
include("includes/include.php") ;

///////////////////////////////////////////////////////////////////////////////
// Génération des archives
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
    $annee2 = substr($listeFichiers[$i], 0, 4) ;

    if ($annee2 == $annee)
    {
        $libelle = convDate($config->formatDateListeArchives, 1, substr($listeFichiers[$i], 4, 6), $annee2, 1, 1, 1) ;
    }
    else
    {
        if (!isset($listeAnnees["$annee2"]))
        {
            $listeAnnees["$annee2"] = 1 ;
            $libelle = $annee2 ;
        }
        else
        {
            // Evite d'avoir autant de fois l'annee que de fichiers
            continue ;
        }
    }

    $monTheme->addVar(array("DATE" => $listeFichiers[$i], "ANNEE" => $annee2,
                            "LIBELLE" => $libelle)) ;

    $monTheme->addVar("LISTE_ARCHIVE", $monTheme->getVarValue("LISTE_ARCHIVE") .
               $monTheme->parseLine($monTheme->getVarValue("MODEL_LIEN_ARCHIVE"))) ;
}

///////////////////////////////////////////////////////////////////////////////
// tableau contenant les noms des fichiers
$listeFichiers = array() ;

// Indique s'il y a eu des erreurs
$erreur = false ;

if (!isset($HTTP_GET_VARS["date"]))
{
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

        $mois = $listeFichiers[0] ;
    }
    else
    {
        $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_LECTURE_REP_NEWS")) ;
        $monTheme->parse("message.htm") ;
        $erreur = true ;
    }
}
else
{
    // Vérifie le format de la date envoyée
    if (ereg("^[0-9]{6}\$", $HTTP_GET_VARS["date"]))
    {
        $mois = $HTTP_GET_VARS["date"] ;

        if (!file_exists("data/news/$mois.xml"))
        {
            // Le fichier n'existe pas
            $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_LECTURE_NEWS_PRECISEE")) ;
            $monTheme->parse("message.htm") ;
            $erreur = true ;
        }
    }
    else
    {
        // Si elle n'est pas bonne on affiche un message d'erreur.
        // Il peut s'agir d'une tentative de pire ratage ;-)
        $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_FORMAT_DATE_INVALIDE")) ;
        $monTheme->parse("message.htm") ;
        $erreur = true ;
    }
}

if ($erreur == false)
{
    // Enregistre le mois
    $monTheme->addVar("DATE", $mois) ;

    // Gestion des brouillons
    if (($HTTP_GET_VARS["brouillon"] == "1") && ($admin == true))
    {
        $brouillon = 1 ;
        $monTheme->addVar("BROUILLON", 1) ;
    }
    else
    {
        $brouillon = 0 ;
        $monTheme->addVar("BROUILLON", 0) ;
    }

    // Se position sur la bonne page
    if (!ereg("^[0-9]+\$", $HTTP_GET_VARS["pos"]))
    {
        $pos = 1 ;
    }
    else
    {
        $pos = $HTTP_GET_VARS["pos"] ;

        if (isset($HTTP_GET_VARS["next"]))
        {
            $pos += $config->nbNewsParPage ;
        }
        else if (isset($HTTP_GET_VARS["prev"]))
        {
            $pos -= $config->nbNewsParPage ;
        }
    }

    $monTheme->addVar("POSITION", $pos) ;

    // Affiche ou non le lien précédent
    if ($pos > 1)
    {
        $monTheme->addVar("LIEN_PRECEDANT", $monTheme->parseLine($monTheme->getVarValue("PRECEDANT"))) ;
    }

    // Création de l'objet gérant les xml
    $categoriesXML = new touv_xml() ;

    if ($brouillon == 1)
    {
        $fichier = "data/brouillons.xml" ;
    }
    else
    {
        $fichier = "data/news/$mois.xml" ;
    }

    // Lecture des categories
    $tableauDeCategories = $categoriesXML->chargerfichier("data/categories.xml") ;
    $tableauDeCategories = convTouv_xmlCategories($tableauDeCategories);

    // Lecture des types
    $tableauDeTypes = $categoriesXML->chargerfichier("data/types.xml") ;
    $tableauDeTypes = convTouv_xmlTypes($tableauDeTypes);

    // Lectures des news
    $tableauDeNews = $categoriesXML->chargerfichier($fichier) ;
    $tableauDeNews = convTouv_xmlNews($tableauDeNews);

    if ($tableauDeNews != -1)
    {
        // Pour chaque news
        $nbNews = count($tableauDeNews) ;
        $tableauPourTri = array() ;

        // Affiche ou non le lien précédent
        if (($pos + $config->nbNewsParPage - 1) < $nbNews)
        {
            $monTheme->addVar("LIEN_SUIVANT", $monTheme->parseLine($monTheme->getVarValue("SUIVANT"))) ;
        }

        $nouvellePos = 0;

        for($i = 0; $i < $nbNews; $i = $i + $config->nbNewsParPage)
        {
            $nouvellePos++;

            if (($i + 1) <> $pos)
            {
                $monTheme->addVar("LISTE_INDEX", $monTheme->getVarValue("LISTE_INDEX") . " <a href='index.php?brouillon=$brouillon&pos=" . ($i + 1) ."&date=$mois'>" . $nouvellePos  . "</a> ") ;
            }
            else
            {
                $monTheme->addVar("LISTE_INDEX", $monTheme->getVarValue("LISTE_INDEX") . " <b>" . $nouvellePos  . "</b> ") ;
            }
        }

        // S'il n'y a qu'un page, LISTE_INDEX est vidé
        if ($i <= 2)
        {
            $monTheme->addVar("LISTE_INDEX", "") ;
        }

        // Pour chaque news, on créer une entrée dans un tableau associatif
        // dont la clef est l'id et la valeur la date et heure au format
        // YYYYMMDDHHmmSS
        // YYYY : année
        // MM   : mois
        // DD   : jour
        // HH   : heure
        // mm   : minute
        // SS   : seconde
        for ($i = 0; $i < $nbNews; $i++)
        {
            $date = $tableauDeNews[$i]->date ;
            $heure = $tableauDeNews[$i]->heure ;

            $jour = substr($date, 0, 2) ;
            $mois = substr($date, 3, 2) ;
            $annee = substr($date, 6, 4) ;

            $hour = substr($heure, 0, 2) ;
            $minute = substr($heure, 3, 2) ;
            $seconde = substr($heure, 6, 2) ;

            $tableauPourTri["" . $tableauDeNews[$i]->id . ""] = $annee . $mois . $jour . $hour . $minute .
                                                                 $seconde ;
        }

        // Tri le tableau la plus recente sera la première
        arsort($tableauPourTri) ;

        $positionNews = 0 ;

        while ((list($k,$v) = each($tableauPourTri)) && (($pos + $config->nbNewsParPage) > ($positionNews + 1)))
        {
            $positionNews++ ;

            if ($positionNews >= $pos)
            {
                // Récupère la news
                $laNews = getObjectWithID($tableauDeNews, $k) ;

                // Récupère la catégorie
                $laCategorie = getObjectWithID($tableauDeCategories, $laNews->categorie) ;

                // Récupère le type
                $leType = getObjectWithID($tableauDeTypes, $laNews->type) ;

                $date = $laNews->date ;

                $jour = substr($date, 0, 2) ;
                $mois = substr($date, 3, 2) ;
                $annee = substr($date, 6, 4) ;

                $libelle = convDate($config->formatDateNews, $jour, $mois,
                                    $annee, 1, 1, 1) ;

                $monTheme->addVar(array("ID_NEWS" => htmlentities($laNews->id),
                                        "TITRE_NEWS" => htmlentities($laNews->titre),
                                        "TEXTE_NEWS" => $laNews->texte,
                                        "HEURE_NEWS" => htmlentities($laNews->heure),
                                        "DATE_NEWS" => $libelle,
                                        "CATEGORIE_NOM_NEWS" => htmlentities($laCategorie->nom),
                                        "CATEGORIE_IMAGE_NEWS" => $laCategorie->image,
                                        "TYPE_NOM_NEWS" => htmlentities($leType->nom),
                                        "TYPE_IMAGE_NEWS" => $leType->image
                                      )) ;

                if ($admin)
                {
                    $monTheme->addVar("ADMIN_NEWS", $monTheme->parseLine($monTheme->getVarValue(
                                      "ADMINISTRATION_NEWS"))) ;

                   if ($brouillon == 1)
                   {
                        $monTheme->addVar("ADMIN_NEWS", $monTheme->getVarValue("ADMIN_NEWS") .
                                   $monTheme->parseLine($monTheme->getVarValue(
                                   "ADMINISTRATION_BROUILLON"))) ;
                   }
                }

                $monTheme->parse("news.htm") ;
            }
        }
    }
    else
    {
        $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_LECTURE_REP_NEWS")) ;
        $monTheme->parse("message.htm") ;
    }
}

$monTheme->addVar("PAGE", $monTheme->getParseResult()) ;
$monTheme->clearResult() ;
$monTheme->parse("squelette.htm") ;
$monTheme->printResult() ;
?>
