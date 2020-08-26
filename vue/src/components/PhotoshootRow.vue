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
        <button>Archiver</button>
      </div>
    </td>
  </tr>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';
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
  setup(props) {
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

    return {
      customerFullname,
      detailRoute,

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
