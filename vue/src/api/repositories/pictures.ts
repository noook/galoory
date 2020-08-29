import { getToken } from '@/composition/auth';
import { ROUTES } from '@/constants';
import { routeMap } from '@/api';
import { api } from '@/api/client';

const apiUrl = process.env.VUE_APP_API_HOST;

export default function usePictures() {
  const token = getToken();

  function listPictures(): Promise<string[]> {
    return api.get<string[]>(routeMap.get(ROUTES.PICTURES), {
      params: {
        auth: token,
      },
    })
      .then(({ data }) => data);
  }

  function getStaticRoute(filename: string): string {
    const endpoint = routeMap.get(ROUTES.PICTURE, { file: filename });

    return `${apiUrl}${endpoint}?auth=${token}`;
  }

  return {
    listPictures,
    getStaticRoute,
  };
}
