<?php
/*************************************************

TOUV - touv_xml.class.php

Copyright (c): 1999 2000 2001, all rights reserved

Mosdifié par Bubule pour que ce soit compatible avec PHP3.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

You may contact the author of TOUV by e-mail at: touv@tuxfamily.org

*************************************************/

/*
** Version 0.6.1
** transforme du XML en tableau PHP
** Liste des fonctions presentes
**  - charger ( STR )
**  - decharger ( TAB )
**  - chargerfichier ( STR )
**  - dechargerfichier ( TAB STR )
**  - ouvrir ( &TAB STR )
**  - ouvrir_branche ( &TAB STR )
**  - trier ( &TAB  )
**  - rtrier ( &TAB  )
**  - parcourir ( &TAB STR [STR] [STR] )
**  - afficher ( &TAB STR [STR] [STR] )
**  - trouver ( TAB STR )
**  - recuperer ( TAB STR )
**  - repertoire ( STR )
** Exemple
**     voir en fin de fichier
*/

/*
** Fonctions de tri
*/
function usorttree1($a,$b) {
    if($a["attributes"]["ORDRE"] < $b["attributes"]["ORDRE"]) return  - 1;
    if($a["attributes"]["ORDRE"] > $b["attributes"]["ORDRE"]) return 1;
    return 0;
}

function usorttree2($a,$b) {
    if($a["attributes"]["ORDRE"] < $b["attributes"]["ORDRE"]) return 1;
    if($a["attributes"]["ORDRE"] > $b["attributes"]["ORDRE"]) return  - 1;
    return 0;
}

function afficher_branche($unNoeud,$chemin) {
/* Affichage par defaut d'un noeud de l'arbre
**
** code retour
**     RIEN
*/
    preg_match_all("|/|",$chemin,$m);
    $leniveau = count($m[0]);
    echo '<DIV style="margin-left: ' . $leniveau . 'em; ">';
    echo($unNoeud["tag"] == "BRANCHE"  ?($unNoeud["attributes"]["OPEN"] == "yes"  ? "-" :
    "+") :
    "#");
    echo "&nbsp;" . $unNoeud["attributes"]["NAME"] . "&nbsp;";
    echo($unNoeud["tag"] == "BRANCHE"  ? "($chemin)" :
    "(" . $unNoeud["value"] . ")");
    echo "</DIV>\n";
}

class touv_xml {
    function _get_children($vals,&$i) {
        $children = array();
        if($vals[$i]['value'])
        {
            //array_push($children,$vals[$i]['value']);
            $children[count($children)] = $vals[$i]['value'] ;
        }

        while(++ $i < count($vals)) { // so pra nao botar while true ;-)
            switch($vals[$i]['type']) {
            case 'cdata' :
                //array_push($children,$vals[$i]['value']);
                $children[count($children)] = $vals[$i]['value'] ;
                break;
            case 'complete' :
                //array_push($children,array('tag' => $vals[$i]['tag'],'attributes' => $vals[$i]['attributes'],'value' => $vals[$i]['value']));
                $children[count($children)] = array('tag' => $vals[$i]['tag'],'attributes' => $vals[$i]['attributes'],'value' => $vals[$i]['value']) ;
                break;
            case 'open' :
                //array_push($children,array('tag' => $vals[$i]['tag'],'attributes' => $vals[$i]['attributes'],'children' => $this->_get_children($vals,$i)));
                $children[count($children)] = array('tag' => $vals[$i]['tag'],'attributes' => $vals[$i]['attributes'],'children' => $this->_get_children($vals,$i)) ;
                break;
            case 'close' :
                return $children;
            }

        }

    }

    function charger($data) {
/* On transforme une chaine xml en tableau (arbre) php
**
** code retour
**     Un tableau
*/
        $p = xml_parser_create("ISO-8859-1");
        xml_parser_set_option($p,XML_OPTION_SKIP_WHITE,1);
	//xml_parser_set_option($p,XML_OPTION_TARGET_ENCODING,"ISO-8859-1");
        xml_parse_into_struct($p,$data,&$vals,&$index);
        xml_parser_free($p);
        $tabxml = array();
        $i = 0;
        //array_push($tabxml,array('tag' => $vals[$i]['tag'],'attributes' => $vals[$i]['attributes'],'children' => $this->_get_children($vals,$i)));
        $tabxml[count($tabxml)] = array('tag' => $vals[$i]['tag'],'attributes' => $vals[$i]['attributes'],'children' => $this->_get_children($vals,$i)) ;
        return $tabxml[0];
    }

    function chargerfichier($filename) {
/* On transforme un fichier xml en tableau (arbre) php
**
** code retour
**     Un tableau
*/
        if(!@is_file($filename)) return;
        $data = implode('',file($filename));
        return $this->charger($data);
    }

    function decharger($tabxml) {
/* On transforme un tableau (arbre) php enune chaine xml
**
** code retour
**     Une Chaine
*/
        $buffer = "";
        if(isset($tabxml["tag"])) {
            $buffer .= "<" . strtolower($tabxml["tag"]);
            if(is_array($tabxml["attributes"])) {
                foreach($tabxml["attributes"] as $k => $v) {
                    $buffer .= " " . strtolower($k) . "=\"$v\"";
                }

            }

            $buffer .= ">";
            if(is_array($tabxml["children"])) {
                foreach($tabxml["children"] as $k => $v) {
                    $buffer .= $this->decharger($v);
                }

            }

            else $buffer .= $tabxml["value"];
            $buffer .= "</" . strtolower($tabxml["tag"]) . ">\n";
        }

        else die("[CPR_TREE] Source invalide : " . $tabxml["tag"] . "\n");
        return $buffer;
    }

    function dechargerfichier($tabxml,$filename) {
/* On transforme un tableau (arbre) php en un fichier xml
**
** code retour
**     TRUE en cas de succes et FALSE en cas d'erreur
*/
        $data = "<?xml version=\"1.0\"?>\n<!DOCTYPE " . strtolower($tabxml["tag"]) . " >\n" . $this->decharger($tabxml);
        $fp = fopen($filename,"w");
        if(!$fp) return(false);
        set_file_buffer($fp,0);
        fputs($fp,$data);
        if(!fclose($fp)) return(false);
        return true;
    }



    function ouvrir($tabxml,$chemin) {
/* On positionne un attribut open=yes dans tout les fils d'une branche
** désigné© par son chemin (exemple: /0/2/6 ou /0/3
**
** code retour
**     RIEN : le tableau en paramé¨tre est passé par reference
*/
        $truc = "";
        $tabtmp = explode("/",substr($chemin,1));
        if($chemin == "/") $tabxml["attributes"]["OPEN"] = "yes";
        else {
            foreach($tabtmp as $k2 => $v2) if(is_int((int) $v2)) $truc .= "[\"children\"][$v2]";
            if(!empty($truc)) {
                eval("\$tabxml" . $truc . "[\"attributes\"][\"OPEN\"]" . "= \"yes\";");
            }

        }

    }

    function ouvrir_branche($tabxml,$chemin) {
/* On positionne un attribut open=yes dans toute une branche
** désigné© par son chemin (exemple: /0/2/6 ouvrira /0 /0/2 /0/2/6)
**
** code retour
**     RIEN : le tableau en paramé¨tre est passé par reference
*/
        $truc = "/";
        $tabtmp = explode("/",substr($chemin,1));
        foreach($tabtmp as $k2 => $v2) {
            if(is_int((int) $v2)) {
                $truc .= "$v2";
                $this->ouvrir(&$tabxml,$truc);
                $truc .= "/";
            }

        }

    }

    function selectionner($tabxml,$chemin) {
/* On positionne un attribut select=yes dans une branche
** désigné© par son chemin (exemple: /0/2/6 ou /0/3
**
** code retour
**     RIEN : le tableau en paramé¨tre est passé par reference
*/
        $truc = "";
        $tabtmp = explode("/",substr($chemin,1));
        if($chemin == "/") $tabxml["attributes"]["OPEN"] = "yes";
        else {
            foreach($tabtmp as $k2 => $v2) if(is_int((int) $v2)) $truc .= "[\"children\"][$v2]";
            if(!empty($truc)) {
                eval("\$tabxml" . $truc . "[\"attributes\"][\"SELECT\"]" . "= \"yes\";");
            }

        }

    }

    function trier($tabxml) {
/* On trie l'arbre suivant l'attribut ordre=X
**
** code retour
**     RIEN : le tableau en paramé¨tre est passé par reference
*/
        if(is_array($tabxml["children"])) {
            usort($tabxml["children"],usorttree1);
            foreach($tabxml["children"] as $k => $v) $this->trier(&$tabxml["children"][$k]);
        }

    }

    function rtrier($tabxml) {
/* On trie ( INVERSE )l'arbre suivant l'attribut ordre=X
**
** code retour
**     RIEN : le tableau en paramé¨tre est passé par reference
*/
        if(is_array($tabxml["children"])) {
            usort($tabxml["children"],usorttree2);
            foreach($tabxml["children"] as $k => $v) $this->trier(&$tabxml["children"][$k]);
        }

    }

    function trouver($tabxml,$chemin) {
/* Renvoit un noeud correspondant é  un chemin
** désigné© par son chemin (exemple: /0/2/6 ou /0/3)
**
** code retour
**     RIEN : le tableau
*/
        $truc = "";
        if(empty($chemin)  || trim($chemin) == "/") return $tabxml;
        $tabtmp = explode("/",substr($chemin,1));
        foreach($tabtmp as $k2 => $v2) if(is_int((int) $v2)) $truc .= "[\"children\"][$v2]";
        if(!empty($truc)) {
            eval("\$tmp =\$tabxml$truc;");
            return $tmp;
        }

    }

    function get_index_for_tag($tabxml,$tag) {
        if(is_array($tabxml["children"])) foreach($tabxml["children"] as $k => $v) if($v["tag"] == strtoupper($tag)) return $k;
    }

    function pathtoindex($tabxml,$chemin) {
/* On transforme un chemin xpath en chemin index
** /TRUC/SERVICE/NAME donne /0/2/6
**
** code retour
**     RIEN : le tableau en parametre est passé par reference
*/
        $savetab = $tabxml;
        $saveche = "";
        $tabtmp = explode("/",substr($chemin,1));
        foreach($tabtmp as $k => $v) {
            $i = $this->get_index_for_tag($savetab,$v);
            if(is_int($i)) {

                //echo "<h3>k=$k i=$i v=$v</h3>";
                $saveche .= "/$i";
                $savetab = $this->trouver($savetab,$saveche);
            }

        }

        return($saveche);
    }

    function recuperer($tabxml,$chemin) {
/* Renvoit la valeur d'un chemin
** exemple: valeur de /TRUC/DATA/VALUE
**
** code retour
**     RIEN : mixe
*/
        $index = $this->pathtoindex($tabxml,$chemin);
        $noeud = $this->trouver($tabxml,$index);
        return($noeud["value"]);
    }

    function parcourir($tabxml,$fonction = "afficher_branche",$leChemin = "",$leniveau = 0) {
/* On parcours un arbre et on appel $fonction pour chaque noeud rencontre
** Le parcourt de l'arbre est conditionn  par la presence de l'attribut open="yes"
**
** code retour
**     RIEN
*/
        $leniveau++;
        if(isset($tabxml["tag"])) {
            $fonction($tabxml,$leChemin);
            if($tabxml["attributes"]["OPEN"] == "yes") {
                if(is_array($tabxml["children"])) {
                    foreach($tabxml["children"] as $k => $v) {
                        $this->parcourir($v,$fonction,$leChemin . "/" . $k,$leniveau);
                    }

                }

            }

        }

    }

    function afficher($tabxml,$fonction = "afficher_branche",$leChemin = "",$leniveau = 0) {
/* On parcours un arbre et on appel $fonction pour chaque noeud rencontré
**
** code retour
**     RIEN
*/
        $leniveau++;
        if(isset($tabxml["tag"])) {
            $fonction($tabxml,$leChemin);
            if(is_array($tabxml["children"])) {
                foreach($tabxml["children"] as $k => $v) {
                    $this->afficher($v,$fonction,$leChemin . "/" . $k,$leniveau);
                }

            }

        }

    }


function repertoire($lerepertoire, $limite = 0, $arbre = "") {
/* On parcours un repertoire pour le transformer en arbre on peut limiter la descente
** avec $limite
**
** code retour
**     un tableau
*/
static $profondeur = 0;

    if ($limite != 0 && $profondeur >= $limite) return;

    if (empty($arbre)) {
        $arbre = array();
        $arbre["tag"] = "arborescence";
        $racine = true;
    }
    else $racine = false;

    if (!preg_match("|/$|", $lerepertoire)) $lerepertoire.= "/";

    $indexA = sizeof($arbre["children"]);

    $arbre["children"][$indexA]["tag"] = "folder";
    $arbre["children"][$indexA]["value"] = $lerepertoire;
    $arbre["children"][$indexA]["attributes"]["NAME"] = basename($lerepertoire);

    // taille du repertoire
    $file_size = filesize($lerepertoire);
    if($file_size >= 1073741824) $arbre["children"][$indexA]["attributes"]["SIZE"] = round($file_size / 1073741824 * 100) / 100 . "g";
    elseif($file_size >= 1048576) $arbre["children"][$indexA]["attributes"]["SIZE"] = round($file_size / 1048576 * 100) / 100 . "m";
    elseif($file_size >= 1024) $arbre["children"][$indexA]["attributes"]["SIZE"] = round($file_size / 1024 * 100) / 100 . "k";
    else $arbre["children"][$indexA]["attributes"]["SIZE"] =  $file_size . "b";

    // date de modification
    $arbre["children"][$indexA]["attributes"]["MODIFIED"] = date("M d H:i", filectime($lerepertoire));

    // permission
    $arbre["children"][$indexA]["attributes"]["PERMISSION"] = sprintf("%o", (fileperms($lerepertoire)) & 0777);

    $listefichiers = array();
    $listerepertoires = array();

    $mydir = @opendir($lerepertoire);

    if (!$mydir) return;

    while($entry = @readdir($mydir)){
        if ($entry != "." && $entry != "..") {
            if (@is_dir($lerepertoire.$entry)) $listerepertoires[] = $entry;
            else $listefichiers[] = $entry;
        }

    }
    @closedir($mydir);

    sort($listefichiers);
    sort($listerepertoires);

    foreach($listerepertoires as $k => $dirname) {
        $profondeur++;
        $this->repertoire($lerepertoire.$dirname."/", $limite, &$arbre["children"][$indexA]);
        $profondeur--;
        $cpt++;
    }
    $indexB = sizeof($arbre["children"][$indexA]["children"]);
    foreach($listefichiers as $k => $filename) {
        $arbre["children"][$indexA]["children"][$indexB]["tag"] = "file";
        $arbre["children"][$indexA]["children"][$indexB]["value"] = $lerepertoire.$filename;
        $arbre["children"][$indexA]["children"][$indexB]["attributes"]["NAME"] = $filename;

        // taille du fichier
        $file_size = filesize($lerepertoire.$filename);
        if($file_size >= 1073741824) $arbre["children"][$indexA]["children"][$indexB]["attributes"]["SIZE"] = round($file_size / 1073741824 * 100) / 100 . "g";
        elseif($file_size >= 1048576) $arbre["children"][$indexA]["children"][$indexB]["attributes"]["SIZE"] = round($file_size / 1048576 * 100) / 100 . "m";
        elseif($file_size >= 1024) $arbre["children"][$indexA]["children"][$indexB]["attributes"]["SIZE"] = round($file_size / 1024 * 100) / 100 . "k";
        else $arbre["children"][$indexA]["children"][$indexB]["attributes"]["SIZE"] =  $file_size . "b";

        // date de modification
        $arbre["children"][$indexA]["children"][$indexB]["attributes"]["MODIFIED"] = date("M d H:i", filectime($lerepertoire.$filename));

        // permission
        $arbre["children"][$indexA]["children"][$indexB]["attributes"]["PERMISSION"] = sprintf("%o", (fileperms($lerepertoire.$filename)) & 0777);

        $indexB++;
    }

    if ($racine) return $arbre;
}

/*** > Ancienne fonctions> */
    function strtoarray($t) {
        return $this->charger($t);
    }

    function arraytostr($t) {
        return $this->decharger($t);
    }

    function filetoarray($t) {
        return $this->chargerfichier($t);
    }

    function arraytofile($t,$u) {
        return $this->dechargerfichier($t,$u);
    }

}


/* ############################################################################

<branche name="A">
        <feuille name="x"></feuille>
        <feuille name="y"></feuille>
        <branche ordre="1" name="B" >
                <feuille ordre="2" name="j"></feuille>
                <feuille ordre="1"  name="k"></feuille>
                <feuille ordre="3"  name="l"></feuille>
        </branche>
        <feuille ordre="2" name="t"></feuille>
</branche>

=> donne =>

Array (
    [0] => Array (
            [tag] => BRANCHE
            [attributes] => Array (
                    [NAME] => A
                    [OPEN] => yes
                )

            [children] => Array (
                    [0] => Array (
                            [tag] => FEUILLE
                            [attributes] => Array (
                                    [NAME] => x
                                )

                            [value] =>
                        )

                    [1] => Array (
                            [tag] => FEUILLE
                            [attributes] => Array (
                                    [NAME] => y
                                )

                            [value] =>
                        )

                    [2] => Array (
                            [tag] => BRANCHE
                            [attributes] => Array (
                                    [ORDRE] => 1
                                    [NAME] => B
                                    [OPEN] => yes
                                )

                            [children] => Array (
                                    [0] => Array (
                                            [tag] => FEUILLE
                                            [attributes] => Array (
                                                    [ORDRE] => 2
                                                    [NAME] => j
                                                )

                                            [value] =>
                                        )

                                    [1] => Array (
                                            [tag] => FEUILLE
                                            [attributes] => Array (
                                                    [ORDRE] => 1
                                                    [NAME] => k
                                                )

                                            [value] =>
                                        )

                                    [2] => Array (
                                            [tag] => FEUILLE
                                            [attributes] => Array (
                                                    [ORDRE] => 3
                                                    [NAME] => l
                                                )

                                            [value] =>
*/
?>
