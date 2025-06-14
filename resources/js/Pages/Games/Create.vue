// filepath: c:\Users\Axel\Documents\Quinto Cuatrimestre\BatallaNaval\resources\js\Pages\Games\Create.vue
<template>
  <AppLayout title="Crear Juego">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Crear Juego
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Reglas del juego -->
        <div v-if="showRules" class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
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
              <li>Como anfitrión, solo puedes tener una partida activa a la vez.</li>
            </ul>
          </div>
          
          <div class="mt-4 flex justify-end">
            <button @click="showRules = false" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              Entendido
            </button>
          </div>
        </div>

        <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="mb-6">
              <label for="scenario" class="block text-sm font-medium text-gray-700">Escenario</label>
              <select id="scenario" v-model="form.scenario" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                <option value="classic">Clásico</option>
                <option value="foggy">Niebla</option>
                <option value="stormy">Tormenta</option>
              </select>
              <div v-if="form.errors.scenario" class="text-red-500 mt-1 text-sm">{{ form.errors.scenario }}</div>
            </div>

            <div class="mb-6">
              <label for="max_response_time" class="block text-sm font-medium text-gray-700">Tiempo máximo de respuesta (segundos)</label>
              <input type="number" id="max_response_time" v-model="form.max_response_time" min="5" max="60" class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 rounded-md shadow-sm">
              <div v-if="form.errors.max_response_time" class="text-red-500 mt-1 text-sm">{{ form.errors.max_response_time }}</div>
            </div>

            <div class="flex items-center justify-between">
              <Link :href="route('games.index')" class="text-gray-600 hover:text-gray-900">Cancelar</Link>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" :disabled="form.processing">
                Crear Juego
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

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
  data() {
    return {
      form: useForm({
        scenario: 'classic',
        max_response_time: 30
      }),
      showRules: this.showRules
    };
  },
  methods: {
    submit() {
      this.form.post(route('games.store'));
    }
  }
};
</script>