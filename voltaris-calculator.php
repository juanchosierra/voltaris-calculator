<?php
/**
 * Plugin Name: Calculadora EcoFlow Voltaris
 * Plugin URI: https://voltaris.co
 * Description: Calculadora inteligente para recomendar productos EcoFlow basado en necesidades energéticas
 * Version: 1.0.0
 * Author: Voltaris
 * Author URI: https://voltaris.co
 * Text Domain: voltaris-calculator
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

// Definir constantes del plugin
define('VOLTARIS_CALC_VERSION', '1.0.0');
define('VOLTARIS_CALC_PATH', plugin_dir_path(__FILE__));
define('VOLTARIS_CALC_URL', plugin_dir_url(__FILE__));
define('VOLTARIS_CALC_FILE', __FILE__);

// Cargar el autoloader
require_once VOLTARIS_CALC_PATH . 'includes/class-voltaris-calculator.php';

// Inicializar el plugin
function voltaris_calculator_init() {
    return Voltaris_Calculator::get_instance();
}
add_action('plugins_loaded', 'voltaris_calculator_init');

// Activación del plugin
register_activation_hook(__FILE__, 'voltaris_calculator_activate');
function voltaris_calculator_activate() {
    require_once VOLTARIS_CALC_PATH . 'includes/class-voltaris-activator.php';
    Voltaris_Calculator_Activator::activate();
}

// Desactivación del plugin
register_deactivation_hook(__FILE__, 'voltaris_calculator_deactivate');
function voltaris_calculator_deactivate() {
    require_once VOLTARIS_CALC_PATH . 'includes/class-voltaris-deactivator.php';
    Voltaris_Calculator_Deactivator::deactivate();
}
