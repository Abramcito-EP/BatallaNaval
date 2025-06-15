<template>
  <AppLayout>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="game-panel">
          <div class="panel-header">
            <h2 class="text-xl font-bold">CENTRO DE COMANDO</h2>
          </div>
          
          <div class="panel-content">
            <h3 class="text-lg text-blue-300 mb-6 font-bold uppercase tracking-wider">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
              </svg>
              Estadísticas de batalla
            </h3>
            
            <div v-if="loading" class="flex justify-center py-10">
              <div class="radar-loader"></div>
            </div>
            
            <div v-else-if="stats" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
              <div class="stat-card bg-navy-800">
                <div class="stat-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                </div>
                <div class="stat-content">
                  <div class="stat-title">Misiones completadas</div>
                  <div class="stat-value">{{ stats.totalGames }}</div>
                </div>
              </div>
              
              <div class="stat-card bg-navy-800">
                <div class="stat-icon success">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="stat-content">
                  <div class="stat-title">Victorias navales</div>
                  <div class="stat-value success">{{ stats.wins }}</div>
                </div>
              </div>
              
              <div class="stat-card bg-navy-800">
                <div class="stat-icon danger">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
                <div class="stat-content">
                  <div class="stat-title">Derrotas en combate</div>
                  <div class="stat-value danger">{{ stats.losses }}</div>
                </div>
              </div>
              
              <div class="stat-card bg-navy-800">
                <div class="stat-icon warning">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                  </svg>
                </div>
                <div class="stat-content">
                  <div class="stat-title">Ratio de eficiencia</div>
                  <div class="stat-value warning">{{ stats.winRate }}%</div>
                </div>
              </div>
            </div>
            
            <div v-if="stats && stats.totalGames > 0" class="mb-8">
              <div class="chart-container">
                <canvas ref="chartCanvas" width="400" height="200"></canvas>
                <div v-if="!loading" class="chart-labels">
                  <div class="chart-label success">
                    <span class="chart-label-dot"></span>
                    Victorias: {{ stats.wins }}
                  </div>
                  <div class="chart-label danger">
                    <span class="chart-label-dot"></span>
                    Derrotas: {{ stats.losses }}
                  </div>
                </div>
              </div>
            </div>
            
            <div class="game-actions">
              <Link :href="route('games.index')" class="game-button primary" @mouseenter="playHoverSound">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                VER MISIONES
              </Link>
              
              <Link :href="route('games.create')" class="game-button success" @mouseenter="playHoverSound">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                NUEVA BATALLA
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
import { ref, onMounted } from 'vue';

export default {
  components: {
    AppLayout,
    Link
  },
  setup() {
    const loading = ref(true);
    const stats = ref(null);
    const chartCanvas = ref(null);
    const hoverSound = ref(null);
    
    const playHoverSound = () => {
      if (hoverSound.value) {
        hoverSound.value.currentTime = 0;
        hoverSound.value.play().catch(e => console.log('Audio play error:', e));
      }
    };
    
    const fetchStats = async () => {
      try {
        const response = await axios.get(route('games.statistics'));
        stats.value = response.data;
        
        setTimeout(() => {
          if (stats.value && stats.value.totalGames > 0 && chartCanvas.value) {
            createChart();
          }
          loading.value = false;
        }, 500);
      } catch (error) {
        console.error('Error al obtener estadísticas:', error);
        loading.value = false;
      }
    };
    
    const createChart = () => {
      const canvas = chartCanvas.value;
      const ctx = canvas.getContext('2d');
      
      // Limpiar el canvas
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      // Datos
      const total = stats.value.totalGames;
      const wins = stats.value.wins;
      const losses = stats.value.losses;
      
      // Calcular los ángulos para el gráfico de pastel
      const winAngle = (wins / total) * Math.PI * 2;
      const lossAngle = (losses / total) * Math.PI * 2;
      
      // Dibujar el gráfico con efecto de brillo
      const centerX = canvas.width / 2;
      const centerY = canvas.height / 2;
      const radius = Math.min(centerX, centerY) - 40;
      
      // Fondo de radar
      ctx.beginPath();
      ctx.arc(centerX, centerY, radius + 20, 0, Math.PI * 2);
      ctx.strokeStyle = "rgba(144, 224, 239, 0.1)";
      ctx.lineWidth = 2;
      ctx.stroke();
      
      // Líneas de radar
      for (let i = 0; i < 4; i++) {
        const angle = (i / 4) * Math.PI * 2;
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(
          centerX + Math.cos(angle) * (radius + 20),
          centerY + Math.sin(angle) * (radius + 20)
        );
        ctx.strokeStyle = "rgba(144, 224, 239, 0.1)";
        ctx.stroke();
      }
      
      // Círculos concéntricos
      for (let i = 1; i <= 3; i++) {
        ctx.beginPath();
        ctx.arc(centerX, centerY, (radius * i) / 3, 0, Math.PI * 2);
        ctx.strokeStyle = "rgba(144, 224, 239, 0.1)";
        ctx.stroke();
      }
      
      // Sector para victorias (verde)
      ctx.beginPath();
      ctx.moveTo(centerX, centerY);
      ctx.arc(centerX, centerY, radius, 0, winAngle);
      ctx.fillStyle = "rgba(16, 185, 129, 0.4)";
      ctx.fill();
      ctx.strokeStyle = "rgba(16, 185, 129, 0.8)";
      ctx.lineWidth = 2;
      ctx.stroke();
      
      // Agregar brillo a victorias
      const gradientWin = ctx.createRadialGradient(
        centerX, centerY, radius * 0.8,
        centerX, centerY, radius
      );
      gradientWin.addColorStop(0, "rgba(16, 185, 129, 0)");
      gradientWin.addColorStop(1, "rgba(16, 185, 129, 0.3)");
      
      ctx.beginPath();
      ctx.moveTo(centerX, centerY);
      ctx.arc(centerX, centerY, radius, 0, winAngle);
      ctx.fillStyle = gradientWin;
      ctx.fill();
      
      // Sector para derrotas (rojo)
      ctx.beginPath();
      ctx.moveTo(centerX, centerY);
      ctx.arc(centerX, centerY, radius, winAngle, Math.PI * 2);
      ctx.fillStyle = "rgba(239, 68, 68, 0.4)";
      ctx.fill();
      ctx.strokeStyle = "rgba(239, 68, 68, 0.8)";
      ctx.lineWidth = 2;
      ctx.stroke();
      
      // Agregar brillo a derrotas
      const gradientLoss = ctx.createRadialGradient(
        centerX, centerY, radius * 0.8,
        centerX, centerY, radius
      );
      gradientLoss.addColorStop(0, "rgba(239, 68, 68, 0)");
      gradientLoss.addColorStop(1, "rgba(239, 68, 68, 0.3)");
      
      ctx.beginPath();
      ctx.moveTo(centerX, centerY);
      ctx.arc(centerX, centerY, radius, winAngle, Math.PI * 2);
      ctx.fillStyle = gradientLoss;
      ctx.fill();
      
      // Círculo central con brillo
      ctx.beginPath();
      ctx.arc(centerX, centerY, 15, 0, Math.PI * 2);
      ctx.fillStyle = "rgba(59, 130, 246, 0.6)";
      ctx.fill();
      ctx.strokeStyle = "#3b82f6";
      ctx.lineWidth = 2;
      ctx.stroke();
      
      // Agregar efecto de radar giratorio
      ctx.beginPath();
      const now = Date.now() / 1000;
      const scanAngle = (now % 4) * (Math.PI / 2);
      ctx.moveTo(centerX, centerY);
      ctx.arc(centerX, centerY, radius + 20, scanAngle, scanAngle + 0.05);
      ctx.strokeStyle = "rgba(59, 130, 246, 0.8)";
      ctx.lineWidth = 3;
      ctx.stroke();
      
      // Configurar animación
      requestAnimationFrame(createChart);
    };
    
    onMounted(() => {
      try {
        hoverSound.value = new Audio('/sounds/hover.mp3');
        hoverSound.value.volume = 0.2;
      } catch (e) {
        console.log('Audio initialization error:', e);
      }
      
      fetchStats();
    });
    
    return {
      loading,
      stats,
      chartCanvas,
      playHoverSound
    };
  }
};
</script>

<style scoped>
/* Panel de juego */
.game-panel {
  background-color: rgba(10, 25, 47, 0.8);
  border: 1px solid #3b82f6;
  border-radius: 8px;
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.3),
              inset 0 0 10px rgba(59, 130, 246, 0.1);
  overflow: hidden;
  backdrop-filter: blur(8px);
  position: relative;
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

/* Animación de scanline para efecto de pantalla */
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
  0% {
    top: 0%;
  }
  100% {
    top: 100%;
  }
}

/* Tarjetas de estadísticas */
.stat-card {
  display: flex;
  align-items: center;
  padding: 1rem;
  border-radius: 6px;
  border: 1px solid rgba(59, 130, 246, 0.3);
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.2),
              inset 0 0 10px rgba(59, 130, 246, 0.05);
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(to right, transparent, rgba(59, 130, 246, 0.5), transparent);
}

.stat-icon {
  background-color: rgba(59, 130, 246, 0.2);
  border-radius: 50%;
  padding: 0.75rem;
  margin-right: 1rem;
  color: #90e0ef;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.stat-icon.success {
  background-color: rgba(16, 185, 129, 0.2);
  color: #10b981;
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.stat-icon.danger {
  background-color: rgba(239, 68, 68, 0.2);
  color: #ef4444;
  box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.stat-icon.warning {
  background-color: rgba(245, 158, 11, 0.2);
  color: #f59e0b;
  box-shadow: 0 0 10px rgba(245, 158, 11, 0.3);
  border: 1px solid rgba(245, 158, 11, 0.3);
}

.stat-content {
  flex: 1;
}

.stat-title {
  font-size: 0.75rem;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 0.25rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: bold;
  color: #e2e8f0;
}

.stat-value.success {
  color: #10b981;
  text-shadow: 0 0 5px rgba(16, 185, 129, 0.5);
}

.stat-value.danger {
  color: #ef4444;
  text-shadow: 0 0 5px rgba(239, 68, 68, 0.5);
}

.stat-value.warning {
  color: #f59e0b;
  text-shadow: 0 0 5px rgba(245, 158, 11, 0.5);
}

/* Contenedor de gráfica */
.chart-container {
  background-color: rgba(17, 34, 64, 0.6);
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid rgba(59, 130, 246, 0.3);
  position: relative;
  margin-top: 1.5rem;
  height: 300px;
}

.chart-labels {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background-color: rgba(10, 25, 47, 0.8);
  padding: 0.75rem;
  border-radius: 4px;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.chart-label {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.chart-label:last-child {
  margin-bottom: 0;
}

.chart-label-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-right: 0.5rem;
}

.chart-label.success .chart-label-dot {
  background-color: rgba(16, 185, 129, 0.7);
  box-shadow: 0 0 5px rgba(16, 185, 129, 0.7);
}

.chart-label.danger .chart-label-dot {
  background-color: rgba(239, 68, 68, 0.7);
  box-shadow: 0 0 5px rgba(239, 68, 68, 0.7);
}

/* Acciones de juego */
.game-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1.5rem;
}

@media (min-width: 640px) {
  .game-actions {
    flex-direction: row;
    justify-content: center;
  }
}

/* Botones de juego */
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
  font-size: 0.875rem;
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

/* Loader */
.radar-loader {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0) 70%);
  position: relative;
}

.radar-loader::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 2px solid transparent;
  border-top-color: #3b82f6;
  border-right-color: rgba(59, 130, 246, 0.6);
  border-bottom-color: rgba(59, 130, 246, 0.2);
  transform: translate(-50%, -50%);
  animation: radar-spin 1s linear infinite;
}

.radar-loader::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 8px;
  height: 8px;
  background: #3b82f6;
  border-radius: 50%;
  transform: translate(-50%, -50%);
  box-shadow: 0 0 10px #3b82f6;
}

@keyframes radar-spin {
  0% {
    transform: translate(-50%, -50%) rotate(0deg);
  }
  100% {
    transform: translate(-50%, -50%) rotate(360deg);
  }
}
</style>