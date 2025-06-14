<template>
  <div>
    <h2>Partidas {{ tipo }}</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="partida in partidas" :key="partida.id">
          <td>{{ partida.id }}</td>
          <td>{{ partida.created_at }}</td>
          <td>
            <button @click="verDetalle(partida.id)">Ver Tableros</button>
          </td>
        </tr>
      </tbody>
    </table>
    <button @click="volver">Volver a gráfica</button>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
  props: {
    partidas: Array,
    tipo: String,
  },
  methods: {
    verDetalle(id) {
      Inertia.get(`/estadisticas/partida/${id}`);
    },
    volver() {
      Inertia.get('/estadisticas');
    }
  }
}
</script>