<template>
  <div class="accounts gutter">
    <h1>Gestion des comptes</h1>
    <div class="head flex justify-end">
      <router-link v-slot="{ navigate }" :to="{ name: 'photoshoot-new' }">
        <button class="btn primary" @click="navigate">
          Nouveau compte
        </button>
      </router-link>
    </div>
    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Formule</th>
          <th>Statut</th>
          <th>Expiration</th>
          <th />
        </tr>
      </thead>
      <tbody>
        <PhotoshootRow
          v-for="photoshoot in photoshoots"
          :key="photoshoot.id"
          :photoshoot="photoshoot" />
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import usePhotoshootRepository from '@/api/repositories/photoshoot';

import PhotoshootRow from '@/components/PhotoshootRow.vue';

export default defineComponent({
  name: 'Accounts',
  components: { PhotoshootRow },
  setup() {
    const repository = usePhotoshootRepository();
    repository.getPhotoshoots();

    return {
      photoshoots: repository.photoshoots,
    };
  },
});
</script>

<style lang="scss" scoped>
.accounts {}
</style>
