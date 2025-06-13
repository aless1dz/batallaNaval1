<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
      <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl">&times;</button>

    
      <div class="mb-4 text-lg font-semibold text-gray-800">
        <slot name="title" />
      </div>

      <div>
        <slot name="body" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Modal",
};
</script>
