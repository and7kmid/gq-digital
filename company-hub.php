<?php
/**
 * Plugin Name: Company Hub
 * Plugin URI: https://yourcompany.com
 * Description: CRM + Painel de Comando Digital para gestão completa da empresa
 * Version: 1.0.0
 * Author: Your Company
 * License: GPL v2 or later
 * Text Domain: company-hub
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('COMPANY_HUB_VERSION', '1.0.0');
define('COMPANY_HUB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('COMPANY_HUB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('COMPANY_HUB_PLUGIN_FILE', __FILE__);

// Main plugin class
class CompanyHub {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    private function load_dependencies() {
        require_once COMPANY_HUB_PLUGIN_DIR . 'includes/class-database.php';
        require_once COMPANY_HUB_PLUGIN_DIR . 'includes/class-auth.php';
        require_once COMPANY_HUB_PLUGIN_DIR . 'includes/class-api.php';
        require_once COMPANY_HUB_PLUGIN_DIR . 'includes/class-modules.php';
        require_once COMPANY_HUB_PLUGIN_DIR . 'includes/class-frontend.php';
    }
    
    public function init() {
        // Initialize plugin components
        CompanyHub_Database::get_instance();
        CompanyHub_Auth::get_instance();
        CompanyHub_API::get_instance();
        CompanyHub_Modules::get_instance();
        CompanyHub_Frontend::get_instance();
        
        // Load text domain
        load_plugin_textdomain('company-hub', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    public function enqueue_scripts() {
        if (is_page('company-hub') || get_query_var('company_hub_page')) {
            wp_enqueue_script(
                'company-hub-app',
                COMPANY_HUB_PLUGIN_URL . 'assets/dist/app.js',
                array(),
                COMPANY_HUB_VERSION,
                true
            );
            
            wp_enqueue_style(
                'company-hub-app',
                COMPANY_HUB_PLUGIN_URL . 'assets/dist/app.css',
                array(),
                COMPANY_HUB_VERSION
            );
            
            wp_localize_script('company-hub-app', 'companyHubConfig', array(
                'apiUrl' => rest_url('company-hub/v1/'),
                'nonce' => wp_create_nonce('wp_rest'),
                'pluginUrl' => COMPANY_HUB_PLUGIN_URL
            ));
        }
    }
    
    public function register_rest_routes() {
        CompanyHub_API::register_routes();
    }
    
    public function activate() {
        CompanyHub_Database::create_tables();
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
CompanyHub::get_instance();