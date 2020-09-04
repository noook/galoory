<template>
  <img :src="spinner" alt="Spinner">
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';
import spinnerDefault from '@/assets/svg/spinner.svg';
import spinnerWhite from '@/assets/svg/spinner-white.svg';

export default defineComponent({
  name: 'Spinner',
  props: {
    white: {
      type: Boolean,
      default: false,
    },
  },
  setup(props) {
    const spinner = computed(() => {
      return props.white ? spinnerWhite : spinnerDefault;
    });

    return {
      spinner,
    };
  },
});
</script>
