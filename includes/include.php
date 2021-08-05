<?php
/******************************************************************************
 * XP-Blog - Le blog personnel en XML
 *
 * Copyright 2003 (c) Bubule
 *
 * Fichier de configuration du site aussi.
 *
 * ATTENTION ! Pour plus de sécurité, interdisez l'écriture de ce fichier à
 *             l'interpreteur PHP et au serveur WEB.
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
include("includes/config.php") ;
///////////////////////////////////////////////////////////////////////////////
// Ne rien toucher en dessous de cette ligne!!!
//
$config = new config() ;

//include("themes/$blog_theme/theme.php");
$sessionid = $HTTP_COOKIE_VARS["sessionId"];

// Vérification de la session
@include("data/sessionid.php") ;

if ( // numéro de session identique ?
    ($sessionid == $numeroDeSession) &&
    // session encore valide
    (($heureDeGeneration + $config->tempSession) > time())
   )
{
    $admin = true ;
}
else
{
    $admin = false ;
}

// Supprime les variables au cas ou
unset($numeroDeSession) ;
unset($heureDeGeneration) ;

// Fast template
include_once("includes/fasttemplate.php") ;
$monTheme = new fasttemplate("themes/" . $config->theme) ;
$monTheme->addVarInFile("lang.tpl") ;
$monTheme->addFunction($config->fonctionsTemplate) ;

// Définit les variables par défaut
$monTheme->addVar(array("THEME" => $config->theme,
                        "VERSION" => $config->version,
                        "URL_SITE" => $config->url,
                        "REP_IMAGE_CATEGORIE" => $config->repImageCategorie,
                        "REP_IMAGE_TYPE" => $config->repImageType)) ;

// Gestion XML
include("includes/touv_xml.class.php") ;

include("includes/fonctions.php") ;
?>
