<template>
  <tr class="photoshoot-row">
    <td class="font-semibold">
      {{ customerFullname }}
    </td>
    <td>{{ photoshoot.package.name }}</td>
    <td>{{ photoshoot.status }}</td>
    <td>{{ toDMY(photoshoot.expiration) }}</td>
    <td>
      <div class="actions">
        <router-link v-slot="{ navigate }" :to="detailRoute">
          <button @click="navigate">
            Voir
          </button>
        </router-link>
        <button @click="deletePhotoshoot">
          Archiver
        </button>
      </div>
    </td>
  </tr>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';
import usePhotoshootRepository from '@/api/repositories/photoshoot';
import { toDMY } from '@/filters';
import { Photoshoot } from '@/types/models';

interface Props {
  photoshoot: {
    type: () => Photoshoot;
    required: true;
  };
}

export default defineComponent({
  name: 'PhotoshootRow',
  props: {
    photoshoot: {
      required: true,
      type: Object as () => Photoshoot,
    },
  },
  emits: ['delete'],
  setup(props, { emit }) {
    const customerFullname = computed(() => {
      const { customer } = props.photoshoot;
      return `${customer.firstname} ${customer.lastname}`;
    });

    const detailRoute = computed(() => ({
      name: 'photoshoot-detail',
      params: {
        photoshootId: props.photoshoot.id,
      },
    }));

    const { remove: removeShoot } = usePhotoshootRepository();
    function deletePhotoshoot() {
      return removeShoot(props.photoshoot)
        .then(() => emit('delete', props.photoshoot));
    }

    return {
      customerFullname,
      detailRoute,

      deletePhotoshoot,

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
