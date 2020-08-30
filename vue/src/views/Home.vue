<template>
  <div class="home">
    <div class="md:px-16">
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
    </div>
    <div v-if="files.loading" class="flex justify-center my-16">
      <Spinner class="w-16 h-16" />
    </div>
    <div class="grid gap-8 items-center">
      <img
        v-for="file in files.data"
        :key="file"
        :src="getStaticRoute(file)"
        :alt="file">
    </div>
    <div class="pagination">
      <button
        class="flex items-center"
        :disabled="files.currentPage == 1"
        @click="files.previous">
        <img class="w-4 h-4" src="@/assets/svg/angle-left.svg" alt="Left arrow">
        <span>Précédent</span>
      </button>
      <div class="page-list">
        <button
          v-for="page in files.pages"
          :key="page"
          :class="{ 'font-extrabold': page === files.currentPage }"
          @click="files.goToPage(page)">
          {{ page }}
        </button>
      </div>
      <button
        class="flex items-center"
        :disabled="files.currentPage === files.lastPage"
        @click="files.next">
        <span>Suivant</span>
        <img
          class="w-4 h-4 transform rotate-180"
          src="@/assets/svg/angle-left.svg"
          alt="Left arrow">
      </button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive } from 'vue';
import usePicturesRepository from '@/api/repositories/pictures';
import { useRouter, useRoute } from 'vue-router';

export default defineComponent({
  name: 'Home',
  setup() {
    const router = useRouter();
    const { page } = useRoute().query;
    const parsedPage = page ? +page : undefined;
    const onPageUpdate = (newPage?: number) => {
      window.scrollTo(0, 0);
      router.push({
        query: {
          page: newPage,
        },
      });
    };

    const {
      listPictures,
      getStaticRoute,
      pagination,
    } = usePicturesRepository(parsedPage, onPageUpdate);

    listPictures();

    return {
      files: reactive(pagination),

      getStaticRoute,
    };
  },
});
</script>

<style lang="scss" scoped>
.home {
  @apply py-5 px-4 md:px-16;

  h2 {
    @apply text-lg font-semibold mb-3;
  }

  div.grid {
    @apply my-8 grid-cols-1 md:grid-cols-3;
    justify-items: center;
  }
}

.pagination {
  @apply flex items-center justify-between md:justify-center;

  .page-list {
    @apply md:mx-16;

    button {
      @apply mx-1 hover:underline;
    }
  }

  > button {
    @apply hover:underline;

    &:disabled {
      @apply opacity-25 hover:no-underline;
    }
  }
}
</style>
