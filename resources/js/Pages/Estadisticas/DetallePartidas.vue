
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