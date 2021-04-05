<template>
  <div class="home">
    <div v-if="photoshoot" class="md:px-16">
      <h1 class="mb-2">
        {{ photoshoot.customer.firstname }} - {{ toReadableDate(photoshoot.date) }}
      </h1>
      <h2>
        Formule {{ photoshoot.quantity }} photos
      </h2>
      <p>{{ photoshoot.comment }}</p>
    </div>
    <div v-if="files.loading" class="flex justify-center my-16">
      <Spinner class="w-16 h-16" />
    </div>
    <div class="grid gap-8 items-center">
      <router-link
        v-for="file in files.data"
        :key="file"
        :to="{ name: 'picture-detail', params: { pictureName: file } }">
        <div class="relative">
          <img
            :src="getStaticRoute(file)"
            :alt="file">
          <div class="picture-hover">
            Voir
          </div>
        </div>
      </router-link>
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
import { defineComponent, reactive, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { toReadableDate } from '@/filters';
import usePicturesRepository from '@/api/repositories/pictures';
import usePhotoshootRepository from '@/api/repositories/photoshoot';
import { Photoshoot } from '@/types/models';

export default defineComponent({
  name: 'PicturesIndex',
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
    const { getUserShoot } = usePhotoshootRepository();
    const photoshoot = ref<Photoshoot>();

    getUserShoot()
      .then(shoot => {
        photoshoot.value = shoot;
      });

    return {
      files: reactive(pagination),
      photoshoot,

      getStaticRoute,

      toReadableDate,
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

.picture-hover {
  @apply flex items-center justify-center;
  @apply absolute inset-0 opacity-0 duration-200;
  @apply bg-black text-white text-2xl;

  &:hover {
    @apply opacity-75;
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
