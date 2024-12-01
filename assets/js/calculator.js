(function($) {
    'use strict';

    class VoltarisCalculator {
        constructor() {
            this.currentStep = 1;
            this.totalSteps = 4;
            this.userData = {
                name: '',
                appliances: [],
                hours: 0,
                email: ''
            };

            this.init();
        }

        init() {
            this.bindEvents();
            this.showStep(1);
        }

        bindEvents() {
            // Navegación entre pasos
            $('.next-step').on('click', () => this.nextStep());
            $('.prev-step').on('click', () => this.prevStep());

            // Selección de electrodomésticos
            $('.appliance-item').on('click', (e) => this.toggleAppliance(e));

            // Selección de tiempo
            $('.time-option').on('click', (e) => this.selectTime(e));

            // Envío de recomendaciones
            $('.send-recommendations').on('click', () => this.sendRecommendations());

            // Input de nombre
            $('#voltaris-name').on('change', (e) => {
                this.userData.name = e.target.value;
                $('.user-name').text(this.userData.name);
            });
        }

        showStep(step) {
            $('.calculator-step').hide();
            $(`[data-step="${step}"]`).show();
        }

        nextStep() {
            if (this.validateCurrentStep()) {
                if (this.currentStep === 3) {
                    this.calculateRecommendations();
                } else {
                    this.currentStep = Math.min(this.currentStep + 1, this.totalSteps);
                    this.showStep(this.currentStep);
                }
            }
        }

        prevStep() {
            this.currentStep = Math.max(this.currentStep - 1, 1);
            this.showStep(this.currentStep);
        }

        validateCurrentStep() {
            switch(this.currentStep) {
                case 1:
                    return this.userData.name.length > 0;
                case 2:
                    return this.userData.appliances.length > 0;
                case 3:
                    return this.userData.hours > 0;
                default:
                    return true;
            }
        }

        toggleAppliance(e) {
            const $item = $(e.currentTarget);
            $item.toggleClass('selected');
            
            const watts = parseInt($item.data('watts'));
            const name = $item.find('.appliance-name').text();
            
            if ($item.hasClass('selected')) {
                this.userData.appliances.push({ name, watts });
            } else {
                this.userData.appliances = this.userData.appliances.filter(a => a.name !== name);
            }
            
            this.updateTotalWatts();
        }

        selectTime(e) {
            const $option = $(e.currentTarget);
            $('.time-option').removeClass('selected');
            $option.addClass('selected');
            this.userData.hours = parseInt($option.data('hours'));
        }

        updateTotalWatts() {
            const total = this.userData.appliances.reduce((sum, app) => sum + app.watts, 0);
            $('#total-watts').text(total);
        }

        calculateRecommendations() {
            const totalWatts = this.userData.appliances.reduce((sum, app) => sum + app.watts, 0);
            const wattHours = totalWatts * this.userData.hours;

            $.ajax({
                url: voltarisData.ajax_url,
                type: 'POST',
                data: {
                    action: 'voltaris_calculate',
                    nonce: voltarisData.nonce,
                    watts: totalWatts,
                    hours: this.userData.hours
                },
                success: (response) => {
                    if (response.success) {
                        this.displayRecommendations(response.data.recommendations);
                        this.currentStep = 4;
                        this.showStep(4);
                    }
                }
            });
        }

        displayRecommendations(recommendations) {
            const $grid = $('.recommendations-grid');
            $grid.empty();

            recommendations.forEach(product => {
                $grid.append(`
                    <div class="product-card">
                        <h3>${product.name}</h3>
                        <div class="product-capacity">Capacidad: ${product.capacity}Wh</div>
                        <div class="product-price">$${new Intl.NumberFormat('es-CO').format(product.price)}</div>
                        <button class="voltaris-button view-product" data-product="${product.name}">
                            Ver detalles
                        </button>
                    </div>
                `);
            });
        }

        sendRecommendations() {
            const email = $('#voltaris-email').val();
            if (!email) return;

            $.ajax({
                url: voltarisData.ajax_url,
                type: 'POST',
                data: {
                    action: 'voltaris_save_lead',
                    nonce: voltarisData.nonce,
                    name: this.userData.name,
                    email: email,
                    appliances: this.userData.appliances,
                    hours: this.userData.hours,
                    recommended: $('.recommendations-grid').html()
                },
                success: (response) => {
                    if (response.success) {
                        alert('¡Te hemos enviado las recomendaciones por correo!');
                    }
                }
            });
        }
    }

    // Inicializar cuando el documento esté listo
    $(document).ready(() => {
        new VoltarisCalculator();
    });

})(jQuery);