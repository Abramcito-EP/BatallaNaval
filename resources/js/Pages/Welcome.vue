<template>
    <Head title="Batalla Naval" />
    <div class="game-background min-h-screen text-gray-100 relative overflow-hidden">
        <!-- Efectos de partículas y olas en el fondo -->
        <div id="particles-js" class="absolute inset-0 z-0 opacity-40"></div>
        <div class="wave-container absolute inset-0 z-0">
            <div class="wave wave1"></div>
            <div class="wave wave2"></div>
            <div class="wave wave3"></div>
        </div>

        <!-- Estructura principal del juego -->
        <div class="relative z-10 flex flex-col h-screen">
            <!-- Barra superior con logo, monedas y perfil -->
            <header class="game-hud py-2 md:py-4 px-3 md:px-6">
                <div class="container mx-auto flex justify-between items-center">
                    <!-- Logo del juego -->
                    <div class="game-logo-container">
                        <div class="game-logo text-2xl md:text-4xl font-bold">
                            <span class="text-yellow-400 glow-text">BATALLA</span> 
                            <span class="text-blue-300 glow-text">NAVAL</span>
                        </div>
                    </div>
                    
                    <!-- Indicadores y perfil (solo mostrar cuando hay sesión) -->
                    <div class="flex items-center gap-2 md:gap-4">
                        <template v-if="$page.props.auth.user">
                            <!-- Monedas del juego -->
                            <div class="game-stat">
                                <div class="icon-container">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 md:w-5 md:h-5 text-yellow-400">
                                        <path d="M10.464 8.746c.227-.18.497-.311.786-.394v2.795a2.252 2.252 0 01-.786-.393c-.394-.313-.546-.681-.546-1.004 0-.323.152-.691.546-1.004zM12.75 15.662v-2.824c.347.085.664.228.921.421.427.32.579.686.579.991 0 .305-.152.671-.579.991a2.534 2.534 0 01-.921.42z" />
                                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v.816a3.836 3.836 0 00-1.72.756c-.712.566-1.112 1.35-1.112 2.178 0 .829.4 1.612 1.113 2.178.502.4 1.102.647 1.719.756v2.978a2.536 2.536 0 01-.921-.421l-.879-.66a.75.75 0 00-.9 1.2l.879.66c.533.4 1.169.645 1.821.75V18a.75.75 0 001.5 0v-.81a4.124 4.124 0 001.821-.749c.745-.559 1.179-1.344 1.179-2.191 0-.847-.434-1.632-1.179-2.191a4.122 4.122 0 00-1.821-.75V8.354c.29.082.559.213.786.393l.415.33a.75.75 0 00.933-1.175l-.415-.33a3.836 3.836 0 00-1.719-.755V6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="value text-xs md:text-base">13,500</span>
                            </div>
                            
                            <!-- Diamantes del juego -->
                            <div class="game-stat">
                                <div class="icon-container">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 md:w-5 md:h-5 text-blue-400">
                                        <path fill-rule="evenodd" d="M9.315 7.584C12.195 3.883 16.695 1.5 21.75 1.5a.75.75 0 01.75.75c0 5.056-2.383 9.555-6.084 12.436A6.75 6.75 0 019.75 22.5a.75.75 0 01-.75-.75v-4.131A15.838 15.838 0 016.382 15H2.25a.75.75 0 01-.75-.75 6.75 6.75 0 017.815-6.666zM15 6.75a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5z" clip-rule="evenodd" />
                                        <path d="M5.26 17.242a.75.75 0 10-.897-1.203 5.243 5.243 0 00-2.05 5.022.75.75 0 00.625.627 5.243 5.243 0 005.022-2.051.75.75 0 10-1.202-.897 3.744 3.744 0 01-3.008 1.51c0-1.23.592-2.323 1.51-3.008z" />
                                    </svg>
                                </div>
                                <span class="value text-xs md:text-base">280</span>
                            </div>
                            
                            <!-- Nivel del jugador -->
                            <div class="player-level">
                                <div class="level-badge w-6 h-6 md:w-8 md:h-8 text-xs md:text-sm">42</div>
                            </div>
                            
                            <!-- Botón de perfil -->
                            <Link
                                :href="route('profile.edit')"
                                class="user-button"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 md:w-5 md:h-5 mr-1">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                                </svg>
                                <span class="hidden md:inline">{{ $page.props.auth.user.name }}</span>
                                <span class="md:hidden">Perfil</span>
                            </Link>
                        </template>
                        
                        <!-- Enlaces de autenticación para usuarios no logueados -->
                        <div class="auth-links" v-if="canLogin && !$page.props.auth.user">
                            <Link
                                :href="route('login')"
                                class="game-button xsmall secondary"
                            >
                                <span class="button-text">INICIAR</span>
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="game-button xsmall secondary ml-2"
                            >
                                <span class="button-text">REGISTRARSE</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenido principal - Menú del juego -->
            <div class="game-container flex-1 flex flex-col justify-center items-center py-6 md:py-8 px-4">
                <!-- Logo centrado grande -->
                <div class="main-logo-container mb-6 md:mb-10 text-center">
                    <h1 class="main-title">
                        <span class="text-yellow-400 glow-text block text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-bold tracking-wider">BATALLA</span>
                        <span class="text-blue-300 glow-text block text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-bold tracking-wider">NAVAL</span>
                    </h1>
                    <div class="game-version text-xs md:text-sm text-gray-400 mt-2">v{{ laravelVersion }}</div>
                </div>
                
                <!-- Menú principal -->
                <div class="main-menu flex flex-col gap-3 md:gap-4 w-48 sm:w-56 md:w-72">
                    <Link
                        href="/games/create"
                        as="button"
                        class="menu-button" 
                    >
                        JUGAR
                    </Link>
                    
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        as="button"
                        class="menu-button" 
                    >
                        ESTADÍSTICAS
                    </Link>
                    
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('logout')" 
                        method="post"
                        as="button"
                        class="menu-button exit"
                    >
                        SALIR
                    </Link>
                </div>
                
                <!-- Botones sociales -->
                <div class="social-buttons mt-6 md:mt-8 flex gap-3 md:gap-4">
                    <button class="social-btn w-8 h-8 md:w-10 md:h-10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 md:w-5 md:h-5">
                            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-2.625 6c-.54 0-.828.419-.936.634a1.96 1.96 0 00-.189.866c0 .298.059.605.189.866.108.215.395.634.936.634.54 0 .828-.419.936-.634.13-.26.189-.568.189-.866 0-.298-.059-.605-.189-.866-.108-.215-.395-.634-.936-.634zm4.314.634c.108-.215.395-.634.936-.634.54 0 .828.419.936.634.13.26.189.568.189.866 0 .298-.059.605-.189.866-.108.215-.395.634-.936.634-.54 0-.828-.419-.936-.634a1.96 1.96 0 01-.189-.866c0-.298.059-.605.189-.866zm2.023 6.828a.75.75 0 10-1.06-1.06 3.75 3.75 0 01-5.304 0 .75.75 0 00-1.06 1.06 5.25 5.25 0 007.424 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <button class="social-btn w-8 h-8 md:w-10 md:h-10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 md:w-5 md:h-5">
                            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                        </svg>
                    </button>
                    <button class="social-btn w-8 h-8 md:w-10 md:h-10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 md:w-5 md:h-5">
                            <path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0112 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 01-3.476.383.39.39 0 00-.297.17l-2.755 4.133a.75.75 0 01-1.248 0l-2.755-4.133a.39.39 0 00-.297-.17 48.9 48.9 0 01-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97zM6.75 8.25a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H7.5z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Barra inferior con copyright -->
            <footer class="game-footer py-2 md:py-3 px-3 md:px-6 text-center text-xs text-gray-500">
                <div class="container mx-auto">
                    <p>© {{ new Date().getFullYear() }} Batalla Naval. Todos los derechos reservados.</p>
                </div>
            </footer>
        </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';

export default {
    name: 'Welcome',
    components: {
        Head,
        Link,
    },
    props: {
        canLogin: {
            type: Boolean,
        },
        canRegister: {
            type: Boolean,
        },
        laravelVersion: {
            type: String,
            required: true,
        },
        phpVersion: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            hoverSound: null,
            clickSound: null
        };
    },
    methods: {
        playHoverSound() {
            if (this.hoverSound) {
                this.hoverSound.currentTime = 0;
                this.hoverSound.play().catch(e => console.log('Audio play error:', e));
            }
        },
        playClickSound() {
            if (this.clickSound) {
                this.clickSound.currentTime = 0;
                this.clickSound.play().catch(e => console.log('Audio play error:', e));
            }
        }
    },
    mounted() {

        // Inicializar particles.js si está disponible
        if (typeof particlesJS !== 'undefined') {
            try {
                particlesJS("particles-js", {
                    particles: {
                        number: {
                            value: 80,
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
                                mode: "grab"
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
    },
    // You can add other lifecycle methods if needed
    beforeMount() {
        // Preparación antes de montar el componente
    },
    beforeUnmount() {
        // Limpiar recursos cuando el componente se destruye
        if (this.hoverSound) {
            this.hoverSound.pause();
            this.hoverSound = null;
        }
        if (this.clickSound) {
            this.clickSound.pause();
            this.clickSound = null;
        }
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

/* HUD superior */
.game-hud {
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), transparent);
    border-bottom: 1px solid rgba(42, 133, 255, 0.2);
}

/* Logo del juego */
.game-logo-container {
    position: relative;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
}

.glow-text {
    text-shadow: 0 0 15px currentColor, 0 0 5px currentColor;
}

/* Logo principal más grande - animación mejorada */
.main-logo-container {
    animation: float 4s ease-in-out infinite;
    width: 100%;
    max-width: 800px;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px) scale(1.01);
    }
}

/* Estadísticas del juego (monedas, diamantes) - mejoradas para responsive */
.game-stat {
    display: flex;
    align-items: center;
    gap: 4px;
    background: rgba(0, 0, 0, 0.4);
    padding: 3px 8px;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

@media (min-width: 768px) {
    .game-stat {
        gap: 6px;
        padding: 4px 10px;
    }
}

.icon-container {
    display: flex;
    align-items: center;
    justify-content: center;
}

.game-stat .value {
    font-weight: bold;
    color: white;
}

/* Nivel del jugador - mejorado para responsive */
.player-level {
    display: flex;
    align-items: center;
    justify-content: center;
}

.level-badge {
    border-radius: 50%;
    background: linear-gradient(135deg, var(--blue-500), var(--blue-300));
    color: #000;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.6);
}

/* Botón de usuario - mejorado para responsive */
.user-button {
    display: flex;
    align-items: center;
    background: rgba(0, 0, 0, 0.4);
    padding: 4px 8px;
    border-radius: 20px;
    color: white;
    border: 1px solid rgba(59, 130, 246, 0.3);
    transition: all 0.2s;
    font-size: 0.8rem;
    white-space: nowrap;
}

@media (min-width: 768px) {
    .user-button {
        padding: 5px 12px;
        font-size: 0.9rem;
    }
}

.user-button:hover {
    background: rgba(59, 130, 246, 0.2);
    border-color: rgba(59, 130, 246, 0.5);
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

@media (min-width: 768px) {
    .menu-button {
        padding: 14px;
        font-size: 1.2rem;
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

.menu-button.exit {
    background: linear-gradient(to bottom, #7e1d1d, #991b1b);
    border-color: #f87171;
}

.menu-button.exit:hover {
    box-shadow: 
        0 6px 10px rgba(0, 0, 0, 0.3),
        0 0 20px rgba(248, 113, 113, 0.6),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

/* Botones sociales */
.social-buttons {
    display: flex;
    gap: 10px;
}

.social-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(59, 130, 246, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #90e0ef;
    transition: all 0.2s;
}

.social-btn:hover {
    background: rgba(59, 130, 246, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.4);
    border-color: rgba(59, 130, 246, 0.5);
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

.game-button.primary {
    background: linear-gradient(to bottom, #3b82f6, #1d4ed8);
    border-color: #60a5fa;
}

.game-button.primary:hover {
    background: linear-gradient(to bottom, #60a5fa, #3b82f6);
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

/* Footer */
.game-footer {
    background: rgba(0, 0, 0, 0.5);
    border-top: 1px solid rgba(59, 130, 246, 0.2);
}

/* Animación de olas */
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

/* Versión de juego */
.game-version {
    opacity: 0.6;
}

/* Media queries */
@media (max-width: 640px) {
    .menu-button {
        padding: 10px;
        font-size: 1rem;
    }
}
</style>