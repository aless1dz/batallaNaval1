<template>
  <AuthenticatedLayout>
    <template #header>
      <h1>Estadísticas de tus enfrentamientos</h1>
    </template>
    <div class="container">
        <Link :href="route('dashboard')" class="inline-block mt-4">
          <PrimaryButton>
            Volver al Dashboard
          </PrimaryButton>
        </Link>
      <EstadisticasGrafica
        :ganadas="ganadas"
        :perdidas="perdidas"
        @barClick="irAPartidas"
      />
    </div>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EstadisticasGrafica from '@/Components/EstadisticasGrafica.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

export default {
  components: {
    AuthenticatedLayout,
    EstadisticasGrafica,
    PrimaryButton,
    Link,
  },
  props: {
    ganadas: Number,
    perdidas: Number,
  },
  methods: {
    irAPartidas(tipo) {
      router.get(`/estadisticas/partidas/${tipo}`);
    },
  },
};
</script>