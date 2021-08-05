<?php
/******************************************************************************
 * XP-Blog - Le blog personnel en XML
 *
 * Copyright 2003 (c) Bubule
 *
 * Fichier d'authentification
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

include("includes/include.php") ;

// si on est déjà connecté, on affiche le centre de contrôle du site
if ($admin)
{
    header("Location: " . $config->url . "admin.php") ;
}

// Test si le login/pass est valide
$login = strtolower($HTTP_POST_VARS["login"]) ;
$pass = $HTTP_POST_VARS["pass"] ;

$logout = $HTTP_GET_VARS["logout"] ;

// On a saissi un mot de passe et un login
if ($login != "" && $pass != "")
{
    // Vérifie le login et mot de passe
    if (
        ($login == strtolower($config->login)) &&
        ($pass == $config->mdp)
       )
    {
            // Génère un numéro de session
            $keySessionId = "" ;
            $tabKeySessionId = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN" ;
            srand((double)microtime()*1000000) ;

            for ($i = 0; $i < $config->longueurSessionId; $i++)
            {
                $keySessionId .= $tabKeySessionId[rand(0, strlen($tabKeySessionId) - 1)] ;
            }

            // Ecrit la session
            $fp = @fopen("data/sessionid.php","w");

            if ($fp)
            {
                fwrite($fp, "<?php\n") ;
                fwrite($fp, "\$numeroDeSession = '$keySessionId' ;\n") ;
                fwrite($fp, "\$heureDeGeneration = " . time() . " ;\n") ;
                fwrite($fp, "?>") ;
                fclose($fp) ;

                setcookie("sessionId", $keySessionId, time() + $config->tempSession,"","",0);

                header("Location: " . $config->url . "admin.php");
            }
            else
            {
                $monTheme->parse("erreur.sessionid.htm") ;
            }
    }
    else
    {
        $monTheme->addVar("MESSAGE_LOGIN", $monTheme->getVarValue("MESSAGE_LOGIN_FAILED"));
        $monTheme->parse("auth.htm") ;
    }
}
else if ($logout == 1)
{
    setcookie("sessionId") ;
    header("Location: " . $config->url . "index.php");
}
else
{
    $monTheme->parse("auth.htm") ;
}

$monTheme->addVar("PAGE", $monTheme->getParseResult()) ;
$monTheme->clearResult() ;
$monTheme->parse("squelette.htm") ;
$monTheme->printResult() ;
?>
