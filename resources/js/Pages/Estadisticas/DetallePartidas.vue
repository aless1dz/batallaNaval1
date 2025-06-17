<template>
  <AuthenticatedLayout>
    <template #header>
      <h1>Detalle de Partida #{{ partida.id }}</h1>
    </template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      
      <div v-if="miJugador">
        <h2 class="text-lg font-bold mb-2">Tu tablero ({{ miJugador.usuario.name }})</h2>
        <Tablero
          :es-propio="true"
          :nombre-jugador="miJugador.usuario.name"
          :posiciones-barcos="miJugador.barcos ? miJugador.barcos.map(b => b.coordenada) : []"
          :disparos="miJugador.movimientos_defensor
            ? miJugador.movimientos_defensor.map(m => ({
                posicion: m.coordenada,
                impacto: m.acierto,
                hundido: m.hundido,
                resultado: m.hundido ? 'hundido' : (m.acierto ? 'impacto' : 'agua')
              }))
            : []"
          :puede-disparar="false"
          :mostrar-barcos="true"
          :mostrar-info="false"
        />
        <h3 class="mt-4 font-semibold">Movimientos recibidos:</h3>
        <ul class="text-sm list-disc ml-5">
          <li v-for="(mov, idx) in miJugador.movimientos_defensor" :key="idx">
            {{ mov.coordenada }} - 
            <span :class="mov.acierto ? 'text-green-600' : 'text-blue-600'">
              {{ mov.acierto ? (mov.hundido ? 'Hundido' : 'Impacto') : 'Agua' }}
            </span>
          </li>
        </ul>
      </div>

      
      <div v-if="oponenteJugador">
        <h2 class="text-lg font-bold mb-2">Tablero de {{ oponenteJugador.usuario.name }}</h2>
        <Tablero
          :es-propio="false"
          :nombre-jugador="oponenteJugador.usuario.name"
          :posiciones-barcos="oponenteJugador.barcos ? oponenteJugador.barcos.map(b => b.coordenada) : []"
          :disparos="oponenteJugador.movimientos_defensor
            ? oponenteJugador.movimientos_defensor.map(m => ({
                posicion: m.coordenada,
                impacto: m.acierto,
                hundido: m.hundido,
                resultado: m.hundido ? 'hundido' : (m.acierto ? 'impacto' : 'agua')
              }))
            : []"
          :puede-disparar="false"
          :mostrar-barcos="true"
          :mostrar-info="false"
        />
        <h3 class="mt-4 font-semibold">Movimientos recibidos:</h3>
        <ul class="text-sm list-disc ml-5">
          <li v-for="(mov, idx) in oponenteJugador.movimientos_defensor" :key="idx">
            {{ mov.coordenada }} - 
            <span :class="mov.acierto ? 'text-green-600' : 'text-blue-600'">
              {{ mov.acierto ? (mov.hundido ? 'Hundido' : 'Impacto') : 'Agua' }}
            </span>
          </li>
        </ul>
      </div>
    </div>
    <PrimaryButton class="mt-8" @click="volver">
      Volver a partidas
    </PrimaryButton>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Tablero from '@/Components/Tablero.vue';
import { router } from '@inertiajs/vue3';

export default {
  components: {
    AuthenticatedLayout,
    PrimaryButton,
    Tablero,
  },
  props: {
    partida: Object,
    from: String,
  },
  computed: {
    miJugador() {
      return this.partida.jugadores.find(j => j.usuario.id === this.$page.props.auth.user.id);
    },
    oponenteJugador() {
      return this.partida.jugadores.find(j => j.usuario.id !== this.$page.props.auth.user.id);
    },
  },
  methods: {
    volver() {
      if (this.from === 'mis-partidas') {
        router.get('/mis-partidas');
      } else if (this.from === 'ganadas' || this.from === 'perdidas') {
        router.get(`/estadisticas/partidas/${this.from}`);
      } else {
        router.get('/mis-partidas');
      }
    },
  },
};
</script>

<style scoped>
.grid {
  display: grid;
  gap: 2rem;
}
.tablero-container {
  border: 1px solid #ccc;
  padding: 1rem;
  border-radius: 8px;
  min-width: 320px;
}
.tablero {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}
.fila {
  display: flex;
}
.celda {
  width: 24px;
  height: 24px;
  border: 1px solid #bbb;
  margin: 1px;
  background: #f9f9f9;
  display: flex;
  align-items: center;
  justify-content: center;
}
.celda.barco {
  background: #4caf50;
}
.celda.hundido {
  background: #f44336;
}
.celda.disparo {
  border: 2px solid #2196f3;
}
.celda.impacto {
  background: gold;
}
</style>