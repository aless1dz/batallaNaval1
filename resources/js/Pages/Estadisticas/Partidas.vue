<template>
  <AuthenticatedLayout>
    <template #header>
      <h1>Partidas {{ tipo === 'ganadas' ? 'Ganadas' : 'Perdidas' }}</h1>
    </template>
    <div class="container">
      <table class="min-w-full border">
        <thead>
          <tr>
            <th class="border px-4 py-2">ID</th>
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
                Ver Partida
              </PrimaryButton>
            </td>
          </tr>
        </tbody>
      </table>
      <Link :href="route('estadisticas.index')" class="inline-block mt-4">
        <PrimaryButton>
          Volver a gráfica
        </PrimaryButton>
      </Link>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

export default {
  components: {
    AuthenticatedLayout,
    PrimaryButton,
    Link,
  },
  props: {
    partidas: Array,
    tipo: String,
  },
  methods: {
    verDetalle(id) {
      router.get(`/estadisticas/partida/${id}`);
    },
  },
};
</script>