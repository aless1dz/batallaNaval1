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
                    <div
                        class="p-6 text-gray-900 border-b border-gray-200 font-semibold text-2xl"
                    >
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
                        <PrimaryButton class="mr-4" @click="openModal">
                            Crear nueva partida
                        </PrimaryButton>
                        <PrimaryButton class="ml-4">
                            Unirse a una partida
                        </PrimaryButton>
                    </div>
                </div>
            </div>

            <Modal @close="showModal = false" v-if="showModal">
                <template #title>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Crea una nueva partida
                    </h3>
                </template>
                <template #body>
                    <form @submit.prevent="crearPartida">
                        <input
                            v-model="nombrePartida"
                            type="text"
                            placeholder="Nombre"
                            class="border p-2 w-full mb-4 rounded"
                        />
                        <PrimaryButton type="submit">Crear</PrimaryButton>
                    </form>
                </template>
            </Modal>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import { Head } from "@inertiajs/vue3";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        PrimaryButton,
        Modal,
    },

    data() {
        return {
            showModal: false,
            nombrePartida: '',
        };
    },
    methods: {
        openModal() {
            this.showModal = true;
        },
        crearPartida() {
            console.log('Creando partida:', this.nombrePartida);
            this.showModal = false;
        }
    },
};
</script>