/**
 * Sistema de Firma Electrónica
 * Similar a Adobe Sign
 */

class SignaturePad {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) {
            console.error('Canvas no encontrado:', canvasId);
            return;
        }
        
        this.ctx = this.canvas.getContext('2d');
        this.isDrawing = false;
        this.lastX = 0;
        this.lastY = 0;
        
        // Opciones
        this.options = {
            lineWidth: options.lineWidth || 2,
            lineColor: options.lineColor || '#000000',
            backgroundColor: options.backgroundColor || '#ffffff',
            ...options
        };
        
        this.setupCanvas();
        this.bindEvents();
    }
    
    setupCanvas() {
        // Ajustar tamaño del canvas al contenedor
        const container = this.canvas.parentElement;
        const w = container ? container.clientWidth : 600;
        const h = container ? container.clientHeight : 300;
        this.canvas.width = w;
        this.canvas.height = h;
        
        // Fondo blanco
        this.ctx.fillStyle = this.options.backgroundColor;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Configurar estilo de línea
        this.ctx.strokeStyle = this.options.lineColor;
        this.ctx.lineWidth = this.options.lineWidth;
        this.ctx.lineCap = 'round';
        this.ctx.lineJoin = 'round';
    }
    
    bindEvents() {
        // Mouse events
        this.canvas.addEventListener('mousedown', (e) => this.startDrawing(e));
        this.canvas.addEventListener('mousemove', (e) => this.draw(e));
        this.canvas.addEventListener('mouseup', () => this.stopDrawing());
        this.canvas.addEventListener('mouseout', () => this.stopDrawing());
        
        // Touch events para móviles
        this.canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            this.canvas.dispatchEvent(mouseEvent);
        });
        
        this.canvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousemove', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            this.canvas.dispatchEvent(mouseEvent);
        });
        
        this.canvas.addEventListener('touchend', (e) => {
            e.preventDefault();
            const mouseEvent = new MouseEvent('mouseup', {});
            this.canvas.dispatchEvent(mouseEvent);
        });
    }
    
    startDrawing(e) {
        this.isDrawing = true;
        const rect = this.canvas.getBoundingClientRect();
        this.lastX = e.clientX - rect.left;
        this.lastY = e.clientY - rect.top;
    }
    
    draw(e) {
        if (!this.isDrawing) return;
        
        const rect = this.canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        this.ctx.beginPath();
        this.ctx.moveTo(this.lastX, this.lastY);
        this.ctx.lineTo(x, y);
        this.ctx.stroke();
        
        this.lastX = x;
        this.lastY = y;
    }
    
    stopDrawing() {
        this.isDrawing = false;
    }
    
    clear() {
        this.ctx.fillStyle = this.options.backgroundColor;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
    }
    
    isEmpty() {
        const pixelBuffer = new Uint32Array(
            this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height).data.buffer
        );
        return !pixelBuffer.some(color => color !== 0xffffffff && color !== 0);
    }
    
    toDataURL(type = 'image/png') {
        return this.canvas.toDataURL(type);
    }
    
    fromDataURL(dataURL) {
        const img = new Image();
        img.onload = () => {
            this.clear();
            this.ctx.drawImage(img, 0, 0);
        };
        img.src = dataURL;
    }
}

// Modal de Firma
class SignatureModal {
    constructor(options = {}) {
        this.options = {
            onSave: options.onSave || (() => {}),
            onCancel: options.onCancel || (() => {}),
            modalId: options.modalId || 'signatureModal_' + Date.now(),
            ...options
        };
        
        this.modal = null;
        this.signaturePad = null;
        this.currentTab = 'draw';
        this.modalId = this.options.modalId;
        this.canvasId = this.modalId + '_canvas';
        
        this.createModal();
    }
    
    createModal() {
        const modalHTML = `
            <div id="${this.modalId}" class="signature-modal" style="display: none;">
                <div class="signature-modal-overlay" onclick="window['${this.modalId}_instance'].close()"></div>
                <div class="signature-modal-content">
                    <div class="signature-modal-header">
                        <h3>Agregar Firma Electrónica</h3>
                        <button class="signature-modal-close" onclick="window['${this.modalId}_instance'].close()">&times;</button>
                    </div>
                    
                    <div class="signature-tabs">
                        <button class="signature-tab active" data-tab="draw" data-modal="${this.modalId}">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                                <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                            </svg>
                            Dibujar
                        </button>
                        <button class="signature-tab" data-tab="type" data-modal="${this.modalId}">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M4 7V4h16v3M9 20h6M12 4v16"></path>
                            </svg>
                            Escribir
                        </button>
                        <button class="signature-tab" data-tab="upload" data-modal="${this.modalId}">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            Subir
                        </button>
                    </div>
                    
                    <div class="signature-content">
                        <!-- Tab: Dibujar -->
                        <div class="signature-tab-content active" data-content="draw">
                            <div class="signature-canvas-container">
                                <canvas id="${this.canvasId}"></canvas>
                            </div>
                            <button class="signature-clear-btn" onclick="window['${this.modalId}_instance'].clearCanvas()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Limpiar
                            </button>
                        </div>
                        
                        <!-- Tab: Escribir -->
                        <div class="signature-tab-content" data-content="type">
                            <input type="text" id="${this.modalId}_text" class="signature-text-input" placeholder="Escribe tu nombre">
                            <div class="signature-font-preview" id="${this.modalId}_preview"></div>
                            <div class="signature-fonts">
                                <button class="signature-font-btn" data-font="Dancing Script" data-modal="${this.modalId}">Dancing Script</button>
                                <button class="signature-font-btn" data-font="Pacifico" data-modal="${this.modalId}">Pacifico</button>
                                <button class="signature-font-btn" data-font="Great Vibes" data-modal="${this.modalId}">Great Vibes</button>
                                <button class="signature-font-btn" data-font="Allura" data-modal="${this.modalId}">Allura</button>
                            </div>
                        </div>
                        
                        <!-- Tab: Subir -->
                        <div class="signature-tab-content" data-content="upload">
                            <div class="signature-upload-area" id="${this.modalId}_uploadArea">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <p>Arrastra una imagen aquí o haz clic para seleccionar</p>
                                <input type="file" id="${this.modalId}_upload" accept="image/*" style="display: none;">
                            </div>
                            <div class="signature-upload-preview" id="${this.modalId}_uploadPreview" style="display: none;">
                                <img id="${this.modalId}_uploadImg" src="" alt="Firma">
                            </div>
                        </div>
                    </div>
                    
                    <div class="signature-modal-footer">
                        <button class="signature-btn signature-btn-secondary" onclick="window['${this.modalId}_instance'].close()">Cancelar</button>
                        <button class="signature-btn signature-btn-primary" onclick="window['${this.modalId}_instance'].save()">Guardar Firma</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById(this.modalId);
        
        // Registrar instancia globalmente
        window[this.modalId + '_instance'] = this;
        
        this.bindEvents();
    }
    
    bindEvents() {
        // Tabs - solo para este modal
        this.modal.querySelectorAll('.signature-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                const tabName = e.currentTarget.dataset.tab;
                this.switchTab(tabName);
            });
        });
        
        // Texto de firma
        const textInput = document.getElementById(this.modalId + '_text');
        if (textInput) {
            textInput.addEventListener('input', (e) => {
                this.updateFontPreview(e.target.value);
            });
        }
        
        // Fuentes - solo para este modal
        this.modal.querySelectorAll('.signature-font-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.modal.querySelectorAll('.signature-font-btn').forEach(b => b.classList.remove('active'));
                e.currentTarget.classList.add('active');
                this.updateFontPreview(textInput.value, e.currentTarget.dataset.font);
            });
        });
        
        // Upload
        const uploadArea = document.getElementById(this.modalId + '_uploadArea');
        const uploadInput = document.getElementById(this.modalId + '_upload');
        
        if (uploadArea && uploadInput) {
            uploadArea.addEventListener('click', () => uploadInput.click());
            
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.handleImageUpload(file);
                }
            });
            
            uploadInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    this.handleImageUpload(file);
                }
            });
        }
    }
    
    switchTab(tabName) {
        this.currentTab = tabName;
        
        // Actualizar tabs - solo en este modal
        this.modal.querySelectorAll('.signature-tab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.tab === tabName);
        });
        
        // Actualizar contenido - solo en este modal
        this.modal.querySelectorAll('.signature-tab-content').forEach(content => {
            content.classList.toggle('active', content.dataset.content === tabName);
        });
    }
    
    updateFontPreview(text, font = 'Dancing Script') {
        const preview = document.getElementById(this.modalId + '_preview');
        if (preview) {
            preview.textContent = text || 'Tu firma aparecerá aquí';
            preview.style.fontFamily = `'${font}', cursive`;
        }
    }
    
    handleImageUpload(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById(this.modalId + '_uploadPreview');
            const img = document.getElementById(this.modalId + '_uploadImg');
            const area = document.getElementById(this.modalId + '_uploadArea');
            
            if (preview && img && area) {
                img.src = e.target.result;
                area.style.display = 'none';
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
    
    open() {
        this.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Inicializar o redimensionar canvas
        setTimeout(() => {
            if (!this.signaturePad) {
                this.signaturePad = new SignaturePad(this.canvasId);
            } else {
                this.signaturePad.setupCanvas();
            }
        }, 50);
    }
    
    close() {
        this.modal.style.display = 'none';
        document.body.style.overflow = '';
        this.options.onCancel();
    }
    
    clearCanvas() {
        if (this.signaturePad) {
            this.signaturePad.clear();
        }
    }
    
    save() {
        let signatureData = null;
        
        switch (this.currentTab) {
            case 'draw':
                if (this.signaturePad && !this.signaturePad.isEmpty()) {
                    signatureData = this.signaturePad.toDataURL();
                } else {
                    alert('Por favor dibuja tu firma');
                    return;
                }
                break;
                
            case 'type':
                const text = document.getElementById(this.modalId + '_text').value;
                const font = this.modal.querySelector('.signature-font-btn.active')?.dataset.font || 'Dancing Script';
                
                if (!text) {
                    alert('Por favor escribe tu nombre');
                    return;
                }
                
                signatureData = this.generateTextSignature(text, font);
                break;
                
            case 'upload':
                const img = document.getElementById(this.modalId + '_uploadImg');
                if (img && img.src && img.src.startsWith('data:')) {
                    signatureData = img.src;
                } else {
                    alert('Por favor sube una imagen de tu firma');
                    return;
                }
                break;
        }
        
        if (signatureData) {
            this.options.onSave(signatureData);
            this.close();
        }
    }
    
    generateTextSignature(text, font) {
        const canvas = document.createElement('canvas');
        canvas.width = 400;
        canvas.height = 150;
        const ctx = canvas.getContext('2d');
        
        // Fondo blanco
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Texto
        ctx.fillStyle = '#000000';
        ctx.font = `48px '${font}', cursive`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(text, canvas.width / 2, canvas.height / 2);
        
        return canvas.toDataURL();
    }
}

// Cargar fuentes de Google Fonts
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadSignatureFonts);
} else {
    loadSignatureFonts();
}

function loadSignatureFonts() {
    if (!document.querySelector('link[href*="Dancing+Script"]')) {
        const link = document.createElement('link');
        link.href = 'https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Pacifico&family=Great+Vibes&family=Allura&display=swap';
        link.rel = 'stylesheet';
        document.head.appendChild(link);
    }
}
