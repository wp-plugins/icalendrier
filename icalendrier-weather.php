<?php
/*  Copyright 2014  Baptiste Placé

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * Plugin Name: iCalendrier
 * Plugin URI: http://icalendrier.fr/widget/wordpress-plugin
 * Description: Un simple calendrier qui affiche des infos du jour, comme le numéro de semaine, la date, la fête du jour et la phase de lune.
 * Version: 1.0
 * Author: Baptiste Placé
 * Author URI: http://icalendrier.fr/
 * License: GNU General Public License, version 2
 */

defined('ABSPATH') or die("No script kiddies please!");

// Include moon phase class
include dirname(__FILE__)."/lib/Solaris/MoonPhase.php";

// ENQUEUE CSS
function icalendrier_wp_head( $posts )
{
	wp_enqueue_style( 'icalendrier', plugins_url( '/css/icalendrier.css', __FILE__ ) );
}
add_action('wp_enqueue_scripts', 'icalendrier_wp_head');


// THE SHORTCODE
add_shortcode( 'icalendrier', 'icalendrier_shortcode' );
function icalendrier_shortcode( $atts )
{
	return icalendrier_logic( $atts );
}


function icalendrier_logic($atts) {
    $type = isset($atts['type']) ? $atts['type'] : 'comp';
    if('comp' !== $type && 'wide' !== $type) {
        return false;
    }

    $show_link 			= (isset($atts['show_link']) AND $atts['show_link'] == 1) ? 1 : 0;
    $custom_bg_color	= isset($atts['custom_bg_color']) ? $atts['custom_bg_color'] : false;

    if('comp' === $type) {
        iCalendrierComp($show_link, $custom_bg_color);
    } else if('wide' === $type) {
        iCalendrierWide($show_link, $custom_bg_color);
    } else {
        return false;
    }

    return true;
}

/**
 * Compact calendar
 */
function iCalendrierComp($show_link = false, $custom_bg_color = false) {
    echo '<div class="icalComp">';
    echo '<div class="ccomp">';
    if($custom_bg_color) {
        echo '    <div class="cheight" style="background:'.$custom_bg_color.' !important;">';
    } else {
        echo '    <div class="cheight">';
    }

    echo '        <div class="ctitle">';
    if($show_link) {
        echo '            <a href="http://icalendrier.fr">iCalendrier.fr</a>';
    } else {
        echo "<a href='javascript:void(0)'>Aujourd'hui</a>";
    }
    echo '        </div>';

    echo '        <div class="cephem">';
    echo '            <div class="today">';
    echo '                <span class="daysem">';

    switch(date('N')) {
        case 1: echo 'Lundi'; break;
        case 2: echo 'Mardi'; break;
        case 3: echo 'Mercredi'; break;
        case 4: echo 'Jeudi'; break;
        case 5: echo 'Vendredi'; break;
        case 6: echo 'Samedi'; break;
        case 7: echo 'Dimanche'; break;
    }

    echo " - ";

    echo "Sem. ".ltrim(date('W'), '0');

    echo '                </span>';

    $day = date('d');
    $daydig1 = substr($day, 0, 1);
    $daydig2 = substr($day, 1, 1);

    echo '                <span class="day">';
    echo '                    <span class="daydig1">';
    echo $daydig1;
    echo '                   </span>';
    echo '                    <span class="daydig1">';
    echo $daydig2;
    echo '                    </span>';
    echo '                </span>';


    echo '                <span class="month">';

    switch(date('m')) {
        case 1: echo 'Jan.'; break;
        case 2: echo 'Fév.'; break;
        case 3: echo 'Mars'; break;
        case 4: echo 'Avr.'; break;
        case 5: echo 'Mai'; break;
        case 6: echo 'Juin'; break;
        case 7: echo 'Juil.'; break;
        case 8: echo 'Août'; break;
        case 9: echo 'Sep.'; break;
        case 10: echo 'Oct.'; break;
        case 11: echo 'Nov.'; break;
        case 12: echo 'Déc.'; break;
    }

    echo '                </span>';
    echo '                <span class="fete">';
    echo fdjToday();
    echo '                </span>';
    echo '                <span class="moon">';
    echo getMoonSide();
    echo '                </span>';
    echo '            </div>';
    echo '        </div>';
    echo '    </div>';
    echo '</div>';
    echo '</div>';
}

/**
 * Wide calendar
 */
function iCalendrierWide($show_link = false, $custom_bg_color = false) {

    if($custom_bg_color) {
        echo '<div class="icalWide" style="background:'.$custom_bg_color.' !important;">';
    } else {
        echo '<div class="icalWide">';
    }

    echo '<div class="today">';

    echo '<span class="day">';

    switch(date('N')) {
        case 1: echo 'Lundi'; break;
        case 2: echo 'Mardi'; break;
        case 3: echo 'Mercredi'; break;
        case 4: echo 'Jeudi'; break;
        case 5: echo 'Vendredi'; break;
        case 6: echo 'Samedi'; break;
        case 7: echo 'Dimanche'; break;
    }

    echo '</span>';

    echo '<span class="num">'. date('d') . '</span>';

    echo '<span class="month">';

    switch(date('m')) {
        case 1: echo 'Janvier'; break;
        case 2: echo 'Février'; break;
        case 3: echo 'Mars'; break;
        case 4: echo 'Avril'; break;
        case 5: echo 'Mai'; break;
        case 6: echo 'Juin'; break;
        case 7: echo 'Juillet'; break;
        case 8: echo 'Août'; break;
        case 9: echo 'Septembre'; break;
        case 10: echo 'Octobre'; break;
        case 11: echo 'Novembre'; break;
        case 12: echo 'Décembre'; break;
    }
    echo '</span>';

    echo '<span class="more">';
    echo "Semaine ".ltrim(date('W'), '0');
    echo ' | ';
    echo fdjToday();
    echo '</span>';

    echo '<span class="moon">';
    echo getMoonSide();
    echo '</span>';

    echo '</div>';
    echo '</div>';

}

/**
 * Select the moon character
 * @return string
 */
function getMoonSide() {
    require_once (dirname(__FILE__).'/lib/Solaris/MoonPhase.php');

    $localMoonPhase = array(
        'New Moon' => 'Nouvelle lune',
        'Waxing Crescent' => 'Premier croissant',
        'First Quarter' => 'Premier quartier',
        'Waxing Gibbous' => 'Gibbeuse croissante',
        'Full Moon' => 'Pleine lune',
        'Waning Gibbous' => 'Gibbeuse décroissante',
        'Third Quarter' => 'Dernier quartier',
        'Waning Crescent' =>  'Dernier croissant',
    );

    $now = new DateTime();

    if(intval($now->format("G")) >= 7) {
        // 7 heure du matin ou +, on prend la prochaine nuit à minuit
        $now->setTime(0, 0); // Minuit
        $now->add(new DateInterval("P1D"));
        $moon = new Solaris_MoonPhase($now->format("U"));
    } else {
        // avant 7 heures du matin, on prend la nuit en cours ou presque finie
        $now->setTime(0, 0); // Minuit
        $moon = new Solaris_MoonPhase($now->format("U"));
    }

    $phase = $moon->phase();

    // Utilisation de la font

    // 29 "phases" pour correspondre à la font
    $moonCharacters = array(0, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', '@', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 0);

    $totalCharacters = count($moonCharacters);
    $step = 1 / ($totalCharacters - 1);

    if($phase < $step / 2){
        $index = 0;
    } else if($phase > (1 - $step / 2)) {
        $index = 28;
    } else {
        for($i = 1; $i <= 27; $i++) {
            // Passage dans l'interval qui entoure l'illustration
            if($phase >= ($step / 2 + ($i - 1) * $step) && $phase < ($step / 2 + $i * $step)) {
                $index = $i;
            }
        }
    }

    $html = '<span class="phase">'.$moonCharacters[$index].'</span>';
    $html .=  $localMoonPhase[$moon->phase_name()];
    return $html;
}

/**
 * Name Day in France
 *
 * @param null $mois
 * @param null $jour
 * @return bool
 */
function fdjToday($mois=null, $jour=null){
    $fetes = array();
    $fetes[1][1] = "Jour de l'an";
    $fetes[1][2] = "Basile";
    $fetes[1][3] = "Geneviève";
    $fetes[1][4] = "Odilon";
    $fetes[1][5] = "Edouard";
    $fetes[1][6] = "Mélaine";
    $fetes[1][7] = "Raymond";
    $fetes[1][8] = "Lucien";
    $fetes[1][9] = "Alix";
    $fetes[1][10] = "Guillaume";
    $fetes[1][11] = "Pauline";
    $fetes[1][12] = "Tatiana";
    $fetes[1][13] = "Yvette";
    $fetes[1][14] = "Nina";
    $fetes[1][15] = "Rémi";
    $fetes[1][16] = "Marcel";
    $fetes[1][17] = "Roseline";
    $fetes[1][18] = "Prisca";
    $fetes[1][19] = "Marius";
    $fetes[1][20] = "Sébastien";
    $fetes[1][21] = "Agnès";
    $fetes[1][22] = "Vincent";
    $fetes[1][23] = "Banard";
    $fetes[1][24] = "François de Sales";
    $fetes[1][25] = "Conversion de Paul";
    $fetes[1][26] = "Paule";
    $fetes[1][27] = "Angèle";
    $fetes[1][28] = "Thomas d'Aquin";
    $fetes[1][29] = "Gildas";
    $fetes[1][30] = "Martine";
    $fetes[1][31] = "Marcelle";
    $fetes[2][1] = "Ella";
    $fetes[2][2] = "Présentation";
    $fetes[2][3] = "Blaise";
    $fetes[2][4] = "Véronique";
    $fetes[2][5] = "Agathe";
    $fetes[2][6] = "Gaston";
    $fetes[2][7] = "Eugènie";
    $fetes[2][8] = "Jacqueline";
    $fetes[2][9] = "Apolline";
    $fetes[2][10] = "Arnaud";
    $fetes[2][11] = "Notre Dame de Lourdes";
    $fetes[2][12] = "Félix";
    $fetes[2][13] = "Béatrice";
    $fetes[2][14] = "Valentin";
    $fetes[2][15] = "Claude";
    $fetes[2][16] = "Julienne";
    $fetes[2][17] = "Alexis";
    $fetes[2][18] = "Bernadette";
    $fetes[2][19] = "Gabin";
    $fetes[2][20] = "Aimée";
    $fetes[2][21] = "Damien";
    $fetes[2][22] = "Isabelle";
    $fetes[2][23] = "Lazare";
    $fetes[2][24] = "Modeste";
    $fetes[2][25] = "Roméo";
    $fetes[2][26] = "Nestor";
    $fetes[2][27] = "Honorine";
    $fetes[2][28] = "Romain";
    $fetes[2][29] = "Auguste";
    $fetes[3][1] = "Aubin";
    $fetes[3][2] = "Charles le Bon";
    $fetes[3][3] = "Guénolé";
    $fetes[3][4] = "Casimir";
    $fetes[3][5] = "Olive";
    $fetes[3][6] = "Colette";
    $fetes[3][7] = "Félicité";
    $fetes[3][8] = "Jean de Dieu";
    $fetes[3][9] = "Françoise";
    $fetes[3][10] = "Vivien";
    $fetes[3][11] = "Rosine";
    $fetes[3][12] = "Justine";
    $fetes[3][13] = "Rodrigue";
    $fetes[3][14] = "Mathilde";
    $fetes[3][15] = "Louise";
    $fetes[3][16] = "Bénédicte";
    $fetes[3][17] = "Patrice";
    $fetes[3][18] = "Cyrille";
    $fetes[3][19] = "Joseph";
    $fetes[3][20] = "Printemps";
    $fetes[3][21] = "Clémence";
    $fetes[3][22] = "Léa";
    $fetes[3][23] = "Victorien";
    $fetes[3][24] = "Catherine";
    $fetes[3][25] = "Annonciation";
    $fetes[3][26] = "Larissa";
    $fetes[3][27] = "Habib";
    $fetes[3][28] = "Gontran";
    $fetes[3][29] = "Gwladys";
    $fetes[3][30] = "Amédée";
    $fetes[3][31] = "Benjamin";
    $fetes[4][1] = "Hugues";
    $fetes[4][2] = "Sandrine";
    $fetes[4][3] = "Richard";
    $fetes[4][4] = "Isidore";
    $fetes[4][5] = "Irène";
    $fetes[4][6] = "Marcellin";
    $fetes[4][7] = "Jean-Baptiste de la Salle";
    $fetes[4][8] = "Julie";
    $fetes[4][9] = "Gautier";
    $fetes[4][10] = "Fulbert";
    $fetes[4][11] = "Stanislas";
    $fetes[4][12] = "Jules";
    $fetes[4][13] = "Ida";
    $fetes[4][14] = "Maxime";
    $fetes[4][15] = "Paterne";
    $fetes[4][16] = "Benoît-Joseph";
    $fetes[4][17] = "Anicet";
    $fetes[4][18] = "Parfait";
    $fetes[4][19] = "Emma";
    $fetes[4][20] = "Odette";
    $fetes[4][21] = "Anselme";
    $fetes[4][22] = "Alexandre";
    $fetes[4][23] = "Georges";
    $fetes[4][24] = "Fidèle";
    $fetes[4][25] = "Marc";
    $fetes[4][26] = "Alida";
    $fetes[4][27] = "Zita";
    $fetes[4][28] = "Valérie";
    $fetes[4][29] = "Catherine de Sienne";
    $fetes[4][30] = "Robert";
    $fetes[5][1] = "Fête du travail";
    $fetes[5][2] = "Boris";
    $fetes[5][3] = "Philippe - Jacques";
    $fetes[5][4] = "Sylvain";
    $fetes[5][5] = "Judith";
    $fetes[5][6] = "Prudence";
    $fetes[5][7] = "Gisèle";
    $fetes[5][8] = "Armistice 1945";
    $fetes[5][9] = "Pacôme";
    $fetes[5][10] = "Solange";
    $fetes[5][11] = "Estelle";
    $fetes[5][12] = "Achille";
    $fetes[5][13] = "Rolande";
    $fetes[5][14] = "Matthias";
    $fetes[5][15] = "Denise";
    $fetes[5][16] = "Honoré";
    $fetes[5][17] = "Pascal";
    $fetes[5][18] = "Eric";
    $fetes[5][19] = "Yves";
    $fetes[5][20] = "Bernardin";
    $fetes[5][21] = "Constantin";
    $fetes[5][22] = "Emile";
    $fetes[5][23] = "Didier";
    $fetes[5][24] = "Donatien";
    $fetes[5][25] = "Sophie";
    $fetes[5][26] = "Bérenger";
    $fetes[5][27] = "Augustin";
    $fetes[5][28] = "Germain";
    $fetes[5][29] = "Aymar";
    $fetes[5][30] = "Ferdinand";
    $fetes[5][31] = "Visit. de la Ste Vierge";
    $fetes[6][1] = "Justin";
    $fetes[6][2] = "Blandine";
    $fetes[6][3] = "Kévin";
    $fetes[6][4] = "Clotilde";
    $fetes[6][5] = "Igor";
    $fetes[6][6] = "Norbert";
    $fetes[6][7] = "Gilbert";
    $fetes[6][8] = "Médard";
    $fetes[6][9] = "Diane";
    $fetes[6][10] = "Landry";
    $fetes[6][11] = "Barnabé";
    $fetes[6][12] = "Guy";
    $fetes[6][13] = "Antoine de Padoue";
    $fetes[6][14] = "Elisée";
    $fetes[6][15] = "Germaine";
    $fetes[6][16] = "Jean François Régis";
    $fetes[6][17] = "Hervé";
    $fetes[6][18] = "Léonce";
    $fetes[6][19] = "Romuald";
    $fetes[6][20] = "Silvère";
    $fetes[6][21] = "Eté";
    $fetes[6][22] = "Alban";
    $fetes[6][23] = "Audrey";
    $fetes[6][24] = "Jean-Baptiste";
    $fetes[6][25] = "Prosper";
    $fetes[6][26] = "Anthelme";
    $fetes[6][27] = "Fernand";
    $fetes[6][28] = "Irénée";
    $fetes[6][29] = "Pierre - Paul";
    $fetes[6][30] = "Martial";
    $fetes[7][1] = "Thierry";
    $fetes[7][2] = "Martinien";
    $fetes[7][3] = "Thomas";
    $fetes[7][4] = "Florent";
    $fetes[7][5] = "Antoine";
    $fetes[7][6] = "Mariette";
    $fetes[7][7] = "Raoul";
    $fetes[7][8] = "Thibault";
    $fetes[7][9] = "Amandine";
    $fetes[7][10] = "Ulrich";
    $fetes[7][11] = "Benoît";
    $fetes[7][12] = "Olivier";
    $fetes[7][13] = "Henri et Joël";
    $fetes[7][14] = "Fête Nationale";
    $fetes[7][15] = "Donald";
    $fetes[7][16] = "Nte Dame Mt Carmel";
    $fetes[7][17] = "Charlotte";
    $fetes[7][18] = "Frédéric";
    $fetes[7][19] = "Arsène";
    $fetes[7][20] = "Marina";
    $fetes[7][21] = "Victor";
    $fetes[7][22] = "Marie Madeleine";
    $fetes[7][23] = "Brigitte";
    $fetes[7][24] = "Christine";
    $fetes[7][25] = "Jacques";
    $fetes[7][26] = "Anne et Joachin";
    $fetes[7][27] = "Nathalie";
    $fetes[7][28] = "Samson";
    $fetes[7][29] = "Marthe";
    $fetes[7][30] = "Juliette";
    $fetes[7][31] = "Ignace de Loyola";
    $fetes[8][1] = "Alphonse";
    $fetes[8][2] = "Julien Eymard";
    $fetes[8][3] = "Lydie";
    $fetes[8][4] = "J.-M. Vianney";
    $fetes[8][5] = "Abel";
    $fetes[8][6] = "Transfiguration";
    $fetes[8][7] = "Gaétan";
    $fetes[8][8] = "Dominique";
    $fetes[8][9] = "Amour";
    $fetes[8][10] = "Laurent";
    $fetes[8][11] = "Claire";
    $fetes[8][12] = "Clarisse";
    $fetes[8][13] = "Hippolyte";
    $fetes[8][14] = "Evrard";
    $fetes[8][15] = "Assomption";
    $fetes[8][16] = "Armel";
    $fetes[8][17] = "Hyacinthe";
    $fetes[8][18] = "Hélène";
    $fetes[8][19] = "Jean Eudes";
    $fetes[8][20] = "Bernard";
    $fetes[8][21] = "Christophe";
    $fetes[8][22] = "Fabrice";
    $fetes[8][23] = "Rose de Lima";
    $fetes[8][24] = "Barthélémy";
    $fetes[8][25] = "Louis";
    $fetes[8][26] = "Natacha";
    $fetes[8][27] = "Monique";
    $fetes[8][28] = "Augustin";
    $fetes[8][29] = "Sabine";
    $fetes[8][30] = "Fiacre";
    $fetes[8][31] = "Aristide";
    $fetes[9][1] = "Gilles";
    $fetes[9][2] = "Ingrid";
    $fetes[9][3] = "Grégoire";
    $fetes[9][4] = "Rosalie";
    $fetes[9][5] = "Raïssa";
    $fetes[9][6] = "Bertrand";
    $fetes[9][7] = "Reine";
    $fetes[9][8] = "Nativité";
    $fetes[9][9] = "Alain";
    $fetes[9][10] = "Inès";
    $fetes[9][11] = "Adelphe";
    $fetes[9][12] = "Apollinaire";
    $fetes[9][13] = "Aimé";
    $fetes[9][14] = "Croix Glorieuse";
    $fetes[9][15] = "Roland";
    $fetes[9][16] = "Edith";
    $fetes[9][17] = "Renaud";
    $fetes[9][18] = "Nadège";
    $fetes[9][19] = "Emilie";
    $fetes[9][20] = "Davy";
    $fetes[9][21] = "Matthieu";
    $fetes[9][22] = "Maurice";
    $fetes[9][23] = "Automne";
    $fetes[9][24] = "Thècle";
    $fetes[9][25] = "Hermann";
    $fetes[9][26] = "Côme et Damien";
    $fetes[9][27] = "Vincent de Paul";
    $fetes[9][28] = "Venceslas";
    $fetes[9][29] = "Michel";
    $fetes[9][30] = "Jérôme";
    $fetes[10][1] = "Thérèse de Jésus";
    $fetes[10][2] = "Léger";
    $fetes[10][3] = "Gérard";
    $fetes[10][4] = "François d'Assise";
    $fetes[10][5] = "Fleur";
    $fetes[10][6] = "Bruno";
    $fetes[10][7] = "Serge";
    $fetes[10][8] = "Pélagie";
    $fetes[10][9] = "Denis";
    $fetes[10][10] = "Ghislain";
    $fetes[10][11] = "Firmin";
    $fetes[10][12] = "Wilfried";
    $fetes[10][13] = "Géraud";
    $fetes[10][14] = "Juste";
    $fetes[10][15] = "Thérèse d'Avila";
    $fetes[10][16] = "Edwige";
    $fetes[10][17] = "Baudoin";
    $fetes[10][18] = "Luc";
    $fetes[10][19] = "René";
    $fetes[10][20] = "Adeline";
    $fetes[10][21] = "Céline";
    $fetes[10][22] = "Elodie";
    $fetes[10][23] = "Jean de Capistran";
    $fetes[10][24] = "Florentin";
    $fetes[10][25] = "Crépin";
    $fetes[10][26] = "Dimitri";
    $fetes[10][27] = "Emeline";
    $fetes[10][28] = "Jude";
    $fetes[10][29] = "Narcisse";
    $fetes[10][30] = "Bienvenue";
    $fetes[10][31] = "Quentin";
    $fetes[11][1] = "Toussaint";
    $fetes[11][2] = "Défunts";
    $fetes[11][3] = "Hubert";
    $fetes[11][4] = "Charles";
    $fetes[11][5] = "Sylvie";
    $fetes[11][6] = "Bertille";
    $fetes[11][7] = "Carine";
    $fetes[11][8] = "Geoffroy";
    $fetes[11][9] = "Théodore";
    $fetes[11][10] = "Léon";
    $fetes[11][11] = "Armistice 1918";
    $fetes[11][12] = "Christian";
    $fetes[11][13] = "Brice";
    $fetes[11][14] = "Sidoine";
    $fetes[11][15] = "Albert";
    $fetes[11][16] = "Marguerite";
    $fetes[11][17] = "Elisabeth";
    $fetes[11][18] = "Aude";
    $fetes[11][19] = "Tanguy";
    $fetes[11][20] = "Edmond";
    $fetes[11][21] = "Présence de Marie";
    $fetes[11][22] = "Cécile";
    $fetes[11][23] = "Clément";
    $fetes[11][24] = "Flora";
    $fetes[11][25] = "Catherine";
    $fetes[11][26] = "Delphine";
    $fetes[11][27] = "Sévrin";
    $fetes[11][28] = "Jacques de la Marche";
    $fetes[11][29] = "Saturnin";
    $fetes[11][30] = "André";
    $fetes[12][1] = "Florence";
    $fetes[12][2] = "Viviane";
    $fetes[12][3] = "François Xavier";
    $fetes[12][4] = "Barbara";
    $fetes[12][5] = "Gérald";
    $fetes[12][6] = "Nicolas";
    $fetes[12][7] = "Ambroise";
    $fetes[12][8] = "Immaculée Conception";
    $fetes[12][9] = "Pierre Fourier";
    $fetes[12][10] = "Romaric";
    $fetes[12][11] = "Daniel";
    $fetes[12][12] = "Jeanne-Fr. de Chantal";
    $fetes[12][13] = "Lucie";
    $fetes[12][14] = "Odile";
    $fetes[12][15] = "Ninon";
    $fetes[12][16] = "Alice";
    $fetes[12][17] = "Gaël";
    $fetes[12][18] = "Gatien";
    $fetes[12][19] = "Urbain";
    $fetes[12][20] = "Théophile";
    $fetes[12][21] = "Hiver";
    $fetes[12][22] = "Françoise Xavière";
    $fetes[12][23] = "Armand";
    $fetes[12][24] = "Adèle";
    $fetes[12][25] = "Noël";
    $fetes[12][26] = "Etienne";
    $fetes[12][27] = "Jean";
    $fetes[12][28] = "Innocents";
    $fetes[12][29] = "David";
    $fetes[12][30] = "Roger";
    $fetes[12][31] = "Sylvestre";

    if(is_null($mois) || is_null($jour)){
        $date = date("d/m");
        list($jour,$mois) = explode("/",$date);
    }
    if(!isset($fetes[intval($mois)][intval($jour)])){
        return false;
    }

    return $fetes[intval($mois)][intval($jour)];
}


/**
 * Class ICalendrier
 */
class ICalendrierWidget extends WP_Widget
{
	function ICalendrierWidget() { parent::WP_Widget(false, $name = 'iCalendrier Widget'); }

    function widget($args, $instance) 
    {	
        extract( $args );
        
        $type 			    = isset($instance['type']) ? $instance['type'] : 'comp';
        $timezone 	        = isset($instance['timezone']) ? $instance['timezone'] : 0;
        $widget_title 		= isset($instance['widget_title']) ? $instance['widget_title'] : false;
        $show_link 			= (isset($instance['show_link']) AND $instance['show_link'] == 1) ? 1 : 0;
        $custom_bg_color	= isset($instance['custom_bg_color']) ? $instance['custom_bg_color'] : false;

        if (($timezone !== 0) and (function_exists('date_default_timezone_set'))) {
            date_default_timezone_set($timezone);
        }

		echo $before_widget;

		if($widget_title != "") echo $before_title . $widget_title . $after_title;

        icalendrier_logic(array('type' => $type, 'show_link' => $show_link, 'custom_bg_color' => $custom_bg_color));

		echo $after_widget;
    }
 
    function update($new_instance, $old_instance) 
    {		
		$instance = $old_instance;
		$instance['type'] 			= strip_tags($new_instance['type']);
		$instance['timezone'] 	= strip_tags($new_instance['timezone']);
		$instance['widget_title'] 		= strip_tags($new_instance['widget_title']);
		$instance['custom_bg_color'] 	= strip_tags($new_instance['custom_bg_color']);
		$instance['show_link'] 			= (isset($new_instance['show_link']) AND $new_instance['show_link'] == 1) ? 1 : 0;
        return $instance;
    }
 
    function form($instance) 
    {	
        $type 			    = isset($instance['type']) ? esc_attr($instance['type']) : "";
        $timezone 		    = isset($instance['timezone']) ? esc_attr($instance['timezone']) : 5;
        $widget_title 		= isset($instance['widget_title']) ? esc_attr($instance['widget_title']) : "";
        $show_link 			= (isset($instance['show_link']) AND $instance['show_link'] == 1) ? 1 : 0;
        $custom_bg_color	= isset($instance['custom_bg_color']) ? esc_attr($instance['custom_bg_color']) : "";
	?>
        <p>
            <label for="<?php echo $this->get_field_id('type'); ?>">Type de calendrier :</label>
            <select class="widefat" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
                <option value="comp"<?php if($type == 'comp') echo " selected=\"selected\""; ?>>Compact 175px</option>
                <option value="wide"<?php if($type == 'wide') echo " selected=\"selected\""; ?>>Wide 100%</option>
            </select>
        </p>
                
		<p>
          <label for="<?php echo $this->get_field_id('timezone'); ?>">Fuseau horaire :</label>
          <select class="widefat" id="<?php echo $this->get_field_id('timezone'); ?>" name="<?php echo $this->get_field_name('timezone'); ?>">
          	<option value="0"<?php if($timezone === 0) echo " selected=\"selected\""; ?>>Automatique</option>
            <option value="Europe/Paris"<?php if($timezone == 'Europe/Paris') echo " selected=\"selected\""; ?>>Europe/Paris</option>
            <option value="Europe/Berlin"<?php if($timezone == 'Europe/Berlin') echo " selected=\"selected\""; ?>>Europe/Berlin</option>
            <option value="Europe/Moscow"<?php if($timezone == 'Europe/Moscow') echo " selected=\"selected\""; ?>>Europe/Moscow</option>
            <option value="America/Montreal"<?php if($timezone == 'America/Montreal') echo " selected=\"selected\""; ?>>America/Montreal</option>
            <option value="America/Guyana"<?php if($timezone == 'America/Guyana') echo " selected=\"selected\""; ?>>America/Guyana</option>
            <option value="UTC"<?php if($timezone == 'UTC') echo " selected=\"selected\""; ?>>UTC</option>
          </select>
		</p>

        <field name="timezone" type="list" default="0" label="Fuseau Horaire" description="Réglage du fuseau horaire utilisé">
            <option value="0">Selon votre configuration hébergeur</option>
            <option value="Europe/Paris">Europe/Paris</option>
            <option value="Europe/Berlin">Europe/Berlin</option>
            <option value="Europe/Moscow">Europe/Moscow</option>
            <option value="America/Montreal">America/Montreal</option>
            <option value="America/Guyana">America/Guyana</option>
            <option value="UTC">UTC</option>
        </field>

        <p>
            <label for="<?php echo $this->get_field_id('show_link'); ?>">Afficher le lien vers iCalendrier.fr</label>  &nbsp;
            <input id="<?php echo $this->get_field_id('show_link'); ?>" name="<?php echo $this->get_field_name('show_link'); ?>" type="checkbox" value="1" <?php if($show_link) echo ' checked="checked"'; ?> />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('widget_title'); ?>">Titre du widget (en option)</label>
            <input class="widefat" id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" value="<?php echo $widget_title; ?>" />
        </p>

        <p>
          <label for="<?php echo $this->get_field_id('custom_bg_color'); ?>">Couleur de fond (en option)</label><br />
          <input class="widefat" id="<?php echo $this->get_field_id('custom_bg_color'); ?>" name="<?php echo $this->get_field_name('custom_bg_color'); ?>" type="text" value="<?php echo $custom_bg_color; ?>" />
        </p>
		
        <?php
    }
}

add_action( 'widgets_init', create_function('', 'return register_widget("ICalendrierWidget");') );