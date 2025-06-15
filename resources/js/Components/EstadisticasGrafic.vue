<template>
  <div class="grafica-css">
    <div class="barra-container">
      <div
        class="barra ganadas"
        :style="{ height: porcentajeGanadas + '%' }"
        @click="$emit('barClick', 'ganadas')"
      >
        <span class="valor">{{ ganadas }}</span>
      </div>
      <span class="etiqueta">Ganadas</span>
    </div>
    <div class="barra-container">
      <div
        class="barra perdidas"
        :style="{ height: porcentajePerdidas + '%' }"
        @click="$emit('barClick', 'perdidas')"
      >
        <span class="valor">{{ perdidas }}</span>
      </div>
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
}
.barra {
  width: 60px;
  transition: height 0.4s;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  cursor: pointer;
  border-radius: 8px 8px 0 0;
  position: relative;
}
.ganadas {
  background: #4caf50;
}
.perdidas {
  background: #f44336;
}
.valor {
  color: #fff;
  font-weight: bold;
  margin-bottom: 8px;
  font-size: 1.2em;
}
.etiqueta {
  margin-top: 10px;
  font-weight: 500;
  color: #333;
}
</style>