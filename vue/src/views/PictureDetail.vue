<template>
  <div class="picture-detail">
    <div class="md:px-16">
      <h1 class="mb-2">
        Gladys - 28 Juin 2020
      </h1>
      <div class="pagination">
        <button
          :disabled="currentIndex - 1 < 1"
          class="flex items-center"
          @click="goTo(currentIndex - 1)">
          <img class="w-4 h-4" src="@/assets/svg/angle-left.svg" alt="Left arrow">
          <span>Précédent</span>
        </button>
        <div class="flex items-center mx-8">
          <input
            v-model.number.lazy="currentIndex"
            min="1"
            :max="totalItems"
            type="number"
            class="w-12 text-center"
            @change="goTo($event.target.valueAsNumber)">
          <span class="mx-2">/</span>
          <span>{{ totalItems }}</span>
        </div>
        <button
          :disabled="currentIndex + 1 > totalItems"
          class="flex items-center"
          @click="goTo(currentIndex + 1)">
          <span>Suivant</span>
          <img
            class="w-4 h-4 transform rotate-180"
            src="@/assets/svg/angle-left.svg"
            alt="Left arrow">
        </button>
      </div>
      <div
        v-if="cachedPictures[currentIndex]"
        role="image-holder"
        class="my-8 flex flex-col items-center">
        <img :src="currentRoute" :alt="cachedPictures[currentIndex]">
        <div class="my-4">
          <Checkbox :model-value="isSelected" @update:modelValue="updateSelection">
            <label>Ajouter à la sélection</label>
          </Checkbox>
          <p
            class="text-center my-2"
            :class="{ 'font-semibold text-red-500': selection.selectedLength > selection.quota }">
            {{ selection.selectedLength }}/{{ selection.quota }} sélectionnée(s)
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import {
  defineComponent, ref, computed, watch, onMounted, onUnmounted, reactive,
} from 'vue';
import { useRoute, useRouter } from 'vue-router';
import usePicturesRepository from '@/api/repositories/pictures';
import selection, { useSelection } from '@/store/selection';

import Checkbox from '@/components/Checkbox.vue';

export default defineComponent({
  name: 'PictureDetail',
  components: { Checkbox },
  setup() {
    const route = useRoute();
    const router = useRouter();
    const filename = computed(() => route.params.pictureName as string);
    let rangeRequest = Promise.resolve();

    useSelection();

    function updateSelection(value: boolean) {
      if (value) {
        selection.addToSelection(filename.value);
      } else {
        selection.removeFromSelection(filename.value);
      }
    }

    const isSelected = computed(() => {
      return selection.files.value.includes(filename.value);
    });

    const {
      getRange,
      getStaticRoute,
    } = usePicturesRepository();

    const totalItems = ref(0);
    const cachedPictures = ref<Record<number, string>>({});
    const currentIndex = ref(0);
    const currentRoute = computed(() => getStaticRoute(cachedPictures.value[currentIndex.value]));

    watch(currentIndex, async newVal => {
      await rangeRequest;

      router.replace({
        params: {
          pictureName: cachedPictures.value[newVal],
        },
      });
    });

    function goTo(index: number) {
      if (index < 1 || index > totalItems.value) return;
      if (!cachedPictures.value[index]) {
        rangeRequest = getRange(index)
          .then(range => {
            Object
              .entries(range.results)
              .forEach(([idx, file]) => {
                cachedPictures.value[+idx] = file;
                const img = new Image();
                img.src = getStaticRoute(file);
              });
          });
      }

      currentIndex.value = index;
    }

    function bindKeyEvents({ code }: KeyboardEvent) {
      if (code === 'ArrowLeft') {
        goTo(currentIndex.value - 1);
      } else if (code === 'ArrowRight') {
        goTo(currentIndex.value + 1);
      }
    }

    getRange(filename.value)
      .then(range => {
        totalItems.value = range.total;
        Object
          .entries(range.results)
          .forEach(([index, file]) => {
            cachedPictures.value[+index] = file;

            if (file === filename.value) {
              currentIndex.value = +index;
            }

            const img = new Image();
            img.src = getStaticRoute(file);
          });
      });

    onMounted(() => {
      window.addEventListener('keydown', bindKeyEvents);
    });
    onUnmounted(() => {
      window.removeEventListener('keydown', bindKeyEvents);
    });

    return {
      isSelected,
      updateSelection,
      selection: reactive(selection),

      currentIndex,
      totalItems,
      cachedPictures,

      currentRoute,

      goTo,
    };
  },
});
</script>

<style lang="scss" scoped>
.picture-detail {
  @apply py-5 px-4 md:px-16
}

.pagination {
  @apply flex items-center justify-center;
}

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

div[role=image-holder] img {
  max-height: 550px;
}
</style>
