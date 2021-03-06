<template>
  <tr class="photoshoot-row">
    <td class="font-semibold">
      {{ customerFullname }}
    </td>
    <td>{{ photoshoot.quantity }} photo{{ photoshoot.quantity > 1 ? 's' : '' }}</td>
    <td>
      <div class="flex items-center">
        <img
          class="mr-2"
          :src="STATUSES_ICONS[photoshoot.status]"
          :alt="photoshoot.status">
        <span>{{ STATUSES_FR[photoshoot.status] }}</span>
      </div>
    </td>
    <td>{{ toDMY(photoshoot.date) }}</td>
    <td>
      <div class="actions">
        <router-link v-slot="{ navigate }" :to="detailRoute">
          <button @click="navigate">
            Voir
          </button>
        </router-link>
        <button v-if="photoshoot.status === 'done'" @click="exportOutput(photoshoot)">
          Exporter
        </button>
        <button @click="deletePhotoshoot">
          Archiver
        </button>
      </div>
    </td>
  </tr>
</template>

<script lang="ts">
import { defineComponent, computed, PropType } from 'vue';
import usePhotoshootRepository from '@/api/repositories/photoshoot';
import { toDMY } from '@/filters';
import { Photoshoot } from '@/types/models';
import { STATUSES_ICONS, STATUSES_FR } from '@/constants';

export default defineComponent({
  name: 'PhotoshootRow',
  props: {
    photoshoot: {
      required: true,
      type: Object as PropType<Photoshoot>,
    },
  },
  emits: ['delete'],
  setup(props, { emit }) {
    const customerFullname = computed(() => {
      const { customer } = props.photoshoot;
      return customer.firstname;
    });

    const detailRoute = computed(() => ({
      name: 'photoshoot-detail',
      params: {
        photoshootId: props.photoshoot.id,
      },
    }));

    const { remove: removeShoot, exportOutput } = usePhotoshootRepository();
    function deletePhotoshoot() {
      return removeShoot(props.photoshoot)
        .then(() => emit('delete', props.photoshoot));
    }

    return {
      STATUSES_FR,
      STATUSES_ICONS,

      customerFullname,
      detailRoute,

      deletePhotoshoot,
      exportOutput,

      toDMY,
    };
  },
});
</script>

<style lang="scss" scoped>
tr {
  .actions {
    @apply duration-150 opacity-0;
  }

  &:hover .actions {
    @apply opacity-100;
  }
}

td {
  @apply py-2;
}
</style>
