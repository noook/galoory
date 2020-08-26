import { ref } from 'vue';
import { routeMap } from '@/api';
import { api } from '@/api/client';
import { ROUTES } from '@/constants';
import { PhotoPackage } from '@/types/models';

export default function usePhotoPackageRepository({
  fetch = false,
}) {
  const packages = ref<PhotoPackage[]>([]);

  function getPhotoPackages(): Promise<PhotoPackage[]> {
    return api.get<PhotoPackage[]>(routeMap.get(ROUTES.PHOTO_PACKAGES))
      .then(({ data }) => {
        packages.value = data;

        return data;
      });
  }

  if (fetch) {
    getPhotoPackages();
  }

  return {
    packages,

    getPhotoPackages,
  };
}
