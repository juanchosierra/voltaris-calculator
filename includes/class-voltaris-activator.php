<?php
/**
 * Funcionalidad de activación del plugin
 */

if (!defined('ABSPATH')) exit;

class Voltaris_Calculator_Activator {
    public static function activate() {
        global $wpdb;
        
        // Crear tabla para almacenar recomendaciones
        $table_name = $wpdb->prefix . 'voltaris_recommendations';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            appliances text NOT NULL,
            usage_hours int NOT NULL,
            recommended_products text NOT NULL,
            status varchar(20) DEFAULT 'new',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Crear páginas necesarias si no existen
        self::create_required_pages();

        // Establecer versión instalada
        add_option('voltaris_calculator_version', VOLTARIS_CALC_VERSION);

        // Establecer opciones por defecto
        add_option('voltaris_calculator_settings', [
            'notification_email' => get_option('admin_email'),
            'enable_whatsapp' => 'yes',
            'whatsapp_number' => '',
            'enable_email_notifications' => 'yes',
            'email_template' => 'default'
        ]);

        // Limpiar caché de permalinks
        flush_rewrite_rules();
    }

    private static function create_required_pages() {
        $pages = [
            'calculadora-ecoflow' => [
                'title' => 'Calculadora EcoFlow',
                'content' => '[voltaris_calculator]',
                'status' => 'publish'
            ]
        ];

        foreach ($pages as $slug => $page) {
            // Verificar si la página ya existe
            $page_exists = get_page_by_path($slug);
            
            if (!$page_exists) {
                wp_insert_post([
                    'post_title' => $page['title'],
                    'post_content' => $page['content'],
                    'post_status' => $page['status'],
                    'post_type' => 'page',
                    'post_name' => $slug
                ]);
            }
        }
    }
}