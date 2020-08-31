<template>
  <div class="selection">
    <div class="md:px-16">
      <h1 class="mb-8">
        Gladys - 28 Juin 2020
      </h1>
      <div class="head">
        <p
          :class="{ 'font-semibold text-red-500': selection.selectedLength > selection.quota }"
          class="count">
          Photos sélectionnées: {{ selection.selectedLength }}/{{ selection.quota }}
        </p>
        <button
          :disabled="selection.selectedLength !== selection.quota"
          class="btn primary"
          @click="selection.validate">
          Valider ma sélection
        </button>
      </div>
    </div>
    <div v-if="selection.files.length" class="grid gap-8 items-center">
      <div
        v-for="file in selection.files"
        :key="file"
        class="relative">
        <img
          :src="getStaticRoute(file)"
          :alt="file">
        <button role="close" class="remove-button" @click="selection.removeFromSelection(file)">
          &times;
        </button>
      </div>
    </div>
    <p v-else class="text-center my-8">
      Aucune photo sélectionnée.
    </p>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive } from 'vue';
import selection, { useSelection } from '@/store/selection';
import usePictureRepository from '@/api/repositories/pictures';

export default defineComponent({
  name: 'Selection',
  setup() {
    useSelection();
    const { getStaticRoute } = usePictureRepository();

    return {
      selection: reactive(selection),

      getStaticRoute,
    };
  },
});
</script>

<style lang="scss" scoped>
.selection {
  @apply py-5 px-4 md:px-16;

  div.grid {
    @apply my-8 grid-cols-1 md:grid-cols-3;
    justify-items: center;

    .remove-button {
      @apply opacity-0 duration-200;
      @apply absolute top-0 right-0;
      @apply w-6 h-6 rounded-full text-white bg-black;
      @apply text-xl leading-3;
      transform: translate(.5rem, -.5rem);
    }

    > div:hover .remove-button {
      @apply opacity-100;
    }
  }

  .head {
    @apply flex flex-col;
    @apply md:flex-row md:justify-between md:items-center;

    p.count {
      @apply font-semibold mb-4 md:mb-0;
    }
  }
}
</style>
