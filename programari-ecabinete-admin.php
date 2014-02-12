<?php

function start_session_ecabinete_programari() {
    if(!session_id()) {
        session_start();
    }
}
add_action('init', 'start_session_ecabinete_programari', 1);



function gen_opt_ecabinete_programari() {
    global $programari_ecabinete_messages;
    
    if(isset($_SESSION['ecab_fail_save']) && $_SESSION['ecab_fail_save']) {
        echo '<div class="error settings-error" id="setting-error-settings_updated"><p>' . $programari_ecabinete_messages['activation_fail'] . '</p></div>';
        unset($_SESSION['ecab_fail_save']);
    }
    if(!function_exists('curl_init')) {
        echo '<div class="error settings-error" id="setting-error-settings_updated"><p>' . $programari_ecabinete_messages['curl_not_found'] . '</p></div>';
    }
}
function manage_options_call_ecabinete_programari() {
    global $programari_ecabinete_messages;
    
    echo "<div class='wrap'>
            <h2>" . $programari_ecabinete_messages['header_options_h2'] . "</h2>
            ";
    if(!get_option('ecab_p_token')) {
        echo "<h3>".$programari_ecabinete_messages['backend_register_message']."</h3>";
    }
    echo "
            <form method='post' action='options.php'>";
    settings_fields( 'option_group' );
    do_settings_sections( 'ecab_p_options' );
    if(!get_option('ecab_p_token')) {
        echo "      <p class='submit'>
                    <input name='submit' type='submit' id='submit' class='button-primary' value='" . esc_attr($programari_ecabinete_messages['save_button']) . "' />
                    </p>
                </form>
              </div>";
    } else {
        echo "
                </form>
              </div>";
    }
}
function admin_menu_ecabinete_programari() {
    global $programari_ecabinete_messages;
    
    add_options_page($programari_ecabinete_messages['settings_page_title'], $programari_ecabinete_messages['settings_page_name'], 'manage_options', 'ecab_p_options', 'manage_options_call_ecabinete_programari');
}
add_action('admin_menu', 'admin_menu_ecabinete_programari');

function draw_ecabinete_programari_email_box() {
    echo '<input name="ecab_p_login" type="text" value="'.esc_attr(get_option('ecab_p_login')).'" />';
}
function draw_ecabinete_programari_password_box() {
    echo '<input name="ecab_p_password" type="password" value="" />';
}
function get_redirecturl_ecabinete_programari() {
    $prefix = 'http://';
    if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        $prefix = 'https://';
    }
    
    return $prefix . $_SERVER['HTTP_HOST'] . str_replace('options.php', 'options-general.php?page=ecab_p_options', $_SERVER['REQUEST_URI']);
}
function apicall_ecabinete_programari($method, $fields) {
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_USERAGENT, 'eCabineteWPAgent/1.0');
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($curl, CURLOPT_AUTOREFERER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_FAILONERROR, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_ENCODING, "UTF-8");  
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
    
    curl_setopt($curl, CURLOPT_URL, 'http://app.ecabinete.ro/api/' . $method);
    $response = curl_exec($curl);
    
    curl_close($curl);
    
    return json_decode($response, true);
}
function save_ecabinete_programari_token($val) {
    if(function_exists('curl_init')) {
        $email = $val;
        $password = $_POST['ecab_p_password'];

        $res = apicall_ecabinete_programari('get-token', array(
            'email' => $email, 
            'password' => $password
        ));
        if(is_array($res) && isset($res['Response']) && isset($res['Response']['Token'])) {
            update_option('ecab_p_token', $res['Response']['Token']);

            $res_comp = apicall_ecabinete_programari('get-company-info', array(
                'email' => $email, 
                'token' => $res['Response']['Token']
            ));

            if(is_array($res_comp) && isset($res_comp['Response']) && isset($res_comp['Response']['CompanyInfo'])) {
                $company_info = serialize($res_comp['Response']['CompanyInfo']);
                $company_slug = $res_comp['Response']['CompanyInfo']["slug"];

                update_option('ecab_p_slug', $company_slug);
                update_option('ecab_p_compinfo', $company_info);
            } else {
                $_SESSION['ecab_fail_save'] = true;
                header('Location: '.get_redirecturl_ecabinete_programari());
                die;
            }
        } else {
            $_SESSION['ecab_fail_save'] = true;
            header('Location: '.get_redirecturl_ecabinete_programari());
            die;
        } 
    } else {
        header('Location: '.get_redirecturl_ecabinete_programari());
        die;
    }
    
    return $val;
}
function admin_init_ecabinete_programari() {
    global $programari_ecabinete_messages;
    
    register_setting('option_group', 'ecab_p_login', 'save_ecabinete_programari_token');
    
    if(get_option('ecab_p_token')) {
        add_settings_section('general_options', $programari_ecabinete_messages['activation_complete'], 'gen_opt_ecabinete_programari', 'ecab_p_options');
    } else {
        add_settings_section('general_options', $programari_ecabinete_messages['login_and_activate'], 'gen_opt_ecabinete_programari', 'ecab_p_options');
        add_settings_field('ecab_p_login', $programari_ecabinete_messages['email_label'], 'draw_ecabinete_programari_email_box', 'ecab_p_options', 'general_options');
        add_settings_field('ecab_p_password', $programari_ecabinete_messages['password_label'], 'draw_ecabinete_programari_password_box', 'ecab_p_options', 'general_options');
    }
}
add_action('admin_init', 'admin_init_ecabinete_programari');