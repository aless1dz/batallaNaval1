export default class Tablero {
    constructor(tamaño = 10) {
        this.tamaño = tamaño;
        this.grilla = this.crearGrilla();
        this.barcos = [];
        this.movimientos = [];
        this.barcosColocados = false;
    }

    crearGrilla() {
        const grilla = [];
        for (let i = 0; i < this.tamaño; i++)  {
            const fila = [];
            for (let j = 0; j < this.tamaño; j++) {
                fila.push({
                    fila: i,
                    columna: j,
                    tieneBarco: false,
                    disparado : false,
                    impacto: false,
                    barcoId: null,
                    hundido: false,
                })
            }
            grilla.push(fila);
        }
        return grilla;
    }

    obtenerCelda(fila, columna) {
        if (this.esPosicionValida(fila, columna)) {
            return this.grilla[fila][columna];
        }
        return null;
    }

    esPosicionValida(fila, columna) {
        return fila >= 0 && fila < this.tamaño && columna >= 0 && columna < this.tamaño;
    }

    colocarBarco(barco) {
        if (this.puedeColocarBarco(barco)) {
            const posiciones = this.obtenerPosicionesBarco(barco);
            
            posiciones.forEach(pos => {
                this.grilla[pos.fila][pos.columna].tieneBarco = true;
                this.grilla[pos.fila][pos.columna].barcoId = barco.id;
            });

            this.barcos.push(barco);
            return true;
        }
        return false;
    }

      puedeColocarBarco(barco) {
        const posiciones = this.obtenerPosicionesBarco(barco);
        
        for (let pos of posiciones) {
            if (!this.esPosicionValida(pos.fila, pos.columna)) {
                return false;
            }

            if (this.grilla[pos.fila][pos.columna].tieneBarco) {
                return false;
            }
        }

        if (!this.verificarEspacioLibre(posiciones)) {
            return false;
        }

        return true;
    }
}