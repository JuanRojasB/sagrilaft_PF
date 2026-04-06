/**
 * Autocompletado automático de direcciones
 * Usa múltiples APIs para mejor cobertura
 */

function initAddressAutocomplete(addressInputId, cityInputId) {
    const direccionInput = document.getElementById(addressInputId);
    const ciudadInput = document.getElementById(cityInputId);
    
    if (!direccionInput) return;

    // Crear wrapper para el input y sugerencias
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'position: relative;';
    direccionInput.parentNode.insertBefore(wrapper, direccionInput);
    wrapper.appendChild(direccionInput);
    
    // Contenedor de botones auxiliares
    const buttonsContainer = document.createElement('div');
    buttonsContainer.style.cssText = `
        display: flex;
        gap: 8px;
        margin-top: 8px;
    `;
    
    // Botón GPS (opcional, fuera del input)
    const btnGPS = document.createElement('button');
    btnGPS.type = 'button';
    btnGPS.className = 'btn-gps-location';
    btnGPS.innerHTML = `
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 2v4m0 12v4M2 12h4m12 0h4m-6 0a6 6 0 1 1-12 0 6 6 0 0 1 12 0z"/>
        </svg>
        <span>Usar mi ubicación</span>
    `;
    btnGPS.title = 'Detectar mi ubicación actual (GPS)';
    btnGPS.style.cssText = `
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--bg-tertiary, #334155);
        color: var(--text-secondary, #cbd5e1);
        border: 1px solid var(--border-primary, #475569);
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.2s;
    `;
    btnGPS.addEventListener('mouseover', () => {
        btnGPS.style.background = 'var(--bg-hover, #475569)';
        btnGPS.style.borderColor = 'var(--border-accent, #64748b)';
    });
    btnGPS.addEventListener('mouseout', () => {
        btnGPS.style.background = 'var(--bg-tertiary, #334155)';
        btnGPS.style.borderColor = 'var(--border-primary, #475569)';
    });
    
    buttonsContainer.appendChild(btnGPS);
    wrapper.appendChild(buttonsContainer);
    
    // Crear contenedor de sugerencias
    const suggestionsDiv = document.createElement('div');
    suggestionsDiv.className = 'address-suggestions';
    wrapper.appendChild(suggestionsDiv);
    
    // Agregar hint informativo
    const hintDiv = document.createElement('div');
    hintDiv.style.cssText = `
        color: var(--text-placeholder, #64748b);
        font-size: 11px;
        margin-top: 6px;
    `;
    hintDiv.textContent = 'Escribe tu dirección para ver sugerencias, o usa el botón GPS';
    wrapper.appendChild(hintDiv);
    
    // Evento: Detectar ubicación con GPS
    btnGPS.addEventListener('click', async function() {
        if (!navigator.geolocation) {
            showNotification('Tu navegador no soporta geolocalización', 'error');
            return;
        }
        
        const originalHTML = btnGPS.innerHTML;
        btnGPS.innerHTML = `
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 6v6l4 2"/>
            </svg>
            <span>Detectando...</span>
        `;
        btnGPS.disabled = true;
        btnGPS.style.opacity = '0.6';
        
        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            });
            
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            
            // Usar proxy PHP para evitar problemas de CORS
            const response = await fetch(
                `/gestion-sagrilaft/public/api/geocode.php?action=reverse&lat=${lat}&lon=${lon}`
            );
            
            if (!response.ok) {
                throw new Error('Error al obtener la dirección');
            }
            
            const data = await response.json();
            
            if (data.display_name) {
                direccionInput.value = data.display_name;
                direccionInput.focus();
                
                if (ciudadInput && data.address) {
                    const city = data.address.city || 
                                data.address.town || 
                                data.address.municipality || 
                                data.address.county || '';
                    if (city) ciudadInput.value = city;
                }
                
                showNotification('✓ Ubicación detectada. Puedes editarla si es necesario', 'success');
            } else {
                throw new Error('No se pudo obtener la dirección');
            }
            
        } catch (error) {
            console.error('Error:', error);
            let message = 'No se pudo detectar tu ubicación';
            if (error.code === 1) {
                message = 'Debes permitir el acceso a tu ubicación en el navegador';
            } else if (error.code === 2) {
                message = 'No se pudo determinar tu ubicación. Escribe tu dirección manualmente';
            } else if (error.code === 3) {
                message = 'Tiempo de espera agotado. Intenta escribir tu dirección';
            }
            showNotification(message, 'error');
            direccionInput.focus();
        } finally {
            btnGPS.innerHTML = originalHTML;
            btnGPS.disabled = false;
            btnGPS.style.opacity = '1';
        }
    });
    
    // Autocompletado mientras escribe (usando múltiples APIs)
    let debounceTimer;
    direccionInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(debounceTimer);
        
        if (query.length < 3) {
            suggestionsDiv.classList.remove('active');
            return;
        }

        suggestionsDiv.innerHTML = '<div class="address-loading">Buscando...</div>';
        suggestionsDiv.classList.add('active');

        debounceTimer = setTimeout(async () => {
            try {
                // Usar proxy PHP para evitar problemas de CORS
                const response = await fetch(
                    `/gestion-sagrilaft/public/api/geocode.php?action=search&q=${encodeURIComponent(query)}`
                );
                
                if (!response.ok) {
                    throw new Error('Error al buscar direcciones');
                }
                
                const data = await response.json();
                
                if (!data.features || data.features.length === 0) {
                    suggestionsDiv.innerHTML = '<div class="address-loading">No se encontraron resultados</div>';
                    return;
                }
                
                suggestionsDiv.innerHTML = '';
                data.features.slice(0, 8).forEach(feature => {
                    const props = feature.properties;
                    const address = buildAddress(props);
                    const city = props.city || props.town || props.municipality || '';
                    
                    const item = document.createElement('div');
                    item.className = 'address-suggestion-item';
                    
                    const parts = address.split(',');
                    const main = parts[0];
                    
                    item.innerHTML = `
                        <div class="address-suggestion-main">${main}</div>
                        <div class="address-suggestion-detail">${address}</div>
                    `;
                    
                    item.addEventListener('click', () => {
                        direccionInput.value = address;
                        if (ciudadInput && city) {
                            ciudadInput.value = city;
                        }
                        suggestionsDiv.classList.remove('active');
                    });
                    
                    suggestionsDiv.appendChild(item);
                });
                
            } catch (error) {
                console.error('Error:', error);
                suggestionsDiv.innerHTML = '<div class="address-loading">Error al buscar</div>';
            }
        }, 500);
    });
    
    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            suggestionsDiv.classList.remove('active');
        }
    });
}

// Función auxiliar para construir dirección desde propiedades
function buildAddress(props) {
    let parts = [];
    if (props.street) parts.push(props.street);
    if (props.housenumber) parts.push(props.housenumber);
    if (props.name && !props.street) parts.push(props.name);
    if (props.city) parts.push(props.city);
    if (props.state) parts.push(props.state);
    if (props.country) parts.push(props.country);
    return parts.join(', ');
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        z-index: 10001;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease-out;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}

// Agregar animaciones CSS
if (!document.getElementById('address-autocomplete-animations')) {
    const style = document.createElement('style');
    style.id = 'address-autocomplete-animations';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
}
