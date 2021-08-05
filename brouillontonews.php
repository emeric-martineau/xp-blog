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

// Récupère le brouillon
// Création de l'objet gérant les xml
$categoriesXML = new touv_xml() ;

$tableauDeBrouillon = $categoriesXML->chargerfichier("data/brouillons.xml") ;
$tableauDeBrouillon = convTouv_xmlNews($tableauDeBrouillon);

// Il nous faut conserver la date et heure du brouillon
$laNews = getObjectWithID($tableauDeBrouillon , $HTTP_GET_VARS["id"]) ;

if ($laNews != -1)
{
        $fichier = "data/news/" . date("Ym") . ".xml" ;

        $tableauDeNews = $categoriesXML->chargerfichier($fichier) ;

        $tableauDeNews = convTouv_xmlNews($tableauDeNews);

        if ($tableauDeNews == -1)
        {
            $tableauDeNews = array() ;
        }

        // ajoute une catégorie
        $uneNews = new classNews() ;

        $uneNews->id = getFirtsFreeID($tableauDeNews) ;
        $uneNews->titre = $laNews->titre ;
        $uneNews->texte = $laNews->texte ;
        $uneNews->heure =  date("H:i:s") ;
        $uneNews->date =  date("d/m/Y") ;
        $uneNews->categorie = $laNews->categorie ;
        $uneNews->type = $laNews->type ;

        $tableauDeNews[count($tableauDeNews)] = $uneNews ;

        if (sauveNews($tableauDeNews, $fichier) != -1)
        {
            $tableauDeBrouillon  = deleteObjectWithID($tableauDeBrouillon , $HTTP_GET_VARS["id"]) ;

            if (sauveNews($tableauDeBrouillon , "data/brouillons.xml") != -1)
            {
                header("Location: index.php?brouillon=1&pos=" . $HTTP_GET_VARS["pos"] . "&date=" .
                       $HTTP_GET_VARS["date"]) ;
            }
            else
            {
                $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_ECRITURE_NEWS")) ;
            }

        }
        else
        {
            $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_ECRITURE_NEWS")) ;
        }
}
else
{
    $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_LECTURE_BROUILLON")) ;
    $monTheme->parse("message.htm") ;
}
$monTheme->addVar("PAGE", $monTheme->getParseResult()) ;
$monTheme->clearResult() ;
$monTheme->parse("squelette.htm") ;
$monTheme->printResult() ;
?>
