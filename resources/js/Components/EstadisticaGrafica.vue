<template>
  <div class="grafica-css">
    <div class="barra-container">
      <span class="valor">{{ ganadas }}</span>
      <span class="porcentaje-texto">{{ porcentajeGanadas.toFixed(0) }}%</span>
      <div
        class="barra ganadas"
        :style="{ height: porcentajeGanadas + '%' }"
        @click="$emit('barClick', 'ganadas')"
      ></div>
      <span class="etiqueta">Ganadas</span>
    </div>
    <div class="barra-container">
      <span class="valor">{{ perdidas }}</span>
      <span class="porcentaje-texto">{{ porcentajePerdidas.toFixed(0) }}%</span>
      <div
        class="barra perdidas"
        :style="{ height: porcentajePerdidas + '%' }"
        @click="$emit('barClick', 'perdidas')"
      ></div>
      <span class="etiqueta">Perdidas</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EstadisticasGrafica',
  props: {
    ganadas: { type: Number, required: true },
    perdidas: { type: Number, required: true },
  },
  computed: {
    total() {
      return this.ganadas + this.perdidas || 1;
    },
    porcentajeGanadas() {
      return (this.ganadas / this.total) * 100;
    },
    porcentajePerdidas() {
      return (this.perdidas / this.total) * 100;
    },
  },
};
</script>

<style scoped>
.grafica-css {
  display: flex;
  gap: 40px;
  align-items: flex-end;
  height: 220px;
  margin: 40px 0;
}
.barra-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 80px;
  height: 100%;
  justify-content: flex-end;
}
.valor {
  color: #333;
  font-weight: bold;
  font-size: 1.5em;
  margin-bottom: 8px;
}
.porcentaje-texto {
  color: #333;
  font-weight: bold;
  font-size: 1em;
  margin-bottom: 4px;
}
.barra {
  width: 60px;
  transition: height 0.4s;
  border-radius: 8px 8px 0 0;
  margin-bottom: 8px;
  display: block;
}
.ganadas {
  background: #4caf50;
}
.perdidas {
  background: #f44336;
}
.etiqueta {
  margin-top: 10px;
  font-weight: 500;
  color: #333;
}
</style>
