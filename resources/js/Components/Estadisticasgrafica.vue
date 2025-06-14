<template>
  <div>
    <canvas ref="grafica"></canvas>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';

export default {
  name: 'EstadisticasGrafica',
  props: {
    ganadas: {
      type: Number,
      required: true
    },
    perdidas: {
      type: Number,
      required: true
    }
  },
  mounted() {
    this.renderChart();
  },
  methods: {
    renderChart() {
      const ctx = this.$refs.grafica.getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Ganadas', 'Perdidas'],
          datasets: [{
            label: 'Partidas',
            data: [this.ganadas, this.perdidas],
            backgroundColor: ['#4caf50', '#f44336'],
          }]
        },
        options: {
          onClick: (evt, elements) => {
            if (elements.length > 0) {
              const tipo = elements[0].index === 0 ? 'ganadas' : 'perdidas';
              this.$emit('barClick', tipo);
            }
          }
        }
      });
    }
  }
};
</script>