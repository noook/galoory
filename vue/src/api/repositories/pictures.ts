import { getToken } from '@/composition/auth';
import { ROUTES } from '@/constants';
import { routeMap } from '@/api';
import { api } from '@/api/client';
import usePaginate from '@/composition/paginate';
import { PaginationInterface, PictureRange } from '@/types/api';

const apiUrl = process.env.VUE_APP_API_HOST;

export default function usePictures(currentPage = 1, onUpdate: (page: number) => void = () => {}) {
  const token = getToken();

  const pagination = usePaginate<string, PaginationInterface<string>>({
    instance: api,
    url: routeMap.get(ROUTES.PICTURES),
    totalPageTransformer: payload => payload.pagination.maxPage,
    dataTransformer: payload => payload.results,
    totalTransformer: payload => payload.pagination.total,
    currentPage,
    onUpdate,
  });

  function listPictures() {
    return pagination.goToPage(currentPage);
  }

  function getRange(query: string | number): Promise<PictureRange> {
    const params = {
      [typeof query === 'string' ? 'file' : 'index']: query,
    };

    return api.get<PictureRange>(routeMap.get(ROUTES.PICTURE_RANGE), {
      params,
    })
      .then(({ data }) => data);
  }

  function getStaticRoute(filename: string): string {
    const endpoint = routeMap.get(ROUTES.PICTURE, { file: filename });

    return `${apiUrl}${endpoint}?auth=${token}`;
  }

  return {
    listPictures,
    getRange,
    getStaticRoute,
    pagination,
  };
}
