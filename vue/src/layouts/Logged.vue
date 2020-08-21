<template>
  <div class="logged-layout">
    <router-view v-slot="{ Component }" class="content">
      <transition name="fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'LoggedLayout',
});
</script>

<style scoped>
</style>
