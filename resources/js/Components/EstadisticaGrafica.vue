<template>
  <div class="grafica-modern">
    <div class="barra-container" v-for="(item, idx) in datos" :key="idx">
      <span class="valor">{{ item.valor }}</span>
      <div class="barra-outer">
        <div
          class="barra-inner"
          :class="item.clase"
          :style="{ height: item.porcentaje + '%' }"
          @click="$emit('barClick', item.tipo)"
        >
          <span class="porcentaje">{{ item.porcentaje.toFixed(0) }}%</span>
        </div>
      </div>
      <span class="etiqueta">{{ item.etiqueta }}</span>
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
    datos() {
      return [
        {
          tipo: 'ganadas',
          valor: this.ganadas,
          porcentaje: this.porcentajeGanadas,
          clase: 'ganadas',
          etiqueta: 'Ganadas',
        },
        {
          tipo: 'perdidas',
          valor: this.perdidas,
          porcentaje: this.porcentajePerdidas,
          clase: 'perdidas',
          etiqueta: 'Perdidas',
        },
      ];
    },
  },
};
</script>

<style scoped>
.grafica-modern {
  display: flex;
  gap: 48px;
  align-items: flex-end;
  justify-content: center;
  height: 260px;
  margin: 48px 0 32px 0;
  background: linear-gradient(135deg, #f8fafc 60%, #e0e7ef 100%);
  border-radius: 24px;
  box-shadow: 0 6px 32px 0 rgba(60, 72, 100, 0.1);
  padding: 32px 0;
}

.barra-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 110px;
}

.valor {
  color: #222;
  font-weight: 700;
  font-size: 2.1em;
  margin-bottom: 10px;
  letter-spacing: 1px;
}

.barra-outer {
  width: 70px;
  height: 170px;
  background: #e9ecef;
  border-radius: 16px;
  box-shadow: 0 2px 8px 0 rgba(60, 72, 100, 0.07);
  display: flex;
  align-items: flex-end;
  margin-bottom: 10px;
  transition: background 0.3s;
}

.barra-inner {
  width: 100%;
  border-radius: 16px 16px 0 0;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  position: relative;
  cursor: pointer;
  min-height: 8px;
  transition:
    height 0.5s cubic-bezier(0.4, 2, 0.6, 1),
    box-shadow 0.3s;
  box-shadow: 0 4px 16px 0 rgba(60, 72, 100, 0.1);
  will-change: height;
}

.barra-inner.ganadas {
  background: linear-gradient(135deg, #4ade80 60%, #16a34a 100%);
}

.barra-inner.perdidas {
  background: linear-gradient(135deg, #f87171 60%, #b91c1c 100%);
}

.barra-inner:hover {
  filter: brightness(1.08);
  box-shadow: 0 8px 24px 0 rgba(60, 72, 100, 0.18);
}

.porcentaje {
  color: #fff;
  font-weight: 700;
  font-size: 1.1em;
  position: absolute;
  left: 50%;
  bottom: 10px;
  transform: translateX(-50%);
  text-shadow: 0 2px 8px rgba(60, 72, 100, 0.18);
  letter-spacing: 0.5px;
  pointer-events: none;
}

.etiqueta {
  margin-top: 12px;
  font-weight: 600;
  color: #444;
  font-size: 1.1em;
  letter-spacing: 0.5px;
  text-align: center;
  text-transform: capitalize;
}
</style>
