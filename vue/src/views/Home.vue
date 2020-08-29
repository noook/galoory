<template>
  <div class="home">
    <h1 class="mb-2">
      Gladys - 28 Juin 2020
    </h1>
    <h2>Formule découverte - 6 photos</h2>
    <p>
      Tu trouveras ci-dessous l'ensemble des photos du shoot, pour l'instant non-retouchées.
      Je te laisse sélectionner tes favorites et me communiquer leur nom, que tu trouveras
      au dessus de chaque photo quand tu les affiches en grand (par exemple, la
      première s'appelle DSC00045). En espérant qu'elles te plaisent !
    </p>
    <div class="grid grid-cols-3 gap-8 items-center">
      <img
        v-for="file in files"
        :key="file"
        :src="getStaticRoute(file)"
        :alt="file">
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import usePicturesRepository from '@/api/repositories/pictures';
// import usePagination from '@/composition/paginate';

export default defineComponent({
  name: 'Home',
  setup() {
    const { listPictures, getStaticRoute } = usePicturesRepository();
    const files = ref<string[]>([]);

    listPictures()
      .then(list => {
        files.value = list;
      });

    return {
      files,

      listPictures,
      getStaticRoute,
    };
  },
});
</script>

<style lang="scss" scoped>
.home {
  @apply py-5 px-4;

  h2 {
    @apply text-lg font-semibold mb-3;
  }

  div.grid {
    justify-items: center;
  }
}
</style>
