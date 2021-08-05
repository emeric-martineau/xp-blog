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

// récupère les variables
if ($HTTP_GET_VARS["type"] == 1)
{
    $fichier ="types/vars.tpl" ;
}
else
{
    $fichier = "categories/vars.tpl" ;
}

$monTheme->addVarInFile($fichier) ;

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

    if (($tableauDeCategories == -1) && (!isset($HTTP_POST_VARS["ajouter"])))
    {
        if ($HTTP_GET_VARS["type"] == 1)
        {
            $monTheme->addVar("MESSAGE_TYPES", $monTheme->getVarValue("ERREUR_LECTURE_TYPES")) ;
            $monTheme->parse("types/types.htm") ;
        }
        else
        {
            $monTheme->addVar("MESSAGE_CATEGORIES", $monTheme->getVarValue("ERREUR_LECTURE_CATEGORIES")) ;
            $monTheme->parse("categories/categories.htm") ;
        }
    }
    else
    {
        if (isset($HTTP_GET_VARS["supprimer"]) || isset($HTTP_POST_VARS["ajouter"]))
        {
            if (isset($HTTP_POST_VARS["ajouter"]))
            {
                if ($tableauDeCategories == -1)
                {
                    $tableauDeCategories = array() ;
                }

                // ajoute une catégorie
                $uneCategorie = new classCategories() ;

                $uneCategorie->id = getFirtsFreeID($tableauDeCategories) ;
                $uneCategorie->nom = gpcDelSlashes($HTTP_POST_VARS["nom"]) ;
                $uneCategorie->description = gpcDelSlashes($HTTP_POST_VARS["description"]) ;
                $uneCategorie->image = gpcDelSlashes($HTTP_POST_VARS["image"]) ;

                $tableauDeCategories[count($tableauDeCategories)] = $uneCategorie ;

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
            else if (isset($HTTP_GET_VARS["supprimer"]))
            {
                $tableauDeCategories = deleteObjectWithID($tableauDeCategories, $HTTP_GET_VARS["id"]) ;

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
        }
        else
        {
            // Affiche la liste des catégories.
            $nb = count($tableauDeCategories) ;

            for ($i = 0; $i < $nb; $i++)
            {
                $monTheme->addVar(array("ID" => $tableauDeCategories[$i]->id,
                                        "NOM" => htmlentities($tableauDeCategories[$i]->nom),
                                        "DESCRIPTION" => htmlentities($tableauDeCategories[$i]->description),
                                        "IMAGE" => htmlentities($tableauDeCategories[$i]->image)
                                 )) ;

               if ($HTTP_GET_VARS["type"] == 1)
               {
                   $monTheme->addVar("LISTE_TYPES", $monTheme->getVarValue("LISTE_TYPES") . $monTheme->parseLine($monTheme->getVarValue("LIGNE_TABLEAU_TYPES"))) ;
               }
               else
               {
                   $monTheme->addVar("LISTE_CATEGORIES", $monTheme->getVarValue("LISTE_CATEGORIES") . $monTheme->parseLine($monTheme->getVarValue("LIGNE_TABLEAU_CATEGORIES"))) ;
               }
            }

            if ($HTTP_GET_VARS["type"] == 1)
            {
                $images = listeImage($config->repImageType, "") ;

                $monTheme->addVar("IMAGE", $images) ;
                $monTheme->parse("types/types.htm") ;
            }
            else
            {
                $images = listeImage($config->repImageCategorie, "") ;
                $monTheme->addVar("IMAGE", $images) ;
                $monTheme->parse("categories/categories.htm") ;
            }
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
