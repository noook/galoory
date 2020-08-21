<template>
  <div class="home">
    <pre>{{ JSON.stringify(tokenInfo, null, 2) }}</pre>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { tokenInfo } from '../composition/auth';

export default defineComponent({
  name: 'Home',
  setup() {
    return {
      tokenInfo,
    };
  },
});
</script>
