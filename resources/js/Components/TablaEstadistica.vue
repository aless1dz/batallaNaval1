<template>
  <div>
    <h2>Partidas {{ tipo === 'ganadas' ? 'Ganadas' : 'Perdidas' }}</h2>
    <table class="min-w-full border">
      <thead>
        <tr>
          <th class="border px-4 py-2">Fecha</th>
          <th class="border px-4 py-2">Acción</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="partida in partidas" :key="partida.id">
          <td class="border px-4 py-2">{{ partida.id }}</td>
          <td class="border px-4 py-2">{{ partida.created_at }}</td>
          <td class="border px-4 py-2">
            <PrimaryButton @click="verDetalle(partida.id)">
              Ver Tableros
            </PrimaryButton>
          </td>
        </tr>
      </tbody>
    </table>
    <button @click="volver" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">
      Volver a gráfica
    </button>
  </div>
</template>

<script>
import { router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

export default {
  name: 'TablaEstadistica',
  props: {
    partidas: Array,
    tipo: String,
  },
  components: {
    PrimaryButton,
  },
  methods: {
    verDetalle(id) {
      // Navega a la vista de detalle de la partida
      router.get(`/estadisticas/partida/${id}`);
    },
    volver() {
      router.get('/estadisticas');
    },
  },
};
</script>