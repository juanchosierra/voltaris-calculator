<?php
/**
 * Funcionalidad de desactivación del plugin
 */

if (!defined('ABSPATH')) exit;

class Voltaris_Calculator_Deactivator {
    public static function deactivate() {
        // No eliminamos la tabla de datos por seguridad
        // Solo limpiamos caches y reglas de reescritura

        // Limpiar caché de permalinks
        flush_rewrite_rules();

        // Limpiar cualquier caché de transients
        delete_transient('voltaris_calculator_cache');

        // Registrar tiempo de desactivación
        update_option('voltaris_calculator_deactivated', current_time('mysql'));

        // Desregistrar cron jobs si existen
        wp_clear_scheduled_hook('voltaris_daily_maintenance');
        
        // Limpiar roles y capacidades personalizadas
        self::clean_capabilities();
    }

    private static function clean_capabilities() {
        global $wp_roles;
        
        if (!class_exists('WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Eliminar capacidades personalizadas
        $capabilities = [
            'manage_voltaris_calculator',
            'view_voltaris_reports'
        ];

        foreach ($capabilities as $cap) {
            // Eliminar de todos los roles
            foreach ($wp_roles->roles as $role => $details) {
                $wp_roles->remove_cap($role, $cap);
            }
        }
    }
}