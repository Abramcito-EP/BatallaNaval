<template>
    <div class="game-background min-h-screen text-gray-100 relative overflow-hidden flex justify-center items-center">
        <!-- Efectos de partículas y olas en el fondo -->
        <div id="particles-js" class="absolute inset-0 z-0 opacity-40"></div>
        <div class="wave-container absolute inset-0 z-0">
            <div class="wave wave1"></div>
            <div class="wave wave2"></div>
            <div class="wave wave3"></div>
        </div>

        <!-- Contenedor principal -->
        <div class="relative z-10 w-full max-w-md px-6 py-8">
            <Head title="Registro - Batalla Naval" />
            
            <!-- Logo del juego -->
            <div class="text-center mb-8">
                <a href="/" class="inline-block">
                    <div class="game-logo text-3xl md:text-4xl font-bold">
                        <span class="text-yellow-400 glow-text">BATALLA</span> 
                        <span class="text-blue-300 glow-text">NAVAL</span>
                    </div>
                </a>
            </div>
            
            <!-- Panel de registro -->
            <div class="game-panel">
                <div class="panel-header">
                    <h2 class="text-xl font-bold text-center">NUEVO RECLUTA</h2>
                </div>
                
                <div class="panel-content">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="name" class="game-label">NOMBRE DE ALMIRANTE</label>
                            <input
                                id="name"
                                type="text"
                                class="game-input"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Almirante Smith"
                            />
                            <div v-if="form.errors.name" class="game-error">{{ form.errors.name }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="game-label">CÓDIGO DE IDENTIFICACIÓN</label>
                            <input
                                id="email"
                                type="email"
                                class="game-input"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="correo@ejemplo.com"
                            />
                            <div v-if="form.errors.email" class="game-error">{{ form.errors.email }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="game-label">CÓDIGO DE ACCESO</label>
                            <input
                                id="password"
                                type="password"
                                class="game-input"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <div v-if="form.errors.password" class="game-error">{{ form.errors.password }}</div>
                        </div>

                        <div class="mb-5">
                            <label for="password_confirmation" class="game-label">CONFIRMAR CÓDIGO</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                class="game-input"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <div v-if="form.errors.password_confirmation" class="game-error">
                                {{ form.errors.password_confirmation }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mt-6">
                            <button
                                type="submit"
                                class="menu-button"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                :disabled="form.processing"
                                @mouseenter="playHoverSound"
                                @click="playClickSound"
                            >
                                REGISTRAR RECLUTA
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="panel-footer flex justify-between items-center">
                    <a
                        href="/"
                        class="game-button secondary xsmall"
                        @mouseenter="playHoverSound"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        VOLVER
                    </a>
                    
                    <Link
                        :href="route('login')"
                        class="game-button secondary xsmall"
                        @mouseenter="playHoverSound"
                    >
                        YA TENGO CUENTA
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

export default {
    name: 'Register',
    components: {
        Head,
        Link,
    },
    setup() {
        const form = useForm({
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        });

        const hoverSound = ref(null);
        const clickSound = ref(null);

        const playHoverSound = () => {
            if (hoverSound.value) {
                hoverSound.value.currentTime = 0;
                hoverSound.value.play().catch(e => console.log('Audio play error:', e));
            }
        };

        const playClickSound = () => {
            if (clickSound.value) {
                clickSound.value.currentTime = 0;
                clickSound.value.play().catch(e => console.log('Audio play error:', e));
            }
        };

        const submit = () => {
            form.post(route('register'), {
                onFinish: () => form.reset('password', 'password_confirmation'),
            });
        };

        onMounted(() => {
            // Inicializar sonidos
            try {
                hoverSound.value = new Audio('/sounds/hover.mp3');
                hoverSound.value.volume = 0.2;
                clickSound.value = new Audio('/sounds/click.mp3');
                clickSound.value.volume = 0.3;
            } catch (e) {
                console.log('Audio initialization error:', e);
            }

            // Inicializar particles.js si está disponible
            if (typeof particlesJS !== 'undefined') {
                try {
                    particlesJS("particles-js", {
                        particles: {
                            number: {
                                value: 60,
                                density: {
                                    enable: true,
                                    value_area: 800
                                }
                            },
                            color: {
                                value: "#ffffff"
                            },
                            shape: {
                                type: "circle",
                            },
                            opacity: {
                                value: 0.5,
                                random: true,
                            },
                            size: {
                                value: 3,
                                random: true,
                            },
                            line_linked: {
                                enable: true,
                                distance: 150,
                                color: "#2a85ff",
                                opacity: 0.2,
                                width: 1
                            },
                            move: {
                                enable: true,
                                speed: 2,
                                direction: "none",
                                random: false,
                                straight: false,
                                out_mode: "out",
                                bounce: false,
                            }
                        },
                        interactivity: {
                            detect_on: "canvas",
                            events: {
                                onhover: {
                                    enable: true,
                                    mode: "bubble"
                                },
                                onclick: {
                                    enable: true,
                                    mode: "push"
                                },
                                resize: true
                            },
                        },
                        retina_detect: true
                    });
                } catch (e) {
                    console.log('ParticleJS initialization error:', e);
                }
            }
        });

        return {
            form,
            submit,
            playHoverSound,
            playClickSound
        };
    }
};
</script>

<style>
/* Estilos de videojuego */
:root {
    --navy-900: #0a192f;
    --navy-800: #112240;
    --navy-700: #1d3557;
    --blue-600: #1e40af;
    --blue-500: #2a85ff;
    --blue-400: #4895ef;
    --blue-300: #90e0ef;
    --gold: #ffd700;
    --orange: #FF7D00;
}

/* Fondos y estilos generales */
.game-background {
    background-color: #0c182c;
    background-image: 
        radial-gradient(circle at 25% 25%, rgba(42, 133, 255, 0.1) 0%, transparent 50%), 
        radial-gradient(circle at 75% 75%, rgba(255, 215, 0, 0.05) 0%, transparent 50%);
}

/* Texto brillante */
.glow-text {
    text-shadow: 0 0 15px currentColor, 0 0 5px currentColor;
}

/* Panel principal con estilo de juego */
.game-panel {
    background-color: rgba(10, 25, 47, 0.8);
    border: 1px solid #3b82f6;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.3),
                inset 0 0 10px rgba(59, 130, 246, 0.1);
    overflow: hidden;
    backdrop-filter: blur(8px);
    position: relative;
}

.panel-header {
    background: linear-gradient(to right, #1e3a8a, #1e40af, #1e3a8a);
    color: #90e0ef;
    padding: 1rem;
    border-bottom: 1px solid #3b82f6;
    text-shadow: 0 0 10px rgba(144, 224, 239, 0.5);
    position: relative;
}

.panel-header::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    height: 1px;
    bottom: 0;
    background: linear-gradient(to right, transparent, rgba(144, 224, 239, 0.7), transparent);
}

.panel-content {
    padding: 1.5rem;
}

.panel-footer {
    padding: 1rem 1.5rem;
    background-color: rgba(0, 0, 0, 0.3);
    border-top: 1px solid rgba(59, 130, 246, 0.2);
}

/* Etiquetas e inputs */
.game-label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: #90e0ef;
    letter-spacing: 1px;
}

.game-input {
    width: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(59, 130, 246, 0.4);
    border-radius: 4px;
    padding: 0.75rem;
    color: white;
    transition: all 0.2s;
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}

.game-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    background-color: rgba(0, 0, 0, 0.5);
}

.game-error {
    font-size: 0.8rem;
    color: #f87171;
    margin-top: 0.5rem;
    padding-left: 0.5rem;
    border-left: 2px solid #f87171;
}

/* Checkbox personalizado con estilo de juego */
.game-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    background-color: rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(59, 130, 246, 0.4);
    border-radius: 3px;
    display: inline-block;
    position: relative;
    margin-right: 8px;
    transition: all 0.2s;
}

.checkbox-custom::after {
    content: '';
    position: absolute;
    display: none;
    left: 5px;
    top: 2px;
    width: 6px;
    height: 10px;
    border: solid #3b82f6;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

input[type="checkbox"]:checked ~ .checkbox-custom {
    background-color: rgba(59, 130, 246, 0.2);
    border-color: #3b82f6;
}

input[type="checkbox"]:checked ~ .checkbox-custom::after {
    display: block;
}

.checkbox-label {
    font-size: 0.85rem;
    color: #e2e8f0;
}

/* Alerta de estado con estilo de juego */
.game-alert {
    padding: 0.75rem 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    position: relative;
    border: 1px solid;
}

.game-alert.success {
    background-color: rgba(16, 185, 129, 0.1);
    border-color: rgba(16, 185, 129, 0.4);
    color: #10b981;
}

/* Animación de scanline para efecto de pantalla */
.game-panel::before {
    content: '';
    position: absolute;
    top: 0;
    width: 100%;
    height: 2px;
    background: rgba(59, 130, 246, 0.3);
    animation: scanline 6s linear infinite;
    z-index: 10;
    pointer-events: none;
}

@keyframes scanline {
    0% {
        top: 0%;
    }
    100% {
        top: 100%;
    }
}

/* Botones del menú principal - mejorados para responsive */
.menu-button {
    padding: 10px;
    background: linear-gradient(to bottom, #1e40af, #1e3a8a);
    border: 1px solid #3b82f6;
    border-radius: 4px;
    color: white;
    font-size: 1rem;
    font-weight: bold;
    letter-spacing: 1px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    transition: all 0.2s;
    box-shadow: 
        0 4px 6px rgba(0, 0, 0, 0.2),
        0 0 15px rgba(59, 130, 246, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
    width: 100%;
}

@media (min-width: 640px) {
    .menu-button {
        padding: 12px;
        font-size: 1.1rem;
        letter-spacing: 2px;
    }
}

.menu-button::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg, 
        transparent, 
        rgba(255, 255, 255, 0.2), 
        transparent
    );
    transition: left 0.7s;
}

.menu-button:hover {
    transform: translateY(-3px);
    box-shadow: 
        0 6px 10px rgba(0, 0, 0, 0.3),
        0 0 20px rgba(59, 130, 246, 0.6),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.menu-button:hover::before {
    left: 100%;
}

.menu-button:active {
    transform: translateY(1px);
    box-shadow: 
        0 2px 3px rgba(0, 0, 0, 0.2),
        0 0 10px rgba(59, 130, 246, 0.3);
}

/* Botones pequeños de juego */
.game-button {
    position: relative;
    background: linear-gradient(to bottom, #1e3a8a, #172554);
    border: 1px solid #3b82f6;
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.3), 
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
    color: #90e0ef;
    font-weight: bold;
    text-transform: uppercase;
    border-radius: 4px;
    transition: all 0.2s ease;
    overflow: hidden;
    padding: 0.5rem 1rem;
    letter-spacing: 1px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.game-button:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 50%;
    background: rgba(255, 255, 255, 0.1);
    pointer-events: none;
}

.game-button:hover {
    background: linear-gradient(to bottom, #2563eb, #1e40af);
    transform: translateY(-2px);
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.5), 
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.game-button:active {
    transform: translateY(1px);
    box-shadow: 0 0 5px rgba(59, 130, 246, 0.3);
}

.game-button.secondary {
    background: linear-gradient(to bottom, #475569, #334155);
    border-color: #64748b;
}

.game-button.secondary:hover {
    background: linear-gradient(to bottom, #64748b, #475569);
}

.game-button.xsmall {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Olas animadas */
.wave-container {
    overflow: hidden;
    pointer-events: none;
}

.wave {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 200%;
    height: 100px;
    background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg"><path d="M0 0v46.29c47.79 22.2 103.59 32.17 158 28 70.36-5.37 136.33-33.31 206.8-37.5 73.84-4.36 147.54 16.88 218.2 35.26 69.27 18 138.3 24.88 209.4 13.08 36.15-6 69.85-17.84 104.45-29.34C989.49 25 1113-14.29 1200 52.47V0z" opacity=".25" fill="%232a85ff" /></svg>') repeat-x;
    animation: wave 10s linear infinite;
    z-index: -1;
    transform: translateX(0);
}

.wave1 {
    opacity: 0.3;
    bottom: 0;
    animation: wave 20s linear infinite;
}

.wave2 {
    opacity: 0.1;
    bottom: 10px;
    animation: wave2 15s linear infinite;
}

.wave3 {
    opacity: 0.2;
    bottom: 20px;
    animation: wave 30s linear infinite;
}

@keyframes wave {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

@keyframes wave2 {
    0% {
        transform: translateX(-50%);
    }
    100% {
        transform: translateX(0);
    }
}
</style>