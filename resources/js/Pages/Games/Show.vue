<template>
  <AppLayout :title="`Batalla Naval #${game.id}`">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Añadir clases de escenario al panel principal -->
        <div class="game-panel" :class="`scenario-${game.scenario}`">
          <!-- Efecto de escenario que cambia según el tipo -->
          <div class="scenario-effect"></div>
          
          <!-- Cabecera del panel de juego -->
          <div class="panel-header">
            <h2 class="text-xl font-bold">ZONA DE COMBATE #{{ game.id }}</h2>
            <div class="scenario-badge" :class="game.scenario">
              {{ scenarioName(game.scenario) }}
            </div>
          </div>
          
          <div class="panel-content">
            <!-- Estado del juego -->
            <div class="game-status-panel">
              <div class="status-section">
                <div class="status-label">ESTADO PARTIDA:</div>
                <div class="status-value">
                  <span v-if="gameState.status === 'waiting'" class="status-badge waiting">
                    <span class="pulse-dot"></span>
                    ESPERANDO
                  </span>
                  <span v-else-if="gameState.status === 'in_progress'" class="status-badge active">
                    <span class="pulse-dot"></span>
                    EN PROGRESO
                  </span>
                  <span v-else class="status-badge finished">
                    FINALIZADO
                  </span>
                </div>
              </div>

              <div v-if="gameState.status === 'in_progress'" class="status-section">
                <div class="status-label">TURNO:</div>
                <div class="status-value">
                  <span v-if="gameState.yourTurn" class="turn-badge your-turn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    TU TURNO
                  </span>
                  <span v-else class="turn-badge enemy-turn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    ESPERANDO AL ENEMIGO
                  </span>
                </div>
              </div>

              <div v-else-if="gameState.status === 'finished'" class="status-section">
                <div class="status-label">RESULTADO:</div>
                <div class="status-value">
                  <span v-if="gameState.winner === $page.props.auth.user.id" class="result-badge victory">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    VICTORIA
                  </span>
                  <span v-else class="result-badge defeat">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    DERROTA
                  </span>
                  <span v-if="gameState.endReason" class="end-reason">
                    ({{ endReasonText(gameState.endReason) }})
                  </span>
                </div>
              </div>

              <div v-if="gameState.status === 'in_progress' && gameState.yourTurn" class="status-section">
                <div class="status-label">TIEMPO RESTANTE:</div>
                <div class="status-value">
                  <div class="countdown-display">
                    <div class="countdown-bar" :style="{ width: (turnCountdown / maxTurnTime * 100) + '%' }"></div>
                    <span class="countdown-value">{{ turnCountdown }}s</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mensaje de espera si no hay oponente -->
            <div v-if="gameState.status === 'waiting'" class="waiting-screen">
              <div class="radar-animation">
                <div class="radar-sweep"></div>
              </div>
              <p class="waiting-text">ESCANEANDO SECTOR</p>
              <p class="waiting-subtext">Esperando a que un capitán enemigo se una a la batalla...</p>
              <div class="waiting-code">
                <span class="code-label">CÓDIGO DE ACCESO:</span>
                <span class="code-value">{{ window.location.href }}</span>
              </div>
            </div>

            <!-- Alerta de turno -->
            <div v-if="gameState.status === 'in_progress' && gameState.yourTurn" 
                 class="turn-alert">
              <div class="alert-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <div class="alert-content">
                <h3 class="text-lg font-bold text-yellow-400">¡ACCIÓN DE COMBATE REQUERIDA!</h3>
                <p class="text-blue-300">Selecciona coordenadas para disparo inmediato o sistema de disparo automático se activará en {{ turnCountdown }} segundos.</p>
              </div>
            </div>

            <!-- Tableros de juego -->
            <div v-if="gameState.status !== 'waiting'" class="game-boards">
              <!-- Mi tablero -->
              <div class="board-container">
                <div class="board-header">
                  <h3 class="text-lg font-bold text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                    </svg>
                    TU FLOTA
                  </h3>
                </div>
                <div class="game-board my-board">
                  <!-- Coordenadas X -->
                  <div class="board-cell board-label"></div>
                  <div v-for="x in 8" :key="`header-${x}`" class="board-cell board-label">{{ x - 1 }}</div>
                  
                  <!-- Filas del tablero -->
                  <template v-for="y in 8" :key="`row-${y}`">
                    <!-- Coordenada Y -->
                    <div class="board-cell board-label">{{ y - 1 }}</div>
                    
                    <!-- Celdas -->
                    <div v-for="x in 8" :key="`cell-${x}-${y}`" 
                        class="board-cell"
                        :class="getCellClass(x-1, y-1, true)">
                      <div v-if="hasShip(x-1, y-1)" class="ship-marker" 
                          :class="isShipHit(x-1, y-1) ? 'ship-hit' : 'ship-safe'"></div>
                      <div v-else-if="hasOpponentShotHere(x-1, y-1)" class="miss-marker">✗</div>
                    </div>
                  </template>
                </div>
              </div>

              <!-- Tablero enemigo -->
              <div class="board-container">
                <div class="board-header enemy">
                  <h3 class="text-lg font-bold text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    FLOTA ENEMIGA
                  </h3>
                </div>
                <div class="game-board enemy-board">
                  <!-- Coordenadas X -->
                  <div class="board-cell board-label"></div>
                  <div v-for="x in 8" :key="`enemy-header-${x}`" class="board-cell board-label">{{ x - 1 }}</div>
                  
                  <!-- Filas del tablero -->
                  <template v-for="y in 8" :key="`enemy-row-${y}`">
                    <!-- Coordenada Y -->
                    <div class="board-cell board-label">{{ y - 1 }}</div>
                    
                    <!-- Celdas -->
                    <div v-for="x in 8" :key="`enemy-cell-${x}-${y}`" 
                        @click="fireShot(x-1, y-1)"
                        class="board-cell"
                        :class="getCellClass(x-1, y-1, false)">
                      <div v-if="hasShotHere(x-1, y-1)" 
                          :class="isShotHit(x-1, y-1) ? 'hit-marker' : 'miss-marker'">
                        {{ isShotHit(x-1, y-1) ? '✓' : '✗' }}
                      </div>
                      <div v-else-if="gameState.yourTurn && gameState.status === 'in_progress'" class="target-reticle"></div>
                    </div>
                  </template>
                </div>
              </div>
            </div>

            <!-- Leyenda del tablero -->
            <div v-if="gameState.status !== 'waiting'" class="board-legend">
              <div class="legend-item">
                <div class="legend-marker ship-safe"></div>
                <div class="legend-text">Nave propia</div>
              </div>
              <div class="legend-item">
                <div class="legend-marker ship-hit"></div>
                <div class="legend-text">Nave impactada</div>
              </div>
              <div class="legend-item">
                <div class="legend-marker">
                  <span class="hit-marker">✓</span>
                </div>
                <div class="legend-text">Impacto enemigo</div>
              </div>
              <div class="legend-item">
                <div class="legend-marker">
                  <span class="miss-marker">✗</span>
                </div>
                <div class="legend-text">Disparo fallado</div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="game-actions">
              <Link :href="route('games.index')" class="game-button secondary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                VOLVER AL CENTRO DE MANDO
              </Link>
              
              <button v-if="gameState.status !== 'finished'" 
                      @click="showAbandonModal = true" 
                      class="game-button danger">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                ABANDONAR PARTIDA
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación para abandonar -->
    <div v-if="showAbandonModal" class="modal-backdrop">
      <div class="modal-container">
        <div class="modal-header">
          <h3 class="text-lg font-bold">CONFIRMACIÓN DE ABANDONO</h3>
        </div>
        <div class="modal-content">
          <div class="warning-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <p>
            Al abandonar esta partida, la flota enemiga reclamará la victoria. 
            ¿Confirmas la orden de retirada?
          </p>
        </div>
        <div class="modal-actions">
          <button @click="showAbandonModal = false" class="game-button secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            CANCELAR
          </button>
          <button @click="abandonGame" class="game-button danger" :disabled="abandonLoading">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            {{ abandonLoading ? 'PROCESANDO...' : 'CONFIRMAR RETIRADA' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';

export default {
  components: {
    AppLayout,
    Link
  },
  props: {
    game: Object,
    myShips: Array,
    myShots: Array,
    isMyTurn: Boolean,
    opponentId: Number
  },
  setup(props) {
    const gameState = reactive({
      status: props.game.status,
      yourTurn: props.isMyTurn,
      winner: props.game.winner_id,
      endReason: props.game.end_reason
    });
    
    const shots = ref(props.myShots || []);
    const opponentShots = ref([]);
    const pollInterval = ref(null);
    const loading = ref(false);
    const showAbandonModal = ref(false);
    const turnCountdown = ref(props.game.max_response_time || 30);
    const turnTimer = ref(null);
    const maxTurnTime = ref(props.game.max_response_time || 30);
    const abandonLoading = ref(false);
    const hoverSound = ref(null);
    const fireSound = ref(null);
    const hitSound = ref(null);
    const missSound = ref(null);
    
    const window = globalThis.window;
    
    const playHoverSound = () => {
      if (hoverSound.value) {
        hoverSound.value.currentTime = 0;
        hoverSound.value.play().catch(e => console.log('Audio play error:', e));
      }
    };
    
    const playFireSound = () => {
      if (fireSound.value) {
        fireSound.value.currentTime = 0;
        fireSound.value.play().catch(e => console.log('Audio play error:', e));
      }
    };
    
    const playHitSound = () => {
      if (hitSound.value) {
        hitSound.value.currentTime = 0;
        hitSound.value.play().catch(e => console.log('Audio play error:', e));
      }
    };
    
    const playMissSound = () => {
      if (missSound.value) {
        missSound.value.currentTime = 0;
        missSound.value.play().catch(e => console.log('Audio play error:', e));
      }
    };
    
    const startPolling = () => {
      pollInterval.value = setInterval(() => {
        pollGameStatus();
      }, 2000); // Cada 2 segundos
    };
    
    const stopPolling = () => {
      clearInterval(pollInterval.value);
    };
    
    const pollGameStatus = async () => {
      try {
        const response = await axios.get(route('games.poll', props.game.id));
        const data = response.data;
        
        const previousTurn = gameState.yourTurn;
        
        // Actualizar el estado del juego
        gameState.status = data.status;
        gameState.yourTurn = data.yourTurn;
        gameState.winner = data.winner;
        gameState.endReason = data.endReason;
        
        // Si acaba de cambiar a mi turno, iniciar el temporizador
        if (!previousTurn && gameState.yourTurn) {
          startTurnTimer();
        }
        
        // Si ya no es mi turno, detener el temporizador
        if (previousTurn && !gameState.yourTurn) {
          stopTurnTimer();
        }
        
        // Si hay un nuevo disparo del oponente, actualizarlo
        if (data.lastOpponentShot && 
            !opponentShots.value.some(shot => shot.id === data.lastOpponentShot.id)) {
          opponentShots.value.push(data.lastOpponentShot);
          
          // Reproducir sonido según el resultado
          if (data.lastOpponentShot.hit) {
            playHitSound();
          } else {
            playMissSound();
          }
        }
        
        // Si el juego terminó, detener el polling
        if (data.status === 'finished') {
          stopPolling();
        }
      } catch (error) {
        console.error('Error al obtener el estado del juego:', error);
      }
    };
    
    const fireShot = async (x, y) => {
      // Verificar si es el turno del jugador y el juego está activo
      if (!gameState.yourTurn || gameState.status !== 'in_progress') {
        return;
      }
      
      // Verificar si ya se disparó en esa posición
      if (hasShotHere(x, y)) {
        return;
      }
      
      loading.value = true;
      stopTurnTimer(); // Detener el temporizador cuando se dispara manualmente
      playFireSound();
      
      try {
        const response = await axios.post(route('games.fire', props.game.id), { x, y });
        
        // Agregar el disparo a la lista
        shots.value.push(response.data.shot);
        
        // Reproducir sonido según el resultado
        setTimeout(() => {
          if (response.data.shot.hit) {
            playHitSound();
          } else {
            playMissSound();
          }
        }, 500);
        
        // Actualizar el estado del juego
        gameState.yourTurn = false;
        
        if (response.data.gameOver) {
          gameState.status = 'finished';
          gameState.winner = props.auth.user.id;
          stopPolling();
        }
      } catch (error) {
        console.error('Error al disparar:', error);
      } finally {
        loading.value = false;
      }
    };
    
    const hasShip = (x, y) => {
      return props.myShips.some(ship => ship.x_position === x && ship.y_position === y);
    };
    
    const isShipHit = (x, y) => {
      const ship = props.myShips.find(ship => ship.x_position === x && ship.y_position === y);
      return ship && ship.is_sunk;
    };
    
    const hasShotHere = (x, y) => {
      return shots.value.some(shot => shot.x_position === x && shot.y_position === y);
    };
    
    const isShotHit = (x, y) => {
      const shot = shots.value.find(shot => shot.x_position === x && shot.y_position === y);
      return shot && shot.hit;
    };
    
    const hasOpponentShotHere = (x, y) => {
      return opponentShots.value.some(shot => shot.x_position === x && shot.y_position === y);
    };
    
    const getCellClass = (x, y, isMyBoard) => {
      let classes = '';
      
      if (isMyBoard) {
        classes = hasShip(x, y) ? 'has-ship' : '';
        if (hasOpponentShotHere(x, y)) {
          classes += isShipHit(x, y) ? ' enemy-hit' : ' enemy-miss';
        }
      } else {
        if (hasShotHere(x, y)) {
          classes = isShotHit(x, y) ? 'your-hit' : 'your-miss';
        } else if (gameState.yourTurn && gameState.status === 'in_progress') {
          classes = 'targetable';
        } else {
          classes = 'not-targetable';
        }
      }
      
      return classes;
    };
    
    const endReasonText = (reason) => {
      const reasons = {
        'abandon': 'Retirada estratégica',
        'host_timeout': 'Tiempo de respuesta agotado',
        'timeout': 'Tiempo de operación agotado',
        'defeat': 'Flota destruida',
        'victory': 'Misión cumplida'
      };
      return reasons[reason] || 'Operación finalizada';
    };
    
    const startTurnTimer = () => {
      stopTurnTimer(); // Asegurarse de que no haya un temporizador activo
      turnCountdown.value = maxTurnTime.value;
      
      turnTimer.value = setInterval(() => {
        turnCountdown.value--;
        
        if (turnCountdown.value <= 0) {
          fireRandomShot();
        }
      }, 1000);
    };
    
    const stopTurnTimer = () => {
      if (turnTimer.value) {
        clearInterval(turnTimer.value);
        turnTimer.value = null;
      }
      turnCountdown.value = 0;
    };
    
    const fireRandomShot = async () => {
      stopTurnTimer();
      
      if (!gameState.yourTurn || gameState.status !== 'in_progress') {
        return;
      }
      
      try {
        playFireSound();
        
        const response = await axios.post(route('games.fireRandom', props.game.id));
        
        // Agregar el disparo a la lista
        shots.value.push(response.data.shot);
        
        // Reproducir sonido según el resultado
        setTimeout(() => {
          if (response.data.shot.hit) {
            playHitSound();
          } else {
            playMissSound();
          }
        }, 500);
        
        // Actualizar el estado del juego
        gameState.yourTurn = false;
        
        if (response.data.gameOver) {
          gameState.status = 'finished';
          gameState.winner = props.auth.user.id;
          stopPolling();
        }
      } catch (error) {
        console.error('Error al realizar disparo aleatorio:', error);
      }
    };
    
    const abandonGame = async () => {
      abandonLoading.value = true;
      
      try {
        await axios.post(route('games.abandon', props.game.id));
        
        showAbandonModal.value = false;
        gameState.status = 'finished';
        gameState.winner = props.opponentId;
        gameState.endReason = 'abandon';
        stopPolling();
        
        // Redireccionar a la lista de juegos
        router.visit(route('games.index'));
      } catch (error) {
        console.error('Error al abandonar la partida:', error);
      } finally {
        abandonLoading.value = false;
      }
    };
    
    // Añadir función para mostrar nombre del escenario
    const scenarioName = (scenario) => {
      const names = {
        'classic': 'Océano Clásico',
        'foggy': 'Mares de Niebla',
        'stormy': 'Tormenta Tropical'
      };
      return names[scenario] || 'Desconocido';
    };
    
    onMounted(() => {
      try {
        // Inicializar sonidos
        hoverSound.value = new Audio('/sounds/hover.mp3');
        hoverSound.value.volume = 0.2;
        
        fireSound.value = new Audio('/sounds/fire.mp3');
        fireSound.value.volume = 0.4;
        
        hitSound.value = new Audio('/sounds/hit.mp3');
        hitSound.value.volume = 0.4;
        
        missSound.value = new Audio('/sounds/miss.mp3');
        missSound.value.volume = 0.3;
        
        // Añadir sonidos específicos según el escenario
        if (props.game.scenario === 'stormy') {
          setTimeout(() => {
            try {
              const stormSound = new Audio('/sounds/storm.mp3');
              stormSound.volume = 0.2;
              stormSound.loop = true;
              stormSound.play().catch(e => console.log('Storm audio error:', e));
            } catch (e) {
              console.log('Storm audio initialization error:', e);
            }
          }, 1000);
        } else if (props.game.scenario === 'foggy') {
          setTimeout(() => {
            try {
              const fogSound = new Audio('/sounds/fog.mp3');
              fogSound.volume = 0.2;
              fogSound.loop = true;
              fogSound.play().catch(e => console.log('Fog audio error:', e));
            } catch (e) {
              console.log('Fog audio initialization error:', e);
            }
          }, 1000);
        }
      } catch (e) {
        console.log('Audio initialization error:', e);
      }
      
      // Iniciar el polling
      startPolling();
      
      // Iniciar temporizador si es mi turno
      if (gameState.yourTurn && gameState.status === 'in_progress') {
        startTurnTimer();
      }
    });
    
    onBeforeUnmount(() => {
      stopPolling();
      stopTurnTimer();
    });
    
    return {
      gameState,
      shots,
      opponentShots,
      loading,
      showAbandonModal,
      turnCountdown,
      maxTurnTime,
      abandonLoading,
      window,
      hasShip,
      isShipHit,
      hasShotHere,
      isShotHit,
      hasOpponentShotHere,
      getCellClass,
      endReasonText,
      fireShot,
      abandonGame,
      playHoverSound,
      scenarioName
    };
  }
};
</script>

<style scoped>
/* Panel principal del juego */
.game-panel {
  background-color: rgba(10, 25, 47, 0.8);
  border: 1px solid #3b82f6;
  border-radius: 8px;
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.3),
              inset 0 0 10px rgba(59, 130, 246, 0.1);
  overflow: hidden;
  backdrop-filter: blur(8px);
  position: relative;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  transition: background-image 0.5s ease;
}

/* Estilos para cada escenario */
.scenario-classic {
  background-image: linear-gradient(to bottom, 
    rgba(10, 25, 47, 0.7), 
    rgba(10, 25, 47, 0.85)
  ), url('/images/scenarios/classic.png');
}

.scenario-foggy {
  background-image: linear-gradient(to bottom, 
    rgba(10, 25, 47, 0.75), 
    rgba(10, 25, 47, 0.9)
  ), url('/images/scenarios/foggy.png');
}

.scenario-stormy {
  background-image: linear-gradient(to bottom, 
    rgba(10, 25, 47, 0.75), 
    rgba(10, 25, 47, 0.95)
  ), url('/images/scenarios/stormy.png');
}

/* Efectos específicos por escenario */
.scenario-effect {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 5;
}

.scenario-foggy .scenario-effect {
  background: repeating-linear-gradient(
    to bottom,
    rgba(255, 255, 255, 0.03) 0%,
    rgba(255, 255, 255, 0.05) 50%,
    rgba(255, 255, 255, 0.03) 100%
  );
  animation: fog-animation 8s linear infinite;
}

.scenario-stormy .scenario-effect {
  opacity: 0;
  background: rgba(255, 255, 255, 0.2);
  animation: lightning-animation 10s linear infinite;
}

.scenario-stormy .game-panel::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url('/images/scenarios/rain-overlay.png');
  background-size: auto;
  opacity: 0.15;
  pointer-events: none;
  z-index: 4;
  animation: rain-animation 0.5s linear infinite;
}

@keyframes rain-animation {
  from { background-position: 0 0; }
  to { background-position: 4% 4%; }
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
    opacity: 0;
  }
  5%, 25%, 65% {
    opacity: 0.2;
  }
  10% {
    opacity: 0.8;
  }
  40.5% {
    opacity: 0.6;
  }
}

/* Badge de escenario */
.scenario-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: bold;
  text-transform: uppercase;
}

.scenario-badge.classic {
  background-color: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
  border: 1px solid rgba(59, 130, 246, 0.5);
}

.scenario-badge.foggy {
  background-color: rgba(107, 114, 128, 0.2);
  color: #9ca3af;
  border: 1px solid rgba(107, 114, 128, 0.5);
}

.scenario-badge.stormy {
  background-color: rgba(245, 158, 11, 0.2);
  color: #f59e0b;
  border: 1px solid rgba(245, 158, 11, 0.5);
}

/* Modificar efectos visuales en la niebla */
.scenario-foggy .board-cell:not(.board-label) {
  transition: all 0.5s ease;
}

.scenario-foggy .board-cell.your-miss,
.scenario-foggy .board-cell.enemy-miss {
  opacity: 0.6;
}

/* Modificar efectos visuales en tormenta */
.scenario-stormy .game-board {
  animation: sway-animation 5s ease-in-out infinite;
}

@keyframes sway-animation {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(3px) rotate(0.5deg); }
  75% { transform: translateX(-3px) rotate(-0.5deg); }
}

.panel-header {
  background: linear-gradient(to right, #1e3a8a, #1e40af, #1e3a8a);
  color: #90e0ef;
  padding: 1rem;
  border-bottom: 1px solid #3b82f6;
  text-shadow: 0 0 10px rgba(144, 224, 239, 0.5);
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
  background: linear-gradient(to right, transparent, rgba(144, 224, 239, 0.7), transparent);
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

/* Panel de estado del juego */
.game-status-panel {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  background-color: rgba(17, 34, 64, 0.6);
  border-radius: 6px;
  border: 1px solid rgba(59, 130, 246, 0.3);
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.status-section {
  display: flex;
  flex-direction: column;
}

.status-label {
  font-size: 0.75rem;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 0.25rem;
}

.status-value {
  font-size: 0.875rem;
  font-weight: 600;
  color: #e2e8f0;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: bold;
}

.status-badge.waiting {
  background-color: rgba(245, 158, 11, 0.2);
  color: #f59e0b;
  border: 1px solid rgba(245, 158, 11, 0.5);
}

.status-badge.active {
  background-color: rgba(16, 185, 129, 0.2);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.5);
}

.status-badge.finished {
  background-color: rgba(107, 114, 128, 0.2);
  color: #9ca3af;
  border: 1px solid rgba(107, 114, 128, 0.5);
}

.turn-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: bold;
}

.turn-badge.your-turn {
  background-color: rgba(16, 185, 129, 0.2);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.5);
  animation: pulsate 2s infinite;
}

.turn-badge.enemy-turn {
  background-color: rgba(239, 68, 68, 0.2);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.5);
}

.result-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: bold;
}

.result-badge.victory {
  background-color: rgba(16, 185, 129, 0.2);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.5);
}

.result-badge.defeat {
  background-color: rgba(239, 68, 68, 0.2);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.5);
}

.end-reason {
  font-size: 0.75rem;
  color: #94a3b8;
  margin-left: 0.5rem;
}

.pulse-dot {
  display: inline-block;
  width: 8px;
  height: 8px;
  background-color: currentColor;
  border-radius: 50%;
  margin-right: 0.5rem;
  animation: pulsate 2s infinite;
}

@keyframes pulsate {
  0% { opacity: 1; }
  50% { opacity: 0.5; }
  100% { opacity: 1; }
}

/* Pantalla de espera */
.waiting-screen {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: rgba(17, 34, 64, 0.4);
  border-radius: 8px;
  border: 1px solid rgba(59, 130, 246, 0.3);
  padding: 3rem 1rem;
  text-align: center;
  margin-bottom: 1.5rem;
}

.radar-animation {
  position: relative;
  width: 150px;
  height: 150px;
  background-color: rgba(59, 130, 246, 0.1);
  border-radius: 50%;
  margin-bottom: 1.5rem;
  overflow: hidden;
}

.radar-sweep {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  transform-origin: bottom right;
  background: linear-gradient(to right, transparent, rgba(59, 130, 246, 0.5));
  animation: sweep 4s infinite linear;
}

@keyframes sweep {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.waiting-text {
  font-size: 1.25rem;
  font-weight: bold;
  color: #90e0ef;
  margin-bottom: 0.5rem;
  text-shadow: 0 0 10px rgba(144, 224, 239, 0.5);
  letter-spacing: 1px;
}

.waiting-subtext {
  font-size: 0.875rem;
  color: #94a3b8;
  margin-bottom: 1.5rem;
}

.waiting-code {
  background-color: rgba(17, 34, 64, 0.8);
  padding: 0.75rem;
  border-radius: 4px;
  border: 1px solid rgba(59, 130, 246, 0.3);
  max-width: 100%;
  word-break: break-all;
}

.code-label {
  font-size: 0.75rem;
  color: #94a3b8;
  letter-spacing: 1px;
  margin-right: 0.5rem;
}

.code-value {
  font-size: 0.875rem;
  color: #10b981;
  font-family: monospace;
}

/* Alerta de turno */
.turn-alert {
  display: flex;
  background-color: rgba(245, 158, 11, 0.1);
  border: 1px solid rgba(245, 158, 11, 0.3);
  border-radius: 6px;
  padding: 1rem;
  margin-bottom: 1.5rem;
  animation: alertPulse 2s infinite;
}

@keyframes alertPulse {
  0% { border-color: rgba(245, 158, 11, 0.3); }
  50% { border-color: rgba(245, 158, 11, 0.6); }
  100% { border-color: rgba(245, 158, 11, 0.3); }
}

.alert-icon {
  background-color: rgba(245, 158, 11, 0.2);
  border-radius: 50%;
  padding: 0.75rem;
  margin-right: 1rem;
  color: #f59e0b;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 10px rgba(245, 158, 11, 0.3);
  border: 1px solid rgba(245, 158, 11, 0.3);
  height: fit-content;
}

.alert-content {
  flex: 1;
}

/* Contenedor de tableros */
.game-boards {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
  margin-bottom: 1rem;
}

@media (min-width: 1024px) {
  .game-boards {
    grid-template-columns: 1fr 1fr;
  }
}

.board-container {
  background-color: rgba(17, 34, 64, 0.4);
  border-radius: 8px;
  border: 1px solid rgba(59, 130, 246, 0.3);
  overflow: hidden;
}

.board-header {
  background: rgba(30, 58, 138, 0.5);
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgba(59, 130, 246, 0.3);
  display: flex;
  justify-content: center;
}

.board-header.enemy {
  background: rgba(138, 30, 30, 0.5);
  border-bottom: 1px solid rgba(239, 68, 68, 0.3);
}

/* Tableros de juego */
.game-board {
  display: grid;
  grid-template-columns: repeat(9, 1fr);
  grid-template-rows: repeat(9, 1fr);
  gap: 1px;
  background: rgba(15, 23, 42, 0.5);
  padding: 1rem;
}

.board-cell {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 2.5rem;
  width: 2.5rem;
  position: relative;
  transition: all 0.2s ease;
}

@media (max-width: 768px) {
  .board-cell {
    height: 2rem;
    width: 2rem;
  }
}

.board-label {
  background: transparent;
  color: #94a3b8;
  font-weight: bold;
  font-size: 0.875rem;
}

.board-cell:not(.board-label) {
  background: rgba(30, 41, 59, 0.5);
  border: 1px solid rgba(71, 85, 105, 0.5);
}

.board-cell.has-ship {
  background: rgba(59, 130, 246, 0.2);
}

.board-cell.enemy-hit {
  background: rgba(239, 68, 68, 0.3);
}

.board-cell.enemy-miss {
  background: rgba(107, 114, 128, 0.2);
}

.board-cell.your-hit {
  background: rgba(16, 185, 129, 0.3);
}

.board-cell.your-miss {
  background: rgba(107, 114, 128, 0.2);
}

.board-cell.targetable {
  cursor: pointer;
}

.board-cell.targetable:hover {
  background: rgba(59, 130, 246, 0.3);
  transform: scale(1.05);
  z-index: 10;
  box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
}

.board-cell.not-targetable {
  cursor: not-allowed;
}

/* Marcadores en el tablero */
.ship-marker {
  width: 1rem;
  height: 1rem;
  border-radius: 50%;
}

.ship-safe {
  background: linear-gradient(to bottom, #60a5fa, #3b82f6);
  box-shadow: 0 0 10px rgba(59, 130, 246, 0.7);
}

.ship-hit {
  background: linear-gradient(to bottom, #f87171, #ef4444);
  box-shadow: 0 0 10px rgba(239, 68, 68, 0.7);
}

.hit-marker {
  color: #10b981;
  font-weight: bold;
  font-size: 1.25rem;
  text-shadow: 0 0 5px rgba(16, 185, 129, 0.7);
}

.miss-marker {
  color: #9ca3af;
  font-weight: bold;
  font-size: 1.25rem;
}

.target-reticle {
  position: absolute;
  width: 1rem;
  height: 1rem;
  border-radius: 50%;
  border: 1px solid rgba(59, 130, 246, 0.5);
  opacity: 0;
  transition: opacity 0.2s;
}

.board-cell.targetable:hover .target-reticle {
  opacity: 1;
  animation: target-pulse 2s infinite;
}

@keyframes target-pulse {
  0% { transform: scale(0.8); border-color: rgba(59, 130, 246, 0.5); }
  50% { transform: scale(1.2); border-color: rgba(59, 130, 246, 0.8); }
  100% { transform: scale(0.8); border-color: rgba(59, 130, 246, 0.5); }
}

/* Leyenda del tablero */
.board-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  margin-top: 1rem;
  padding: 0.75rem;
  background-color: rgba(15, 23, 42, 0.3);
  border-radius: 6px;
  border: 1px solid rgba(59, 130, 246, 0.2);
}

.legend-item {
  display: flex;
  align-items: center;
  margin-right: 1rem;
}

.legend-marker {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1.5rem;
  height: 1.5rem;
  margin-right: 0.5rem;
}

.legend-text {
  font-size: 0.75rem;
  color: #94a3b8;
}

/* Temporizador */
.countdown-display {
  position: relative;
  height: 1.5rem;
  background: rgba(15, 23, 42, 0.6);
  border-radius: 3px;
  border: 1px solid rgba(59, 130, 246, 0.3);
  overflow: hidden;
  width: 100%;
}

.countdown-bar {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  background: linear-gradient(to right, #ef4444, #f59e0b);
  transition: width 1s linear;
}

.countdown-value {
  position: relative;
  z-index: 5;
  text-align: center;
  line-height: 1.5rem;
  font-weight: bold;
  font-family: monospace;
  color: white;
  mix-blend-mode: difference;
}

/* Acciones de juego */
.game-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
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
  background: linear-gradient(to bottom, #1e40af, #1e3a8a);
  border: 1px solid #3b82f6;
  color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2),
              0 0 15px rgba(59, 130, 246, 0.4),
              inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.game-button.primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3),
              0 0 20px rgba(59, 130, 246, 0.6),
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

.game-button.danger {
  background: linear-gradient(to bottom, #b91c1c, #991b1b);
  border: 1px solid #ef4444;
  color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2),
              0 0 15px rgba(239, 68, 68, 0.4),
              inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.game-button.danger:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3),
              0 0 20px rgba(239, 68, 68, 0.6),
              inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.game-button:active {
  transform: translateY(1px);
}

.game-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Modal de abandonar */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(5px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}

.modal-container {
  background-color: rgba(15, 23, 42, 0.95);
  border: 1px solid #ef4444;
  border-radius: 8px;
  box-shadow: 0 0 20px rgba(239, 68, 68, 0.4),
              inset 0 0 10px rgba(239, 68, 68, 0.1);
  width: 90%;
  max-width: 500px;
  overflow: hidden;
}

.modal-header {
  background: linear-gradient(to right, #7f1d1d, #b91c1c, #7f1d1d);
  color: white;
  padding: 1rem;
  border-bottom: 1px solid #ef4444;
  text-align: center;
}

.modal-content {
  padding: 2rem 1rem;
  text-align: center;
  color: #e2e8f0;
  font-size: 0.875rem;
  line-height: 1.5;
}

.warning-icon {
  color: #ef4444;
  margin-bottom: 1rem;
}

.modal-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
  padding: 1rem;
  border-top: 1px solid rgba(239, 68, 68, 0.3);
  background-color: rgba(15, 23, 42, 0.5);
}
</style>