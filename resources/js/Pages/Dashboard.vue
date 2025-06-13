<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        You're logged in!
                        <button
                            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                            @click="mostrarModal = true"
                        >
                            Crear Juego
                        </button>
                        <button
                            class="mt-4 ml-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                            @click="verJuegos"
                        >
                            Ver Juegos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <ModalCrear
            :show="mostrarModal"
            @cerrar="mostrarModal = false"
            @crear="handleCrearJuego"
        />
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import ModalCrear from "@/Components/forms/ModalCrear.vue";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        ModalCrear,
    },
    data() {
        return {
            mostrarModal: false,
        };
    },
    methods: {
        handleCrearJuego(datos) {
            alert(`Juego creado:\nNombre: ${datos.nombre}\nDescripción: ${datos.descripcion}`);
            this.mostrarModal = false;
        },
        verJuegos() {
            router.visit('/batallanaval/juego'); 
        },
    },
};
</script>
