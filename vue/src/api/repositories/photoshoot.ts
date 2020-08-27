import { ref } from 'vue';
import { routeMap } from '@/api';
import { api } from '@/api/client';
import { ROUTES } from '@/constants';
import { PhotoshootDTO, Photoshoot, NewPhotoshoot } from '@/types/models';
import { FileInterface } from '@/composition/file-handler';

export default function usePhotoshootRepository() {
  const photoshoots = ref<Photoshoot[]>([]);

  const dtoTransformer = ({ expiration, ...rest }: PhotoshootDTO) => ({
    ...rest,
    expiration: new Date(expiration),
  });

  function get(id: string): Promise<Photoshoot> {
    return api.get<PhotoshootDTO>(routeMap.get(ROUTES.PHOTOSHOOT, {
      photoshoot: id,
    }))
      .then(({ data }) => dtoTransformer(data));
  }

  function list(): Promise<Photoshoot[]> {
    return api.get<PhotoshootDTO[]>(routeMap.get(ROUTES.PHOTOSHOOTS))
      .then(({ data }) => {
        photoshoots.value = data.map(dtoTransformer);

        return photoshoots.value;
      });
  }

  function create(payload: NewPhotoshoot): Promise<Photoshoot> {
    const formattedPayload = (({ expiration, ...rest }) => ({
      ...rest,
      expiration: expiration.toISOString(),
    }))(payload);

    return api.post<PhotoshootDTO>(routeMap.get(ROUTES.PHOTOSHOOTS), formattedPayload)
      .then(({ data }) => dtoTransformer(data));
  }

  function remove(shoot: Photoshoot): Promise<void> {
    return api.delete(routeMap.get(ROUTES.PHOTOSHOOT, { photoshoot: shoot.id }));
  }

  function saveFiles(shoot: Photoshoot, files: FileInterface[]) {
    const formData: FormData = files.reduce((acc, file, index) => {
      acc.set(`files[${index}]`, file.file);

      return acc;
    }, new FormData());

    return api.post(routeMap.get(ROUTES.PHOTOSHOOT_FILES, { photoshoot: shoot.id }), formData)
      .then(({ data }) => data);
  }

  return {
    photoshoots,

    get,
    list,

    create,
    remove,
    saveFiles,
  };
}
