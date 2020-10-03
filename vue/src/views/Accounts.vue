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
          <th>Date</th>
          <th />
        </tr>
      </thead>
      <tbody>
        <PhotoshootRow
          v-for="photoshoot in photoshoots"
          :key="photoshoot.id"
          :photoshoot="photoshoot"
          @delete="removePhotoshoot" />
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import usePhotoshootRepository from '@/api/repositories/photoshoot';

import PhotoshootRow from '@/components/PhotoshootRow.vue';
import { Photoshoot } from '@/types/models';

export default defineComponent({
  name: 'Accounts',
  components: { PhotoshootRow },
  setup() {
    const repository = usePhotoshootRepository();
    repository.list();

    function removePhotoshoot(photoshoot: Photoshoot) {
      const { photoshoots } = repository;
      photoshoots.value.splice(
        photoshoots.value.findIndex(({ id }) => id === photoshoot.id),
        1,
      );
    }

    return {
      photoshoots: repository.photoshoots,

      removePhotoshoot,
    };
  },
});
</script>

<style lang="scss" scoped>
.accounts {}
</style>
