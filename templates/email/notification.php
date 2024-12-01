<?php
if (!defined('ABSPATH')) exit;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recomendación de Productos EcoFlow</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h1 style="color: #333; margin-bottom: 20px;">Nueva Solicitud de Recomendación</h1>
        
        <div style="background-color: white; padding: 20px; border-radius: 4px;">
            <h2 style="color: #666; font-size: 18px;">Detalles del Cliente</h2>
            <p><strong>Nombre:</strong> <?php echo esc_html($name); ?></p>
            <p><strong>Email:</strong> <?php echo esc_html($email); ?></p>
            
            <h3 style="color: #666; font-size: 16px; margin-top: 20px;">Necesidades Energéticas</h3>
            <ul>
                <?php foreach ($appliances as $appliance): ?>
                <li><?php echo esc_html($appliance); ?></li>
                <?php endforeach; ?>
            </ul>
            
            <p><strong>Tiempo de uso necesario:</strong> <?php echo esc_html($hours); ?> horas</p>
            
            <h3 style="color: #666; font-size: 16px; margin-top: 20px;">Productos Recomendados</h3>
            <p><?php echo esc_html($recommended); ?></p>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="<?php echo esc_url(admin_url('admin.php?page=voltaris-calculator')); ?>"
               style="display: inline-block; padding: 10px 20px; background-color: #0066cc; color: white; text-decoration: none; border-radius: 4px;">
                Ver en el Panel de Administración
            </a>
        </div>
    </div>
    
    <div style="text-align: center; color: #666; font-size: 12px; margin-top: 20px;">
        <p>Este correo fue enviado desde el Recomendador de Productos Voltaris</p>
    </div>
</body>
</html>