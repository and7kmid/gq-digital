<?php

class CompanyHub_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'add_rewrite_rules'));
        add_action('template_redirect', array($this, 'handle_frontend_routes'));
        add_filter('query_vars', array($this, 'add_query_vars'));
    }
    
    public function add_rewrite_rules() {
        add_rewrite_rule('^company-hub/?$', 'index.php?company_hub_page=dashboard', 'top');
        add_rewrite_rule('^company-hub/([^/]+)/?$', 'index.php?company_hub_page=$matches[1]', 'top');
    }
    
    public function add_query_vars($vars) {
        $vars[] = 'company_hub_page';
        return $vars;
    }
    
    public function handle_frontend_routes() {
        $page = get_query_var('company_hub_page');
        
        if ($page) {
            $this->render_app();
            exit;
        }
    }
    
    private function render_app() {
        // Check if user is logged in for protected routes
        $auth = CompanyHub_Auth::get_instance();
        $current_page = get_query_var('company_hub_page', 'dashboard');
        
        if ($current_page !== 'login' && !$auth->is_logged_in()) {
            wp_redirect(home_url('/company-hub/login'));
            exit;
        }
        
        // Enqueue scripts and styles
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
            'pluginUrl' => COMPANY_HUB_PLUGIN_URL,
            'currentPage' => $current_page,
            'isLoggedIn' => $auth->is_logged_in(),
            'currentUser' => $auth->get_current_user(),
            'enabledModules' => CompanyHub_Modules::get_instance()->get_enabled_modules()
        ));
        
        // Render the app container
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Company Hub</title>
            <?php wp_head(); ?>
        </head>
        <body class="company-hub-app">
            <div id="company-hub-root"></div>
            <?php wp_footer(); ?>
        </body>
        </html>
        <?php
    }
}