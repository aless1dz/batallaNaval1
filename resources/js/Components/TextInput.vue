<script>
import { ref, onMounted } from 'vue';
export default {
    props: {
        modelValue: {
            type: String,
            default: '',
        },
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const input = ref(null);

        onMounted(() => {
            if (input.value.hasAttribute('autofocus')) {
                input.value.focus();
            }
        });

        return { input, modelValue: props.modelValue, emit };
    },
};
</script>

<template>
    <input
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
