import { ref } from 'vue';
import { routeMap } from '@/api';
import { api } from '@/api/client';
import { ROUTES } from '@/constants';
import { PhotoshootDTO, Photoshoot, NewPhotoshoot } from '@/types/models';
import { FileInterface } from '@/composition/file-handler';
import { toDMY } from '@/filters';

export default function usePhotoshootRepository() {
  const photoshoots = ref<Photoshoot[]>([]);

  const dtoTransformer = ({ date, ...rest }: PhotoshootDTO) => ({
    ...rest,
    date: new Date(date),
  });

  const payloadTransformer = ({ date, ...rest }: Photoshoot | NewPhotoshoot) => ({
    ...rest,
    date: date.toISOString(),
  });

  function get(id: string): Promise<Photoshoot> {
    return api.get<PhotoshootDTO>(routeMap.get(ROUTES.PHOTOSHOOT, {
      photoshoot: id,
    }))
      .then(({ data }) => dtoTransformer(data));
  }

  function getUserShoot(): Promise<Photoshoot> {
    return api.get<PhotoshootDTO>(routeMap.get(ROUTES.PHOTOSHOOT_ME))
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
        const { customer, date } = shoot;
        const filename = `${customer.firstname}-${toDMY(date, '-')}.txt`;
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        link.click();
      });
  }

  return {
    photoshoots,

    get,
    getUserShoot,
    list,
    exportOutput,

    create,
    remove,
    update,
    saveFiles,
  };
}
