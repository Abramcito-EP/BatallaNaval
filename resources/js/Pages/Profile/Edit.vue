<template>
    <Head title="Perfil del Capitán" />

    <AppLayout>
        <!-- Efectos de partículas de fondo -->
        <div id="profile-particles" class="absolute inset-0 z-0 opacity-30"></div>
        
        <template #header>
            <h2 class="font-bold text-xl text-cyan-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block mr-2 text-yellow-200">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                PERFIL DEL CAPITÁN
            </h2>
        </template>

        <div class="py-12 relative z-10">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <!-- Panel de información de perfil -->
                <div class="game-panel">
                    <div class="panel-header">
                        <h3 class="text-lg font-bold text-cyan-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-2 text-amber-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            DATOS PERSONALES
                        </h3>
                    </div>
                    
                    <div class="panel-content">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <!-- Panel de cambio de contraseña -->
                <div class="game-panel">
                    <div class="panel-header">
                        <h3 class="text-lg font-bold text-cyan-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-2 text-amber-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            CAMBIAR CLAVE DE ACCESO
                        </h3>
                    </div>
                    
                    <div class="panel-content">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>

                <!-- Panel de eliminación de cuenta -->
                <div class="game-panel danger">
                    <div class="panel-header danger">
                        <h3 class="text-lg font-bold text-red-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 inline-block mr-2 text-red-300">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            ZONA RESTRINGIDA
                        </h3>
                    </div>
                    
                    <div class="panel-content">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

export default {
    name: 'EditProfile',
    components: {
        AppLayout,
        DeleteUserForm,
        UpdatePasswordForm,
        UpdateProfileInformationForm,
        Head,
    },
    props: {
        mustVerifyEmail: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    },
    setup() {
        onMounted(() => {
            // Inicializar partículas de fondo
            if (typeof particlesJS !== 'undefined') {
                try {
                    particlesJS("profile-particles", {
                        particles: {
                            number: { value: 40, density: { enable: true, value_area: 800 } },
                            color: { value: "#ffffff" },
                            shape: { type: "circle", },
                            opacity: { value: 0.3, random: true, },
                            size: { value: 2, random: true, },
                            line_linked: {
                                enable: true, distance: 150, color: "#2a85ff", opacity: 0.2, width: 1
                            },
                            move: {
                                enable: true, speed: 1, direction: "none",
                                random: false, straight: false, out_mode: "out", bounce: false,
                            }
                        },
                        interactivity: {
                            detect_on: "canvas",
                            events: {
                                onhover: { enable: true, mode: "grab" },
                                onclick: { enable: false },
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

        return {};
    }
};
</script>

<style scoped>
/* Estilos de panel de juego */
.game-panel {
    @apply rounded-lg overflow-hidden shadow-lg relative mb-6;
    background: linear-gradient(to bottom, rgba(17, 34, 64, 0.9), rgba(10, 20, 40, 0.9));
    border: 1px solid rgba(59, 130, 246, 0.3);
    box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
}

.game-panel.danger {
    background: linear-gradient(to bottom, rgba(64, 17, 17, 0.9), rgba(40, 10, 10, 0.9));
    border: 1px solid rgba(246, 59, 59, 0.3);
    box-shadow: 0 0 30px rgba(246, 59, 59, 0.2);
}

.panel-header {
    @apply px-6 py-4 border-b;
    background: rgba(0, 0, 0, 0.4);
    border-color: rgba(59, 130, 246, 0.2);
    position: relative;
}

.panel-header::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 1px;
  background: linear-gradient(to right, 
    rgba(34, 211, 238, 0),
    rgba(34, 211, 238, 0.7),
    rgba(34, 211, 238, 0)
  );
}

.panel-header.danger::after {
  background: linear-gradient(to right, 
    rgba(246, 59, 59, 0),
    rgba(246, 59, 59, 0.7),
    rgba(246, 59, 59, 0)
  );
}

.panel-content {
    @apply p-6;
}

/* Animación de scanline */
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

.game-panel.danger::before {
    background: rgba(246, 59, 59, 0.3);
}

@keyframes scanline {
    0% { top: 0%; }
    100% { top: 100%; }
}

/* Asegurar que cualquier botón en los formularios heredan nuestros estilos */
:deep(.game-button) {
    position: relative;
    background: linear-gradient(to bottom, #1e3a8a, #172554);
    border: 1px solid #3b82f6;
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.3), 
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
    color: #a5f3fc;
    font-weight: bold;
    text-transform: uppercase;
    border-radius: 4px;
    transition: all 0.2s ease;
    overflow: hidden;
    padding: 0.75rem 1.5rem;
    letter-spacing: 1px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

:deep(.game-button:hover) {
    background: linear-gradient(to bottom, #2563eb, #1e40af);
    transform: translateY(-2px);
    box-shadow: 0 0 15px rgba(59, 130, 246, 0.5), 
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

:deep(.game-button:active) {
    transform: translateY(1px);
    box-shadow: 0 0 5px rgba(59, 130, 246, 0.3);
}

:deep(.game-button:disabled) {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

:deep(.game-button.danger) {
    background: linear-gradient(to bottom, #dc2626, #991b1b);
    border-color: #f87171;
}

:deep(.game-button.danger:hover) {
    background: linear-gradient(to bottom, #ef4444, #dc2626);
}

:deep(.form-label) {
  @apply block text-sm font-medium text-cyan-200 mb-1;
}

:deep(.form-input) {
  @apply block w-full rounded;
  background: rgba(0, 0, 0, 0.4);
  border: 1px solid rgba(34, 211, 238, 0.3);
  color: #f0f9ff;
  padding: 0.5rem 0.75rem;
}

:deep(.form-input:focus) {
  @apply outline-none;
  border-color: rgba(34, 211, 238, 0.7);
  box-shadow: 0 0 0 2px rgba(34, 211, 238, 0.25);
}

:deep(.text-gray-600) {
  @apply text-gray-300;
}

:deep(.text-gray-700) {
  @apply text-gray-200;
}

:deep(.text-gray-800) {
  @apply text-cyan-100;
}

:deep(.text-red-500) {
  @apply text-red-300;
}
</style>