<?php
/******************************************************************************
 * XP-Blog - Le blog personnel en XML
 *
 * Copyright 2003 (c) Bubule
 *
 * Fichier éditant une catégorie
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

// si on est déjà connecté, on affiche le centre de contrôle du site
if ($admin)
{
    if (isset($HTTP_POST_VARS["ajouter"]))
    {
        if ($HTTP_POST_VARS["brouillon"] == 1)
        {
            $fichier = "data/brouillons.xml" ;
        }
        else
        {
            $fichier = "data/news/" . date("Ym") . ".xml" ;
        }

        // Création de l'objet gérant les xml
        $categoriesXML = new touv_xml() ;

        $categoriesXML = $categoriesXML->chargerfichier($fichier) ;

        $tableauDeNews = convTouv_xmlNews($categoriesXML);

        if ($tableauDeNews == -1)
        {
            $tableauDeNews = array() ;
        }

        // ajoute une catégorie
        $uneNews = new classNews() ;

        $uneNews->id = getFirtsFreeID($tableauDeNews) ;
        $uneNews->titre = gpcDelSlashes($HTTP_POST_VARS["titre"]) ;
        $uneNews->texte = gpcDelSlashes($HTTP_POST_VARS["texte"]) ;
        $uneNews->heure =  date("H:i:s") ;
        $uneNews->date =  date("d/m/Y") ;
        $uneNews->categorie = gpcDelSlashes($HTTP_POST_VARS["categorie"]) ;
        $uneNews->type = gpcDelSlashes($HTTP_POST_VARS["type"]) ;

        $tableauDeNews[count($tableauDeNews)] = $uneNews ;

        if (sauveNews($tableauDeNews, $fichier) != -1)
        {
            header("Location: admin.php") ;
        }
        else
        {
            $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_ECRITURE_NEWS")) ;
        }

        $monTheme->parse("message.htm") ;
    }
    else
    {
        $monTheme->addVar("TYPE_NEWS", listeTypesCategorie(1)) ;
        $monTheme->addVar("CATEGORIE_NEWS", listeTypesCategorie(0)) ;

        $monTheme->parse("news/addnew.htm") ;
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
