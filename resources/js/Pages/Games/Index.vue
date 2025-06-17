<template>
  <AppLayout title="Centro de Operaciones">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Alerta de oponente unido con estilo naval -->
        <div v-if="showOpponentJoinedAlert" class="alert-panel mb-6">
          <div class="alert-content">
            <div class="alert-icon success">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-bold text-green-400">¡Contacto enemigo establecido!</h3>
              <p class="text-blue-300">{{ opponentName }} se ha unido a tu partida. ¿Quieres dirigirte a la zona de combate?</p>
              <div class="mt-3 flex space-x-3">
                <button @click="goToGame" class="game-button primary" @mouseenter="playHoverSound">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                  DIRIGIRSE A LA BATALLA
                </button>
                <button @click="closeAlert" class="game-button secondary" @mouseenter="playHoverSound">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  MÁS TARDE
                </button>
              </div>
              <div class="countdown-timer">
                Alerta de misión: Abandono en <span class="text-red-400 font-mono">{{ alertCountdown }}</span> segundoss
              </div>
            </div>
          </div>
        </div>

        <div class="game-panel">
          <div class="panel-header">
            <h2 class="text-xl font-bold">CENTRO DE OPERACIONES</h2>
          </div>
          
          <div class="panel-content">
            <div class="section-header">
              <h3 class="text-lg text-cyan-300 mb-4 font-bold uppercase tracking-wider">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                  <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                </svg>
                Partidas disponibles
              </h3>

              <div class="action-buttons">
                <Link v-if="!hasActiveHostedGame" :href="route('games.create')" class="game-button success" @mouseenter="playHoverSound">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                  NUEVA Partida
                </Link>
                <span v-else class="status-message">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Ya tienes una misión activaa
                </span>
              </div>
            </div>

            <div v-if="availableGames.length === 0" class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
              </svg>
              <p>No se detectan partidas activas en el radar</p>
              <p class="text-sm text-blue-300 mt-1">Crea una nueva misión o espera a que otras naves capitanas inicien operaciones</p>>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
              <div v-for="game in availableGames" :key="game.id" class="mission-card">
                <div class="mission-header">
                  <div class="mission-id">MISIÓN #{{ game.id }}</div>>
                  <div class="mission-scenario" :class="scenarioClass(game.scenario)">
                    {{ scenarioName(game.scenario) }}
                  </div>
                </div>
                
                <!-- Miniprevisualizacion del escenario -->
                <div class="mission-preview" :class="`preview-${game.scenario}`"></div>
                
                <div class="mission-info">
                  <div class="info-row">
                    <span class="info-label">Capitán:</span>
                    <span class="info-value">{{ game.host.name }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Tiempo respuesta:</span>
                    <span class="info-value">{{ game.max_response_time }}s</span>
                  </div>
                </div>
                
                <div class="mission-action">
                  <Link :href="route('games.show', game.id)" class="join-button" @mouseenter="playHoverSound">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    UNIRSE
                  </Link>
                </div>
              </div>
            </div>

            <div class="section-header mt-8">
              <h3 class="text-lg text-cyan-300 mb-4 font-bold uppercase tracking-wider">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                Historial de partidas
              </h3>
            </div>

            <div v-if="myGames.length === 0" class="empty-state">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p>No has participado en ninguna misión</p>>
              <p class="text-sm text-blue-300 mt-1">Tu historial de batallas aparecerá aquí</p>
            </div>
            
            <div v-else class="missions-table">
              <div class="table-header">
                <div class="header-cell flex-1">OPONENTE</div>
                <div class="header-cell w-28">ESTADO</div>
                <div class="header-cell w-24">RESULTADO</div>
                <div class="header-cell w-28">ACCIONES</div>
              </div>
              
              <div v-for="game in myGames" :key="game.id" class="table-row">
                <div class="table-cell flex-1">
                  <span v-if="game.guest_id && game.host_id === $page.props.auth.user.id">{{ game.guest.name }}</span>
                  <span v-else-if="game.host_id !== $page.props.auth.user.id">{{ game.host.name }}</span>
                  <span v-else class="waiting-text">Esperando oponente</span>
                </div>
                <div class="table-cell w-28">
                  <span v-if="game.status === 'waiting'" class="status-badge waiting">Esperando</span>
                  <span v-else-if="game.status === 'in_progress'" class="status-badge active">En progreso</span>
                  <span v-else class="status-badge finished">Finalizado</span>
                </div>
                <div class="table-cell w-24">
                  <span v-if="game.status !== 'finished'">-</span>
                  <span v-else-if="game.winner_id === $page.props.auth.user.id" class="win-text">Victoria</span>
                  <span v-else class="loss-text">Derrota</span>
                </div>
                <div class="table-cell w-28 actions">
                  <Link v-if="game.status !== 'waiting' || game.host_id === $page.props.auth.user.id" 
                        :href="route('games.show', game.id)" 
                        class="action-button"
                        @mouseenter="playHoverSound">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                  </Link>
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
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, onMounted, onBeforeUnmount } from 'vue';

export default {
  components: {
    AppLayout,
    Link
  },
  props: {
    availableGames: Array,
    myGames: Array,
    hasActiveHostedGame: Boolean
  },
  setup(props) {
    const showOpponentJoinedAlert = ref(false);
    const opponentName = ref('');
    const joinedGameId = ref(null);
    const pollInterval = ref(null);
    const alertCountdown = ref(30);
    const countdownInterval = ref(null);
    const hoverSound = ref(null);
    
    const playHoverSound = () => {
      if (hoverSound.value) {
        hoverSound.value.currentTime = 0;
        hoverSound.value.play().catch(e => console.log('Audio play error:', e));
      }
    };
    
    const scenarioName = (scenario) => {
      const names = {
        classic: 'Clásico',
        foggy: 'Niebla de Guerra',
        stormy: 'Tormenta'
      };
      return names[scenario] || scenario;
    };
    
    const scenarioClass = (scenario) => {
      return {
        'classic': 'scenario-classic',
        'foggy': 'scenario-foggy',
        'stormy': 'scenario-stormy'
      }[scenario] || '';
    };
    
    const startPolling = () => {
      pollInterval.value = setInterval(() => {
        checkOpponentJoined();
      }, 2000); // Cada 2 segundos
    };
    
    const stopPolling = () => {
      if (pollInterval.value) {
        clearInterval(pollInterval.value);
        pollInterval.value = null;
      }
    };
    
    const checkOpponentJoined = async () => {
      try {
        const response = await axios.get(route('games.checkOpponentJoined'));
        if (response.data.opponentJoined && !showOpponentJoinedAlert.value) {
          opponentName.value = response.data.opponentName;
          joinedGameId.value = response.data.gameId;
          showOpponentJoinedAlert.value = true;
          startCountdown();
          stopPolling(); // Detener el polling una vez que se encuentra un oponente
        }
      } catch (error) {
        console.error('Error al verificar oponentes:', error);
      }
    };
    
    const startCountdown = () => {
      alertCountdown.value = 30;
      countdownInterval.value = setInterval(() => {
        alertCountdown.value--;
        if (alertCountdown.value <= 0) {
          timeExpired();
        }
      }, 1000);
    };
    
    const stopCountdown = () => {
      if (countdownInterval.value) {
        clearInterval(countdownInterval.value);
        countdownInterval.value = null;
      }
    };
    
    const goToGame = () => {
      stopCountdown();
      router.visit(route('games.show', joinedGameId.value));
    };
    
    const closeAlert = () => {
      showOpponentJoinedAlert.value = false;
      // No detener el temporizador, seguirá contando en segundo plano
    };
    
    const timeExpired = async () => {
      stopCountdown();
      showOpponentJoinedAlert.value = false;
      
      try {
        // Abandonar automáticamente el juego si expira el tiempo
        await axios.post(route('games.abandon', joinedGameId.value), {
          reason: 'host_timeout'
        });
        
        // Actualizar la lista de juegos
        router.reload();
      } catch (error) {
        console.error('Error al abandonar juego por timeout:', error);
      }
    };
    
    onMounted(() => {
      try {
        hoverSound.value = new Audio('/sounds/hover.mp3');
        hoverSound.value.volume = 0.2;
      } catch (e) {
        console.log('Audio initialization error:', e);
      }
      
      // Iniciar polling para verificar si un oponente se unió
      if (props.hasActiveHostedGame) {
        startPolling();
      }
    });
    
    onBeforeUnmount(() => {
      stopPolling();
      stopCountdown();
    });
    
    return {
      showOpponentJoinedAlert,
      opponentName,
      alertCountdown,
      scenarioName,
      scenarioClass,
      goToGame,
      closeAlert,
      playHoverSound
    };
  }
};
</script>

<style scoped>
/* Panel de juego */
.game-panel {
  background-color: rgba(10, 25, 47, 0.8);
  border: 1px solid #22d3ee;
  border-radius: 8px;
  box-shadow: 0 0 20px rgba(34, 211, 238, 0.3),
              inset 0 0 10px rgba(34, 211, 238, 0.1);
  overflow: hidden;
  backdrop-filter: blur(8px);
  position: relative;
}

.panel-header {
  background: linear-gradient(to right, #155e75, #0e7490, #155e75);
  color: #e0f2fe;
  padding: 1rem;
  border-bottom: 1px solid #22d3ee;
  text-shadow: 0 0 10px rgba(224, 242, 254, 0.5);
  position: relative;
  text-align: center;
}

.panel-header::after {
  content: '';
  position: absolute;
  left: 0;
  right: 0;
  height: 1px;
  bottom: 0;
  background: linear-gradient(to right, transparent, rgba(34, 211, 238, 0.7), transparent);
}

.panel-content {
  padding: 1.5rem;
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

@keyframes scanline {
  0% { top: 0%; }
  100% { top: 100%; }
}

/* Sección de encabezado */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  border-bottom: 1px solid rgba(34, 211, 238, 0.2);
  padding-bottom: 0.75rem;
}

/* Estado vacío */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  text-align: center;
  color: #94a3b8;
  background: rgba(15, 30, 50, 0.3);
  border-radius: 8px;
  border: 1px dashed rgba(34, 211, 238, 0.3);
}

.empty-state svg {
  color: #22d3ee;
  opacity: 0.6;
}

/* Tarjetas de partida (antes misión) */
.mission-card {
  background-color: rgba(17, 34, 64, 0.6);
  border-radius: 6px;
  border: 1px solid rgba(34, 211, 238, 0.3);
  overflow: hidden;
  transition: all 0.3s ease;
  position: relative;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.mission-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3), 0 0 15px rgba(34, 211, 238, 0.4);
}

/* Cabecera de partida */
.mission-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: rgba(30, 58, 138, 0.5);
  border-bottom: 1px solid rgba(34, 211, 238, 0.3);
}

/* ID de partida */
.mission-id {
  font-weight: bold;
  color: #e0f2fe;
}

.mission-scenario {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: bold;
}

.scenario-classic {
  background-color: rgba(34, 211, 238, 0.2);
  color: #67e8f9;
  border: 1px solid rgba(34, 211, 238, 0.5);
}

.scenario-foggy {
  background-color: rgba(148, 163, 184, 0.2);
  color: #cbd5e1;
  border: 1px solid rgba(148, 163, 184, 0.5);
}

.scenario-stormy {
  background-color: rgba(251, 191, 36, 0.2);
  color: #fcd34d;
  border: 1px solid rgba(251, 191, 36, 0.5);
}

.mission-info {
  padding: 0.75rem;
}

.info-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.info-row:last-child {
  margin-bottom: 0;
}

.info-label {
  color: #94a3b8;
}

.info-value {
  color: #e0f2fe;
  font-weight: 500;
}

.mission-action {
  padding: 0.75rem;
  display: flex;
  justify-content: flex-end;
  border-top: 1px solid rgba(34, 211, 238, 0.2);
}

.join-button {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  background: linear-gradient(to bottom, #0e7490, #155e75);
  border: 1px solid #22d3ee;
  border-radius: 4px;
  color: white;
  font-size: 0.75rem;
  font-weight: bold;
  letter-spacing: 1px;
  transition: all 0.2s ease;
  box-shadow: 0 0 10px rgba(34, 211, 238, 0.3);
  position: relative;
  overflow: hidden;
}

.join-button::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.7s;
}

.join-button:hover::before {
  left: 100%;
}

.join-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 0 15px rgba(34, 211, 238, 0.5);
}

/* Previsualización de escenario */
.mission-preview {
  height: 70px;
  background-size: cover;
  background-position: center;
  position: relative;
}

.mission-preview::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, rgba(10, 25, 47, 0.4), rgba(10, 25, 47, 0.7));
}

.preview-classic {
  background-image: url('/images/scenarios/classic.png');
}

.preview-foggy {
  background-image: url('/images/scenarios/foggy.png');
}

.preview-stormy {
  background-image: url('/images/scenarios/stormy.png');
}

/* Efectos específicos */
.preview-foggy::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: repeating-linear-gradient(
    to bottom,
    rgba(255, 255, 255, 0.03) 0%,
    rgba(255, 255, 255, 0.05) 50%,
    rgba(255, 255, 255, 0.03) 100%
  );
}

.preview-stormy::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(
    to bottom,
    rgba(0, 0, 0, 0.2),
    rgba(0, 0, 0, 0.4)
  );
  animation: storm-preview 5s infinite;
}

@keyframes storm-preview {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 0.8; }
  10%, 60% { opacity: 0.9; }
}

/* Tabla de misiones - formato mejorado */
.missions-table {
  background-color: rgba(17, 34, 64, 0.4);
  border-radius: 8px;
  border: 1px solid rgba(34, 211, 238, 0.3);
  overflow: hidden;
}

.table-header {
  display: flex;
  background: linear-gradient(to right, rgba(8, 145, 178, 0.7), rgba(14, 116, 144, 0.5));
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgba(34, 211, 238, 0.3);
  color: #e0f2fe;
  font-size: 0.75rem;
  font-weight: bold;
  letter-spacing: 1px;
}

.table-row {
  display: flex;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgba(34, 211, 238, 0.15);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.table-row:last-child {
  border-bottom: none;
}

.table-row:hover {
  background-color: rgba(34, 211, 238, 0.1);
}

.table-row:hover::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(to bottom, #22d3ee, #0891b2);
  box-shadow: 0 0 8px rgba(34, 211, 238, 0.6);
}

.header-cell, .table-cell {
  display: flex;
  align-items: center;
}

.table-cell {
  color: #e0f2fe;
  font-size: 0.875rem;
}

.waiting-text {
  color: #94a3b8;
  font-style: italic;
}

.win-text {
  color: #34d399;
  font-weight: bold;
}

.loss-text {
  color: #f87171;
  font-weight: bold;
}

.status-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: bold;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 90px;
}

.status-badge.waiting {
  background-color: rgba(251, 191, 36, 0.2);
  color: #fcd34d;
  border: 1px solid rgba(251, 191, 36, 0.5);
}

.status-badge.active {
  background-color: rgba(52, 211, 153, 0.2);
  color: #5eead4;
  border: 1px solid rgba(52, 211, 153, 0.5);
}

.status-badge.finished {
  background-color: rgba(148, 163, 184, 0.2);
  color: #cbd5e1;
  border: 1px solid rgba(148, 163, 184, 0.5);
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.25rem;
  height: 2.25rem;
  border-radius: 6px;
  background-color: rgba(34, 211, 238, 0.2);
  color: #67e8f9;
  border: 1px solid rgba(34, 211, 238, 0.3);
  transition: all 0.2s ease;
}

.action-button:hover {
  background-color: rgba(34, 211, 238, 0.3);
  box-shadow: 0 0 15px rgba(34, 211, 238, 0.4);
  transform: translateY(-2px);
}

/* Botones */
.game-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: bold;
  letter-spacing: 1px;
  text-transform: uppercase;
  transition: all 0.2s ease;
  font-size: 0.75rem;
  position: relative;
  overflow: hidden;
}

.game-button::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.7s;
}

.game-button:hover::before {
  left: 100%;
}

.game-button.primary {
  background: linear-gradient(to bottom, #0e7490, #0e7490);
  border: 1px solid #22d3ee;
  color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2),
              0 0 15px rgba(34, 211, 238, 0.4),
              inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.game-button.primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3),
              0 0 20px rgba(34, 211, 238, 0.6),
              inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.game-button.secondary {
  background: rgba(31, 41, 55, 0.8);
  border: 1px solid rgba(75, 85, 99, 0.5);
  color: #e5e7eb;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2),
              inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.game-button.secondary:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3),
              inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.game-button.success {
  background: linear-gradient(to bottom, #059669, #047857);
  border: 1px solid #10b981;
  color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2),
              0 0 15px rgba(16, 185, 129, 0.4),
              inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.game-button.success:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3),
              0 0 20px rgba(16, 185, 129, 0.6),
              inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.game-button:active {
  transform: translateY(1px);
}

/* Panel de alerta */
.alert-panel {
  background-color: rgba(10, 25, 47, 0.9);
  border: 1px solid #10b981;
  border-radius: 8px;
  box-shadow: 0 0 20px rgba(16, 185, 129, 0.3),
              inset 0 0 10px rgba(16, 185, 129, 0.1);
  overflow: hidden;
  backdrop-filter: blur(8px);
}

.alert-content {
  display: flex;
  padding: 1rem;
}

.alert-icon {
  background-color: rgba(16, 185, 129, 0.2);
  border-radius: 50%;
  padding: 0.75rem;
  margin-right: 1rem;
  color: #10b981;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
  border: 1px solid rgba(16, 185, 129, 0.3);
  height: fit-content;
}

.countdown-timer {
  margin-top: 1rem;
  padding: 0.5rem;
  background-color: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 4px;
  font-size: 0.875rem;
  color: #e2e8f0;
  text-align: center;
}

.status-message {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  background: rgba(31, 41, 55, 0.5);
  border: 1px solid rgba(75, 85, 99, 0.5);
  border-radius: 4px;
  color: #9ca3af;
  font-size: 0.75rem;
  letter-spacing: 1px;
}
</style>