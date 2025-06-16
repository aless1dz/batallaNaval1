<template>
  <AuthenticatedLayout>
    <template #header>
      <h1>Mis Partidas</h1>
    </template>
    <div class="container">
      <table class="min-w-full border">
        <thead>
          <tr>
            <th class="border px-4 py-2">Nombre</th>
            <th class="border px-4 py-2">Estado</th>
            <th class="border px-4 py-2">Resultado</th>
            <th class="border px-4 py-2">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="partida in partidas" :key="partida.id">
            <td class="border px-4 py-2">{{ partida.nombre }}</td>
            <td class="border px-4 py-2">
              <span v-if="partida.estado === 'en_curso'" class="text-green-600 font-bold">En curso</span>
              <span v-else-if="partida.estado === 'finalizada'" class="text-gray-600">Finalizada</span>
              <span v-else class="text-yellow-600">Esperando</span>
            </td>
            <td class="border px-4 py-2">
            <span v-if="partida.resultado === 'Ganada'" class="text-green-600 font-bold">Ganada</span>
            <span v-else-if="partida.resultado === 'Perdida'" class="text-red-600 font-bold">Perdida</span>
            <span v-else>-</span>
            </td>
            <td class="border px-4 py-2">
                <Link
                    v-if="partida.estado === 'en_progreso'"
                    :href="route('juego.tablero', partida.id)"
                    class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600"
                >
                    Unirse
                </Link>
                <Link
                    v-else-if="partida.estado === 'finalizada'"
                    :href="route('estadisticas.detalle', [partida.id]) + '?from=mis-partidas'"
                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
                >
                    Ver detalles
                </Link>
                <span v-else>-</span>
            </td>
          </tr>
        </tbody>
      </table>
      <Link :href="route('dashboard')" class="inline-block mt-4">
        <PrimaryButton>
          Volver al Dashboard
        </PrimaryButton>
      </Link>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

export default {
  components: {
    AuthenticatedLayout,
    PrimaryButton,
    Link,
  },
  props: {
    partidas: Array,
  },
};
</script>