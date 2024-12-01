<?php
if (!defined('ABSPATH')) exit;
?>
<div id="voltaris-calculator" class="voltaris-calculator-wrapper">
    <!-- Paso 1: Nombre -->
    <div class="calculator-step" data-step="1">
        <h2>¡Bienvenido! 👋</h2>
        <p>¿Cómo te llamas?</p>
        <div class="form-group">
            <input type="text" id="voltaris-name" class="voltaris-input" placeholder="Tu nombre" required>
        </div>
        <button class="voltaris-button next-step">Siguiente</button>
    </div>

    <!-- Paso 2: Electrodomésticos -->
    <div class="calculator-step" data-step="2" style="display: none;">
        <h2><span class="user-name"></span>, ¿qué electrodomésticos necesitas alimentar?</h2>
        <div class="appliances-grid">
            <div class="appliance-item" data-watts="200">
                <span class="appliance-icon">🧊</span>
                <span class="appliance-name">Nevera</span>
                <span class="appliance-watts">200W</span>
            </div>
            <div class="appliance-item" data-watts="100">
                <span class="appliance-icon">📺</span>
                <span class="appliance-name">Televisor</span>
                <span class="appliance-watts">100W</span>
            </div>
            <div class="appliance-item" data-watts="50">
                <span class="appliance-icon">💡</span>
                <span class="appliance-name">5 Bombillos LED</span>
                <span class="appliance-watts">50W</span>
            </div>
            <div class="appliance-item" data-watts="20">
                <span class="appliance-icon">📡</span>
                <span class="appliance-name">Router de Internet</span>
                <span class="appliance-watts">20W</span>
            </div>
            <div class="appliance-item" data-watts="100">
                <span class="appliance-icon">🛰️</span>
                <span class="appliance-name">Starlink</span>
                <span class="appliance-watts">100W</span>
            </div>
            <div class="appliance-item" data-watts="75">
                <span class="appliance-icon">💨</span>
                <span class="appliance-name">Ventilador</span>
                <span class="appliance-watts">75W</span>
            </div>
        </div>
        <div class="total-consumption">
            Consumo total: <span id="total-watts">0</span>W
        </div>
        <div class="step-buttons">
            <button class="voltaris-button prev-step">Anterior</button>
            <button class="voltaris-button next-step">Siguiente</button>
        </div>
    </div>

    <!-- Paso 3: Tiempo de uso -->
    <div class="calculator-step" data-step="3" style="display: none;">
        <h2>¿Por cuánto tiempo necesitas energía?</h2>
        <div class="time-options">
            <div class="time-option" data-hours="3">
                <span class="time-icon">⏱️</span>
                <span class="time-text">2 a 3 horas</span>
            </div>
            <div class="time-option" data-hours="5">
                <span class="time-icon">⏱️</span>
                <span class="time-text">3 a 5 horas</span>
            </div>
            <div class="time-option" data-hours="8">
                <span class="time-icon">⏱️</span>
                <span class="time-text">5 a 8 horas</span>
            </div>
            <div class="time-option" data-hours="12">
                <span class="time-icon">⏱️</span>
                <span class="time-text">Más de 8 horas</span>
            </div>
        </div>
        <div class="step-buttons">
            <button class="voltaris-button prev-step">Anterior</button>
            <button class="voltaris-button next-step">Ver recomendaciones</button>
        </div>
    </div>

    <!-- Paso 4: Recomendaciones -->
    <div class="calculator-step" data-step="4" style="display: none;">
        <h2><span class="user-name"></span>, estas son tus opciones recomendadas:</h2>
        <div class="recommendations-grid">
            <!-- Las recomendaciones se llenarán dinámicamente con JS -->
        </div>
        <div class="email-form">
            <p>¿Quieres recibir estas recomendaciones por correo?</p>
            <input type="email" id="voltaris-email" class="voltaris-input" placeholder="Tu correo electrónico">
            <button class="voltaris-button send-recommendations">Recibir información</button>
        </div>
        <div class="step-buttons">
            <button class="voltaris-button prev-step">Anterior</button>
        </div>
    </div>
</div>