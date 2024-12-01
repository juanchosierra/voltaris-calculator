<?php
/**
 * Clase principal del plugin
 */

if (!defined('ABSPATH')) exit;

class Voltaris_Calculator {
    private static $instance = null;
    private $products_data = [
        'RIVER 2' => ['price' => 1399900, 'capacity' => 256],
        'RIVER 2 MAX' => ['price' => 2399900, 'capacity' => 512],
        'RIVER 2 PRO' => ['price' => 3299900, 'capacity' => 768],
        'DELTA 2' => ['price' => 4599900, 'capacity' => 1024],
        'DELTA 2 MAX' => ['price' => 8999900, 'capacity' => 2048],
        'DELTA PRO' => ['price' => 16999900, 'capacity' => 3600],
        'DELTA PRO 3' => ['price' => 20999900, 'capacity' => 5400],
        'DELTA PRO ULTRA' => ['price' => 27999900, 'capacity' => 7200]
    ];

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        // Hooks principales
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // Registrar shortcode
        add_shortcode('voltaris_calculator', [$this, 'render_calculator']);

        // AJAX handlers
        add_action('wp_ajax_voltaris_calculate', [$this, 'ajax_calculate']);
        add_action('wp_ajax_nopriv_voltaris_calculate', [$this, 'ajax_calculate']);
        add_action('wp_ajax_voltaris_save_lead', [$this, 'ajax_save_lead']);
        add_action('wp_ajax_nopriv_voltaris_save_lead', [$this, 'ajax_save_lead']);
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'voltaris-calculator',
            VOLTARIS_CALC_URL . 'assets/css/calculator.css',
            [],
            VOLTARIS_CALC_VERSION
        );

        wp_enqueue_script(
            'voltaris-calculator',
            VOLTARIS_CALC_URL . 'assets/js/calculator.js',
            ['jquery'],
            VOLTARIS_CALC_VERSION,
            true
        );

        wp_localize_script('voltaris-calculator', 'voltarisData', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('voltaris_calculator'),
            'products' => $this->products_data
        ]);
    }

    public function admin_enqueue_scripts($hook) {
        if ('toplevel_page_voltaris-calculator' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'voltaris-admin',
            VOLTARIS_CALC_URL . 'assets/css/admin.css',
            [],
            VOLTARIS_CALC_VERSION
        );

        wp_enqueue_script(
            'voltaris-admin',
            VOLTARIS_CALC_URL . 'assets/js/admin.js',
            ['jquery'],
            VOLTARIS_CALC_VERSION,
            true
        );
    }

    public function add_admin_menu() {
        add_menu_page(
            'Recomendador Voltaris',
            'Recomendador Voltaris',
            'manage_options',
            'voltaris-calculator',
            [$this, 'render_admin_page'],
            'dashicons-calculator',
            30
        );
    }

    public function render_calculator() {
        ob_start();
        require_once VOLTARIS_CALC_PATH . 'templates/calculator.php';
        return ob_get_clean();
    }

    public function render_admin_page() {
        require_once VOLTARIS_CALC_PATH . 'templates/admin/dashboard.php';
    }

    public function ajax_calculate() {
        check_ajax_referer('voltaris_calculator', 'nonce');

        $appliances = isset($_POST['appliances']) ? array_map('sanitize_text_field', $_POST['appliances']) : [];
        $hours = isset($_POST['hours']) ? intval($_POST['hours']) : 0;

        $total_watts = 0;
        foreach ($appliances as $appliance) {
            // Aquí agregaríamos la lógica para calcular el consumo de cada electrodoméstico
            $total_watts += 100; // Ejemplo
        }

        $total_watt_hours = $total_watts * $hours;
        $recommended_products = $this->get_recommended_products($total_watt_hours);

        wp_send_json_success([
            'watt_hours' => $total_watt_hours,
            'recommendations' => $recommended_products
        ]);
    }

    public function ajax_save_lead() {
        check_ajax_referer('voltaris_calculator', 'nonce');

        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $appliances = isset($_POST['appliances']) ? array_map('sanitize_text_field', $_POST['appliances']) : [];
        $hours = intval($_POST['hours'] ?? 0);
        $recommended = sanitize_text_field($_POST['recommended'] ?? '');

        global $wpdb;
        $inserted = $wpdb->insert(
            $wpdb->prefix . 'voltaris_recommendations',
            [
                'name' => $name,
                'email' => $email,
                'appliances' => json_encode($appliances),
                'usage_hours' => $hours,
                'recommended_products' => $recommended
            ],
            ['%s', '%s', '%s', '%d', '%s']
        );

        if ($inserted) {
            // Enviar correo de notificación
            $this->send_notification_email($name, $email, $recommended);
            wp_send_json_success(['message' => 'Lead guardado correctamente']);
        } else {
            wp_send_json_error(['message' => 'Error al guardar el lead']);
        }
    }

    private function get_recommended_products($watt_hours) {
        $recommendations = [];
        foreach ($this->products_data as $name => $data) {
            if ($data['capacity'] >= $watt_hours) {
                $recommendations[] = [
                    'name' => $name,
                    'price' => $data['price'],
                    'capacity' => $data['capacity']
                ];
            }
        }
        
        // Ordenar por capacidad (menor a mayor)
        usort($recommendations, function($a, $b) {
            return $a['capacity'] - $b['capacity'];
        });

        // Devolver máximo 3 recomendaciones
        return array_slice($recommendations, 0, 3);
    }

    private function send_notification_email($name, $email, $recommended) {
        $to = 'ventas@voltaris.co';
        $subject = 'Nueva solicitud de recomendación - Voltaris';
        
        ob_start();
        include VOLTARIS_CALC_PATH . 'templates/email/notification.php';
        $message = ob_get_clean();

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        
        wp_mail($to, $subject, $message, $headers);
    }
}