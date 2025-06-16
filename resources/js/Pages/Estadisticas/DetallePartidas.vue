<template>
  <AuthenticatedLayout>
    <template #header>
      <h1>Detalle de Partida #{{ partida.id }}</h1>
    </template>
    <div class="flex gap-8">
      <div
        v-for="jugador in partida.jugadores"
        :key="jugador.id"
        class="tablero-container"
      >
        <Tablero
          :esPropio="false"
          :nombreJugador="jugador.usuario.name || jugador.usuario.nombre_usuario"
          :posicionesBarcos="jugador.barcos.map(b => b.coordenada)"
          :disparos="jugador.movimientos_defensor.map(m => ({
            posicion: m.coordenada,
            impacto: m.acierto ?? false, // true si fue acierto, false si no
            hundido: false // pon true si tienes esa info en el movimiento/barco
          }))"
          :mostrarBarcos="true"
          :mostrarInfo="true"
        />
        <h4 class="mt-4">Barcos:</h4>
        <ul>
          <li
            v-for="barco in jugador.barcos"
            :key="barco.id"
            :style="{ color: barco.hundido ? 'red' : 'black' }"
          >
            {{ barco.coordenada }} <span v-if="barco.hundido">(Hundido)</span>
          </li>
        </ul>
        <h4>Movimientos recibidos:</h4>
        <ul>
          <li v-for="mov in jugador.movimientos_defensor" :key="mov.id">
            {{ mov.coordenada }} <span v-if="mov.acierto">(Acierto)</span>
          </li>
        </ul>
        <h4>Movimientos realizados:</h4>
        <ul>
          <li v-for="mov in jugador.movimientos_atacante" :key="mov.id">
            {{ mov.coordenada }} <span v-if="mov.acierto">(Acierto)</span>
          </li>
        </ul>
      </div>
    </div>
    <PrimaryButton class="mt-4" @click="volver">
      Volver a partidas
    </PrimaryButton>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Tablero from '@/Pages/Juegos/Partida.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { router } from '@inertiajs/vue3';

export default {
  components: {
    AuthenticatedLayout,
    Tablero,
    PrimaryButton,
  },
  props: {
    partida: Object,
  },
  methods: {
    volver() {
      router.get('/estadisticas/partidas/ganadas'); // o 'perdidas', según tu flujo
    },
  },
};
</script>

<style scoped>
.flex {
  display: flex;
  gap: 2rem;
}
.tablero-container {
  border: 1px solid #ccc;
  padding: 1rem;
  border-radius: 8px;
  min-width: 320px;
}
</style>