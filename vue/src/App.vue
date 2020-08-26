<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { useRouter } from 'vue-router';
import { injectRouter } from './api/client';

export default defineComponent({
  name: 'App',
  setup() {
    injectRouter(useRouter());
    return {};
  },
});
</script>

<style lang="scss" src="@/assets/scss/main.scss"></style>
