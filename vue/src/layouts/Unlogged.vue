<template>
  <div class="disconnected-layout">
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
  name: 'DisconnectedLayout',
});
</script>

<style scoped>
</style>
