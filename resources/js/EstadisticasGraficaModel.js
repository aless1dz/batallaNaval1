export default class EstadisticasGraficaHelper {
  static calcularPorcentajes(ganadas, perdidas) {
    const total = ganadas + perdidas || 1;
    return {
      porcentajeGanadas: (ganadas / total) * 100,
      porcentajePerdidas: (perdidas / total) * 100,
    };
  }
}