<template>
  <div class="logged-layout">
    <Navbar />
    <router-view v-slot="{ Component }" class="content">
      <transition name="fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>
</template>

<script>
import { defineComponent } from 'vue';

import Navbar from '@/components/Navbar.vue';

export default defineComponent({
  name: 'LoggedLayout',
  components: { Navbar },
});
</script>

<style lang="scss" scoped>
.logged-layout {
  @apply pt-16;

  .navbar {
    @apply fixed top-0 left-0 right-0 h-16;
    @apply bg-black;
  }
}
</style>
