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

  const payloadTransformer = ({ expiration, ...rest }: Photoshoot | NewPhotoshoot) => ({
    ...rest,
    expiration: expiration.toISOString(),
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
    const formattedPayload = payloadTransformer(payload);

    return api.post<PhotoshootDTO>(routeMap.get(ROUTES.PHOTOSHOOTS), formattedPayload)
      .then(({ data }) => dtoTransformer(data));
  }

  function remove(shoot: Photoshoot): Promise<void> {
    return api.delete(routeMap.get(ROUTES.PHOTOSHOOT, { photoshoot: shoot.id }));
  }

  function update(id: string, payload: NewPhotoshoot): Promise<Photoshoot> {
    const transformedPayload = payloadTransformer(payload);

    return api.put<PhotoshootDTO>(
      routeMap.get(ROUTES.PHOTOSHOOT, { photoshoot: id }),
      transformedPayload,
    )
      .then(({ data }) => {
        return dtoTransformer(data);
      });
  }

  function saveFiles(shoot: Photoshoot, files: FileInterface[]): Promise<Photoshoot> {
    const formData: FormData = files.reduce((acc, file, index) => {
      acc.set(`files[${index}]`, file.file);

      return acc;
    }, new FormData());

    return api.post(routeMap.get(ROUTES.PHOTOSHOOT_FILES, { photoshoot: shoot.id }), formData)
      .then(() => shoot);
  }

  function exportOutput(shoot: Photoshoot) {
    return api.get<string>(routeMap.get(ROUTES.PHOTOSHOOT_EXPORT, { photoshoot: shoot.id }), {
      responseType: 'text',
    })
      .then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const { customer } = shoot;
        const filename = `${customer.firstname}-${customer.lastname}-${shoot.package.name}.txt`;
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        link.click();
      });
  }

  return {
    photoshoots,

    get,
    list,
    exportOutput,

    create,
    remove,
    update,
    saveFiles,
  };
}
