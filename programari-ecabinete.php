<?php
/**
 * Plugin Name: eCabinete - Programari online
 * Plugin URI: http://nyxpoint.com
 * Description: Ecabinete este o platformă online creată cu scopul de a facilita gestiunea propriului cabinet medical într-un mod cât mai accesibil nevoilor dumneavoastră.
 * Version: 1.0.1
 * Author: eCabinete
 * Author URI: http://www.ecabinete.ro/
 * License: MIT
 */

$programari_ecabinete_messages = array(
    'activation_fail' => 'Activarea nu a reușit, vă rugăm verificați datele de autentificare!', 
    'curl_not_found' => 'Extensia curl nu este prezentă și/sau activată!', 
    'header_options_h2' => 'Activare', 
    'save_button' => translate("Save Changes", "default"), 
    'settings_page_title' => 'eCabinete - Programări online', 
    'settings_page_name' => 'eCabinete - Programări online', 
    'activation_complete' => 'Pentru a activa modulul de programări online pe site-ul dvs., tot ce trebuie să faceți este să copiați shortcode-ul de mai jos in orice pagină doriți. Puteți modifica lățimea (width) și înălțimea (height) pentru o incadrare mai bună: <br/><br/><br/>

[programari-online-ecabinete width=\'960\' height=\'900\']', 
    'login_and_activate' => 'Dacă aveți deja un cont, tot ce trebuie să faceți este să vă autentificați cu emailul si parola dvs. de pe eCabinete.ro:', 
    'email_label' => 'Email: ', 
    'password_label' => 'Parola: ', 
    'frontend_unactivated' => 'Vă rugăm sa verificați setările pentru pluginul eCabinete - Programări online', 
    'backend_register_message' => "Pentru a putea face programări online vă puteți înregistra gratuit pe <a target='blank' href='http://www.ecabinete.ro/'>ecabinete.ro</a>."
);

require_once 'programari-ecabinete-admin.php';

function shortcode_action_programari_ecabinete($atts, $content = null) {
    global $programari_ecabinete_messages;
    
    if(get_option('ecab_p_token') && get_option('ecab_p_slug')) {
        $atts_p = shortcode_atts( array(
            'width' => '960',
            'height' => '900'
	), $atts);
        
        return '<iframe frameBorder="0" width="' . esc_attr($atts_p['width']) . '" height="' . esc_attr($atts_p['height']) . '" src="' . esc_url('http://programari.ecabinete.ro/' . get_option('ecab_p_slug')) . '">' . esc_html($content ? $content : '') . '</iframe>';
    }
    
    return $programari_ecabinete_messages['frontend_unactivated'];
}
add_shortcode('programari-online-ecabinete', 'shortcode_action_programari_ecabinete');
