<template>
  <div>
    <h2>Detalle de Partida #{{ partida.id }}</h2>
    <pre>{{ partida }}</pre>
    <button @click="volver">Volver a partidas</button>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';

export default {
  props: {
    partida: Object,
  },
  methods: {
    volver() {
      Inertia.get('/estadisticas');
    }
  }
}
</script>