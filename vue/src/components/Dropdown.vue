<template>
  <div
    v-click-outside="() => opened = false"
    class="dropdown"
    :class="{ disabled }"
    @click="open">
    <div class="dropdown-value">
      <slot name="value" />
      <slot v-if="!disabled" name="open-indicator">
        <img src="@/assets/svg/dropdown-caret-down.svg" alt="dropdown-open-indicator">
      </slot>
    </div>
    <div v-if="opened" class="dropdown-content">
      <slot name="content" :close="close" :select="select">
        <ul>
          <li v-for="value in values || []" :key="value" @click="select(value)">
            {{ value }}
          </li>
        </ul>
      </slot>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'Dropdown',
  props: {
    values: {
      type: Array as new () => Array<string | number>,
      default: () => [] as Array<string | number>,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['select'],
  setup(props, { emit }) {
    const opened = ref<boolean>(false);

    function open() {
      if (props.disabled) return;

      opened.value = true;
    }

    function close() {
      opened.value = false;
    }

    function select(value: unknown) {
      emit('select', value);
      opened.value = false;
    }

    return {
      opened,

      open,
      close,
      select,
    };
  },
});
</script>

<style lang="scss" scoped>
.dropdown {
  @apply border border-gray-border rounded;
  @apply relative flex items-center h-9;

  &:not(.disabled) .dropdown-value {
    @apply cursor-pointer;
  }
}

.dropdown-value {
  @apply flex items-center justify-between px-3 w-full h-full;
  @apply text-sm text-gray-400;
}

.dropdown-content {
  @apply absolute left-0 shadow-popup z-10;
  @apply bg-white rounded border border-gray-border;
  @apply w-full;
  top: 100%;

  ::v-deep(ul) {
    max-height: 250px;
    @apply overflow-y-auto;

    li {
      @apply px-4 py-2;
      @apply cursor-pointer;

      &:hover {
        @apply bg-lightgray-300;
      }

      &:not(:last-child) {
        @apply border-b border-gray-border;
      }
    }
  }
}
</style>
