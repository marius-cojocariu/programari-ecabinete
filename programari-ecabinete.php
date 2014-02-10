<?php
/**
 * Plugin Name: eCabinete - Programari online
 * Plugin URI: http://nyxpoint.com
 * Description: Ecabinete este o platformă online creată cu scopul de a facilita gestiunea propriului cabinet medical într-un mod cât mai accesibil nevoilor dumneavoastră.
 * Version: 1.0
 * Author: eCabinete
 * Author URI: http://www.ecabinete.ro/
 * License: MIT
 */

$programari_ecabinete_messages = array(
    'activation_fail' => 'Activarea nu a reusit, va rugam verificati datele de autentificare!', 
    'curl_not_found' => 'Extensia curl nu este prezenta si/sau activata!', 
    'header_options_h2' => 'Activare', 
    'save_button' => translate("Save Changes", "default"), 
    'settings_page_title' => 'eCabinete - Programari online', 
    'settings_page_name' => 'eCabinete - Programari online', 
    'activation_complete' => 'Pentru a activa modulul de programari online pe site-ul dvs., tot ce trebuie sa faceti este sa copiati shortcode-ul de mai jos in orice pagina doriti. Puteti modifica latimea (width) si inaltimea (height) pentru o incadrare mai buna: <br/><br/><br/>

[programari-online-ecabinete width=\'960\' height=\'900\']', 
    'login_and_activate' => 'Logheaza-te cu emailul si parola ta de pe eCabinete.ro', 
    'email_label' => 'Email: ', 
    'password_label' => 'Parola: ', 
    'frontend_unactivated' => 'Va rugam sa verificati setarile pentru pluginul eCabinete - Programari online'
);

require_once 'programari-ecabinete-admin.php';

function shortcode_action_programari_ecabinete($atts, $content = null) {
    global $programari_ecabinete_messages;
    
    if(get_option('ecab_p_token') && get_option('ecab_p_slug')) {
        $atts_p = shortcode_atts( array(
            'width' => '960',
            'height' => '900'
	), $atts);
        
        return '<iframe frameBorder="0" width="' . esc_attr($atts_p['width']) . '" height="' . esc_attr($atts_p['height']) . '" src="' . esc_url('http://programari.dev.ecabinete.ro/' . get_option('ecab_p_slug')) . '">' . esc_html($content ? $content : '') . '</iframe>';
    }
    
    return $programari_ecabinete_messages['frontend_unactivated'];
}
add_shortcode('programari-online-ecabinete', 'shortcode_action_programari_ecabinete');