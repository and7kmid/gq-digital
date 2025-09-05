<?php

class CompanyHub_API {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public static function register_routes() {
        // Auth routes
        register_rest_route('company-hub/v1', '/auth/login', array(
            'methods' => 'POST',
            'callback' => array(self::get_instance(), 'login'),
            'permission_callback' => '__return_true'
        ));
        
        register_rest_route('company-hub/v1', '/auth/logout', array(
            'methods' => 'POST',
            'callback' => array(self::get_instance(), 'logout'),
            'permission_callback' => '__return_true'
        ));
        
        register_rest_route('company-hub/v1', '/auth/me', array(
            'methods' => 'GET',
            'callback' => array(self::get_instance(), 'get_current_user'),
            'permission_callback' => array(self::get_instance(), 'check_auth')
        ));
        
        // Dashboard routes
        register_rest_route('company-hub/v1', '/dashboard/stats', array(
            'methods' => 'GET',
            'callback' => array(self::get_instance(), 'get_dashboard_stats'),
            'permission_callback' => array(self::get_instance(), 'check_auth')
        ));
        
        // Sites routes
        register_rest_route('company-hub/v1', '/sites', array(
            'methods' => 'GET',
            'callback' => array(self::get_instance(), 'get_sites'),
            'permission_callback' => array(self::get_instance(), 'check_auth')
        ));
        
        register_rest_route('company-hub/v1', '/sites', array(
            'methods' => 'POST',
            'callback' => array(self::get_instance(), 'create_site'),
            'permission_callback' => array(self::get_instance(), 'check_admin_auth')
        ));
        
        // Leads routes
        register_rest_route('company-hub/v1', '/leads', array(
            'methods' => 'GET',
            'callback' => array(self::get_instance(), 'get_leads'),
            'permission_callback' => array(self::get_instance(), 'check_auth')
        ));
        
        register_rest_route('company-hub/v1', '/leads', array(
            'methods' => 'POST',
            'callback' => array(self::get_instance(), 'create_lead'),
            'permission_callback' => array(self::get_instance(), 'check_auth')
        ));
        
        // Tasks routes
        register_rest_route('company-hub/v1', '/tasks', array(
            'methods' => 'GET',
            'callback' => array(self::get_instance(), 'get_tasks'),
            'permission_callback' => array(self::get_instance(), 'check_auth')
        ));
        
        register_rest_route('company-hub/v1', '/tasks', array(
            'methods' => 'POST',
            'callback' => array(self::get_instance(), 'create_task'),
            'permission_callback' => array(self::get_instance(), 'check_auth')
        ));
        
        // Financial routes
        register_rest_route('company-hub/v1', '/financial', array(
            'methods' => 'GET',
            'callback' => array(self::get_instance(), 'get_financial_records'),
            'permission_callback' => array(self::get_instance(), 'check_admin_auth')
        ));
        
        // Metrics routes
        register_rest_route('company-hub/v1', '/metrics/(?P<site_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array(self::get_instance(), 'get_site_metrics'),
            'permission_callback' => array(self::get_instance(), 'check_auth')
        ));
    }
    
    public function check_auth() {
        return CompanyHub_Auth::get_instance()->is_logged_in();
    }
    
    public function check_admin_auth() {
        $auth = CompanyHub_Auth::get_instance();
        return $auth->is_logged_in() && $auth->has_permission('admin');
    }
    
    public function login($request) {
        $username = sanitize_text_field($request->get_param('username'));
        $password = $request->get_param('password');
        
        if (empty($username) || empty($password)) {
            return new WP_Error('missing_credentials', 'Username and password are required', array('status' => 400));
        }
        
        $result = CompanyHub_Auth::get_instance()->login($username, $password);
        
        if ($result['success']) {
            return rest_ensure_response($result);
        } else {
            return new WP_Error('login_failed', $result['message'], array('status' => 401));
        }
    }
    
    public function logout($request) {
        $result = CompanyHub_Auth::get_instance()->logout();
        return rest_ensure_response($result);
    }
    
    public function get_current_user($request) {
        $user = CompanyHub_Auth::get_instance()->get_current_user();
        return rest_ensure_response($user);
    }
    
    public function get_dashboard_stats($request) {
        global $wpdb;
        
        $stats = array(
            'total_sites' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ch_sites WHERE status = 'active'"),
            'total_leads' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ch_leads"),
            'new_leads' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ch_leads WHERE status = 'new'"),
            'active_tasks' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ch_tasks WHERE status IN ('todo', 'in_progress')"),
            'sites_down' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}ch_sites WHERE uptime_status = 'down'")
        );
        
        return rest_ensure_response($stats);
    }
    
    public function get_sites($request) {
        global $wpdb;
        
        $sites = $wpdb->get_results("
            SELECT s.*, u.username as responsible_name 
            FROM {$wpdb->prefix}ch_sites s 
            LEFT JOIN {$wpdb->prefix}ch_users u ON s.responsible_user_id = u.id 
            ORDER BY s.created_at DESC
        ");
        
        return rest_ensure_response($sites);
    }
    
    public function create_site($request) {
        global $wpdb;
        
        $data = array(
            'name' => sanitize_text_field($request->get_param('name')),
            'url' => esc_url_raw($request->get_param('url')),
            'server' => sanitize_text_field($request->get_param('server')),
            'responsible_user_id' => intval($request->get_param('responsible_user_id'))
        );
        
        $result = $wpdb->insert($wpdb->prefix . 'ch_sites', $data);
        
        if ($result) {
            return rest_ensure_response(array('success' => true, 'id' => $wpdb->insert_id));
        }
        
        return new WP_Error('create_failed', 'Failed to create site', array('status' => 500));
    }
    
    public function get_leads($request) {
        global $wpdb;
        
        $leads = $wpdb->get_results("
            SELECT l.*, u.username as assigned_name 
            FROM {$wpdb->prefix}ch_leads l 
            LEFT JOIN {$wpdb->prefix}ch_users u ON l.assigned_to = u.id 
            ORDER BY l.created_at DESC
        ");
        
        return rest_ensure_response($leads);
    }
    
    public function create_lead($request) {
        global $wpdb;
        
        $data = array(
            'name' => sanitize_text_field($request->get_param('name')),
            'email' => sanitize_email($request->get_param('email')),
            'phone' => sanitize_text_field($request->get_param('phone')),
            'company' => sanitize_text_field($request->get_param('company')),
            'source' => sanitize_text_field($request->get_param('source')),
            'notes' => sanitize_textarea_field($request->get_param('notes'))
        );
        
        $result = $wpdb->insert($wpdb->prefix . 'ch_leads', $data);
        
        if ($result) {
            return rest_ensure_response(array('success' => true, 'id' => $wpdb->insert_id));
        }
        
        return new WP_Error('create_failed', 'Failed to create lead', array('status' => 500));
    }
    
    public function get_tasks($request) {
        global $wpdb;
        
        $tasks = $wpdb->get_results("
            SELECT t.*, u.username as assigned_name 
            FROM {$wpdb->prefix}ch_tasks t 
            LEFT JOIN {$wpdb->prefix}ch_users u ON t.assigned_to = u.id 
            ORDER BY t.created_at DESC
        ");
        
        return rest_ensure_response($tasks);
    }
    
    public function create_task($request) {
        global $wpdb;
        
        $data = array(
            'title' => sanitize_text_field($request->get_param('title')),
            'description' => sanitize_textarea_field($request->get_param('description')),
            'priority' => sanitize_text_field($request->get_param('priority')),
            'assigned_to' => intval($request->get_param('assigned_to')),
            'project' => sanitize_text_field($request->get_param('project')),
            'due_date' => sanitize_text_field($request->get_param('due_date'))
        );
        
        $result = $wpdb->insert($wpdb->prefix . 'ch_tasks', $data);
        
        if ($result) {
            return rest_ensure_response(array('success' => true, 'id' => $wpdb->insert_id));
        }
        
        return new WP_Error('create_failed', 'Failed to create task', array('status' => 500));
    }
    
    public function get_financial_records($request) {
        global $wpdb;
        
        $records = $wpdb->get_results("
            SELECT f.*, s.name as site_name 
            FROM {$wpdb->prefix}ch_financial f 
            LEFT JOIN {$wpdb->prefix}ch_sites s ON f.site_id = s.id 
            ORDER BY f.date DESC
        ");
        
        return rest_ensure_response($records);
    }
    
    public function get_site_metrics($request) {
        global $wpdb;
        
        $site_id = intval($request->get_param('site_id'));
        
        $metrics = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM {$wpdb->prefix}ch_metrics 
            WHERE site_id = %d 
            ORDER BY date DESC 
            LIMIT 30
        ", $site_id));
        
        return rest_ensure_response($metrics);
    }
}