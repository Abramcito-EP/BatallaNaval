<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h3 class="text-lg font-medium mb-4">Mis estadísticas de Batalla Naval</h3>
            
            <div v-if="loading" class="flex justify-center">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
            
            <div v-else-if="stats" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
              <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-sm text-blue-800">Partidas jugadas</div>
                <div class="text-2xl font-bold">{{ stats.totalGames }}</div>
              </div>
              
              <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-sm text-green-800">Victorias</div>
                <div class="text-2xl font-bold">{{ stats.wins }}</div>
              </div>
              
              <div class="bg-red-50 p-4 rounded-lg">
                <div class="text-sm text-red-800">Derrotas</div>
                <div class="text-2xl font-bold">{{ stats.losses }}</div>
              </div>
              
              <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-sm text-purple-800">Ratio de victorias</div>
                <div class="text-2xl font-bold">{{ stats.winRate }}%</div>
              </div>
            </div>
            
            <div v-if="stats && stats.totalGames > 0" class="mb-8">
              <h4 class="text-md font-medium mb-2">Resultados</h4>
              <canvas ref="chartCanvas" width="400" height="200"></canvas>
            </div>
            
            <div class="flex justify-between">
              <Link :href="route('games.index')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Ver juegos
              </Link>
              
              <Link :href="route('games.create')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Crear nuevo juego
              </Link>
            </div>
          </div>
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
  data() {
    return {
      loading: true,
      stats: null,
      chart: null
    };
  },
  mounted() {
    this.fetchStats();
  },
  methods: {
    async fetchStats() {
      try {
        const response = await axios.get(route('games.statistics'));
        this.stats = response.data;
        
        this.$nextTick(() => {
          if (this.stats.totalGames > 0) {
            this.createChart();
          }
        });
      } catch (error) {
        console.error('Error al obtener estadísticas:', error);
      } finally {
        this.loading = false;
      }
    },
    createChart() {
      // Esta es una implementación simulada
      // En una aplicación real, usarías una biblioteca como Chart.js
      const canvas = this.$refs.chartCanvas;
      const ctx = canvas.getContext('2d');
      
      // Limpiar el canvas
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      // Datos
      const total = this.stats.totalGames;
      const wins = this.stats.wins;
      const losses = this.stats.losses;
      
      // Dibujar el gráfico de pastel
      const centerX = canvas.width / 2;
      const centerY = canvas.height / 2;
      const radius = Math.min(centerX, centerY) - 10;
      
      // Sector para victorias (verde)
      const winAngle = (wins / total) * Math.PI * 2;
      ctx.beginPath();
      ctx.moveTo(centerX, centerY);
      ctx.arc(centerX, centerY, radius, 0, winAngle);
      ctx.fillStyle = '#10B981';
      ctx.fill();
      
      // Sector para derrotas (rojo)
      ctx.beginPath();
      ctx.moveTo(centerX, centerY);
      ctx.arc(centerX, centerY, radius, winAngle, Math.PI * 2);
      ctx.fillStyle = '#EF4444';
      ctx.fill();
      
      // Leyenda
      ctx.font = '14px Arial';
      ctx.fillStyle = '#111827';
      
      // Victorias
      ctx.fillStyle = '#10B981';
      ctx.fillRect(50, 20, 20, 20);
      ctx.fillStyle = '#111827';
      ctx.fillText('Victorias: ' + wins, 80, 35);
      
      // Derrotas
      ctx.fillStyle = '#EF4444';
      ctx.fillRect(50, 50, 20, 20);
      ctx.fillStyle = '#111827';
      ctx.fillText('Derrotas: ' + losses, 80, 65);
    }
  }
};
</script>