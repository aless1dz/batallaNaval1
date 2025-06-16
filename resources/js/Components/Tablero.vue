<!-- resources/js/Components/Tablero.vue -->
<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <!-- Header del tablero -->
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">
        {{ esPropio ? 'Tu Tablero' : `Tablero de ${nombreJugador}` }}
      </h2>
      <div v-if="mostrarInfo" class="text-sm text-gray-600">
        Disparos: {{ estadisticas.total_disparos }} | 
        Impactos: {{ estadisticas.impactos }} | 
        Precisión: {{ estadisticas.precision }}%
      </div>
    </div>

    <!-- Coordenadas superiores (A-J) -->
    <div class="grid grid-cols-11 gap-1 mb-2">
      <div></div> <!-- Espacio vacío para la esquina -->
      <div 
        v-for="letra in letras" 
        :key="letra"
        class="h-8 flex items-center justify-center text-sm font-medium text-gray-600"
      >
        {{ letra }}
      </div>
    </div>

    <!-- Tablero principal -->
    <div class="grid grid-cols-11 gap-1">
      <!-- Fila por fila -->
      <template v-for="(numero, filaIndex) in numeros" :key="'fila-' + numero">
        <!-- Número de fila -->
        <div class="h-8 flex items-center justify-center text-sm font-medium text-gray-600">
          {{ numero }}
        </div>
        
        <!-- Celdas de la fila -->
        <div 
          v-for="(letra, colIndex) in letras"
          :key="letra + numero"
          class="h-8 w-8 border-2 border-gray-300 cursor-pointer transition-all duration-200 flex items-center justify-center text-xs font-bold"
          :class="obtenerClaseCelda(letra + numero)"
          @click="manejarClick(letra + numero)"
          @mouseenter="$emit('celda-hover', { posicion: letra + numero })"
        >
          {{ obtenerContenidoCelda(letra + numero) }}
        </div>
      </template>
    </div>

    <!-- Leyenda -->
    <div class="mt-4 flex flex-wrap gap-4 text-xs">
      <div class="flex items-center gap-1">
        <div class="w-4 h-4 bg-gray-300 border-2 border-gray-400"></div>
        <span>Agua</span>
      </div>
      <div v-if="mostrarBarcos" class="flex items-center gap-1">
        <div class="w-4 h-4 bg-blue-500 border-2 border-blue-600"></div>
        <span>Barco</span>
      </div>
      <div class="flex items-center gap-1">
        <div class="w-4 h-4 bg-blue-400 border-2 border-blue-500"></div>
        <span>Agua (disparado)</span>
      </div>
      <div class="flex items-center gap-1">
        <div class="w-4 h-4 bg-red-500 border-2 border-red-600"></div>
        <span>Impacto</span>
      </div>
      <div class="flex items-center gap-1">
        <div class="w-4 h-4 bg-red-800 border-2 border-red-900"></div>
        <span>Hundido</span>
      </div>
    </div>

    <!-- Mensaje de estado -->
    <div v-if="juegoTerminado" class="mt-4 p-4 rounded-lg text-center font-bold text-lg">
      <div v-if="ganador === 'jugador'" class="bg-green-100 text-green-800">
        ¡Has ganado!
      </div>
      <div v-else-if="ganador === 'oponente'" class="bg-red-100 text-red-800">
        Has perdido
      </div>
      <div v-else class="bg-yellow-100 text-yellow-800">
        Juego terminado
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'Tablero',
  
  props: {
    esPropio: {
      type: Boolean,
      default: false
    },
    nombreJugador: {
      type: String,
      required: true
    },
    posicionesBarcos: {
      type: Array,
      default: () => []
    },
    disparos: {
      type: Array,
      default: () => []
    },
    puedeDisparar: {
      type: Boolean,
      default: false
    },
    mostrarBarcos: {
      type: Boolean,
      default: false
    },
    mostrarInfo: {
      type: Boolean,
      default: true
    },
    estadisticas: {
      type: Object,
      default: () => ({
        total_disparos: 0,
        impactos: 0,
        precision: 0
      })
    },
    juegoTerminado: {
      type: Boolean,
      default: false
    },
    ganador: {
      type: String,
      default: null
    }
  },
  
  emits: ['disparar', 'celda-hover'],
  
  setup(props, { emit }) {
    const letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J']
    const numeros = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
    
    // Crear mapa de disparos para búsqueda rápida
    const mapaDisparos = computed(() => {
      const mapa = {}
      props.disparos.forEach(disparo => {
        mapa[disparo.posicion] = disparo
      })
      return mapa
    })
    
    // Verificar si una posición tiene barco
    const tieneBarco = (posicion) => {
      return props.posicionesBarcos.includes(posicion)
    }
    
    // Verificar si una posición fue disparada
    const fueDisparado = (posicion) => {
      return posicion in mapaDisparos.value
    }
    
    // Obtener información del disparo
    const obtenerDisparo = (posicion) => {
      return mapaDisparos.value[posicion] || null
    }
    
    // Obtener clases CSS para una celda
    const obtenerClaseCelda = (posicion) => {
      const disparo = obtenerDisparo(posicion)
      const hayBarco = tieneBarco(posicion)
      const disparado = fueDisparado(posicion)
      
      let clases = []
      
      // Estado base
      if (!disparado && !hayBarco) {
        clases.push('bg-gray-100 hover:bg-gray-200')
      }
      
      // Barco visible (solo en mi tablero o si fue hundido)
      if (hayBarco && (props.mostrarBarcos || (disparo && disparo.hundido))) {
        if (!disparado) {
          clases.push('bg-blue-500 border-blue-600')
        }
      }
      
      // Efectos de disparo
      if (disparado && disparo) {
        if (disparo.impacto) {
          if (disparo.hundido) {
            clases.push('bg-red-800 border-red-900 text-white') // Hundido
          } else {
            clases.push('bg-red-500 border-red-600 text-white') // Impacto
          }
        } else {
          clases.push('bg-blue-400 border-blue-500') // Agua
        }
      }
      
      // Efectos de interacción
      if (props.puedeDisparar && !disparado && !props.juegoTerminado) {
        clases.push('hover:bg-yellow-200 hover:border-yellow-400')
      }
      
      // Cursor
      if (props.puedeDisparar && !disparado && !props.juegoTerminado) {
        clases.push('cursor-pointer')
      } else {
        clases.push('cursor-default')
      }
      
      return clases.join(' ')
    }
    
    // Obtener contenido visual de una celda
    const obtenerContenidoCelda = (posicion) => {
      const disparo = obtenerDisparo(posicion)
      
      if (disparo) {
        if (disparo.hundido) {
          return '💥' // Hundido
        } else if (disparo.impacto) {
          return '🔥' // Impacto
        } else {
          return '💧' // Agua
        }
      }
      
      // Mostrar barco si corresponde
      if (tieneBarco(posicion) && props.mostrarBarcos) {
        return '🚢' // Barco
      }
      
      return '' // Celda vacía
    }
    
    // Manejar click en celda
    const manejarClick = (posicion) => {
      if (!props.puedeDisparar || props.juegoTerminado) return
      if (fueDisparado(posicion)) return
      
      emit('disparar', { posicion })
    }
    
    return {
      letras,
      numeros,
      obtenerClaseCelda,
      obtenerContenidoCelda,
      manejarClick,
      tieneBarco,
      fueDisparado,
      obtenerDisparo
    }
  }
}
</script>

<style scoped>
/* Estilos adicionales si necesitas */
.tablero-celda {
  aspect-ratio: 1;
}
</style>