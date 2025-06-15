<!-- resources/js/Components/Tablero.vue -->
<template>
  <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200">
    <!-- Encabezado del tablero -->
    <div class="mb-4">
      <h3 class="text-lg font-semibold text-gray-800">
        {{ esPropio ? 'Tu Tablero' : `Tablero de ${nombreJugador}` }}
      </h3>
      <div class="text-sm text-gray-600" v-if="estadisticas">
        <span>
          Disparos: {{ estadisticas.total_disparos }} | 
          Impactos: {{ estadisticas.impactos }} | 
          Precisión: {{ estadisticas.precision }}%
        </span>
      </div>
    </div>

    <!-- Grilla del tablero -->
    <div class="inline-block border-2 border-gray-800 bg-blue-50">
      <!-- Coordenadas horizontales -->
      <div class="flex">
        <div class="w-8 h-6"></div>
        <div 
          v-for="n in 10" 
          :key="`col-${n}`"
          class="w-8 h-6 flex items-center justify-center text-xs font-bold text-gray-700 bg-gray-200 border-r border-gray-400"
        >
          {{ n }}
        </div>
      </div>

      <!-- Filas del tablero -->
      <div 
        v-for="(letra, filaIndex) in letras" 
        :key="`fila-${filaIndex}`"
        class="flex border-b border-gray-400 last:border-b-0"
      >
        <!-- Coordenada vertical -->
        <div class="w-8 h-8 flex items-center justify-center text-xs font-bold text-gray-700 bg-gray-200 border-r border-gray-400">
          {{ letra }}
        </div>

        <!-- Celdas de la fila -->
        <div 
          v-for="columna in 10" 
          :key="`celda-${filaIndex}-${columna}`"
          class="w-8 h-8 border-r border-gray-400 last:border-r-0 relative bg-blue-100 transition-colors duration-150"
          :class="obtenerClaseCelda(letra, columna)"
          @click="manejarClickCelda(letra, columna)"
          @mouseenter="manejarHoverCelda(letra, columna, true)"
          @mouseleave="manejarHoverCelda(letra, columna, false)"
          :title="obtenerTooltipCelda(letra, columna)"
        >
          <!-- Contenido de la celda -->
          <div class="w-full h-full flex items-center justify-center relative">
            <!-- Barco (solo si debe mostrarse y no ha sido disparado) -->
            <div 
              v-if="mostrarBarcos && tieneBarco(letra, columna) && !fueDisparado(letra, columna)" 
              class="w-full h-full bg-gray-700 border border-gray-800"
              :class="{ 'bg-red-800': estaHundido(letra, columna) }"
            ></div>

            <!-- Disparo -->
            <div v-if="fueDisparado(letra, columna)" class="w-full h-full flex items-center justify-center text-lg font-bold">
              <div 
                v-if="esImpacto(letra, columna)" 
                class="text-red-600"
                :class="{ 'text-white': estaHundido(letra, columna) }"
              >
                {{ estaHundido(letra, columna) ? '✕' : '●' }}
              </div>
              <div v-else class="text-blue-600">○</div>
            </div>

            <!-- Indicador de hover para disparo -->
            <div 
              v-if="!esPropio && puedeDisparar && hover.activo && 
                    hover.posicion === (letra + columna) && 
                    !fueDisparado(letra, columna)" 
              class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-yellow-600 bg-yellow-100 bg-opacity-50"
            >
              +
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Información adicional -->
    <div v-if="mostrarInfo" class="mt-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 bg-gray-50 p-3 rounded border">
        <div>
          <strong>Barcos ({{ posicionesBarcos.length }}):</strong>
          <div class="mt-1 flex flex-wrap gap-1">
            <span 
              v-for="posicion in posicionesBarcos" 
              :key="posicion"
              class="px-2 py-1 bg-gray-200 rounded text-xs"
              :class="{ 'bg-red-200 line-through': estaHundidoPorPosicion(posicion) }"
            >
              {{ posicion }}
            </span>
          </div>
        </div>
        <div v-if="ultimoDisparo">
          <strong>Último disparo:</strong>
          <div class="mt-1">
            {{ ultimoDisparo.posicion }} - {{ ultimoDisparo.resultado }}
          </div>
        </div>
      </div>
    </div>

    <!-- Estado del juego -->
    <div v-if="juegoTerminado" class="mt-4 p-3 rounded border-2" :class="ganador === 'jugador' ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50'">
      <div class="font-bold text-center">
        {{ ganador === 'jugador' ? '¡Has Ganado!' : '¡Has Perdido!' }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Tablero',
  
  props: {
    // Configuración básica
    esPropio: {
      type: Boolean,
      default: false
    },
    nombreJugador: {
      type: String,
      default: 'Oponente'
    },
    
    // Posiciones de barcos (array de strings como ['A1', 'B3', 'C5'])
    posicionesBarcos: {
      type: Array,
      default: () => []
    },
    
    // Disparos realizados (array de objetos con posicion, impacto, hundido)
    disparos: {
      type: Array,
      default: () => []
    },
    
    // Interactividad
    puedeDisparar: {
      type: Boolean,
      default: false
    },
    
    // Mostrar elementos
    mostrarBarcos: {
      type: Boolean,
      default: true
    },
    mostrarInfo: {
      type: Boolean,
      default: true
    },
    
    // Estadísticas
    estadisticas: {
      type: Object,
      default: null
    },
    
    // Estado del juego
    juegoTerminado: {
      type: Boolean,
      default: false
    },
    ganador: {
      type: String,
      default: null // 'jugador' o 'oponente'
    }
  },
  
  emits: ['disparar', 'celda-hover'],
  
  data() {
    return {
      letras: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
      hover: {
        activo: false,
        posicion: null
      }
    }
  },
  
  computed: {
    ultimoDisparo() {
      return this.disparos.length > 0 ? this.disparos[this.disparos.length - 1] : null;
    },
    
    // Mapear disparos por posición para acceso rápido
    disparosPorPosicion() {
      const mapa = {};
      this.disparos.forEach(disparo => {
        mapa[disparo.posicion] = disparo;
      });
      return mapa;
    }
  },
  
  methods: {
    manejarClickCelda(letra, columna) {
      const posicion = letra + columna;
      
      // Solo permitir disparar en tablero enemigo
      if (!this.esPropio && this.puedeDisparar && !this.fueDisparado(letra, columna)) {
        this.$emit('disparar', { 
          posicion: posicion,
          fila: letra,
          columna: columna 
        });
      }
    },
    
    manejarHoverCelda(letra, columna, entrando) {
      if (!this.esPropio && this.puedeDisparar) {
        const posicion = letra + columna;
        this.hover = {
          activo: entrando,
          posicion: entrando ? posicion : null
        };
        
        this.$emit('celda-hover', { 
          posicion: posicion,
          fila: letra, 
          columna: columna, 
          entrando 
        });
      }
    },
    
    obtenerClaseCelda(letra, columna) {
      const clases = [];
      const posicion = letra + columna;
      const disparo = this.disparosPorPosicion[posicion];
      
      // Estados de disparo
      if (disparo) {
        clases.push('bg-white');
        if (disparo.impacto) {
          clases.push('bg-red-200');
          if (disparo.hundido) {
            clases.push('bg-red-600');
          }
        } else {
          clases.push('bg-blue-200');
        }
      }
      
      // Barco visible (solo si debe mostrarse y no fue disparado)
      else if (this.mostrarBarcos && this.tieneBarco(letra, columna)) {
        clases.push('bg-gray-600');
      }
      
      // Interactividad
      if (!this.esPropio && this.puedeDisparar && !disparo) {
        clases.push('cursor-pointer', 'hover:bg-yellow-200');
      }
      
      return clases;
    },
    
    obtenerTooltipCelda(letra, columna) {
      const posicion = letra + columna;
      const disparo = this.disparosPorPosicion[posicion];
      
      if (disparo) {
        if (disparo.impacto) {
          return disparo.hundido ? `${posicion} - Hundido` : `${posicion} - Impacto`;
        } else {
          return `${posicion} - Agua`;
        }
      }
      
      if (!this.esPropio && this.puedeDisparar) {
        return `${posicion} - Click para disparar`;
      }
      
      return posicion;
    },
    
    // Verificar si hay barco en esta posición
    tieneBarco(letra, columna) {
      const posicion = letra + columna;
      return this.posicionesBarcos.includes(posicion);
    },
    
    // Verificar si fue disparado
    fueDisparado(letra, columna) {
      const posicion = letra + columna;
      return !!this.disparosPorPosicion[posicion];
    },
    
    // Verificar si es impacto
    esImpacto(letra, columna) {
      const posicion = letra + columna;
      const disparo = this.disparosPorPosicion[posicion];
      return disparo && disparo.impacto;
    },
    
    // Verificar si está hundido
    estaHundido(letra, columna) {
      const posicion = letra + columna;
      const disparo = this.disparosPorPosicion[posicion];
      return disparo && disparo.hundido;
    },
    
    // Verificar si está hundido por posición
    estaHundidoPorPosicion(posicion) {
      const disparo = this.disparosPorPosicion[posicion];
      return disparo && disparo.hundido;
    }
  }
}
</script>
