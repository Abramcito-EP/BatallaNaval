<template>
  <AppLayout :title="`Unirse al Juego #${game.id}`">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Unirse al Juego #{{ game.id }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Reglas del juego -->
        <div v-if="showRulesModal" class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
          <h3 class="text-lg font-medium text-blue-800 mb-4">Reglas del juego de Batalla Naval</h3>
          
          <div class="text-blue-700 space-y-3">
            <p><strong>Objetivo:</strong> Hundir todos los barcos del oponente antes de que él hunda los tuyos.</p>
            
            <p><strong>Configuración:</strong></p>
            <ul class="list-disc pl-6">
              <li>Cada jugador tiene un tablero de 8x8 casillas.</li>
              <li>Cada jugador tiene 15 barcos (de 1x1) distribuidos aleatoriamente en su tablero.</li>
              <li>Los barcos no pueden superponerse.</li>
            </ul>
            
            <p><strong>Cómo jugar:</strong></p>
            <ul class="list-disc pl-6">
              <li>Los jugadores se turnan para disparar a una casilla del tablero enemigo.</li>
              <li>Si el disparo impacta en un barco enemigo, se marca como "acierto" y ese barco se hunde.</li>
              <li>Si el disparo cae en agua, se marca como "fallo".</li>
              <li>El primer jugador en hundir todos los barcos enemigos gana.</li>
            </ul>
            
            <p><strong>Reglas especiales:</strong></p>
            <ul class="list-disc pl-6">
              <li>Si no realizas una acción durante tu turno, el sistema disparará automáticamente.</li>
              <li>Si estás inactivo durante 3 turnos consecutivos, perderás la partida por inactividad (AFK).</li>
              <li>Puedes abandonar la partida, pero el oponente ganará automáticamente.</li>
              <li>No puedes cerrar sesión mientras tengas partidas activas.</li>
            </ul>
          </div>
          
          <div class="mt-4 flex justify-end">
            <button @click="showRulesModal = false" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              Entendido
            </button>
          </div>
        </div>

        <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900">Detalles del juego</h3>
            <div class="mt-2">
              <p><span class="font-medium">Anfitrión:</span> {{ game.host.name }}</p>
              <p><span class="font-medium">Escenario:</span> {{ scenarioName(game.scenario) }}</p>
              <p><span class="font-medium">Tiempo de respuesta máximo:</span> {{ game.max_response_time }} segundos</p>
            </div>
          </div>

          <div class="mt-8 flex justify-between">
            <Link :href="route('games.index')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
              Volver
            </Link>
            <button @click="joinGame" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" :disabled="processing">
              <span v-if="processing">Uniéndose...</span>
              <span v-else>Unirse al juego</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

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
  }
};
</script>