<template>
    <Head title="Partidas disponibles" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Partidas disponibles
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Estado</th>
                                <th class="px-4 py-2 text-left">Jugadores</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="partida in partidasLocal" :key="partida.id" class="border-b">
                                <td class="px-4 py-2">{{ partida.id }}</td>
                                <td class="px-4 py-2 capitalize">{{ partida.estado }}</td>
                                <td class="px-4 py-2">
                                    {{ partida.jugadores.length }}/2
                                    <span v-if="partida.estado === 'en_progreso'" class="text-green-600 ml-2">Jugando</span>
                                    <span v-else-if="partida.estado === 'esperando'" class="text-yellow-600 ml-2">Esperando</span>
                                </td>
                                <td class="px-4 py-2">
                                    <button
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400"
                                        :disabled="partida.jugadores.length >= 2 || partida.estado !== 'esperando'"
                                        @click="unirse(partida.id)"
                                    >
                                        Unirse
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!partidasLocal.length">
                                <td colspan="4" class="text-center py-4 text-gray-500">No hay partidas disponibles.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import axios from "axios";

export default {
    components: {
        AuthenticatedLayout,
        Head,
    },
    props: {
        partidas: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            partidasLocal: this.partidas,
            intervalId: null,
        };
    },
    mounted() {
        this.intervalId = setInterval(this.fetchPartidas, 2000);
    },
    beforeUnmount() {
        clearInterval(this.intervalId);
    },
    methods: {
        unirse(id) {
            router.post(`/batallanaval/unirse/${id}`);
        },
        fetchPartidas() {
            axios.get('/batallanaval/partidas') 
                .then(res => {
                    this.partidasLocal = res.data.partidas;
                });
        },
    },
};
</script>