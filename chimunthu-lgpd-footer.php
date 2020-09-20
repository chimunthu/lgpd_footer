<?php 
/* 
Plugin Name: Chimunthu LGPD footer 
Plugin URI: https://chimunthu.com.br/lgpd
Description: Exibe a mensagem de que o site usa cookies e um link para a pagina de politica de privacidade
Version: 1.0 
Author: Ebel D. Chimunthu
Author URI: http://start.co.mz/ 
License: GPLv2 
Text Domain: chimunthu_lgpd
*/

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['lgpd_main_message'])) {
    
    // Retrieve original plugin options array
    $options = chimunthu_lgpd_get_options();
    
    // Cycle through all text form fields and store their values
    // in the options array
    if ( isset( $_POST['lgpd_main_message'] ) ) {
        $options['lgpd_main_message'] = sanitize_text_field( $_POST['lgpd_main_message'] );
    }
    
    // Cycle through all check box form fields and set the options
    // array to true or false values based on presence of variables
    if ( isset( $_POST['lgpd_main_privacy_link'] ) ) {
        $options['lgpd_main_privacy_link'] = sanitize_text_field( $_POST['lgpd_main_privacy_link'] );
    }

    // Cycle through all check box form fields and set the options
    // array to true or false values based on presence of variables
    if ( isset( $_POST['lgpd_is_visible'] ) ) {
        $options['lgpd_is_visible'] = sanitize_text_field( $_POST['lgpd_is_visible'] );
    }
    
    // Store updated options array to database
    update_option( 'chimunthu_lgpd_options', $options );

    header('Location: '.$_POST["_wp_http_referer"]);
    exit();
}

add_action( 'admin_menu', 'chimunthu_lgpd_settings_menu' , 1);

function chimunthu_lgpd_settings_menu() {
    add_options_page( 'Configuraçāo de Mensagem de cookie',
                        'Mensagem de cookie', 'manage_options',
                        'chimunthu-lgpd-message-configuration', 
                        'chimunthu_lgpd_config_page' );
}


function chimunthu_lgpd_get_options(){
    $options = get_option( 'chimunthu_lgpd_options', array() );
    $new_options['lgpd_main_message'] = 'Utilizamos cookies para garantir a melhor experiência em nosso site. Os cookies nos permitem fornecer funcionalidades como segurança, gerenciamento de rede e acessibilidade. Eles melhoram a usabilidade e o desempenho por meio de vários recursos, como reconhecimento de idioma, resultados de pesquisa e, assim, melhoram o que oferecemos a você. Nosso site também pode usar cookies de terceiros para enviar publicidade mais relevante para você. Ao clicar nos botões, você pode aceitar todos os cookies ou, se quiser saber mais sobre os cookies que usamos e como gerenciá-los,';
    $new_options['lgpd_main_privacy_link'] = "";
    $new_options['lgpd_is_visible'] = 'off';
    
    $merged_options = wp_parse_args( $options, $new_options );
    $compare_options = array_diff_key( $new_options, $options );
    if ( empty( $options ) || !empty( $compare_options ) ) {
        update_option( 'chimunthu_lgpd_options', $merged_options );
    }
    return $merged_options;
}

function chimunthu_lgpd_get_pages(){
    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'post_type' => 'page',
        'post_status' => 'publish'
    ); 
    return get_pages($args);

}

function chimunthu_lgpd_config_page() {
    // Retrieve plugin configuration options from database
    $options = chimunthu_lgpd_get_options();
    ?>
    
    <div id="chimunthu-lgpd-general" class="wrap">
    <h1>Configuraçāo de Mensagem de cookie</h1><br />
    
    <form method="post" action=""> 
    <input type="hidden" name="action" value="save_chimunthu_lgpd_options" />
    <!-- Adding security through hidden referrer field -->
    <?php wp_nonce_field( 'chimunthulgpd' ); ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="lgpd_main_message">Mensagem de cookie:</label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Mensagem de cookie</span>
                        </legend>
                        <p><label for="lgpd_main_message">Essa é a mensagem que vai aparecer no campo reservado.</label></p>
                        <p><textarea name="lgpd_main_message" rows="10" cols="50" id="lgpd_main_message" class="large-text code" spellcheck="false"><?php echo esc_html( $options['lgpd_main_message'] );?></textarea></p>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="lgpd_main_message">Página da política de privacidade:</label>
                </th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Página da política de privacidade</span>
                        </legend>
                        <select name="lgpd_main_privacy_link" id="lgpd_main_privacy_link">
                            <?php 
                                foreach(chimunthu_lgpd_get_pages() as $page): 
                                    if($page->ID == intval($options['lgpd_main_privacy_link'])):
                            ?>
                                <option class="level-0" value="<?php echo $page->ID; ?>" selected><?php echo $page->post_title; ?></option>
                            <?php else: ?>
                                <option class="level-0" value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                            <?php endif; endforeach; ?>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <tr>
            <th scope="row">
                <label for="lgpd_main_message">Visibilidade da mensagem:</label>
            </th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text">
                        <span>Visibilidade da mensagem:</span>
                    </legend>
                    <input name="lgpd_is_visible" type="checkbox" id="lgpd_is_visible" <?php echo $options['lgpd_is_visible'] == 'on' ? 'checked' : ''; ?>>
                    Exibir ou nāo a mensagem no site
                </fieldset>
            </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="Salvar" class="button-primary"/>
    </form>
    </div>
<?php }


add_action( 'wp_enqueue_scripts', 'chimunthu_lgpd_queue_stylesheet' );

function chimunthu_lgpd_queue_stylesheet(){
    $my_js_verions  = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'js/chimunthu_lgpd.js' ));

    wp_register_style( 'chimunthu_lgpd_css',    plugins_url( 'css/stylesheet.css',    __FILE__ ), false,   $my_js_verions );
    wp_enqueue_style( 'chimunthu_lgpd_css' );

    wp_enqueue_script( 'chimunthu_lgpd', plugins_url( 'js/chimunthu_lgpd.js', __FILE__ ), array(), $my_js_verions,true );
}

add_action( 'wp_footer', 'chimunthu_lgpd_footer_output' );

function chimunthu_lgpd_footer_output(){
    $options = chimunthu_lgpd_get_options();
    if($options['lgpd_is_visible'] == 'on'):
?>
    <div class="chimunthu-lgpd-bar-main" style="--bg-color:#212652; --fg-color:#EDEDE3; display:none">
    <div class="chimunthu-lgpd-bar">
        <div class="chimunthu-lgpd-bar__content">
            <div class="chimunthu-lgpd-bar__content__text">
                <span><?php echo $options['lgpd_main_message']; ?> <a href="<?php echo get_the_permalink($options['lgpd_main_privacy_link']); ?>" target="_blank">Política de Privacidade.</a></span>
            </div>
        </div> 
        <div class="chimunthu-lgpd-bar__close-btn">
            <a href="" target="" class="chimunthu-lgpd-button lgpd-button"> 
                <span class="chimunthu-lgpd-button__label">Fechar</span> 
            </a>
        </div>
    </div>
</div>
<?php
    endif;
}