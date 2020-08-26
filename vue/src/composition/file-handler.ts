import { ref } from 'vue';
import { randomId } from '@/utils';

export interface FileInterface {
  id: string;
  file: File;
}

export type FileInputEvent = InputEvent & {
  dataTransfer: DataTransfer;
}

export function countFiles(event: FileInputEvent): number {
  let droppedFiles;
  if (event.type === 'input') {
    droppedFiles = (event.target as HTMLInputElement).files;
  } else if (event.type === 'drop') {
    droppedFiles = event.dataTransfer.files;
  }

  if (!droppedFiles) return 0;

  return droppedFiles.length;
}

export default function useFileHandler() {
  const files = ref<FileInterface[]>([]);

  function addFile(event: FileInputEvent) {
    let droppedFiles;
    if (event.type === 'input') {
      droppedFiles = (event.target as HTMLInputElement).files;
    } else if (event.type === 'drop') {
      droppedFiles = event.dataTransfer.files;
    }

    if (!droppedFiles) return;

    files.value.push(...[...droppedFiles].map((file) => ({
      id: randomId(),
      file,
    })));
  }

  function removeFile(file: FileInterface) {
    files.value.splice(
      files.value.findIndex(({ id }) => id === file.id),
      1,
    );
  }

  function clear() {
    files.value.splice(
      0,
      files.value.length,
    );
  }

  return {
    files,
    addFile,
    removeFile,
    clear,
  };
}
