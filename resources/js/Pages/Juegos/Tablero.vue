<!-- resources/js/Pages/Juegos/Tablero.vue -->
<template>
    <div class="min-h-screen bg-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header del juego -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">
                            Batalla Naval
                        </h1>
                        <p class="text-gray-600">Partida #{{ partida.id }}</p>
                    </div>
                    <div class="text-right">
                        <div
                            class="text-lg font-semibold"
                            :class="
                                esMiTurno ? 'text-green-600' : 'text-red-600'
                            "
                        >
                            {{
                                esMiTurno
                                    ? "¡Tu turno!"
                                    : `Turno de ${oponente.usuario.name}`
                            }}
                        </div>
                        <div class="text-sm text-gray-500">
                            Estado: {{ partida.estado }}
                        </div>
                    </div>
                </div>

                <!-- Indicador de carga -->
                <div v-if="cargando" class="mt-4">
                    <div
                        class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded"
                    >
                        <div class="flex items-center">
                            <svg
                                class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            Procesando disparo...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableros -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Mi Tablero -->
                <div>
                    <Tablero
                        :es-propio="true"
                        :nombre-jugador="$page.props.auth.user.name"
                        :posiciones-barcos="miTablero.barcos"
                        :disparos="miTablero.disparos"
                        :puede-disparar="false"
                        :mostrar-barcos="true"
                        :mostrar-info="true"
                        :estadisticas="miTablero.estadisticas"
                        :juego-terminado="juegoTerminado"
                        :ganador="ganador"
                        @disparar="realizarDisparo"
                    />
                </div>

                <!-- Tablero del Oponente -->
                <div>
                    <Tablero
                        :es-propio="false"
                        :nombre-jugador="oponente.usuario.name"
                        :posiciones-barcos="tableroOponente.barcos"
                        :disparos="tableroOponente.disparos"
                        :puede-disparar="
                            tableroOponente.puedeDisparar &&
                            !juegoTerminado &&
                            !cargando
                        "
                        :mostrar-barcos="false"
                        :mostrar-info="true"
                        :estadisticas="tableroOponente.estadisticas"
                        :juego-terminado="juegoTerminado"
                        :ganador="ganador"
                        @disparar="realizarDisparo"
                        @celda-hover="manejarHover"
                    />
                </div>
            </div>

            <!-- Historial de movimientos -->
            <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">
                    Historial de Movimientos
                </h3>
                <div class="max-h-40 overflow-y-auto">
                    <div
                        v-if="ultimosMovimientos.length === 0"
                        class="text-gray-500 text-center py-4"
                    >
                        No hay movimientos aún
                    </div>
                    <div
                        v-for="(movimiento, index) in ultimosMovimientos"
                        :key="index"
                        class="flex justify-between items-center py-2 px-3 rounded mb-2"
                        :class="
                            movimiento.esPropio ? 'bg-blue-50' : 'bg-gray-50'
                        "
                    >
                        <span class="font-medium">
                            {{
                                movimiento.esPropio
                                    ? "Tú"
                                    : oponente.usuario.name
                            }}
                        </span>
                        <span class="font-mono">{{ movimiento.posicion }}</span>
                        <span
                            class="px-2 py-1 rounded text-xs font-bold"
                            :class="{
                                'bg-red-200 text-red-800':
                                    movimiento.resultado === 'impacto',
                                'bg-red-800 text-white':
                                    movimiento.resultado === 'hundido',
                                'bg-blue-200 text-blue-800':
                                    movimiento.resultado === 'agua',
                            }"
                        >
                            {{ obtenerTextoResultado(movimiento.resultado) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Controles adicionales -->
            <div class="mt-8 flex justify-center gap-4">
                <button
                    @click="actualizarJuego"
                    :disabled="cargando"
                    class="bg-blue-500 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded"
                >
                    {{ cargando ? "Actualizando..." : "Actualizar Juego" }}
                </button>

                <button
                    @click="volverAPartidas"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    Volver a Partidas
                </button>
            </div>

            <!-- Mensajes de estado -->
            <div v-if="mensaje" class="fixed top-4 right-4 z-50">
                <div
                    class="bg-white border-l-4 p-4 rounded shadow-lg max-w-sm"
                    :class="{
                        'border-green-500 bg-green-50':
                            mensaje.tipo === 'exito',
                        'border-red-500 bg-red-50': mensaje.tipo === 'error',
                        'border-blue-500 bg-blue-50': mensaje.tipo === 'info',
                        'border-yellow-500 bg-yellow-50':
                            mensaje.tipo === 'advertencia',
                    }"
                >
                    <p class="font-medium">{{ mensaje.texto }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import { Inertia } from "@inertiajs/inertia";
import Tablero from "../../Components/Tablero.vue";
import axios from "axios";

export default {
    name: "TableroJuego",

    components: {
        Tablero,
    },

    props: {
        partida: Object,
        jugadorActual: Object,
        oponente: Object,
        miTablero: Object,
        tableroOponente: Object,
        esMiTurno: Boolean,
        juegoTerminado: Boolean,
        ganador: String,
    },

    setup(props) {
        const mensaje = ref(null);
        const cargando = ref(false);
        let intervalId = null;

        // Computed para obtener movimientos recientes
        const ultimosMovimientos = computed(() => {
            const movimientos = [];

            // Combinar movimientos de ambos tableros
            if (props.miTablero?.disparos) {
                props.miTablero.disparos.forEach((disparo) => {
                    movimientos.push({
                        posicion: disparo.posicion,
                        resultado: disparo.resultado,
                        esPropio: false, // Disparo que me hicieron
                    });
                });
            }

            if (props.tableroOponente?.disparos) {
                props.tableroOponente.disparos.forEach((disparo) => {
                    movimientos.push({
                        posicion: disparo.posicion,
                        resultado: disparo.resultado,
                        esPropio: true, // Disparo que hice
                    });
                });
            }

            // Ordenar por más reciente y limitar a 10
            return movimientos.slice(-10).reverse();
        });

        const realizarDisparo = async (evento) => {
            if (cargando.value || !props.esMiTurno || props.juegoTerminado) {
                mostrarMensaje(
                    "No puedes disparar en este momento",
                    "advertencia"
                );
                return;
            }

            cargando.value = true;

            try {
                const response = await axios.post(
                    `/partidas/${props.partida.id}/disparar`,
                    {
                        posicion: evento.posicion,
                    }
                );

                const data = response.data;

                let textoMensaje = "";
                let tipoMensaje = "exito";

                switch (data.resultado) {
                    case "agua":
                        textoMensaje = `Agua en ${evento.posicion}`;
                        tipoMensaje = "info";
                        break;
                    case "impacto":
                        textoMensaje = `¡Impacto en ${evento.posicion}!`;
                        break;
                    case "hundido":
                        textoMensaje = `¡Barco hundido en ${evento.posicion}!`;
                        break;
                }

                if (data.juegoTerminado) {
                    textoMensaje += " ¡Has ganado la partida!";
                }

                mostrarMensaje(textoMensaje, tipoMensaje);

                // Aquí recargas las props para actualizar la vista
                Inertia.reload({ only: ["partida"] });
            } catch (error) {
                console.error("Error al disparar:", error);
                const errorMsg =
                    error.response?.data?.message ||
                    "Error al realizar el disparo";
                mostrarMensaje(errorMsg, "error");
            } finally {
                cargando.value = false;
            }
        };

        const manejarHover = (evento) => {
            // Lógica para efectos visuales del hover si es necesario
            console.log("Hover en:", evento.posicion);
        };

        const mostrarMensaje = (texto, tipo = "info") => {
            mensaje.value = { texto, tipo };
            setTimeout(() => {
                mensaje.value = null;
            }, 4000);
        };

        const obtenerTextoResultado = (resultado) => {
            switch (resultado) {
                case "agua":
                    return "AGUA";
                case "impacto":
                    return "IMPACTO";
                case "hundido":
                    return "HUNDIDO";
                default:
                    return resultado?.toUpperCase() || "DESCONOCIDO";
            }
        };

        const actualizarJuego = () => {
            if (cargando.value) return;

            cargando.value = true;
            router.reload({
                only: [
                    "miTablero",
                    "tableroOponente",
                    "esMiTurno",
                    "juegoTerminado",
                    "ganador",
                    "partida",
                ],
                preserveScroll: true,
                onFinish: () => {
                    cargando.value = false;
                },
            });
        };

        const volverAPartidas = () => {
            router.visit("/partidas/index");
        };

        // Polling para actualizar el estado del juego
        const iniciarPolling = () => {
            intervalId = setInterval(() => {
                if (
                    !props.esMiTurno &&
                    !props.juegoTerminado &&
                    !cargando.value
                ) {
                    router.reload({
                        only: [
                            "miTablero",
                            "tableroOponente",
                            "esMiTurno",
                            "juegoTerminado",
                            "ganador",
                        ],
                        preserveScroll: true,
                    });
                }
            }, 5000); // Cada 5 segundos
        };

        const detenerPolling = () => {
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }
        };

        onMounted(() => {
            console.log("Tablero montado:", {
                partida: props.partida,
                esMiTurno: props.esMiTurno,
                juegoTerminado: props.juegoTerminado,
            });

            iniciarPolling();

            // Mostrar mensaje de bienvenida
            if (props.esMiTurno && !props.juegoTerminado) {
                mostrarMensaje(
                    "¡Es tu turno! Haz click en el tablero enemigo para disparar",
                    "info"
                );
            } else if (props.juegoTerminado) {
                const textoGanador =
                    props.ganador === "jugador"
                        ? "¡Has ganado!"
                        : "Has perdido";
                mostrarMensaje(
                    `Juego terminado. ${textoGanador}`,
                    props.ganador === "jugador" ? "exito" : "error"
                );
            }
        });

        onUnmounted(() => {
            detenerPolling();
        });

        return {
            mensaje,
            cargando,
            ultimosMovimientos,
            realizarDisparo,
            manejarHover,
            mostrarMensaje,
            obtenerTextoResultado,
            actualizarJuego,
            volverAPartidas,
        };
    },
};
</script>

<style scoped>
/* Estilos adicionales si son necesarios */
</style>
