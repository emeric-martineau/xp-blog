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

// récupère les variables
if ($HTTP_GET_VARS["type"] == 1)
{
    $fichier ="types/vars.tpl" ;
}
else
{
    $fichier = "categories/vars.tpl" ;
}


// si on est déjà connecté, on affiche le centre de contrôle du site
if ($admin)
{
    // Création de l'objet gérant les xml
    $categoriesXML = new touv_xml() ;

    // Lecture du fichier
    if ($HTTP_GET_VARS["type"] == 1)
    {
        $fichier = "data/types.xml" ;
    }
    else
    {
        $fichier = "data/categories.xml" ;
    }

    $categoriesXML = $categoriesXML->chargerfichier($fichier) ;

    if ($HTTP_GET_VARS["type"] == 1)
    {
        $tableauDeCategories = convTouv_xmlTypes($categoriesXML);
    }
    else
    {
        $tableauDeCategories = convTouv_xmlCategories($categoriesXML);
    }

    if (isset($HTTP_POST_VARS["modifier"]))
    {

        if ((getObjectWithID($tableauDeCategories, $HTTP_POST_VARS["id"]) == -1)
            || ($HTTP_POST_VARS["oldid"] == $HTTP_POST_VARS["id"]))
        {
            $tableauDeCategories = setCategorie($tableauDeCategories,
                                                $HTTP_POST_VARS["oldid"],
                                                $HTTP_POST_VARS["id"],
                                                gpcDelSlashes($HTTP_POST_VARS["nom"]),
                                                gpcDelSlashes($HTTP_POST_VARS["description"]),
                                                gpcDelSlashes($HTTP_POST_VARS["image"]));

            if ($HTTP_GET_VARS["type"] == 1)
            {
                if (sauveType($tableauDeCategories) != -1)
                {
                    header("Location: categories.php?type=1") ;
                }
                else
                {
                    $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_ECRITURE_TYPES")) ;
                }
            }
            else
            {
                if (sauveCategorie($tableauDeCategories) != -1)
                {
                    header("Location: categories.php") ;
                }
                else
                {
                    $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ERREUR_ECRITURE_CATEGORIES")) ;
                }
            }

            $monTheme->parse("message.htm") ;
        }
        else
        {
            $monTheme->addVar("MESSAGE", $monTheme->getVarValue("ID_DEJA_UTILISE")) ;

            $monTheme->parse("message.htm") ;
        }
    }
    else
    {
        $laCategorie = getObjectWithID($tableauDeCategories, $HTTP_GET_VARS["id"]) ;

        $monTheme->addVar(array("ID" => $laCategorie->id,
                                "NOM" => htmlentities($laCategorie->nom),
                                "DESCRIPTION" => htmlentities($laCategorie->description),
                                ));
        if ($HTTP_GET_VARS["type"] == 1)
        {
            $images = listeImage($config->repImageType, htmlentities($laCategorie->image)) ;
            $monTheme->addVar("IMAGE", $images) ;
            $monTheme->parse("types/edittype.htm") ;
        }
        else
        {
            $images = listeImage($config->repImageCategorie, htmlentities($laCategorie->image)) ;
            $monTheme->addVar("IMAGE", $images) ;
            $monTheme->parse("categories/editcategorie.htm") ;
        }
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
