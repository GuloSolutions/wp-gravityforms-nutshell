<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.gulosolutions.com/
 * @since      1.0.0
 *
 * @package    Gravityforms_Nutshell_Integration
 * @subpackage Gravityforms_Nutshell_Integration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Gravityforms_Nutshell_Integration
 * @subpackage Gravityforms_Nutshell_Integration/public
 * @author     Gulo <Gulo Solutions>
 */
class Gravityforms_Nutshell_Integration_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The Nutshell API var.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $nutshell;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }


    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Gravityforms_Nutshell_Integration_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Gravityforms_Nutshell_Integration_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/gravityforms-nutshell-integration-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Gravityforms_Nutshell_Integration_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Gravityforms_Nutshell_Integration_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/gravityforms-nutshell-integration-public.js', array( 'jquery' ), $this->version, false);
    }

    public function startService()
    {
        global $gravity_forms;

        $gravity_forms = new Controllers\GravityFormsController();
    }

    public function after_submission()
    {

        add_action('gform_after_submission', 'send_data_to_nutshell', 10, 2);

        function send_data_to_nutshell($entry, $form)
        {
            global $gravity_forms;

            $idLabelMap = [];
            $dataToSend = [];

            error_log(print_r($entry, true));

            foreach ($form['fields'] as $field){
                $idLabelMap[$field->id] = $field->label;
            }

            error_log(print_r($idLabelMap, true));

            foreach($entry as $k=>$v){
                if (array_keys($idLabelMap, $k) !== NULL) {
                    if(!empty($idLabelMap[$k]) && (strtolower($idLabelMap[$k]) == 'name' || strtolower($idLabelMap[$k]) == 'email' || strtolower($idLabelMap[$k]) == 'phone')) {
                        $dataToSend[strtolower($idLabelMap[$k])] = $v;
                    }
                }
            }

            error_log(print_r($dataToSend, true));

            $contacts = $gravity_forms->getContacts();

            foreach ($contacts as $contact) {
                $contact->name = strtolower($contact->name);
                $names[] = $contact->name;
            }

            if (array_search($dataToSend['name'], $names) > 0) {
                 return;
            }

            $params['contact'] = $dataToSend;

            if ($gravity_forms->addContact($params)) {
                error_log(print_r('added', true));
            }
        }
    }

    public function pre_render_add_note(){

        error_log(print_r('in prerender', true));

        add_action('gform_pre_render_1', 'set_is_note', 1, 1);

        function set_is_note($form)
        {
            error_log(print_r('in prerender2', true));
            $props = array(
                'id' => 100,
                'label' => 'Is it a note?',
                'type' => 'checkbox'
            );
            $field = GF_Fields::create( $props );
            array_push( $form['fields'], $field );

            return $form;
        }
    }

}
