<?php
/******************************************************************************
 * XP-Blog - Le blog personnel en XML
 *
 * Copyright 2003 (c) Bubule
 *
 * Fichier contenant les fonctions
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

class classNews
{
    var $id = 0 ;
    var $titre = "" ;
    var $texte = "" ;
    var $heure = "" ;
    var $date = "" ;
//    var $brouillon = 0 ;
    var $categorie = 0 ;
    var $type = 0 ;
}

class classCategories
{
    var $id = 0 ;
    var $nom = "" ;
    var $description = "" ;
    var $image = "" ;
}

////////////////////////////////////////////////////////////////////////////////
// Enlève d'anti-slashes selon "Magic Quotes GPC"
function gpcDelSlashes($chaine)
{
    return(get_magic_quotes_gpc() == 1 ? stripslashes($chaine) : $chaine) ;
}

///////////////////////////////////////////////////////////////////////////////
// Retourne le premier id de libre
//
// Paramètres : le tableau contenant les objets avec un attribut id
//
// Retour     : id
function getFirtsFreeID($tableau)
{
    $tab = array() ;

    $nb = count($tableau) ;

    // Récupère tous les id et les met dans un tableau
    for($i = 0; $i < $nb; $i++)
    {
        $tab[$i] = $tableau[$i]->id ;
    }

    // Tri les ids dans l'ordre
    sort($tab) ;

    // Parcours le tableau. Quand $i différent de l'id, c'est que l'id de libre
    // est $i
    for($i = 0; $i < $nb; $i++)
    {
        if ($tab[$i] != $i)
        {
            $freeID = $i ;
            break ;
        }
    }

    // Si on a parcourut le tab et qu'il n'y a rien c'est qu'il faut rajouter le
    // 1 au dernier.
    if (!isset($freeID))
    {
        $freeID = $nb + 1 ;
    }

    return $freeID ;
}

///////////////////////////////////////////////////////////////////////////////
// Converti un tableau venant de la classe touv_xml en tableau d'objet
// classNews.
function convTouv_xmlNews($newsXML)
{
    if ($newsXML["tag"] == "ROOT")
    {
        // Se positionne sur le tableau contenant la liste des balises NEWS
        $news = $newsXML["children"] ;

        // Nombre de news
        $nbNews = count($news) ;

        for($i = 0; $i < $nbNews; $i++)
        {
            // créer un nouvel objet
            $uneNews = new classNews() ;

            // Récupère les sous éléments
            $tabSousElement = $news[$i]["children"] ;

            if ($news[$i]["tag"] == "NEWS")
            {
                // Nombre de sous éléments
                $nbSousElement = count($tabSousElement) ;

                // Remonte
                $tabSousElement = $news[$i]["children"] ;

                for($j = 0; $j < $nbSousElement; $j++)
                {
                    switch ($tabSousElement[$j]["tag"])
                    {
                        case "ID" : $uneNews->id = $tabSousElement[$j]["value"] ;
                                    break ;
                        case "TITRE" : $uneNews->titre = $tabSousElement[$j]["value"] ;
                                       break ;
                        case "TEXTE" : $uneNews->texte = $tabSousElement[$j]["value"] ;
                                       break ;
                        case "HEURE" : $uneNews->heure = $tabSousElement[$j]["value"] ;
                                       break ;
                        case "DATE" : $uneNews->date = $tabSousElement[$j]["value"] ;
                                      break ;
                        case "CATEGORIE" : $uneNews->categorie = $tabSousElement[$j]["value"] ;
                                           break ;
                        case "TYPE" : $uneNews->type = $tabSousElement[$j]["value"] ;
                                      break ;
                    }
                }

                $tableauDeNews[count($tableauDeNews)] = $uneNews ;
            }
        }

    }
    else
    {
        $tableauDeNews = -1 ;
    }

    return $tableauDeNews ;
}

///////////////////////////////////////////////////////////////////////////////
// Converti un tableau venant de la classe touv_xml en tableau d'objet
// classCategories.
function convTouv_xmlCategories($categoriesXML)
{
    return convTouv_xml($categoriesXML, "CATEGORIES") ;
}

///////////////////////////////////////////////////////////////////////////////
// Converti un tableau venant de la classe touv_xml en tableau d'objet
// classCategories.
function convTouv_xmlTypes($categoriesXML)
{
    return convTouv_xml($categoriesXML, "TYPES") ;
}

///////////////////////////////////////////////////////////////////////////////
// Converti un tableau venant de la classe touv_xml en tableau d'objet
// classCategories.
//
// Paramètre tableau des catégories ou types, la balise "CATEGORIES" ou "TYPES"
function convTouv_xml($categoriesXML, $tag)
{
    if ($categoriesXML["tag"] == "ROOT")
    {
        // Se positionne sur le tableau contenant la liste des balises NEWS
        $categories = $categoriesXML["children"] ;

        // Nombre de categories
        $nbCategories = count($categories) ;

        for($i = 0; $i < $nbCategories; $i++)
        {
            // créer un nouvel objet
            $uneCategories = new classCategories() ;

            // Récupère les sous éléments
            $tabSousElement = $categories[$i]["children"] ;

            if ($categories[$i]["tag"] == $tag)
            {
                // Nombre de sous éléments
                $nbSousElement = count($tabSousElement) ;

                // Remonte
                $tabSousElement = $categories[$i]["children"] ;

                for($j = 0; $j < $nbSousElement; $j++)
                {
                    switch ($tabSousElement[$j]["tag"])
                    {
                        case "ID" : $uneCategories->id = $tabSousElement[$j]["value"] ;
                                    break ;
                        case "NOM" : $uneCategories->nom = $tabSousElement[$j]["value"] ;
                                       break ;
                        case "DESCRIPTION" : $uneCategories->description = $tabSousElement[$j]["value"] ;
                                       break ;
                        case "IMAGE" : $uneCategories->image = $tabSousElement[$j]["value"] ;
                                       break ;
                    }
                }

                $tableauDeCategories[count($tableauDeCategories)] = $uneCategories ;
            }
        }

    }
    else
    {
        $tableauDeCategories = -1 ;
    }

    return $tableauDeCategories ;
}

///////////////////////////////////////////////////////////////////////////////
// Enregistre les catégories
//
// Paramètre : tableau
function sauveCategorie($tableauDeCategories)
{
    sauveCategorieType($tableauDeCategories, "data/categories.xml", "categories") ;
}

///////////////////////////////////////////////////////////////////////////////
// Enregistre les catégories
//
// Paramètre : tableau
function sauveType($tableauDeCategories)
{
    sauveCategorieType($tableauDeCategories, "data/types.xml", "types") ;
}

///////////////////////////////////////////////////////////////////////////////
// Enregistre les catégories
//
// Paramètre : tableau
function sauveCategorieType($tableauDeCategories, $fichier, $tag)
{
    $fp = fopen($fichier, "w") ;

    if ($fp)
    {
        // Imprime l'entete
        fwrite($fp, "<?xml version=\"1.0\"?>\n  <root>\n") ;

        $nb = count($tableauDeCategories) ;

        for($i = 0; $i < $nb; $i++)
        {
            fwrite($fp, "    <$tag>\n") ;
            fwrite($fp, "      <id>" . htmlspecialchars($tableauDeCategories[$i]->id) . "</id>\n") ;
            fwrite($fp, "      <nom>" . htmlspecialchars($tableauDeCategories[$i]->nom) . "</nom>\n") ;
            fwrite($fp, "      <description>" . htmlspecialchars($tableauDeCategories[$i]->description) . "</description>\n") ;
            fwrite($fp, "      <image>" . htmlspecialchars($tableauDeCategories[$i]->image) . "</image>\n") ;
            fwrite($fp, "    </$tag>\n") ;
        }

        fwrite($fp, "  </root>") ;

        fclose($fp) ;
    }
    else
    {
        return -1;
    }
}

///////////////////////////////////////////////////////////////////////////////
// Fonction supprimant un objet dans un tableau suivant l'id
//
// Paramètre : le tableau, l'id
function deleteObjectWithID($tab, $id)
{
    $nb = count($tab) ;

    for ($i = 0; $i < $nb; $i++)
    {
        if ($tab[$i]->id == $id)
        {
            unset($tab[$i]) ;
        }
    }

    return $tab ;
}

///////////////////////////////////////////////////////////////////////////////
// Retourne l'objet suivant l'id
//
// Paramètres : le tableau contenant les objet, l'id permettant de l'identifier,
//
// Retour     : l'objet correspondant
function getObjectWithID($tableau, $id)
{
    $nb = count($tableau) ;

    // Parcours le tableau
    for($i = 0; $i < $nb; $i++)
    {
        if ($tableau[$i]->id == $id)
        {
            return $tableau[$i] ;
        }
    }

    return -1 ;
}

///////////////////////////////////////////////////////////////////////////////
// Met à jour une catégorie dans le tableau
//
// Paramètres : le tableau contenant les cat, l'id permettant de l'identifier,
//              le nom, la description, l'image.
//
// Retour     : le tableau donné, modifié.
function setCategorie($tableauDeCategories, $id, $idNew, $nom, $description, $image)
{
    $nbCategories = count($tableauDeCategories) ;

    // Parcours le tableau
    for($i = 0; $i < $nbCategories; $i++)
    {
        if ($tableauDeCategories[$i]->id == $id)
        {
            $tableauDeCategories[$i]->id = $idNew ;
            $tableauDeCategories[$i]->nom = $nom ;
            $tableauDeCategories[$i]->description = $description ;
            $tableauDeCategories[$i]->image = $image ;
            break ;
        }
    }

    return $tableauDeCategories ;
}

///////////////////////////////////////////////////////////////////////////////
// Met à jour une news dans le tableau
//
// Paramètres : le tableau contenant les news, l'id permettant de l'identifier,
//              le titre, le texte, l'heure, la date, si c'est un brouillon,
//              la catégorie.
//
// Retour     : le tableau donné, modifié.
function setNews($tableauDeNews, $id, $idNew, $titre, $texte, $heure, $date, $categorie, $type)
{
    $nbNews = count($tableauDeNews) ;

    // Parcours le tableau
    for($i = 0; $i < $nbNews; $i++)
    {
        if ($tableauDeNews[$i]->id == $id)
        {
            $tableauDeNews[$i]->id = $idNew ;
            $tableauDeNews[$i]->titre = $titre ;
            $tableauDeNews[$i]->texte = $texte ;
            $tableauDeNews[$i]->heure = $heure ;
            $tableauDeNews[$i]->date = $date ;
//            $tableauDeNews[$i]->brouillon = $brouillon ;
            $tableauDeNews[$i]->categorie = $categorie ;
            $tableauDeNews[$i]->type = $type ;
            break ;
        }
    }

    return $tableauDeNews ;
}

///////////////////////////////////////////////////////////////////////////////
// Enregistre les catégories
//
// Paramètre : tableau
function sauveNews($tableauDeNews, $fichier)
{
    $fp = fopen($fichier, "w") ;

    if ($fp)
    {
        // Imprime l'entete
        fwrite($fp, "<?xml version=\"1.0\"?>\n  <root>\n") ;

        $nb = count($tableauDeNews) ;

        for($i = 0; $i < $nb; $i++)
        {
            fwrite($fp, "    <news>\n") ;
            fwrite($fp, "      <id>" . htmlspecialchars($tableauDeNews[$i]->id) . "</id>\n") ;
            fwrite($fp, "      <titre>" . htmlspecialchars($tableauDeNews[$i]->titre) . "</titre>\n") ;
            fwrite($fp, "      <texte>" . htmlspecialchars($tableauDeNews[$i]->texte) . "</texte>\n") ;
            fwrite($fp, "      <heure>" . htmlspecialchars($tableauDeNews[$i]->heure) . "</heure>\n") ;
            fwrite($fp, "      <date>" . htmlspecialchars($tableauDeNews[$i]->date) . "</date>\n") ;
            fwrite($fp, "      <categorie>" . htmlspecialchars($tableauDeNews[$i]->categorie) . "</categorie>\n") ;
            fwrite($fp, "      <type>" . htmlspecialchars($tableauDeNews[$i]->type) . "</type>\n") ;
            fwrite($fp, "    </news>\n") ;
        }

        fwrite($fp, "  </root>") ;

        fclose($fp) ;
    }
    else
    {
        return -1;
    }
}

///////////////////////////////////////////////////////////////////////////////
// converti un date passée en paramètre
function convDate($format, $jour, $mois, $annee, $heure, $minute, $seconde)
{
    global $config ;
    $chaine = "" ;

    $long = strlen($format) ;

    for ($i = 0; $i < $long; $i++)
    {
        if ($format[$i] == "%")
        {
            $i++ ;

            switch ($format[$i])
            {
                case "%" : $caractere = "%" ;
                           break ;
                case "J" : $caractere = $config->listeDesJours[date("w", mktime($heure, $minute, $seconde, $mois, $jour, $annee))] ;
                           break ;
                case "j" : $caractere = $jour ;
                           break ;
                case "M" : $caractere = date("m", mktime($heure, $minute, $seconde, $mois, $jour, $annee)) ;
                           break ;
                case "m" : $caractere = $config->listeDesMois[$mois - 1] ;
                           break ;
                case "Y" : $caractere = date("Y", mktime($heure, $minute, $seconde, $mois, $jour, $annee)) ;
                           break ;
            }

            $chaine .= $caractere ;
        }
        else
        {
            $chaine .= $format[$i] ;
        }
    }

    return $chaine ;
}

///////////////////////////////////////////////////////////////////////////////
// Fait le liste des images en utilisant un formulaire HTML.
// Répertoire de lecture, fichier à sélectionner
function listeImage($rep, $fichierSelectionne)
{
    $liste = "" ;

    $fd = opendir($rep) ;

    if ($fd)
    {
        while ($fichier = @readdir($fd))
        {
            // On ignore les fichier commençant pas un .
            if (!ereg("^\.", $fichier))
            {
                    $fichier = htmlentities($fichier) ;

                    $liste .= "<option value='$fichier'" ;

                    if ($fichier == $fichierSelectionne)
                    {
                        $liste .= " selected" ;
                    }


                    $liste .= ">$fichier</option>" ;
            }
        }
    }

    return $liste ;
}

function listeTypesCategorie($type, $idSelectionne = "")
{
    $resultat = "" ;

    // Liste des catégories
    $categoriesXML = new touv_xml() ;

    if ($type == 1)
    {
        $categoriesXML = $categoriesXML->chargerfichier("data/types.xml") ;
        $tableauDeCategories = convTouv_xmlTypes($categoriesXML);
    }
    else
    {
        $categoriesXML = $categoriesXML->chargerfichier("data/categories.xml") ;
        $tableauDeCategories = convTouv_xmlCategories($categoriesXML);
    }

    $nbCategories = count($tableauDeCategories) ;

    for ($i = 0; $i < $nbCategories; $i++)
    {
        $resultat .= "<option value='" . $tableauDeCategories[$i]->id . "'";

        if ($tableauDeCategories[$i]->id == $idSelectionne)
        {
            $resultat .= " selected" ;
        }

        $resultat .= ">" . htmlentities($tableauDeCategories[$i]->nom) . "</option>" ;
    }

    return $resultat ;
}
?>
