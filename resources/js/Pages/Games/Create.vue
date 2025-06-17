<template>
  <AppLayout title="Crear Juego">
    <!-- Efectos de partículas de fondo -->
    <div id="game-particles" class="absolute inset-0 z-0 opacity-30"></div>
    
    <div class="py-6 md:py-12 relative z-10">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Reglas del juego -->
        <div v-if="showRules" class="game-panel rules-panel mb-6">
          <div class="panel-header">
            <h3 class="text-xl font-bold text-blue-300">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block mr-2 text-yellow-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
              </svg>
              REGLAS DE BATALLA NAVAL
            </h3>
          </div>
          
          <div class="panel-content text-gray-200 space-y-4">
            <div class="rule-section">
              <h4 class="text-yellow-400 font-bold text-lg mb-2">OBJETIVO</h4>
              <p>Hundir todos los barcos del oponente antes de que él hunda los tuyos.</p>
            </div>
            
            <div class="rule-section">
              <h4 class="text-yellow-400 font-bold text-lg mb-2">CONFIGURACIÓN</h4>
              <ul class="list-disc pl-6 space-y-1 rule-list">
                <li>Cada jugador tiene un tablero de 8x8 casillas.</li>
                <li>Cada jugador tiene 15 barcos (de 1x1) distribuidos aleatoriamente en su tablero.</li>
                <li>Los barcos no pueden superponerse.</li>
              </ul>
            </div>
            
            <div class="rule-section">
              <h4 class="text-yellow-400 font-bold text-lg mb-2">CÓMO JUGAR</h4>
              <ul class="list-disc pl-6 space-y-1 rule-list">
                <li>Los jugadores se turnan para disparar a una casilla del tablero enemigo.</li>
                <li>Si el disparo impacta en un barco enemigo, se marca como "acierto" y ese barco se hunde.</li>
                <li>Si el disparo cae en agua, se marca como "fallo".</li>
                <li>El primer jugador en hundir todos los barcos enemigos gana.</li>
              </ul>
            </div>
            
            <div class="rule-section">
              <h4 class="text-yellow-400 font-bold text-lg mb-2">REGLAS ESPECIALES</h4>
              <ul class="list-disc pl-6 space-y-1 rule-list">
                <li>Si no realizas una acción durante tu turno, el sistema disparará automáticamente.</li>
                <li>Si estás inactivo durante 3 turnos consecutivos, perderás la partida por inactividad (AFK).</li>
                <li>Puedes abandonar la partida, pero el oponente ganará automáticamente.</li>
                <li>No puedes cerrar sesión mientras tengas partidas activas.</li>
                <li>Como anfitrión, solo puedes tener una partida activa a la vez.</li>
              </ul>
            </div>
          </div>
          
          <div class="panel-footer">
            <button 
              @click="showRules = false; playClickSound()" 
              @mouseenter="playHoverSound" 
              class="game-button primary"
            >
              <span class="button-glow"></span>
              <span class="button-text">ENTENDIDO</span>
            </button>
          </div>
        </div>

        <div v-else class="game-panel create-game-panel">
          <div class="panel-header">
            <h3 class="text-xl font-bold text-blue-300">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block mr-2 text-yellow-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
              </svg>
              CONFIGURACIÓN DE BATALLA
            </h3>
          </div>
          
          <div class="panel-content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <!-- Columna izquierda: configuración -->
              <form @submit.prevent="submit" class="space-y-8">
                <div class="form-group">
                  <div class="label-container">
                    <label for="scenario" class="game-label">ESCENARIO</label>
                    <div class="tooltip">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM8.94 6.94a.75.75 0 11-1.061-1.061 3 3 0 112.871 5.026v.345a.75.75 0 01-1.5 0v-.5c0-.72.57-1.172 1.081-1.287A1.5 1.5 0 108.94 6.94zM10 15a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                      </svg>
                      <span class="tooltip-text">El escenario afecta a las condiciones de la partida</span>
                    </div>
                  </div>
                  <div class="relative mt-2 scenario-selector">
                    <select 
                      id="scenario" 
                      v-model="form.scenario" 
                      class="game-input"
                      @mouseenter="playHoverSound"
                      @change="playClickSound"
                    >
                      <option value="classic">Océano Clásico</option>
                      <option value="foggy">Mares de Niebla</option>
                      <option value="stormy">Tormenta Tropical</option>
                    </select>
                    <div class="game-input-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                      </svg>
                    </div>
                  </div>
                  <div v-if="form.errors.scenario" class="game-error">{{ form.errors.scenario }}</div>
                </div>

                <div class="form-group">
                  <div class="label-container">
                    <label for="max_response_time" class="game-label">TIEMPO DE RESPUESTA</label>
                    <div class="tooltip">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM8.94 6.94a.75.75 0 11-1.061-1.061 3 3 0 112.871 5.026v.345a.75.75 0 01-1.5 0v-.5c0-.72.57-1.172 1.081-1.287A1.5 1.5 0 108.94 6.94zM10 15a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                      </svg>
                      <span class="tooltip-text">Tiempo máximo para realizar tu jugada (5-60 segundos)</span>
                    </div>
                  </div>

                  <div class="relative mt-2 time-selector">
                    <div class="slider-container">
                      <input 
                        type="range" 
                        id="max_response_time_slider" 
                        v-model="form.max_response_time" 
                        min="5" 
                        max="60" 
                        step="1"
                        class="game-slider"
                        @input="playClickSound"
                      >
                      <div class="slider-value">{{ form.max_response_time }} seg.</div>
                    </div>
                  </div>
                  <div v-if="form.errors.max_response_time" class="game-error">{{ form.errors.max_response_time }}</div>
                </div>

                <div class="form-actions">
                  <button 
                    type="submit" 
                    class="game-button primary pulse-animation" 
                    :disabled="form.processing"
                    @mouseenter="playHoverSound"
                    @click="playClickSound"
                  >
                    <span class="button-glow"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                    </svg>
                    <span class="button-text">INICIAR BATALLA</span>
                  </button>
                  <button 
                    type="button" 
                    class="game-button help"
                    @click="showRules = true"
                    @mouseenter="playHoverSound"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                    </svg>
                  </button>
                </div>
              </form>

              <!-- Columna derecha: vista previa de escenario -->
              <div class="scenario-preview">
                <h4 class="text-center text-yellow-400 font-bold mb-4">VISTA PREVIA</h4>
                <div :class="`preview-image ${form.scenario}`">
                  <!-- Animación de radar -->
                  <div class="radar-sweep"></div>
                  
                  <!-- Información específica del escenario -->
                  <div class="scenario-info">
                    <h5 v-if="form.scenario === 'classic'" class="scenario-name">OCÉANO CLÁSICO</h5>
                    <h5 v-else-if="form.scenario === 'foggy'" class="scenario-name">MARES DE NIEBLA</h5>
                    <h5 v-else class="scenario-name">TORMENTA TROPICAL</h5>
                    
                    <div class="scenario-stats">
                      <div class="stat">
                        <span class="stat-label">Visibilidad</span>
                        <div class="stat-bar-container">
                          <div 
                            class="stat-bar" 
                            :style="{
                              width: form.scenario === 'classic' ? '90%' : form.scenario === 'foggy' ? '40%' : '60%'
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
                              width: form.scenario === 'classic' ? '40%' : form.scenario === 'foggy' ? '75%' : '90%'
                            }"
                          ></div>
                        </div>
                      </div>
                    </div>
                    
                    <p class="scenario-desc" v-if="form.scenario === 'classic'">
                      Condiciones estándar de batalla. Perfecta visibilidad y mares tranquilos.
                    </p>
                    <p class="scenario-desc" v-else-if="form.scenario === 'foggy'">
                      La niebla reduce la visibilidad. Los disparos fallidos no se muestran hasta que la niebla se despeja parcialmente.
                    </p>
                    <p class="scenario-desc" v-else>
                      Mares agitados y tormentas. Posibilidad de que los disparos se desvíen una casilla.
                    </p>
                  </div>
                </div>
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
import { Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

export default {
  components: {
    AppLayout,
    Link
  },
  props: {
    showRules: {
      type: Boolean,
      default: true
    }
  },
  setup(props) {
    const form = useForm({
      scenario: 'classic',
      max_response_time: 30
    });
    
    const showRulesState = ref(props.showRules);
    const hoverSound = ref(null);
    const clickSound = ref(null);
    
    function playHoverSound() {
      if (hoverSound.value) {
        hoverSound.value.currentTime = 0;
        hoverSound.value.play().catch(e => console.log('Audio play error:', e));
      }
    }
    
    function playClickSound() {
      if (clickSound.value) {
        clickSound.value.currentTime = 0;
        clickSound.value.play().catch(e => console.log('Audio play error:', e));
      }
    }
    
    function submit() {
      form.post(route('games.store'));
    }
    
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
    });
    
    return {
      form,
      showRules: showRulesState,
      playHoverSound,
      playClickSound,
      submit
    };
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
    rgba(59, 130, 246, 0),
    rgba(59, 130, 246, 0.7),
    rgba(59, 130, 246, 0)
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
  color: #60a5fa;
  font-weight: bold;
  position: absolute;
  left: -1rem;
}

/* Estilos para el formulario */
.form-group {
  @apply rounded-lg p-4;
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(59, 130, 246, 0.15);
}

.label-container {
  @apply flex items-center gap-2;
}

.game-label {
  @apply text-sm font-bold text-blue-300;
  letter-spacing: 0.5px;
}

.tooltip {
  @apply text-gray-400 relative cursor-help;
}

.tooltip-text {
  @apply absolute z-10 w-48 px-2 py-1 -mt-24 -ml-20 text-xs text-white bg-gray-900 rounded-lg shadow-lg opacity-0 invisible transition-opacity;
}

.tooltip:hover .tooltip-text {
  @apply opacity-100 visible;
}

.game-input {
  @apply block w-full pl-4 pr-10 py-3 rounded-md appearance-none;
  background: rgba(0, 0, 0, 0.4);
  border: 1px solid rgba(59, 130, 246, 0.3);
  color: white;
  transition: all 0.2s;
}

.game-input:focus {
  @apply outline-none;
  border-color: rgba(59, 130, 246, 0.7);
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
}

.game-input:hover {
  border-color: rgba(59, 130, 246, 0.5);
}

.game-input-icon {
  @apply absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400;
}

.game-error {
  @apply text-red-400 mt-2 text-sm font-medium;
}

/* Slider personalizado */
.slider-container {
  @apply flex items-center gap-4;
}

.game-slider {
  @apply appearance-none w-full h-2 rounded-full;
  background: rgba(0, 0, 0, 0.4);
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.game-slider::-webkit-slider-thumb {
  @apply appearance-none h-5 w-5 rounded-full cursor-pointer;
  background: linear-gradient(135deg, #60a5fa, #2563eb);
  border: 2px solid #90e0ef;
  box-shadow: 0 0 10px rgba(59, 130, 246, 0.6);
}

.slider-value {
  @apply text-blue-300 font-bold text-sm w-20;
}

/* Botones */
.form-actions {
  @apply flex items-center justify-between mt-8;
}

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
  padding: 0.75rem 1.5rem;
  letter-spacing: 1px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
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

.game-back-button {
  @apply flex items-center text-sm text-gray-400 hover:text-blue-300 transition-colors;
}

/* Título con acento */
.header-accent {
  @apply absolute -bottom-2 left-0 h-0.5 bg-blue-500 rounded;
  width: 50%;
  box-shadow: 0 0 10px rgba(59, 130, 246, 0.6);
}

/* Vista previa de escenario */
.scenario-preview {
  @apply h-full flex flex-col;
}

.preview-image {
  @apply flex-1 rounded-lg overflow-hidden bg-cover bg-center relative flex flex-col justify-end;
  min-height: 300px;
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

/* Efectos de escenario */
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
</style>