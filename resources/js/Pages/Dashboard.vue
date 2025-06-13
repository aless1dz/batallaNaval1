<template>
    <Head title="Inicio" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Unete a la batalla naval
            </h2>
        </template>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-2xl">
                        Bienvenido a batalla naval!
                    </div>

                    <div class="p-4 text-gray-900">
                        <p>
                            Para comenzar, puedes crear una nueva partida o
                            unirte a una existente.
                        </p>
                        <p>¡Diviértete jugando!</p>
                    </div>

                    <div class="p-8 text-gray-900">
                        <PrimaryButton class="mr-4" @click="$inertia.visit('/games/create')">
                            Crear nueva partida
                        </PrimaryButton>
                        <PrimaryButton @click="$inertia.visit('/batallanaval/juego')">
                            Unirse a una partida
                        </PrimaryButton>
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
import  PrimaryButton  from "@/Components/PrimaryButton.vue";
import { Head } from "@inertiajs/vue3";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        PrimaryButton
    },
};
</script>
