<template>
    <AuthenticatedLayout>
        <table >
            <tr></tr>
        </table>
    </AuthenticatedLayout>
</template>

<script>

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    components: {
        AuthenticatedLayout
    },
    name: 'ShowPartida',
    props: {
        partida: {
            type: Object,
            required: true
        }
    }
}
</script>