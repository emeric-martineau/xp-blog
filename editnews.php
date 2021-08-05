<?php
/******************************************************************************
 * XP-Blog - Le blog personnel en XML
 *
 * Copyright 2003 (c) Bubule
 *
 * Fichier éditant une news
 *
 * suppression, modification
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

$monTheme->addVar("POSITION", $HTTP_GET_VARS["pos"]) ;

if ($admin)
{
    $erreur = false ;

    $monTheme->addVar("DATE", $HTTP_GET_VARS["date"]) ;

    // récupère les variables
    if ($HTTP_GET_VARS["brouillon"] == 1)
    {
        $fichier = "data/brouillons.xml" ;
        $monTheme->addVar("BROUILLON", 1) ;
        $brouillon = 1 ;
    }
    else
    {
        $monTheme->addVar("BROUILLON", 0) ;
        $brouillon = 0 ;

        // Vérifie le format de la date envoyée
        if (ereg("^[0-9]{6}\$", $HTTP_GET_VARS["date"]))
        {
            $fichier = "data/news/" . $HTTP_GET_VARS["date"] . ".xml" ;

            if (!file_exists($fichier))
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
        // Création de l'objet gérant les xml
        $categoriesXML = new touv_xml() ;

        $categoriesXML = $categoriesXML->chargerfichier($fichier) ;
        $tableauDeNews = convTouv_xmlNews($categoriesXML);

        if (isset($HTTP_POST_VARS["modifier"]))
        {
            // Il nous faut conserver la date et heure du brouillon
            $laNews = getObjectWithID($tableauDeNews, $HTTP_POST_VARS["id"]) ;

            if (($laNews == -1)
                || ($HTTP_POST_VARS["oldid"] == $HTTP_POST_VARS["id"]))
            {
                $laNews = getObjectWithID($tableauDeNews, $HTTP_POST_VARS["oldid"]) ;

                $tableauDeNews = setNews($tableauDeNews, $HTTP_POST_VARS["oldid"],
                                         $HTTP_POST_VARS["id"],
                                         gpcDelSlashes($HTTP_POST_VARS["titre"]),
                                         gpcDelSlashes($HTTP_POST_VARS["texte"]),
                                         $laNews->heure, $laNews->date,
                                         $HTTP_POST_VARS["categorie"],
                                         $HTTP_POST_VARS["type"]) ;

                if (sauveNews($tableauDeNews, $fichier) != -1)
                {
                    header("Location: index.php?brouillon=" . $brouillon .
                           "&pos=" . $HTTP_GET_VARS["pos"] . "&date=" .
                           $HTTP_GET_VARS["date"]) ;
                }
                else
                {
                    $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_ECRITURE_NEWS")) ;
                }
            }
        }
        else if (isset($HTTP_GET_VARS["supprimer"]))
        {
            $tableauDeNews = deleteObjectWithID($tableauDeNews, $HTTP_GET_VARS["id"]) ;

            if (sauveNews($tableauDeNews, $fichier) != -1)
            {
                header("Location: index.php?brouillon=" . $brouillon .
                       "&pos=" . $HTTP_GET_VARS["pos"] . "&date=" .
                       $HTTP_GET_VARS["date"]) ;
            }
            else
            {
                $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_ECRITURE_NEWS")) ;
            }
        }
        else
        {
            // Affiche la zone de saisie
            $laNews = getObjectWithID($tableauDeNews, $HTTP_GET_VARS["id"]) ;

            $monTheme->addVar(array("TITRE_NEWS" => $laNews->titre,
                                    "TEXTE_NEWS" => $laNews->texte,
                                    "TYPE_NEWS" => $laNews->type,
                                    "CATEGORIE_NEWS" => $laNews->categorie,
                                    "ID_NEWS" => $laNews->id
                                    ));

            $monTheme->addVar("TYPE_NEWS", listeTypesCategorie(1, $laNews->type)) ;
            $monTheme->addVar("CATEGORIE_NEWS", listeTypesCategorie(0, $laNews->categorie)) ;

            $monTheme->parse("news/editnews.htm") ;
        }
    }
    else
    {
        // Si elle n'est pas bonne on affiche un message d'erreur.
        // Il peut s'agir d'une tentative de pire ratage ;-)
        $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_FORMAT_DATE_INVALIDE")) ;
        $monTheme->parse("message.htm") ;
    }
}
else
{
    $monTheme->parse("acces.refuse.htm") ;
}

$monTheme->addVar("PAGE", $monTheme->getParseResult()) ;
$monTheme->clearResult() ;
$monTheme->parse("squelette.htm") ;
$monTheme->printResult() ;
?>
