<?php

class CompanyHub_Database {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Users table
        $table_users = $wpdb->prefix . 'ch_users';
        $sql_users = "CREATE TABLE $table_users (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            username varchar(50) NOT NULL,
            email varchar(100) NOT NULL,
            password varchar(255) NOT NULL,
            role enum('admin','collaborator') DEFAULT 'collaborator',
            status enum('active','inactive') DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY username (username),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        // Sites table
        $table_sites = $wpdb->prefix . 'ch_sites';
        $sql_sites = "CREATE TABLE $table_sites (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            url varchar(255) NOT NULL,
            server varchar(255),
            responsible_user_id mediumint(9),
            status enum('active','inactive','maintenance') DEFAULT 'active',
            ssl_status enum('valid','invalid','expired') DEFAULT 'valid',
            domain_expiry date,
            uptime_status enum('up','down','unknown') DEFAULT 'unknown',
            last_check datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY responsible_user_id (responsible_user_id)
        ) $charset_collate;";
        
        // Accounts table
        $table_accounts = $wpdb->prefix . 'ch_accounts';
        $sql_accounts = "CREATE TABLE $table_accounts (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            type enum('google_ads','analytics','search_console','affiliate','social','other') NOT NULL,
            credentials text,
            api_key varchar(255),
            access_token text,
            refresh_token text,
            expires_at datetime,
            status enum('active','inactive','expired') DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Leads table
        $table_leads = $wpdb->prefix . 'ch_leads';
        $sql_leads = "CREATE TABLE $table_leads (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255),
            phone varchar(50),
            company varchar(255),
            source varchar(100),
            status enum('new','contacted','qualified','converted','lost') DEFAULT 'new',
            assigned_to mediumint(9),
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY assigned_to (assigned_to),
            KEY status (status)
        ) $charset_collate;";
        
        // Tasks table
        $table_tasks = $wpdb->prefix . 'ch_tasks';
        $sql_tasks = "CREATE TABLE $table_tasks (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            status enum('todo','in_progress','done','cancelled') DEFAULT 'todo',
            priority enum('low','medium','high','urgent') DEFAULT 'medium',
            assigned_to mediumint(9),
            project varchar(255),
            due_date datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY assigned_to (assigned_to),
            KEY status (status)
        ) $charset_collate;";
        
        // Backlinks table
        $table_backlinks = $wpdb->prefix . 'ch_backlinks';
        $sql_backlinks = "CREATE TABLE $table_backlinks (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            source_url varchar(500) NOT NULL,
            target_url varchar(500) NOT NULL,
            anchor_text varchar(255),
            type enum('internal','external') DEFAULT 'external',
            status enum('active','broken','removed') DEFAULT 'active',
            last_checked datetime,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type (type),
            KEY status (status)
        ) $charset_collate;";
        
        // Financial records table
        $table_financial = $wpdb->prefix . 'ch_financial';
        $sql_financial = "CREATE TABLE $table_financial (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            type enum('income','expense') NOT NULL,
            category varchar(100) NOT NULL,
            description varchar(255) NOT NULL,
            amount decimal(10,2) NOT NULL,
            currency varchar(3) DEFAULT 'BRL',
            site_id mediumint(9),
            date date NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type (type),
            KEY site_id (site_id),
            KEY date (date)
        ) $charset_collate;";
        
        // Metrics table
        $table_metrics = $wpdb->prefix . 'ch_metrics';
        $sql_metrics = "CREATE TABLE $table_metrics (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            site_id mediumint(9) NOT NULL,
            metric_type varchar(50) NOT NULL,
            metric_value varchar(255) NOT NULL,
            date date NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY site_id (site_id),
            KEY metric_type (metric_type),
            KEY date (date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        dbDelta($sql_users);
        dbDelta($sql_sites);
        dbDelta($sql_accounts);
        dbDelta($sql_leads);
        dbDelta($sql_tasks);
        dbDelta($sql_backlinks);
        dbDelta($sql_financial);
        dbDelta($sql_metrics);
        
        // Create default admin user
        self::create_default_admin();
    }
    
    private static function create_default_admin() {
        global $wpdb;
        
        $table_users = $wpdb->prefix . 'ch_users';
        
        // Check if admin already exists
        $admin_exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_users WHERE role = %s",
            'admin'
        ));
        
        if (!$admin_exists) {
            $wpdb->insert(
                $table_users,
                array(
                    'username' => 'admin',
                    'email' => 'admin@company.com',
                    'password' => password_hash('admin123', PASSWORD_DEFAULT),
                    'role' => 'admin',
                    'status' => 'active'
                ),
                array('%s', '%s', '%s', '%s', '%s')
            );
        }
    }
}