<template>
    <div v-if="show" class="modal-overlay">
        <div class="modal-content">
            <h2>Crear Nuevo Juego</h2>
            <form @submit.prevent="crearJuego">
                <div class="form-group">
                    <InputLabel for="nombre" value="Nombre del Juego:" />
                    <TextInput
                        id="nombre"
                        v-model="nombre"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                    />
                </div>
                <div class="form-group">
                    <InputLabel for="descripcion" value="Descripción:" />
                    <TextInput
                        id="descripcion"
                        v-model="descripcion"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                </div>
                <div class="modal-actions">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Crear</button>
                    <button type="button" @click="cerrarModal" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

export default {
    name: "ModalCrear",
    components: {
        TextInput,
        InputLabel,
    },
    props: {
        show: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            nombre: "",
            descripcion: ""
        };
    },
    methods: {
        crearJuego() {
            this.$emit("crear", {
                nombre: this.nombre,
                descripcion: this.descripcion
            });
            this.resetForm();
        },
        cerrarModal() {
            this.$emit("cerrar");
            this.resetForm();
        },
        resetForm() {
            this.nombre = "";
            this.descripcion = "";
        }
    }
};
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal-content {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    min-width: 300px;
    max-width: 90vw;
}
.form-group {
    margin-bottom: 1rem;
}
.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}
</style>