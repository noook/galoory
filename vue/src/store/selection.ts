import { ref, computed } from 'vue';
import { api } from '@/api/client';
import { routeMap } from '@/api';
import { ROUTES } from '@/constants';
import { NewSelectedPicture } from '@/types/models';
import { AxiosError } from 'axios';

const files = ref<string[]>([]);
const quota = ref<number>(0);

const selectedLength = computed(() => files.value.length);

function getSelection() {
  return api.get<string[]>(routeMap.get(ROUTES.PHOTOSHOOT_SELECTION))
    .then(({ data }) => {
      files.value = data;
    });
}

function getQuota() {
  return api.get<{ quota: number }>(routeMap.get(ROUTES.PHOTOSHOOT_SELECTION_QUOTA))
    .then(({ data }) => {
      quota.value = data.quota;
    });
}

function clearSelection() {
  files.value.splice(
    0,
    files.value.length,
  );
}

function addToSelection(filename: string) {
  const route = routeMap.get(ROUTES.PHOTOSHOOT_SELECTION_EDIT, { filename });
  return api.post<NewSelectedPicture>(route, {})
    .then(({ data }) => {
      files.value.push(data.filename);
    })
    .catch((err: AxiosError<{ message: string; file: string }>) => {
      if (err.response?.status === 409) {
        if (!files.value.includes(filename)) {
          files.value.push(err.response.data.file);
        }
      }
    });
}

function removeFromSelection(filename: string) {
  return api.delete(routeMap.get(ROUTES.PHOTOSHOOT_SELECTION_EDIT, { filename }))
    .finally(() => {
      const index = files.value.findIndex(name => name === filename);
      if (index > -1) {
        files.value.splice(index, 1);
      }
    });
}

export function useSelection() {
  getSelection();
  getQuota();
}

export default {
  files,
  selectedLength,
  quota,

  getSelection,
  clearSelection,

  addToSelection,
  removeFromSelection,
};
