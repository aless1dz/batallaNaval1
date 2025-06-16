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
        <h3>Tablero de {{ jugador.usuario.name || jugador.usuario.nombre_usuario }}</h3>
        <div class="tablero">
          <div v-for="fila in tableroPorJugador[jugador.id]?.grilla" :key="fila[0]?.fila" class="fila">
            <div
              v-for="celda in fila"
              :key="celda.columna"
              class="celda"
              :class="{
                barco: celda.tieneBarco,
                disparo: celda.disparado,
                impacto: celda.impacto,
                hundido: celda.hundido
              }"
            >
              
              <span v-if="celda.impacto">💥</span>
              <span v-else-if="celda.tieneBarco">🚢</span>
              <span v-else-if="celda.disparado">•</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <PrimaryButton class="mt-4" @click="volver">
      Volver a partidas
    </PrimaryButton>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Tablero from '@/models/tablero.js';
import { router } from '@inertiajs/vue3';

export default {
  components: {
    AuthenticatedLayout,
    PrimaryButton,
  },
  props: {
    partida: Object,
    tipo: String,
    from: String,
  },
  data() {
    return {
      tableroPorJugador: {},
    };
  },
  mounted() {
    this.generarTableros();
  },
  methods: {
  volver() {
    if (this.from === 'mis-partidas') {
      router.get('/mis-partidas');
    } else if (this.from === 'ganadas' || this.from === 'perdidas') {
      router.get(`/estadisticas/partidas/${this.from}`);
    } else {
      router.get('/mis-partidas'); // fallback
    }
  },
    generarTableros() {
      
      this.tableroPorJugador = {};
      this.partida.jugadores.forEach(jugador => {
        const tablero = new Tablero(10);

        
        jugador.barcos.forEach(barco => {
          
          const fila = "ABCDEFGHIJ".indexOf(barco.coordenada[0]);
          const columna = parseInt(barco.coordenada.slice(1), 10) - 1;
          if (fila >= 0 && columna >= 0) {
            tablero.grilla[fila][columna].tieneBarco = true;
            tablero.grilla[fila][columna].hundido = barco.hundido;
          }
        });

       
        if (jugador.movimientos_defensor) {
          jugador.movimientos_defensor.forEach(mov => {
            const fila = "ABCDEFGHIJ".indexOf(mov.coordenada[0]);
            const columna = parseInt(mov.coordenada.slice(1), 10) - 1;
            if (fila >= 0 && columna >= 0) {
              tablero.grilla[fila][columna].disparado = true;
              tablero.grilla[fila][columna].impacto = mov.acierto;
            }
          });
        }

        this.tableroPorJugador[jugador.id] = tablero;
      });
    },
  },
  watch: {
    partida: {
      handler() {
        this.generarTableros();
      },
      deep: true,
      immediate: true,
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