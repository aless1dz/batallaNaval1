<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Partidas
                <span v-if="isLoading" class="text-sm text-gray-500 ml-2">Actualizando...</span>
            </h2>
        </template>

        <div class="p-6">
            <div class="mb-4 flex justify-between items-center">
                <button 
                    @click="refreshData" 
                    :disabled="isLoading"
                    class="px-4 py-2 bg-black text-white rounded hover:bg-gray-600 disabled:opacity-50"
                >
                    {{ isLoading ? 'Actualizando...' : 'Actualizar' }}
                </button>
                <div class="text-sm text-gray-500">
                    Última actualización: {{ lastUpdate }}
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dueño de la sala</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participantes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="partida in partidas" :key="partida.id">
                        <td class="px-6 py-4 whitespace-nowrap">{{ partida.nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ partida.usuarios && partida.usuarios.length > 0 ? partida.usuarios[0].name : 'Sin dueño' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ partida.descripcion }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ partida.usuarios_count || 0 }} / {{ '2' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="getEstadoClass(partida.estado)" class="px-2 py-1 text-xs rounded-full">
                                {{ partida.estado }} 
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button 
                                @click="unirsePartida(partida.id)"
                                class="px-4 py-2 text-sm font-medium bg-fuchsia-200 text-gray-900 hover:text-gray-600 rounded"
                            >
                                Unirse
                            </button>
                        </td>
                    </tr>
                    <tr v-if="partidas.length === 0">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay partidas disponibles
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage } from "@inertiajs/vue3";

export default {
    components: {
        AuthenticatedLayout,
    },
    name: "Partidas",
    props: {},
    data() {
        return {
            partidas: usePage().props.partidas || [],
            isLoading: false,
            lastUpdate: this.formatTime(new Date()),
            pollingInterval: null,
        };
    },
    methods: {
        async refreshData() {
            if (this.isLoading) return;
            
            this.isLoading = true;
            try {
                this.$inertia.reload({ 
                    only: ['partidas'],
                    onSuccess: (page) => {
                        this.partidas = page.props.partidas;
                        this.lastUpdate = this.formatTime(new Date());
                    }
                });
            } catch (error) {
                console.error('Error al actualizar partidas:', error);
            } finally {
                this.isLoading = false;
            }
        },
        
        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.refreshData();
            }, 10000); 
        },
        
        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },
        
        formatTime(date) {
            return date.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            });
        },
        
        getEstadoClass(estado) {
            switch (estado) {
                case 'esperando':
                    return 'bg-yellow-100 text-yellow-800';
                case 'jugando':
                    return 'bg-green-100 text-green-800';
                case 'finalizada':
                    return 'bg-gray-100 text-gray-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        },
        
        unirsePartida(partidaId) {
            this.$inertia.post(route('partidas.join', partidaId), {}, {
                onSuccess: () => {
                    this.refreshData(); 
                },
                onError: (errors) => {
                    console.error('Error al unirse a la partida:', errors);
                }
            });
        }
    },
    
    mounted() {
        this.startPolling();
    },
    
    beforeUnmount() {
        this.stopPolling();
    }
};
</script>