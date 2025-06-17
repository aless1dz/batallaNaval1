
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
        <TableroVisual
          :nombre-jugador="jugador.usuario.name || jugador.usuario.nombre_usuario"
          :barcos="jugador.barcos"
          :movimientos="jugador.movimientos_defensor"
          :es-propio="false"
          :mostrar-barcos="true"
        />
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
      router.get('/mis-partidas'); 
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
        <script>
        import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
        import PrimaryButton from '@/Components/PrimaryButton.vue';
        import TableroVisual from '@/Pages/Juegos/Tablero.vue'; // Usa el componente visual
        import { router } from '@inertiajs/vue3';
        
        export default {
          components: {
            AuthenticatedLayout,
            PrimaryButton,
            TableroVisual,
          },
          props: {
            partida: Object,
            from: String,
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