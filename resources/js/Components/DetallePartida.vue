<template>
  <div>
    <h2>Detalle de Partida #{{ partida.id }}</h2>
    <div class="flex gap-8">
      <div v-for="jugador in partida.jugadores" :key="jugador.id" class="tablero-container">
        <h3>Tablero de {{ jugador.usuario.nombre_usuario }}</h3>
        <!-- Aquí puedes renderizar una cuadrícula visual si quieres -->
        <ul>
          <li v-for="barco in jugador.barcos" :key="barco.id">
            Barco en {{ barco.coordenada }} <span v-if="barco.hundido">(Hundido)</span>
          </li>
        </ul>
        <h4>Movimientos realizados:</h4>
        <ul>
          <li v-for="mov in jugador.movimientos_atacante" :key="mov.id">
            Atacó en {{ mov.coordenada }} <span v-if="mov.acierto">(Acierto)</span>
          </li>
        </ul>
        <h4>Movimientos recibidos:</h4>
        <ul>
          <li v-for="mov in jugador.movimientos_defensor" :key="mov.id">
            Recibió en {{ mov.coordenada }} <span v-if="mov.acierto">(Acierto)</span>
          </li>
        </ul>
      </div>
    </div>
    <div class="mt-8">
      <h3>Todos los movimientos de la partida</h3>
      <ul>
        <li v-for="mov in movimientos" :key="mov.id">
          {{ mov.atacante.usuario.nombre_usuario }} atacó a {{ mov.defensor.usuario.nombre_usuario }} en {{ mov.coordenada }} <span v-if="mov.acierto">(Acierto)</span>
        </li>
      </ul>
    </div>
    <button @click="volver" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">
      Volver a partidas
    </button>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
  props: {
    partida: Object,
    movimientos: Array,
  },
  methods: {
    volver() {
      router.get('/estadisticas');
    },
  },
};
</script>

<style scoped>
.flex {
  display: flex;
}
.gap-8 {
  gap: 2rem;
}
.tablero-container {
  border: 1px solid #ccc;
  padding: 1rem;
  border-radius: 8px;
}
</style>