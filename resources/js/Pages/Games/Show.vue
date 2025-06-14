<template>
  <AppLayout :title="`Juego #${game.id}`">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Juego #{{ game.id }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <!-- Estado del juego -->
          <div class="mb-6 flex justify-between items-center">
            <div>
              <span class="font-medium">Estado:</span> 
              <span v-if="gameState.status === 'waiting'" class="ml-2 px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                Esperando oponente
              </span>
              <span v-else-if="gameState.status === 'in_progress'" class="ml-2 px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                En progreso
              </span>
              <span v-else class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                Finalizado
              </span>
            </div>
            <div v-if="gameState.status === 'in_progress'">
              <span class="font-medium">Turno:</span>
              <span v-if="gameState.yourTurn" class="ml-2 text-green-600">Tu turno</span>
              <span v-else class="ml-2 text-red-600">Turno del oponente</span>
            </div>
            <div v-else-if="gameState.status === 'finished'">
              <span class="font-medium">Resultado:</span>
              <span v-if="gameState.winner === $page.props.auth.user.id" class="ml-2 text-green-600 font-medium">Victoria</span>
              <span v-else class="ml-2 text-red-600 font-medium">Derrota</span>
              <span v-if="gameState.endReason" class="ml-2 text-gray-600 text-sm">
                ({{ endReasonText(gameState.endReason) }})
              </span>
            </div>
          </div>

          <!-- Mensaje de espera si no hay oponente -->
          <div v-if="gameState.status === 'waiting'" class="my-8 text-center py-10 bg-gray-50 rounded-lg">
            <p class="text-lg text-gray-600">Esperando a que un oponente se una al juego...</p>
            <p class="text-sm text-gray-500 mt-2">Comparte el enlace del juego o espera a que alguien se una.</p>
          </div>

          <!-- Alerta de turno -->
          <div v-if="gameState.status === 'in_progress' && gameState.yourTurn && showTurnAlert" 
               class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
            <p class="font-bold">¡Es tu turno!</p>
            <p>Tienes {{ turnCountdown }} segundos para realizar tu jugada o se seleccionará una posición aleatoria.</p>
          </div>

          <!-- Tableros de juego -->
          <div v-if="gameState.status !== 'waiting'" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Mi tablero -->
            <div>
              <h3 class="text-lg font-medium text-blue-800 mb-3">Mi tablero</h3>
              <div class="grid grid-cols-9 gap-1">
                <!-- Coordenadas X -->
                <div class="flex justify-center items-center h-8 w-8"></div>
                <div v-for="x in 8" :key="`header-${x}`" class="flex justify-center items-center h-8 w-8 font-medium">
                  {{ x - 1 }}
                </div>
                
                <!-- Filas del tablero -->
                <template v-for="y in 8" :key="`row-${y}`">
                  <!-- Coordenada Y -->
                  <div class="flex justify-center items-center h-8 w-8 font-medium">{{ y - 1 }}</div>
                  
                  <!-- Celdas -->
                  <div v-for="x in 8" :key="`cell-${x}-${y}`" 
                       class="h-8 w-8 border flex justify-center items-center"
                       :class="getCellClass(x-1, y-1, true)">
                    <div v-if="hasShip(x-1, y-1)" class="w-3 h-3 rounded-full" 
                         :class="isShipHit(x-1, y-1) ? 'bg-red-500' : 'bg-blue-500'"></div>
                    <div v-else-if="hasOpponentShotHere(x-1, y-1)" class="text-gray-400">✗</div>
                  </div>
                </template>
              </div>
            </div>

            <!-- Tablero enemigo -->
            <div>
              <h3 class="text-lg font-medium text-red-800 mb-3">Tablero enemigo</h3>
              <div class="grid grid-cols-9 gap-1">
                <!-- Coordenadas X -->
                <div class="flex justify-center items-center h-8 w-8"></div>
                <div v-for="x in 8" :key="`enemy-header-${x}`" class="flex justify-center items-center h-8 w-8 font-medium">
                  {{ x - 1 }}
                </div>
                
                <!-- Filas del tablero -->
                <template v-for="y in 8" :key="`enemy-row-${y}`">
                  <!-- Coordenada Y -->
                  <div class="flex justify-center items-center h-8 w-8 font-medium">{{ y - 1 }}</div>
                  
                  <!-- Celdas -->
                  <div v-for="x in 8" :key="`enemy-cell-${x}-${y}`" 
                       @click="fireShot(x-1, y-1)"
                       class="h-8 w-8 border flex justify-center items-center"
                       :class="getCellClass(x-1, y-1, false)">
                    <div v-if="hasShotHere(x-1, y-1)" 
                         :class="isShotHit(x-1, y-1) ? 'text-red-500' : 'text-gray-400'">
                      {{ isShotHit(x-1, y-1) ? '✓' : '✗' }}
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </div>

          <!-- Añadir temporizador de turno -->
          <div v-if="gameState.yourTurn && turnCountdown > 0" class="mt-3 bg-yellow-100 p-3 rounded">
            <p class="text-yellow-800">
              Es tu turno - Dispara en {{ turnCountdown }} segundos o se realizará un disparo aleatorio
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Botón de abandonar partida -->
    <div v-if="gameState.status !== 'finished'" class="mt-6 flex justify-end">
      <button @click="showAbandonModal = true" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
        Abandonar partida
      </button>
    </div>

    <!-- Modal de confirmación para abandonar -->
    <div v-if="showAbandonModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-medium text-gray-900 mb-3">¿Abandonar partida?</h3>
        <p class="text-gray-600 mb-4">
          Si abandonas la partida, tu oponente ganará automáticamente. ¿Estás seguro de que deseas abandonar?
        </p>
        <div class="flex justify-end space-x-3">
          <button @click="showAbandonModal = false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
            Cancelar
          </button>
          <button @click="abandonGame" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Abandonar
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

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
  data() {
    return {
      gameState: {
        status: this.game.status,
        yourTurn: this.isMyTurn,
        winner: this.game.winner_id
      },
      shots: this.myShots || [],
      opponentShots: [],
      pollInterval: null,
      loading: false,
      showAbandonModal: false,
      turnCountdown: 0,
      turnTimer: null,
      maxTurnTime: this.game.max_response_time || 30,
      abandonLoading: false
    };
  },
  mounted() {
    // Iniciar el polling
    this.startPolling();
  },
  beforeUnmount() {
    // Detener el polling cuando el componente se desmonta
    this.stopPolling();
  },
  methods: {
    startPolling() {
      this.pollInterval = setInterval(() => {
        this.pollGameStatus();
      }, 2000); // Cada 2 segundos
    },
    stopPolling() {
      clearInterval(this.pollInterval);
    },
    async pollGameStatus() {
      try {
        const response = await axios.get(route('games.poll', this.game.id));
        const data = response.data;
        
        const previousTurn = this.gameState.yourTurn;
        
        // Actualizar el estado del juego
        this.gameState = {
          status: data.status,
          yourTurn: data.yourTurn,
          winner: data.winner
        };
        
        // Si acaba de cambiar a mi turno, iniciar el temporizador
        if (!previousTurn && this.gameState.yourTurn) {
          this.startTurnTimer();
        }
        
        // Si ya no es mi turno, detener el temporizador
        if (previousTurn && !this.gameState.yourTurn) {
          this.stopTurnTimer();
        }
        
        // Si hay un nuevo disparo del oponente, actualizarlo
        if (data.lastOpponentShot && 
            !this.opponentShots.some(shot => shot.id === data.lastOpponentShot.id)) {
          this.opponentShots.push(data.lastOpponentShot);
        }
        
        // Si el juego terminó, detener el polling
        if (data.status === 'finished') {
          this.stopPolling();
        }
      } catch (error) {
        console.error('Error al obtener el estado del juego:', error);
      }
    },
    async fireShot(x, y) {
      // Verificar si es el turno del jugador y el juego está activo
      if (!this.gameState.yourTurn || this.gameState.status !== 'in_progress') {
        return;
      }
      
      // Verificar si ya se disparó en esa posición
      if (this.hasShotHere(x, y)) {
        return;
      }
      
      this.loading = true;
      this.stopTurnTimer(); // Detener el temporizador cuando se dispara manualmente
      
      try {
        const response = await axios.post(route('games.fire', this.game.id), { x, y });
        
        // Agregar el disparo a la lista
        this.shots.push(response.data.shot);
        
        // Actualizar el estado del juego
        this.gameState.yourTurn = false;
        
        if (response.data.gameOver) {
          this.gameState.status = 'finished';
          this.gameState.winner = this.$page.props.auth.user.id;
          this.stopPolling();
        }
      } catch (error) {
        console.error('Error al disparar:', error);
      } finally {
        this.loading = false;
      }
    },
    hasShip(x, y) {
      return this.myShips.some(ship => ship.x_position === x && ship.y_position === y);
    },
    isShipHit(x, y) {
      const ship = this.myShips.find(ship => ship.x_position === x && ship.y_position === y);
      return ship && ship.is_sunk;
    },
    hasShotHere(x, y) {
      return this.shots.some(shot => shot.x_position === x && shot.y_position === y);
    },
    isShotHit(x, y) {
      const shot = this.shots.find(shot => shot.x_position === x && shot.y_position === y);
      return shot && shot.hit;
    },
    hasOpponentShotHere(x, y) {
      return this.opponentShots.some(shot => shot.x_position === x && shot.y_position === y);
    },
    getCellClass(x, y, isMyBoard) {
      let classes = 'bg-white hover:bg-gray-50';
      
      if (isMyBoard) {
        classes = this.hasShip(x, y) ? 'bg-blue-100' : 'bg-white';
        if (this.hasOpponentShotHere(x, y)) {
          classes = this.isShipHit(x, y) ? 'bg-red-100' : 'bg-gray-100';
        }
      } else {
        if (this.hasShotHere(x, y)) {
          classes = this.isShotHit(x, y) ? 'bg-red-100' : 'bg-gray-100';
        } else if (this.gameState.yourTurn && this.gameState.status === 'in_progress') {
          classes += ' cursor-pointer';
        } else {
          classes += ' cursor-not-allowed';
        }
      }
      
      return classes;
    },
    endReasonText(reason) {
      const reasons = {
        'abandon': 'Abandonaste el juego',
        'timeout': 'Se acabó el tiempo',
        'defeat': 'Derrota',
        'victory': 'Victoria'
      };
      return reasons[reason] || 'Finalizado';
    },
    startTurnTimer() {
      this.stopTurnTimer(); // Asegurarse de que no haya un temporizador activo
      this.turnCountdown = this.maxTurnTime;
      
      this.turnTimer = setInterval(() => {
        this.turnCountdown--;
        
        if (this.turnCountdown <= 0) {
          this.fireRandomShot();
        }
      }, 1000);
    },
    stopTurnTimer() {
      if (this.turnTimer) {
        clearInterval(this.turnTimer);
        this.turnTimer = null;
      }
      this.turnCountdown = 0;
    },
    async fireRandomShot() {
      this.stopTurnTimer();
      
      if (!this.gameState.yourTurn || this.gameState.status !== 'in_progress') {
        return;
      }
      
      try {
        const response = await axios.post(route('games.fireRandom', this.game.id));
        
        // Agregar el disparo a la lista
        this.shots.push(response.data.shot);
        
        // Actualizar el estado del juego
        this.gameState.yourTurn = false;
        
        if (response.data.gameOver) {
          this.gameState.status = 'finished';
          this.gameState.winner = this.$page.props.auth.user.id;
          this.stopPolling();
        }
      } catch (error) {
        console.error('Error al realizar disparo aleatorio:', error);
      }
    },
    async abandonGame() {
      this.abandonLoading = true;
      
      try {
        await axios.post(route('games.abandon', this.game.id));
        
        this.showAbandonModal = false;
        this.gameState.status = 'finished';
        this.gameState.winner = this.opponentId;
        this.stopPolling();
        
        // Opcional: redireccionar a la lista de juegos
        router.visit(route('games.index'), {
          onSuccess: () => {
            router.reload();
          }
        });
      } catch (error) {
        console.error('Error al abandonar la partida:', error);
      } finally {
        this.abandonLoading = false;
      }
    }
  }
};
</script>