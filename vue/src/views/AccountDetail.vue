<template>
  <div class="account-detail">
    <router-link v-slot="{ navigate }" :to="{ name: 'accounts' }">
      <button class="flex items-center" @click="navigate">
        <img src="@/assets/svg/angle-left.svg" alt="Angle left" class="icon mr-2">
        <span>Retour</span>
      </button>
    </router-link>
    <main>
      <div class="left">
        <h1>
          <span>Détails du compte</span>
          <template v-if="!isNewPhotoshoot">
            <button
              v-if="editMode"
              class="w-3 ml-2 leading-3 text-2xl"
              title="Annuler les modifications"
              @click="editMode = false">
              &times;
            </button>
            <button
              v-else
              class="w-3 ml-2"
              title="Modifier le compte"
              @click="editMode = true">
              <img src="@/assets/svg/pen.svg" alt="pen">
            </button>
          </template>
        </h1>
        <form v-if="photoshoot !== null" class="grid" @submit.prevent="savePhotoshoot">
          <div class="fields">
            <div class="form-input">
              <label for="firstname">Prénom</label>
              <input
                id="firstname"
                v-model="form.firstname"
                :disabled="!editMode"
                required
                type="text">
            </div>
            <div class="form-input">
              <label for="email">Email</label>
              <input
                id="email"
                v-model="form.email"
                :disabled="!editMode"
                required
                type="email">
            </div>
            <div class="form-input">
              <label>Nombre de photos</label>
              <input
                id="quantity"
                v-model="photoshoot.quantity"
                :disabled="!editMode"
                required
                min="1"
                type="number">
            </div>
            <div class="form-input">
              <label>Date</label>
              <Datepicker
                v-model="photoshoot.date"
                :disabled="!editMode" />
            </div>
          </div>
          <div class="form-input mt-1 mb-2">
            <label for="comment">Commentaire</label>
            <textarea
              id="comment"
              v-model="photoshoot.comment"
              name="comment"
              rows="5" />
          </div>
          <section id="files">
            <h3 class="text-xl text-gray-400">
              Galerie
            </h3>
            <div
              class="dropzone"
              :class="{ dragover: isDraggingOver }"
              @drop.prevent="addFile"
              @dragover.prevent="isDraggingOver = true"
              @dragleave.prevent="isDraggingOver = false">
              <p>Déposez les fichiers ici</p>
              <label class="btn primary" for="pictures">
                <span>Ou sélectionnez</span>
                <input
                  id="pictures"
                  type="file"
                  name="files"
                  accept=".jpg,.zip,.jpeg,.png.jpf,.webp"
                  hidden
                  multiple
                  @input="addFile">
              </label>
            </div>
            <div class="flex justify-center my-8">
              <button type="submit" class="btn primary">
                <Spinner v-if="submitting" class="w-6 h-6 mx-12" white />
                <span v-else>
                  {{ isNewPhotoshoot ? 'Créer le compte' : 'Sauvegarder les modifications' }}
                </span>
              </button>
            </div>
          </section>
        </form>
      </div>
      <div class="separator" />
      <div class="right">
        <h2 class="text-2xl font-bold mb-5">
          Fichiers à ajouter ({{ files.length }})
        </h2>
        <p class="text-gray-400 text-sm mb-5">
          Limité à 15 photos. Passer par une archive .zip pour aller au dela de la limite.
          Les fichiers portant des noms déjà existants remplaceront les anciens.
        </p>
        <ul id="file-list">
          <li
            v-for="file in files"
            :key="file.id">
            <div>
              <img :src="typeofFile(file)" alt="Zip archive" class="icon-lg mr-2">
              <p>{{ file.file.name }}</p>
            </div>
            <button class="text-2xl leading-3" @click="removeFile(file)">
              &times;
            </button>
          </li>
        </ul>
      </div>
    </main>
    <Popup v-model:visible="popupVisible">
      <p class="mx-6">
        {{ isNewPhotoshoot ? 'Le compte a bien été créé.' : 'Le compte a bien été sauvegardé.' }}
      </p>
      <template #actions />
    </Popup>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { Photoshoot } from '@/types/models';
import usePhotoshootRepository from '@/api/repositories/photoshoot';
import useFileHandler, { FileInputEvent, countFiles, FileInterface } from '@/composition/file-handler';

import Datepicker from '@/components/Datepicker.vue';
import Popup from '@/components/Popup.vue';
import zipIcon from '@/assets/svg/zip.svg';
import fileIcon from '@/assets/svg/file.svg';

export default defineComponent({
  name: 'AccountDetail',
  components: {
    Datepicker,
    Popup,
  },
  setup() {
    const popupVisible = ref(false);
    const route = useRoute();
    const router = useRouter();
    const isNewPhotoshoot = computed<boolean>(() => route.name === 'photoshoot-new');
    const editMode = ref(isNewPhotoshoot.value);
    const {
      get: getPhotoshoot, update, create, saveFiles,
    } = usePhotoshootRepository();

    const photoshoot = ref<Photoshoot>({} as Photoshoot);
    if (isNewPhotoshoot.value === true) {
      photoshoot.value.comment = '';
      photoshoot.value.quantity = 6;
    }

    const form = ref({
      firstname: '',
      email: '',
    });

    const isDraggingOver = ref<boolean>(false);
    const fileHandler = useFileHandler();
    function addFile(event: FileInputEvent) {
      isDraggingOver.value = false;
      const totalFiles = countFiles(event) + fileHandler.files.value.length;
      if (totalFiles > 15) {
        // eslint-disable-next-line no-alert
        alert([
          'Limité à 15 photos. Passer par une archive .zip pour aller',
          'au dela de la limite. Les fichiers portant des noms déjà',
          'existants remplaceront les anciens.',
        ].join(' '));
      } else {
        fileHandler.addFile(event);
      }
    }

    function typeofFile(file: FileInterface): string {
      const extension = file.file.name.split('.').pop();
      switch (extension) {
        case 'zip': return zipIcon;
        default: return fileIcon;
      }
    }

    const submitting = ref(false);

    async function savePhotoshoot() {
      submitting.value = true;

      if (isNewPhotoshoot.value) {
        await create({
          user: form.value,
          date: photoshoot.value.date,
          comment: photoshoot.value.comment,
          quantity: photoshoot.value.quantity,
        })
          .then(shoot => saveFiles(shoot, fileHandler.files.value))
          .then(shoot => {
            photoshoot.value = shoot;
            fileHandler.clear();
            editMode.value = false;
            router.push({
              name: 'photoshoot-detail',
              params: {
                photoshootId: shoot.id,
              },
            });
          });
      } else {
        await update(photoshoot.value.id, {
          user: form.value,
          quantity: photoshoot.value.quantity,
          date: photoshoot.value.date,
          comment: photoshoot.value.comment,
        })
          .then(shoot => saveFiles(shoot, fileHandler.files.value))
          .then(shoot => {
            fileHandler.clear();
            editMode.value = false;
            photoshoot.value = shoot;
          })
          .catch(console.error);
      }

      submitting.value = false;
      popupVisible.value = true;
    }

    if (route.name === 'photoshoot-detail') {
      getPhotoshoot(route.params.photoshootId as string)
        .then(shoot => {
          photoshoot.value = shoot;
          const { firstname, email } = shoot.customer;
          form.value = { firstname, email };
        });
    } else {
      photoshoot.value.date = new Date();
    }

    return {
      popupVisible,
      submitting,

      editMode,
      isNewPhotoshoot,

      isDraggingOver,
      files: fileHandler.files,
      typeofFile,
      addFile,
      removeFile: fileHandler.removeFile,

      form,
      photoshoot,
      savePhotoshoot,
    };
  },
});
</script>

<style lang="scss" scoped>
.account-detail {
  @apply px-16 py-4;
}

h1 {
  @apply mb-8;
}

main {
  @apply flex mt-8;

  > div.left, div.right {
    flex: 1 0 50%;
  }

  > .separator {
    @apply mx-4;
    @apply flex border-r border-gray-border;
  }
}

form {
  .fields {
    @apply grid grid-cols-2 grid-rows-2 grid-flow-col;
    @apply gap-x-12 gap-y-2;
  }
}

.form-input {
  @apply flex flex-col;

  label {
    @apply text-gray-400 mb-1;
  }

  input {
    @apply text-sm;
  }

  textarea {
    @apply border border-gray-border rounded p-2 text-sm;
  }
}

.dropzone {
  @apply flex flex-col justify-center items-center py-16;
  @apply border border-dashed border-gray-border rounded;
  &.dragover {
    @apply bg-lightgray-300;
  }

  label.btn {
    @apply mt-2 flex justify-center items-center;
  }
}

#file-list {
  li {
    > div {
      @apply flex items-center flex-grow;

      p {
        @apply flex-auto overflow-hidden whitespace-no-wrap min-w-0;
        text-overflow: ellipsis;
      }
    }

    @apply flex items-center justify-between px-4 py-2 mb-4;
    @apply border rounded border-gray-border;
  }
}
</style>
