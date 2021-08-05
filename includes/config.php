<?php
/******************************************************************************
 * XP-Blog - Le blog personnel en XML
 *
 * Copyright 2003 (c) Bubule
 *
 * Fichier de configuration du site.
 *
 * ATTENTION ! Pour plus de s�curit�, interdisez l'�criture de ce fichier �
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

class config
{
    // Th�me. Nom du r�pertoire.
    var $theme = "phpbb" ;

    // Longueur de la clef de session. Plus le nombre est �lev�, plus il sera
    // difficile de la trouver mais plus elle sera longue � g�n�rer.
    var $longueurSessionId = 30 ;

    // Nombre de seconde pendant laquelle la session est valide apr�s l'identi-
    // fication.
    // ATTENTION ! N'ex�dez pas une heure pour garantir plus de s�curit� au site !
    var $tempSession = 40000 ;

    // url du site http://www.monsitequidechirsarace.com/
    // NE PAS OUBLIER LE / A LA FIN
    var $url = "http://localhost/" ;

    // Le login (�viter de prendre votre pseudo ou admin, root ...). Prenez
    // quelque chose d'inhabituel
    // Le login ne fait pas de diff�rence entre majuscule et minuscule.
    var $login = "admin" ;

    // Mot de passe. Diff�rence majuscule/minuscule.
    // Prenez un mot de passe de plus de 8 caract�res et compos� de chiffres, de
    // lettres et d'autre caract�res. Le tout sans signification.
    var $mdp = "pass" ;

    // Nombre de news par page � afficher
    var $nbNewsParPage = 4 ;

    // Fonction dans les templates
    var $fonctionsTemplate = array("date" => "\$STDOUT = date('Y');") ;

    // Configure le nom des jours. On commence par dimanche
    var $listeDesJours = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi",
                               "Vendredi", "Samedi") ;

    // Configure le nom des jours. On commence par Janvier
    var $listeDesMois = array("Janvier", "F�vrier", "Mars", "Avril", "Mai",
                              "Juin", "Juillet", "Ao�t", "Septembre", "Octobre",
                              "Novembre", "Descembre") ;

    // Sp�cifie le format de la date quand elle est affich�e dans la news
    // %J : le nom du jour, %j : le num�ro du jour, %M : mois en chiffre, %m :
    // mois en lettre, %Y : ann�e en chiffre, %% : %
    var $formatDateNews = "%J %j %m %Y" ;

    // Sp�cifie le format de la date pour la liste des archives
    // %M : mois en chiffre, %m : mois en lettre, %Y : ann�e en chiffre, %% : %
    var $formatDateListeArchives = "%Y, %m" ;

    // R�pertoires contenant les images pour les types
    var $repImageType = "images/types/" ;

    // R�pertoires contenant les images pour les types
    var $repImageCategorie = "images/categories/" ;

    // Indique le version de XP-Blog. NE PAS MODIFIER
    var $version = "XP-Blog version 1.0" ;
}
?>
