<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wrap voltaris-admin">
    <h1>Recomendador de Productos Voltaris</h1>

    <!-- Dashboard Stats -->
    <div class="voltaris-dashboard-stats">
        <div class="stat-box">
            <h3>Total Leads</h3>
            <span class="stat-number"><?php echo esc_html($this->get_total_leads()); ?></span>
        </div>
        <div class="stat-box">
            <h3>Nuevos</h3>
            <span class="stat-number"><?php echo esc_html($this->get_new_leads()); ?></span>
        </div>
        <div class="stat-box">
            <h3>Contactados</h3>
            <span class="stat-number"><?php echo esc_html($this->get_contacted_leads()); ?></span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="voltaris-filters">
        <form method="get">
            <input type="hidden" name="page" value="voltaris-calculator">
            
            <div class="filter-group">
                <label>Rango de Fechas:</label>
                <input type="date" name="date_from" value="<?php echo esc_attr($_GET['date_from'] ?? ''); ?>">
                <span>hasta</span>
                <input type="date" name="date_to" value="<?php echo esc_attr($_GET['date_to'] ?? ''); ?>">
            </div>

            <div class="filter-group">
                <label>Estado:</label>
                <select name="status">
                    <option value="">Todos</option>
                    <option value="new" <?php selected($_GET['status'] ?? '', 'new'); ?>>Nuevos</option>
                    <option value="contacted" <?php selected($_GET['status'] ?? '', 'contacted'); ?>>Contactados</option>
                </select>
            </div>

            <button type="submit" class="button">Filtrar</button>
            <a href="?page=voltaris-calculator" class="button">Limpiar Filtros</a>
        </form>
    </div>

    <!-- Lista de Leads -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Electrodomésticos</th>
                <th>Horas de Uso</th>
                <th>Productos Recomendados</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->get_leads() as $lead): ?>
            <tr>
                <td><?php echo esc_html(date('d/m/Y H:i', strtotime($lead->created_at))); ?></td>
                <td><?php echo esc_html($lead->name); ?></td>
                <td>
                    <a href="mailto:<?php echo esc_attr($lead->email); ?>">
                        <?php echo esc_html($lead->email); ?>
                    </a>
                </td>
                <td><?php echo esc_html($lead->appliances); ?></td>
                <td><?php echo esc_html($lead->usage_hours); ?></td>
                <td><?php echo esc_html($lead->recommended_products); ?></td>
                <td>
                    <span class="status-badge status-<?php echo esc_attr($lead->status); ?>">
                        <?php echo esc_html(ucfirst($lead->status)); ?>
                    </span>
                </td>
                <td>
                    <button class="button mark-contacted" data-id="<?php echo esc_attr($lead->id); ?>">
                        Marcar como Contactado
                    </button>
                    <button class="button add-note" data-id="<?php echo esc_attr($lead->id); ?>">
                        Agregar Nota
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <?php echo $this->get_pagination_links(); ?>

    <!-- Template para el modal de notas -->
    <div id="note-modal" class="voltaris-modal" style="display: none;">
        <div class="voltaris-modal-content">
            <span class="close">&times;</span>
            <h3>Agregar Nota</h3>
            <textarea id="lead-note" rows="4" class="widefat"></textarea>
            <button class="button button-primary save-note">Guardar Nota</button>
        </div>
    </div>
</div>