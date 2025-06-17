<template>
  <div class="game-background min-h-screen text-gray-100">
    <!-- Efectos de fondo -->
    <div id="particles-js" class="absolute inset-0 z-0 opacity-30"></div>
    <div class="wave-container absolute inset-0 z-0">
      <div class="wave wave1"></div>
      <div class="wave wave2"></div>
      <div class="wave wave3"></div>
    </div>

    <!-- Barra de navegación -->
    <nav class="relative z-10 bg-opacity-80 bg-navy-900 border-b border-blue-500 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="shrink-0 flex items-center">
              <Link :href="route('dashboard')" class="text-xl font-bold">
                <div class="game-logo">
                  <span class="text-yellow-400 glow-text">BATALLA</span> 
                  <span class="text-blue-300 glow-text">NAVAL</span>
                </div>
              </Link>
            </div>

            <div class="hidden space-x-4 sm:-my-px sm:ml-10 sm:flex">
              <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="game-nav-link">
                CONTROL DE MANDO
              </NavLink>
              <NavLink :href="route('games.index')" :active="route().current('games.*')" class="game-nav-link">
    PARTIDAS
              </NavLink>
            </div>
          </div>

          <div class="hidden sm:flex sm:items-center sm:ml-6">
            <div class="ml-3 relative">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <span class="inline-flex rounded-md">
                    <button type="button" class="game-nav-button">
                      <span class="radar-ping"></span>
                      {{ $page.props.auth.user.name }}

                      <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </span>
                </template>

                <template #content>
                  <div class="game-dropdown">
                    <DropdownLink :href="route('profile.edit')" class="game-dropdown-link"> 
                      PERFIL DE ALMIRANTE 
                    </DropdownLink>
                    <DropdownLink :href="route('logout')" method="post" as="button" class="game-dropdown-link">
                      ABANDONAR EL PUENTE
                    </DropdownLink>
                  </div>
                </template>
              </Dropdown>
            </div>
          </div>

          <div class="-mr-2 flex items-center sm:hidden">
            <button @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="game-menu-toggle">
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-navy-800 border-t border-blue-500 border-opacity-30">
        <div class="pt-2 pb-3 space-y-1">
          <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')" class="mobile-game-link">
            CONTROL DE MANDO
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('games.index')" :active="route().current('games.*')" class="mobile-game-link">
            MISIONES
          </ResponsiveNavLink>
        </div>

        <div class="pt-4 pb-1 border-t border-blue-500 border-opacity-30">
          <div class="px-4 py-2 bg-navy-900 bg-opacity-70">
            <div class="font-medium text-base text-blue-300">{{ $page.props.auth.user.name }}</div>
            <div class="font-medium text-sm text-gray-400">{{ $page.props.auth.user.email }}</div>
          </div>

          <div class="mt-3 space-y-1">
            <ResponsiveNavLink :href="route('profile.edit')" class="mobile-game-link"> 
              PERFIL DE ALMIRANTE 
            </ResponsiveNavLink>
            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="mobile-game-link">
              ABANDONAR EL PUENTE
            </ResponsiveNavLink>
          </div>
        </div>
      </div>
    </nav>

    <header v-if="$slots.header" class="relative z-10 bg-navy-800 bg-opacity-80 shadow-lg border-b border-blue-500 border-opacity-30">
      <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <main class="relative z-10">
      <slot />
    </main>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { onMounted } from 'vue';

export default {
  components: {
    Dropdown,
    DropdownLink,
    Link,
    NavLink,
    ResponsiveNavLink,
  },
  data() {
    return {
      showingNavigationDropdown: false,
    };
  },
  mounted() {
    // Inicializar particles.js si está disponible
    if (typeof particlesJS !== 'undefined') {
      try {
        particlesJS("particles-js", {
          particles: {
            number: {
              value: 50,
              density: {
                enable: true,
                value_area: 1000
              }
            },
            color: {
              value: "#2a85ff"
            },
            shape: {
              type: "circle",
            },
            opacity: {
              value: 0.3,
              random: true,
            },
            size: {
              value: 2,
              random: true,
            },
            line_linked: {
              enable: true,
              distance: 150,
              color: "#2a85ff",
              opacity: 0.1,
              width: 1
            },
            move: {
              enable: true,
              speed: 1,
              direction: "none",
              random: true,
              straight: false,
              out_mode: "out",
              bounce: false,
            }
          },
          interactivity: {
            detect_on: "canvas",
            events: {
              onhover: {
                enable: true,
                mode: "bubble"
              },
              onclick: {
                enable: true,
                mode: "push"
              },
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

<style>
/* Variables globales */
:root {
  --navy-900: #0a192f;
  --navy-800: #112240;
  --navy-700: #1d3557;
  --blue-600: #1e40af;
  --blue-500: #2a85ff;
  --blue-400: #4895ef;
  --blue-300: #90e0ef;
  --gold: #ffd700;
  --orange: #FF7D00;
}

/* Fondo de juego */
.game-background {
  background-color: #0c182c;
  background-image: 
      radial-gradient(circle at 25% 25%, rgba(42, 133, 255, 0.1) 0%, transparent 50%), 
      radial-gradient(circle at 75% 75%, rgba(255, 215, 0, 0.05) 0%, transparent 50%);
}

/* Efecto de texto brillante */
.glow-text {
  text-shadow: 0 0 15px currentColor, 0 0 5px currentColor;
}

/* Estilos de navegación */
.game-nav-link {
  display: inline-flex;
  align-items: center;
  height: 100%;
  padding: 0 0.75rem;
  color: #90e0ef;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 1px;
  transition: all 0.2s;
  border-bottom: 2px solid transparent;
  position: relative;
}

.game-nav-link:hover, .game-nav-link.active {
  color: #ffffff;
  border-bottom-color: #3b82f6;
  text-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
}

.game-nav-link::before {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 50%;
  width: 0;
  height: 2px;
  background: #3b82f6;
  box-shadow: 0 0 8px #3b82f6;
  transition: all 0.3s ease;
}

.game-nav-link:hover::before {
  width: 100%;
  left: 0;
}

/* Botón de navegación */
.game-nav-button {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 0.75rem;
  border-radius: 4px;
  background: rgba(29, 78, 216, 0.2);
  border: 1px solid rgba(59, 130, 246, 0.3);
  color: #90e0ef;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all 0.2s ease;
  position: relative;
}

.game-nav-button:hover {
  background: rgba(29, 78, 216, 0.3);
  border-color: rgba(59, 130, 246, 0.6);
  box-shadow: 0 0 12px rgba(59, 130, 246, 0.4);
}

/* Botón de menú móvil */
.game-menu-toggle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem;
  border-radius: 4px;
  color: #90e0ef;
  transition: all 0.2s ease;
  border: 1px solid rgba(59, 130, 246, 0.3);
  background: rgba(29, 78, 216, 0.2);
}

.game-menu-toggle:hover {
  background: rgba(29, 78, 216, 0.3);
  border-color: rgba(59, 130, 246, 0.6);
  box-shadow: 0 0 12px rgba(59, 130, 246, 0.4);
}

/* Estilos de dropdown */
.game-dropdown {
  background-color: var(--navy-900);
  border: 1px solid #3b82f6;
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
  padding: 0.5rem 0;
  border-radius: 4px;
}

.game-dropdown-link {
  display: block;
  padding: 0.5rem 1rem;
  color: #90e0ef;
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 1px;
  transition: all 0.15s ease;
}

.game-dropdown-link:hover {
  background-color: rgba(59, 130, 246, 0.1);
  color: #ffffff;
  text-shadow: 0 0 5px rgba(144, 224, 239, 0.5);
}

/* Links móviles */
.mobile-game-link {
  padding: 0.75rem 1rem;
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 1px;
  color: #90e0ef;
  transition: all 0.2s;
  border-left: 3px solid transparent;
}

.mobile-game-link:hover, .mobile-game-link.active {
  background-color: rgba(59, 130, 246, 0.1);
  color: #ffffff;
  border-left-color: #3b82f6;
}

/* Efecto de ping radar */
.radar-ping {
  position: absolute;
  top: 50%;
  left: 10px;
  transform: translateY(-50%);
  width: 6px;
  height: 6px;
  background-color: #10B981;
  border-radius: 50%;
}

.radar-ping::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background-color: rgba(16, 185, 129, 0.3);
  transform: translate(-50%, -50%);
  animation: ping 2s ease-in-out infinite;
}

@keyframes ping {
  0% {
    transform: translate(-50%, -50%) scale(0);
    opacity: 1;
  }
  80%, 100% {
    transform: translate(-50%, -50%) scale(1);
    opacity: 0;
  }
}

/* Olas animadas */
.wave-container {
  overflow: hidden;
  pointer-events: none;
}

.wave {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 200%;
  height: 100px;
  background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg"><path d="M0 0v46.29c47.79 22.2 103.59 32.17 158 28 70.36-5.37 136.33-33.31 206.8-37.5 73.84-4.36 147.54 16.88 218.2 35.26 69.27 18 138.3 24.88 209.4 13.08 36.15-6 69.85-17.84 104.45-29.34C989.49 25 1113-14.29 1200 52.47V0z" opacity=".25" fill="%232a85ff" /></svg>') repeat-x;
  animation: wave 10s linear infinite;
  z-index: -1;
  transform: translateX(0);
  opacity: 0.1;
}

.wave1 {
  opacity: 0.1;
  bottom: 0;
  animation: wave 20s linear infinite;
}

.wave2 {
  opacity: 0.05;
  bottom: 10px;
  animation: wave2 15s linear infinite;
}

.wave3 {
  opacity: 0.07;
  bottom: 20px;
  animation: wave 30s linear infinite;
}

@keyframes wave {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}

@keyframes wave2 {
  0% {
    transform: translateX(-50%);
  }
  100% {
    transform: translateX(0);
  }
}
</style>