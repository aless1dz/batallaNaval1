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

                    <Link :href="route('partidas.index')" class="inline-block" >
                        <PrimaryButton >
                            Unirse a una partida
                        </PrimaryButton>
                    </Link>
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

                        <div class="mb-4">
                            <InputLabel for="nombrePartida" value="Nombre de la partida" />
                            <TextInput
                                id="nombrePartida"
                                v-model="form.nombre"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="$page.props.errors.nombre" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="descripcionPartida" value="Descripción" />
                            <TextInput
                                id="descripcionPartida"
                                v-model="form.descripcion"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="$page.props.errors.descripcion" class="mt-2" />
                        </div>

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
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head,Link } from "@inertiajs/vue3";

export default {
    components: {
        AuthenticatedLayout,
        Head,
        PrimaryButton,
        Modal,
        InputLabel,
        InputError,
        TextInput,
        Link,
    },

    data() {
        return {
            showModal: false,
            form: {
                nombre: '',
                descripcion: '',
            },
        };
    },
    methods: {
        openModal() {
            this.showModal = true;
        },
        crearPartida() {
            this.$inertia.post(route('partidas.store'), {
                nombre: this.form.nombre,
                descripcion: this.form.descripcion,
            }, {
                onSuccess: () => {
                    this.showModal = false;
                    this.nombre = '';
                    this.descripcion = '';
                },
            });
        }
    },
};
</script>
