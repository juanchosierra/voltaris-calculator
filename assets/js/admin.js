(function($) {
    'use strict';

    class VoltarisAdmin {
        constructor() {
            this.initChart();
            this.bindEvents();
        }

        bindEvents() {
            // Marcar lead como contactado
            $('.mark-contacted').on('click', (e) => this.markAsContacted(e));
            
            // Manejo de notas
            $('.add-note').on('click', (e) => this.showNoteModal(e));
            $('.close').on('click', () => this.closeNoteModal());
            $('.save-note').on('click', () => this.saveNote());
            
            // Exportación
            $('#export-excel').on('click', () => this.exportData('excel'));
            $('#export-csv').on('click', () => this.exportData('csv'));
            
            // Filtros
            $('#date-range').on('change', () => this.updateDateRange());
        }

        initChart() {
            if (!$('#leads-chart').length) return;

            // Obtener datos para el gráfico
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'voltaris_get_chart_data',
                    nonce: voltarisAdmin.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.renderChart(response.data);
                    }
                }
            });
        }

        renderChart(data) {
            const ctx = document.getElementById('leads-chart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Leads por día',
                        data: data.values,
                        borderColor: '#0066cc',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        markAsContacted(e) {
            const leadId = $(e.target).data('id');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'voltaris_mark_contacted',
                    nonce: voltarisAdmin.nonce,
                    lead_id: leadId
                },
                success: (response) => {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }

        showNoteModal(e) {
            const leadId = $(e.target).data('id');
            $('#note-modal').data('lead-id', leadId).show();
        }

        closeNoteModal() {
            $('#note-modal').hide();
            $('#lead-note').val('');
        }

        saveNote() {
            const leadId = $('#note-modal').data('lead-id');
            const note = $('#lead-note').val();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'voltaris_save_note',
                    nonce: voltarisAdmin.nonce,
                    lead_id: leadId,
                    note: note
                },
                success: (response) => {
                    if (response.success) {
                        this.closeNoteModal();
                        location.reload();
                    }
                }
            });
        }

        exportData(format) {
            const filters = {
                date_from: $('[name="date_from"]').val(),
                date_to: $('[name="date_to"]').val(),
                status: $('[name="status"]').val()
            };

            // Crear un form temporal para el POST
            const $form = $('<form>', {
                action: ajaxurl,
                method: 'POST',
                style: 'display: none;'
            });

            // Agregar campos
            $form.append($('<input>', {
                type: 'hidden',
                name: 'action',
                value: 'voltaris_export_leads'
            }));

            $form.append($('<input>', {
                type: 'hidden',
                name: 'format',
                value: format
            }));

            $form.append($('<input>', {
                type: 'hidden',
                name: 'nonce',
                value: voltarisAdmin.nonce
            }));

            // Agregar filtros
            Object.entries(filters).forEach(([key, value]) => {
                if (value) {
                    $form.append($('<input>', {
                        type: 'hidden',
                        name: key,
                        value: value
                    }));
                }
            });

            // Agregar al body y enviar
            $('body').append($form);
            $form.submit();
            $form.remove();
        }

        updateDateRange() {
            const range = $('#date-range').val();
            const today = new Date();
            let fromDate = new Date();

            switch(range) {
                case '7':
                    fromDate.setDate(today.getDate() - 7);
                    break;
                case '30':
                    fromDate.setDate(today.getDate() - 30);
                    break;
                case '90':
                    fromDate.setDate(today.getDate() - 90);
                    break;
            }

            $('[name="date_from"]').val(fromDate.toISOString().split('T')[0]);
            $('[name="date_to"]').val(today.toISOString().split('T')[0]);
        }
    }

    // Inicializar cuando el documento esté listo
    $(document).ready(() => {
        new VoltarisAdmin();
    });

})(jQuery);