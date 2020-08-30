<template>
  <div class="checkbox" @click="toggle">
    <div
      class="box"
      :class="{ checked: modelValue }">
      <img v-if="modelValue" src="@/assets/svg/white-tick.svg" alt="White tick">
    </div>
    <slot name="default" />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'Checkbox',
  props: {
    modelValue: {
      type: Boolean,
      required: true,
    },
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    function toggle() {
      emit('update:modelValue', !props.modelValue);
    }

    return {
      toggle,
    };
  },
});
</script>

<style lang="scss" scoped>
.checkbox {
  @apply flex items-center;

  > .box {
    @apply rounded border border-gray-border w-4 h-4 bg-white;
    @apply flex items-center justify-center;

    &.checked {
      @apply bg-blue-600 border-blue-600;
    }
  }
}
</style>

<style>
.box + label {
  @apply ml-2 text-base text-black;
}
</style>
