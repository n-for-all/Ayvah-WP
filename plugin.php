<?php

/*
Plugin Name: Cf7 Ayvah
Plugin URI: https://ayvah.io
Description: Integrates Ayvah with Contact form 7
Author: AAAGENCY - @n-for-all
Author URI: https://aaagency.ae
Text Domain: cf7-ava
Version: 1.0.4
*/


/* Not tested with older versions of wpcf7, lower the version number at your own risk */
define("WPCF7_AVA_VERSION", "5.0");


define("WPCF7_AVA_TEXT_DOMAIN", "cf7-fields-ava");
define("WPCF7_AVA_PLUGIN_URL", plugins_url('', __FILE__));

class WPCF7_Ava
{
    private $posted_data;
    public function __construct()
    {
        add_action('wpcf7_init', array(&$this, 'wpcf7_init'));
    }
    public function wpcf7_init()
    {
        if (version_compare(WPCF7_VERSION, WPCF7_AVA_VERSION) >= 0) {
            if(extension_loaded('curl')){
                $this->filters();
                $this->actions();
                require_once dirname(__FILE__).'/lib/Form.php';
            }else{
                add_action('admin_notices', array(&$this, 'curl_notice__error'));
            }
        } else {
            add_action('admin_notices', array(&$this, 'version_notice__error'));
        }
    }
    public function filters()
    {
        add_filter('wpcf7_editor_panels', array(&$this, 'editor_panels'), 10, 1);
        add_filter('wpcf7_contact_form_properties', array(&$this, 'properties'), 10, 2);
        add_filter('wpcf7_posted_data', array($this, 'save_posted'), 999, 3);
        add_filter('wpcf7_before_send_mail', array($this, 'handle_submission'), 999, 3);
    }
    public function actions()
    {
        add_action('wpcf7_save_contact_form', array(&$this, 'save_ava'), 10, 1);
        add_action('admin_enqueue_scripts', array(&$this, 'admin_scripts'));

        if (is_admin()) {
            add_action('admin_init', array(&$this, 'admin_init'), 56);
        }
        add_action('wpcf7_enqueue_scripts', array(&$this, 'scripts'));
    }

    public function version_notice__error()
    {
        $class = 'notice notice-error';
        $message = __('Contact form 7 Ayvah requires Contact Form 7 version '.WPCF7_AVA_VERSION.' or higher.', 'cf7-ava');

        printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
    }
    public function curl_notice__error()
    {
        $class = 'notice notice-error';
        $message = __('CURL extension should be enabled and active on this webserver for Ayvah to work.', 'cf7-ava');

        printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
    }
    function save_posted($posted_data){
        $this->posted_data = $posted_data;
        return $posted_data;
    }
    public function admin_init()
    {
    }

    public function save_ava($contact_form)
    {
        $properties = array();
        if (isset($_POST['ava_form']) && sizeof($_POST['ava_form']) > 0) {
            $properties['ava'] = $_POST['ava_form'];
        }
        $properties = array_merge($contact_form->get_properties(), $properties);
        $contact_form->set_properties($properties);
    }
    public function editor_panels($panels)
    {
        $panels['ava-panel'] = array(
          'title' => __('Ayvah', WPCF7_AVA_TEXT_DOMAIN),
          'callback' => array(&$this, 'editor_panel')
        );
        return $panels;
    }
    public function editor_panel($post)
    {
        $ava = (array)$post->prop('ava');
        $list = isset($ava['fields']) ? (array)$ava['fields']: array();

        ?>
        <div id="wpcf7-ava-list">
            <div class="wpcf7-ava-item">
                <label>Form ID</label>
                <input type="text" name="ava_form[id]" value="<?php echo isset($ava['id']) ? $ava['id']: '' ?>" />
            </div>
            <div class="wpcf7-ava-item">
                <label>Form Key</label>
                <input type="text" name="ava_form[key]" value="<?php echo isset($ava['key']) ? $ava['key']: '' ?>" />
            </div>
            <div class="wpcf7-ava-item">
                <label>Form Locale</label>
                <input type="text" name="ava_form[locale]" value="<?php echo isset($ava['locale']) ? $ava['locale']: 'en' ?>" />
            </div>
            <div class="wpcf7-ava-item">
                <label>Form Domain</label>
                <input type="text" name="ava_form[domain]" value="<?php echo isset($ava['domain']) ? $ava['domain']: '' ?>" />
            </div>
            <script type="text/template" id="tmpl-ajaxy-ava">
                <div class="wpcf7-ava-item">
                    <div class="left">
                        <label>From</label>
                        <input type="text" name="ava_form[fields][{{ data.index }}][from]" value="" />
                    </div>
                    <div class="right">
                        <label>To</label>
                        <input type="text" name="ava_form[fields][{{ data.index }}][to]" value="" />
                    </div>
                    <a class="button button-remove-ava" href="#">Remove</a>
                </div>
            </script>
            <div id="wpcf7-ava-items">
                <h3>Map your fields</h3>
                <div class="mailtags"><small><pre>Form Fields: <?php echo implode(', ', (array)$post->collect_mail_tags()); ?></pre></small></div>
                <?php
                $i = 0;
                foreach ($list as $label) { ?>
                    <div class="wpcf7-ava-item">
                        <div class="left">
                            <label>From</label>
                            <input type="text" name="ava_form[fields][<?php echo $i; ?>][from]" value="<?php echo $label['from']; ?>" />
                        </div>
                        <div class="right">
                            <label>To</label>
                            <input type="text" name="ava_form[fields][<?php echo $i; ?>][to]" value="<?php echo $label['to']; ?>" />
                        </div>
                        <a class="button button-remove-ava" href="#">Remove</a>
                    </div>
                <?php
                $i ++;
                } ?>
            </div>
            <a id="add-field-ava" class="button" href="#">Map Field</a>
            <h4>Last submission</h4>
            <small>A log will appear below for the last submission, use it for testing the form.</small>
            <?php if(get_option('ava_output')): ?>
                <pre><?php print_r(json_decode(get_option('ava_output'))); ?></pre>
            <?php else: ?>
                <pre>Nothing to display</pre>
            <?php endif;?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    var index = <?php echo $i; ?>;
                    jQuery(document).on('click', '.button-remove-ava', function(event){
                        event.preventDefault();
                        event.stopPropagation();
                        jQuery(this).closest('.wpcf7-ava-item').remove();
                    });
                    jQuery(document).on('click', '#add-field-ava', function(event){
                        event.preventDefault();
                        event.stopPropagation();
                        var template = wp.template('ajaxy-ava');
                        if(template){
                            jQuery('#wpcf7-ava-items').append(template({index:index}));
                            index ++;
                        }
                    });
                });
            </script>
        </div>
        <?php
    }

    function handle_submission($contact_form){
        if($contact_form->prop('ava')){
            $this->save_submission($this->posted_data, $contact_form->prop('ava'));
        }
    }

    private function save_submission($submission, $settings){
        if(isset($settings['id']) && isset($settings['key']) && isset($settings['domain']) && isset($settings['fields'])){
            $form = new Form($settings['domain'], $settings['id'], $settings['locale']);
            //Eat Basir's cookie
            $utms = array();
            if(isset($_COOKIE['_ava_utmz'])){
                list($source, $medium, $term, $campaign, $content) = array_map(explode('|', $_COOKIE['_ava_utmz']), 'trim');
                $utms = array(
                    'utm_source' => $source,
                    'utm_medium' => $medium,
                    'utm_campaign' => $campaign,
                    'utm_term' => $term,
                    'utm_content' => $content
                );
            }
            if(isset($_COOKIE['_ava_utmz_referer']) && trim($_COOKIE['_ava_utmz_referer']) != ''){
                $utms['referer'] = $_COOKIE['_ava_utmz_referer'];
            }elseif(isset($_SERVER['HTTP_REFERER'])){
                $utms['referer'] =$_SERVER['HTTP_REFERER'];
            }
            $fields = array(
                'key' => $settings['key'],
                'timestamp' => time(),
                '_ip' => $this->getRealIpAddress()
            );
            $honey_pot = count($settings['fields']);
            foreach((array)$settings['fields'] as $map){
                $fields[$map['to']] = isset($this->posted_data[$map['from']]) ? $this->posted_data[$map['from']]: '';
                $honey_pot += intval(str_replace('field_', '', $map['to']));
            }
            $fields['field_'.$honey_pot] = '';
            $fields = array_merge($fields, $utms);
            $output = $form->submit($fields);
            update_option('ava_output', json_encode($output));
        }
    }
    public function properties($properties, $WPCF7_ContactForm)
    {
        if (!isset($properties['ava'])) {
            $properties['ava'] = array();
        }
        return $properties;
    }
    public function admin_scripts()
    {
        wp_enqueue_script(WPCF7_AVA_TEXT_DOMAIN.'-admin', plugin_dir_url( __FILE__ ) . 'admin/js/scripts.js', array( 'jquery', 'wp-util' ), '1.0', true );
        wp_enqueue_style(WPCF7_AVA_TEXT_DOMAIN."-style", WPCF7_AVA_PLUGIN_URL. '/admin/css/styles.css');
    }
    public function scripts()
    {
        $in_footer = true;
        if ('header' === wpcf7_load_js()) {
            $in_footer = false;
        }
        wp_enqueue_script( WPCF7_AVA_TEXT_DOMAIN, WPCF7_AVA_PLUGIN_URL. '/js/front.js', array( 'contact-form-7' ), "1.0.0", $in_footer);
    }

    public function getRealIpAddress()
    {
        $ip= '';
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(isset($_SERVER['REMOTE_ADDR'])){
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

global $WPCF7_Ava;
$WPCF7_Ava = new WPCF7_Ava();

?>
