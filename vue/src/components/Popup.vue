<template>
  <transition name="fade" mode="out-in">
    <div
      v-if="visible"
      class="popup"
      @keypress.esc="$emit('update:visible', false)"
      @click="$emit('update:visible', false)">
      <div class="popup-content" @click.stop>
        <div class="popup-head">
          <slot name="head">
            <div class="flex justify-end">
              <button role="close" @click="$emit('update:visible', false)">
                &times;
              </button>
            </div>
          </slot>
        </div>
        <div class="popup-body">
          <slot name="default" />
        </div>
        <div class="popup-actions">
          <slot name="actions">
            <div class="actions">
              <button class="btn secondary" @click="$emit('update:visible', false)">
                Annuler
              </button>
              <button class="btn primary" @click="$emit('confirm', 'open')">
                Confirmer
              </button>
            </div>
          </slot>
        </div>
      </div>
    </div>
  </transition>
</template>

<script lang="ts">
import {
  defineComponent, onMounted, onUnmounted, watch,
} from 'vue';

export default defineComponent({
  name: 'Popup',
  props: {
    visible: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:visible', 'confirm', 'open', 'close'],
  setup(props, { emit }) {
    watch(() => props.visible, (isVisible) => {
      emit(isVisible === true ? 'open' : 'close');
    });

    function closePopup(event: KeyboardEvent) {
      // 27 = Escape
      if (event.keyCode === 27 && props.visible === true) {
        emit('update:visible', false);
      }
    }

    onMounted(() => {
      document.addEventListener('keydown', closePopup);
    });

    onUnmounted(() => {
      document.removeEventListener('keydown', closePopup);
    });

    return {};
  },
});
</script>

<style lang="scss" scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity .3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.popup {
  @apply fixed inset-0 z-40;
  @apply bg-black-overlay flex justify-center items-center;
}

.popup-content {
  @apply bg-white rounded p-4;
  @apply flex flex-col;
}

.popup-body {
  @apply flex-1 overflow-y-auto my-4;
  max-height: 80vh;
  max-width: 80vw
}

.popup-actions {
  .actions {
    @apply flex justify-end items-end;
  }

  button {
    @apply ml-2;
  }
}
</style>
