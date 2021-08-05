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

$monTheme->addVarInFile("gestfichier/vars.tpl") ;

if ($admin)
{
    if ($HTTP_GET_VARS["type"] == 1)
    {
        $rep = $config->repImageType ;
    }
    else
    {
        $rep = $config->repImageCategorie ;
    }

    $monTheme->addVar("TYPE", $HTTP_GET_VARS["type"]) ;

    // Supprime un fichier si besoin est
    if ($HTTP_GET_VARS["action"] == "supp")
    {
        unlink($rep . urldecode($HTTP_GET_VARS["fichier"])) ;
    }

    if ($HTTP_GET_VARS["action"] == "download")
    {
        $fileup = $HTTP_POST_FILES["fichier_image"];

        if ($fileup != "none" and $fileup != "")
        {
            if ($fileup["error"] != 0)
            {
                $monTheme->addVar("MESSAGE", "Erreur lors du téléchargement ! Erreur n°" . $fileup["error"]) ;
            }
            else
            {
                if (copy($fileup["tmp_name"], $rep . $fileup["name"]))
                {
                    if (filesize($rep . $fileup["name"]) != $fileup["size"])
                    {
                        $monTheme->addVar("MESSAGE", "Erreur lors du téléchargement ! Le fichier n'a pu être entièrement copié.") ;
                        @unlink($rep . $fileup["name"]);
                    }

                    @unlink($fileup["tmp_name"]);
                }
            }
        /*
                [name] => essai.gif
        [type] => image/gif
        [tmp_name] => C:\Program Files\EasyPHP\tmp\php1C.tmp
        [error] => 0
        [size] => 174

        */
        }
    }


    $ligne = "" ;

    $fd = @opendir($rep) ;

    if ($fd)
    {
        while ($fichier = @readdir($fd))
        {
            // On ignore les fichier commençant pas un .
            if (!ereg("^\.", $fichier))
            {
                $monTheme->addVar(array("FICHIER" => $fichier,
                                        "FICHIER_URL" => urlencode($fichier),
                                        "TAILLE_FICHIER" => @filesize($rep . $fichier)
                                        )) ;
                //$ligne .= "<tr><td>$fichier</td><td>" . filesize($rep . $fichier) . "</td><td>&nbsp;</td></tr>" ;

                $ligne .= $monTheme->parseLine($monTheme->getVarValue("LIGNE_TABLEAU")) ;
            }
        }
    }

    $monTheme->addVar("LISTE_FICHIER", $ligne) ;
    $monTheme->parse("gestfichier/gestfichier.htm") ;
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
