<template>
  <AppLayout title="Juegos">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Juegos
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Alerta para notificar que un oponente se ha unido -->
        <div v-if="showOpponentJoinedAlert" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 relative" role="alert">
          <p class="font-bold">¡Un oponente se ha unido a tu partida!</p>
          <p>{{ opponentName }} se ha unido a tu juego. ¿Quieres ir a la partida?</p>
          <div class="mt-3 flex space-x-3">
            <button @click="goToGame" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
              Ir a la partida
            </button>
            <button @click="closeAlert" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
              Más tarde
            </button>
          </div>
          <div class="mt-2 text-sm">
            La partida se cancelará si no te unes en {{ alertCountdown }} segundos
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <div class="flex justify-between mb-6">
            <h3 class="text-lg font-medium text-gray-900">Juegos disponibles</h3>
            <Link v-if="!hasActiveHostedGame" :href="route('games.create')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              Crear juego
            </Link>
            <span v-else class="text-gray-500 italic">Ya eres anfitrión de una partida activa</span>
          </div>

          <div v-if="availableGames.length === 0" class="text-gray-500">
            No hay juegos disponibles para unirse.
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="game in availableGames" :key="game.id" class="border rounded p-4 hover:bg-gray-50">
              <div class="font-medium">Juego #{{ game.id }}</div>
              <div class="text-sm text-gray-600">Creado por: {{ game.host.name }}</div>
              <div class="text-sm text-gray-600">Escenario: {{ scenarioName(game.scenario) }}</div>
              <div class="text-sm text-gray-600">Tiempo máx: {{ game.max_response_time }}s</div>
              <div class="mt-3">
                <Link :href="route('games.show', game.id)" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                  Unirse
                </Link>
              </div>
            </div>
          </div>

          <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4">Mis juegos</h3>
          <div v-if="myGames.length === 0" class="text-gray-500">
            No has participado en ningún juego.
          </div>
          <div v-else>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oponente</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="game in myGames" :key="game.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ game.id }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <span v-if="game.guest_id && game.host_id === $page.props.auth.user.id">{{ game.guest.name }}</span>
                    <span v-else-if="game.host_id !== $page.props.auth.user.id">{{ game.host.name }}</span>
                    <span v-else class="text-gray-500">Esperando oponente</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span v-if="game.status === 'waiting'" class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Esperando</span>
                    <span v-else-if="game.status === 'in_progress'" class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">En progreso</span>
                    <span v-else class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Finalizado</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span v-if="game.status !== 'finished'">-</span>
                    <span v-else-if="game.winner_id === $page.props.auth.user.id" class="text-green-600 font-medium">Victoria</span>
                    <span v-else class="text-red-600 font-medium">Derrota</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <Link v-if="game.status !== 'waiting' || game.host_id === $page.props.auth.user.id" 
                          :href="route('games.show', game.id)" 
                          class="text-blue-600 hover:text-blue-800 mr-3">
                      {{ game.status === 'finished' ? 'Ver detalles' : 'Continuar' }}
                    </Link>
                    <Link v-if="game.status === 'finished'" 
                          :href="route('games.replay', game.id)" 
                          class="text-gray-600 hover:text-gray-800">
                      <i class="fas fa-eye"></i>
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
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
  data() {
    return {
      showOpponentJoinedAlert: false,
      opponentName: '',
      joinedGameId: null,
      pollInterval: null,
      alertCountdown: 30,
      countdownInterval: null
    };
  },
  mounted() {
    // Iniciar polling para verificar si un oponente se unió
    if (this.hasActiveHostedGame) {
      this.startPolling();
    }
  },
  beforeUnmount() {
    this.stopPolling();
    this.stopCountdown();
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
    startPolling() {
      this.pollInterval = setInterval(() => {
        this.checkOpponentJoined();
      }, 2000); // Cada 2 segundos
    },
    stopPolling() {
      if (this.pollInterval) {
        clearInterval(this.pollInterval);
        this.pollInterval = null;
      }
    },
    async checkOpponentJoined() {
      try {
        const response = await axios.get(route('games.checkOpponentJoined'));
        if (response.data.opponentJoined && !this.showOpponentJoinedAlert) {
          this.opponentName = response.data.opponentName;
          this.joinedGameId = response.data.gameId;
          this.showOpponentJoinedAlert = true;
          this.startCountdown();
          this.stopPolling(); // Detener el polling una vez que se encuentra un oponente
        }
      } catch (error) {
        console.error('Error al verificar oponentes:', error);
      }
    },
    startCountdown() {
      this.alertCountdown = 30;
      this.countdownInterval = setInterval(() => {
        this.alertCountdown--;
        if (this.alertCountdown <= 0) {
          this.timeExpired();
        }
      }, 1000);
    },
    stopCountdown() {
      if (this.countdownInterval) {
        clearInterval(this.countdownInterval);
        this.countdownInterval = null;
      }
    },
    goToGame() {
      this.stopCountdown();
      router.visit(route('games.show', this.joinedGameId));
    },
    closeAlert() {
      this.showOpponentJoinedAlert = false;
      // No detener el temporizador, seguirá contando en segundo plano
    },
    async timeExpired() {
      this.stopCountdown();
      this.showOpponentJoinedAlert = false;
      
      try {
        // Abandonar automáticamente el juego si expira el tiempo
        await axios.post(route('games.abandon', this.joinedGameId), {
          reason: 'host_timeout'
        });
        
        // Actualizar la lista de juegos
        router.reload();
      } catch (error) {
        console.error('Error al abandonar juego por timeout:', error);
      }
    }
  }
};
</script>