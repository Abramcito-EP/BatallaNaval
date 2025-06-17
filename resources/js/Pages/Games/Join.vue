<template>
  <AppLayout :title="`Unirse al Juego`">
    <!-- Efectos de partículas de fondo -->
    <div id="game-particles" class="absolute inset-0 z-0 opacity-30"></div>

    <div class="py-6 md:py-12 relative z-10">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Reglas del juego -->
        <div v-if="showRulesModal" class="game-panel rules-panel mb-6">
          <div class="panel-header">
            <h3 class="text-xl font-bold text-cyan-300 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block mr-2 text-amber-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
              </svg>
              REGLAS DE BATALLA NAVAL
            </h3>
          </div>
          
          <div class="panel-content text-gray-200 space-y-4">
            <div class="rule-section">
              <h4 class="text-amber-300 font-bold text-lg mb-2">OBJETIVO</h4>
              <p>Hundir todos los barcos del oponente antes de que él hunda los tuyos.</p>
            </div>
            
            <div class="rule-section">
              <h4 class="text-amber-300 font-bold text-lg mb-2">CONFIGURACIÓN</h4>
              <ul class="list-disc pl-6 space-y-1 rule-list">
                <li>Cada jugador tiene un tablero de 8x8 casillas.</li>
                <li>Cada jugador tiene 15 barcos (de 1x1) distribuidos aleatoriamente en su tablero.</li>
                <li>Los barcos no pueden superponerse.</li>
              </ul>
            </div>
            
            <div class="rule-section">
              <h4 class="text-amber-300 font-bold text-lg mb-2">CÓMO JUGAR</h4>
              <ul class="list-disc pl-6 space-y-1 rule-list">
                <li>Los jugadores se turnan para disparar a una casilla del tablero enemigo.</li>
                <li>Si el disparo impacta en un barco enemigo, se marca como "acierto" y ese barco se hunde.</li>
                <li>Si el disparo cae en agua, se marca como "fallo".</li>
                <li>El primer jugador en hundir todos los barcos enemigos gana.</li>
              </ul>
            </div>
            
            <div class="rule-section">
              <h4 class="text-amber-300 font-bold text-lg mb-2">REGLAS ESPECIALES</h4>
              <ul class="list-disc pl-6 space-y-1 rule-list">
                <li>Si no realizas una acción durante tu turno, el sistema disparará automáticamente.</li>
                <li>Si estás inactivo durante 3 turnos consecutivos, perderás la partida por inactividad (AFK).</li>
                <li>Puedes abandonar la partida, pero el oponente ganará automáticamente.</li>
                <li>No puedes cerrar sesión mientras tengas partidas activas.</li>
              </ul>
            </div>
          </div>
          
          <div class="panel-footer">
            <button 
              @click="showRulesModal = false" 
              class="game-button primary"
            >
              <span class="button-glow"></span>
              <span class="button-text">ENTENDIDO</span>
            </button>
          </div>
        </div>

        <div v-else class="game-panel">
          <div class="panel-header">
            <h3 class="text-xl font-bold text-cyan-300 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block mr-2 text-amber-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              DETALLES DE BATALLA
            </h3>
          </div>
          
          <div class="panel-content">
            <!-- Escenario preview - Ahora primero para destacarlo mejor -->
            <div class="mb-8 scenario-preview">
              <div :class="`preview-image ${game.scenario}`">
                <!-- Radar sweep effect -->
                <div class="radar-sweep"></div>
                
                <div class="scenario-info">
                  <h5 class="scenario-name">
                    {{ scenarioDisplayName(game.scenario).toUpperCase() }}
                  </h5>
                  
                  <div class="scenario-stats">
                    <div class="stat">
                      <span class="stat-label">Visibilidad</span>
                      <div class="stat-bar-container">
                        <div 
                          class="stat-bar" 
                          :style="{
                            width: game.scenario === 'classic' ? '90%' : game.scenario === 'foggy' ? '40%' : '60%'
                          }"
                        ></div>
                      </div>
                    </div>
                    <div class="stat">
                      <span class="stat-label">Dificultad</span>
                      <div class="stat-bar-container">
                        <div 
                          class="stat-bar" 
                          :style="{
                            width: game.scenario === 'classic' ? '40%' : game.scenario === 'foggy' ? '75%' : '90%'
                          }"
                        ></div>
                      </div>
                    </div>
                  </div>
                  
                  <p class="scenario-desc" v-if="game.scenario === 'classic'">
                    Condiciones estándar de batalla. Perfecta visibilidad y mares tranquilos.
                  </p>
                  <p class="scenario-desc" v-else-if="game.scenario === 'foggy'">
                    La niebla reduce la visibilidad. Los disparos fallidos no se muestran hasta que la niebla se despeja parcialmente.
                  </p>
                  <p class="scenario-desc" v-else>
                    Mares agitados y tormentas. Posibilidad de que los disparos se desvíen una casilla.
                  </p>
                </div>
              </div>
            </div>

            <!-- Información del juego -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Anfitrión -->
              <div class="info-card">
                <div class="info-card-header">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                  </svg>
                  <span>ANFITRIÓN</span>
                </div>
                <div class="info-card-body">
                  <div class="text-lg font-bold text-cyan-300">{{ game.host.name }}</div>
                </div>
              </div>
              
              <!-- Escenario -->
              <div class="info-card">
                <div class="info-card-header">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>ESCENARIO</span>
                </div>
                <div class="info-card-body">
                  <div class="text-lg font-bold" :class="{
                    'text-cyan-300': game.scenario === 'classic',
                    'text-slate-300': game.scenario === 'foggy',
                    'text-amber-300': game.scenario === 'stormy'
                  }">
                    {{ scenarioDisplayName(game.scenario) }}
                  </div>
                </div>
              </div>
              
              <!-- Tiempo de respuesta -->
              <div class="info-card">
                <div class="info-card-header">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>TIEMPO DE RESPUESTA</span>
                </div>
                <div class="info-card-body">
                  <div class="flex items-center">
                    <div class="w-full bg-gray-700 rounded-full h-2.5">
                      <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full" 
                           :style="{ width: `${(game.max_response_time / 60) * 100}%` }"></div>
                    </div>
                    <span class="ml-3 text-lg font-bold text-blue-300">{{ game.max_response_time }}s</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-8 flex justify-between">
              <Link :href="route('games.index')" class="game-button secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span class="button-text">VOLVER</span>
              </Link>
              
              <div class="flex gap-4">
                <button 
                  @click="showRulesModal = true" 
                  class="game-button help">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                  </svg>
                </button>
                
                <button 
                  @click="joinGame" 
                  :disabled="processing" 
                  class="game-button primary pulse-animation"
                >
                  <span class="button-glow"></span>
                  <svg v-if="!processing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                  </svg>
                  <svg v-else class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span class="button-text">{{ processing ? 'UNIÉNDOSE...' : 'UNIRSE A BATALLA' }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

export default {
  components: {
    AppLayout,
    Link
  },
  props: {
    game: Object,
    showRules: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      processing: false,
      showRulesModal: this.showRules
    };
  },
  methods: {
    scenarioName(scenario) {
      const names = {
        classic: 'Clásico',
        foggy: 'Niebla',
        stormy: 'Tormenta'
      };
      return names[scenario] || scenario;
    },
    scenarioDisplayName(scenario) {
      const names = {
        classic: 'Océano Clásico',
        foggy: 'Mares de Niebla',
        stormy: 'Tormenta Tropical'
      };
      return names[scenario] || scenario;
    },
    joinGame() {
      this.processing = true;
      router.post(route('games.join', this.game.id), {}, {
        onSuccess: () => {
          this.processing = false;
        },
        onError: () => {
          this.processing = false;
        }
      });
    }
  },
  mounted() {
    // Inicializar partículas de fondo
    if (typeof particlesJS !== 'undefined') {
      try {
        particlesJS("game-particles", {
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
  }
};
</script>

<style scoped>
/* Estilos principales del panel */
.game-panel {
  @apply rounded-lg overflow-hidden shadow-lg relative;
  background: linear-gradient(to bottom, rgba(17, 34, 64, 0.9), rgba(10, 20, 40, 0.9));
  border: 1px solid rgba(59, 130, 246, 0.3);
  box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
}

.rules-panel {
  max-height: 80vh;
  overflow-y: auto;
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

.panel-content {
  @apply p-6;
}

.panel-footer {
  @apply px-6 py-4 border-t flex justify-end;
  border-color: rgba(59, 130, 246, 0.2);
  background: rgba(0, 0, 0, 0.2);
}

/* Estilos para las reglas */
.rule-section {
  @apply p-4 rounded-lg;
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(59, 130, 246, 0.15);
}

.rule-list li {
  position: relative;
  padding-left: 0.5rem;
}

.rule-list li::before {
  content: '•';
  color: #22d3ee;
  font-weight: bold;
  position: absolute;
  left: -1rem;
}

/* Info cards */
.info-card {
  @apply rounded-lg overflow-hidden;
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(34, 211, 238, 0.15);
}

.info-card-header {
  @apply flex items-center px-4 py-2 bg-black/40 text-cyan-300 text-sm font-bold;
  letter-spacing: 0.5px;
}

.info-card-body {
  @apply p-4;
}

/* Botones */
.game-button {
  position: relative;
  background: linear-gradient(to bottom, #155e75, #083344);
  border: 1px solid #22d3ee;
  box-shadow: 0 0 10px rgba(34, 211, 238, 0.3), 
              inset 0 1px 0 rgba(255, 255, 255, 0.2);
  color: #e0f2fe;
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

.game-button:hover {
  background: linear-gradient(to bottom, #0891b2, #155e75);
  transform: translateY(-2px);
  box-shadow: 0 0 15px rgba(34, 211, 238, 0.5), 
              inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.game-button:active {
  transform: translateY(1px);
  box-shadow: 0 0 5px rgba(59, 130, 246, 0.3);
}

.game-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.game-button.primary {
  background: linear-gradient(to bottom, #0e7490, #0e7490);
  border-color: #22d3ee;
}

.game-button.primary:hover {
  background: linear-gradient(to bottom, #22d3ee, #0e7490);
}

.game-button.secondary {
  background: linear-gradient(to bottom, #334155, #1e293b);
  border-color: #64748b;
}

.game-button.secondary:hover {
  background: linear-gradient(to bottom, #475569, #334155);
}

.game-button.help {
  @apply p-2 rounded-full min-w-0;
  background: rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.game-button.help:hover {
  @apply bg-blue-900/50;
}

.button-glow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 50%;
  background: rgba(255, 255, 255, 0.1);
  pointer-events: none;
}

/* Vista previa de escenario */
.scenario-preview {
  @apply h-full flex flex-col;
}

.preview-image {
  @apply rounded-lg overflow-hidden bg-cover bg-center relative flex flex-col justify-end;
  height: 250px; /* Aumentado de 200px a 250px para una mejor visualización */
  border: 1px solid rgba(59, 130, 246, 0.3);
  position: relative;
  transition: all 0.5s ease;
}

.preview-image::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, 
    rgba(15, 23, 42, 0.2) 0%, 
    rgba(15, 23, 42, 0.5) 50%, 
    rgba(15, 23, 42, 0.9) 100%
  );
  z-index: 1;
}

.preview-image.classic {
  background-image: url('/images/scenarios/classic.png');
}

.preview-image.foggy {
  background-image: url('/images/scenarios/foggy.png');
}

.preview-image.stormy {
  background-image: url('/images/scenarios/stormy.png');
}

/* Efectos de escenario - Mejorados */
.preview-image.foggy::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: repeating-linear-gradient(
    to bottom,
    rgba(255, 255, 255, 0.05) 0%,
    rgba(255, 255, 255, 0.1) 50%,
    rgba(255, 255, 255, 0.05) 100%
  );
  animation: fog-animation 8s linear infinite;
  z-index: 2;
  pointer-events: none;
  backdrop-filter: blur(1px);
}

.preview-image.stormy::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0);
  animation: lightning-animation 10s linear infinite;
  z-index: 2;
  pointer-events: none;
}

@keyframes fog-animation {
  0% {
    opacity: 0.3;
    transform: translateY(0);
  }
  50% {
    opacity: 0.5;
    transform: translateY(-10px);
  }
  100% {
    opacity: 0.3;
    transform: translateY(0);
  }
}

@keyframes lightning-animation {
  0%, 20%, 40%, 60%, 80%, 100% {
    background-color: rgba(255, 255, 255, 0);
  }
  5%, 25%, 65% {
    background-color: rgba(255, 255, 255, 0.03);
  }
  10% {
    background-color: rgba(255, 255, 255, 0.1);
  }
  40.5% {
    background-color: rgba(255, 255, 255, 0.07);
  }
}

.scenario-info {
  @apply p-4 relative z-10;
}

/* Estilos de la info del escenario */
.scenario-name {
  @apply text-amber-300 font-bold text-lg mb-2;
  text-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
}

.scenario-stats {
  @apply mb-4 space-y-2;
}

.stat {
  @apply flex items-center justify-between;
}

.stat-label {
  @apply text-cyan-100 text-sm;
}

.stat-bar-container {
  @apply h-1 bg-gray-700 rounded-full overflow-hidden w-32;
}

.stat-bar {
  @apply h-full bg-cyan-500;
  box-shadow: 0 0 8px rgba(34, 211, 238, 0.8);
}

.scenario-desc {
  @apply text-cyan-100 text-sm;
}

/* Radar sweep effect */
.radar-sweep {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: conic-gradient(
    from 0deg,
    rgba(34, 211, 238, 0.3) 0deg,
    rgba(34, 211, 238, 0.1) 30deg,
    rgba(34, 211, 238, 0) 60deg,
    rgba(34, 211, 238, 0) 360deg
  );
  animation: radar-sweep 4s linear infinite;
  mix-blend-mode: screen;
  opacity: 0.7;
  z-index: 2;
  pointer-events: none;
}

@keyframes radar-sweep {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Pulsing button effect - Mejorado */
.pulse-animation {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(34, 211, 238, 0.7);
  }
  70% {
    box-shadow: 0 0 0 8px rgba(34, 211, 238, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(34, 211, 238, 0);
  }
}

/* Efecto adicional para el botón primario */
.game-button.primary:after {
  content: '';
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  z-index: -1;
  border-radius: 6px;
  background: linear-gradient(124deg, #ff2400, #e81d1d, #e8b71d, #e3e81d, #1de840, #1ddde8, #2b1de8, #dd00f3, #dd00f3);
  background-size: 1800% 1800%;
  animation: rainbow-border 8s ease infinite;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.game-button.primary:hover:after {
  opacity: 0.5;
}

@keyframes rainbow-border {
  0% { background-position: 0% 82% }
  50% { background-position: 100% 19% }
  100% { background-position: 0% 82% }
}
</style>