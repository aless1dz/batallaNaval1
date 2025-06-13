<template>
    <AuthenticatedLayout>
        <div
            class="min-h-screen bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center p-4"
        >
            <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
                <div v-if="flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ flash.success }}
                </div>

                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    🎮 Esperando Jugador
                </h1>

                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h2 class="text-xl font-semibold text-gray-700">
                        {{ partida.nombre }}
                    </h2>
                    <p v-if="partida.descripcion" class="text-gray-600 mt-2">
                        {{ partida.descripcion }}
                    </p>
                </div>

                <div class="flex justify-center mb-6">
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"
                    ></div>
                </div>

                <div class="text-lg text-gray-600 mb-4">
                    {{ statusMessage }}
                </div>

                <div class="text-xl font-bold text-gray-800 mb-6">
                    Jugadores conectados: {{ jugadoresActuales }}/2
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                    <div
                        class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                        :style="{ width: (jugadoresActuales / 2) * 100 + '%' }"
                    ></div>
                </div>

                <button
                    @click="cancelar"
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg transition-colors"
                >
                    Cancelar y volver
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

export default {
    components: {
        AuthenticatedLayout,
    },
    name: "EsperarPartida",
    props: {
        partida: {
            type: Object,
            required: true,
        },
        totalJugadores: {
            type: Number,
            required: true,
        },
        flash: {
            type: Object,
            default: () => ({}),
        },
    },
    data() {
        return {
            jugadoresActuales: this.totalJugadores,
            polling: true,
            intervalId: null,
        };
    },
    computed: {
        statusMessage() {
            if (this.jugadoresActuales >= 2) {
                return "¡Jugador encontrado! Iniciando partida...";
            }
            return "Esperando que otro jugador se una a la partida...";
        },
    },
    methods: {
        async verificarEstado() {
            if (!this.polling) return;

            try {
                const response = await axios.get(
                    `/partidas/${this.partida.id}/verificar-estado`
                );

                this.jugadoresActuales = response.data.totalJugadores;

                if (response.data.puedeIniciar) {
                    this.polling = false;
                    setTimeout(() => {
                        router.visit(`/partida/${this.partida.id}/jugar`);
                    }, 2000);
                }
            } catch (error) {
                console.error("Error al verificar estado:", error);
            }
        },
        cancelar() {
            this.polling = false;
            router.visit("/dashboard");
        },
    },
    mounted() {
        this.verificarEstado();
        this.intervalId = setInterval(this.verificarEstado, 2000);
    },
    beforeUnmount() {
        this.polling = false;
        if (this.intervalId) {
            clearInterval(this.intervalId);
        }
    },
};
</script>