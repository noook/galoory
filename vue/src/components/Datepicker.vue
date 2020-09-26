<template>
  <div
    v-click-outside="() => opened = false"
    class="datepicker"
    :class="{ disabled }">
    <div class="display" @click="open">
      <slot name="value" :date="modelValue">
        {{ modelValue ? toDMY(modelValue) : '-' }}
      </slot>
    </div>
    <div v-if="opened" class="picker">
      <div class="head">
        <button type="button" @click="switchMonth(-1)">
          <img src="@/assets/svg/dropdown-caret-down.svg">
        </button>
        <h3>{{ currentMonthDisplay }}</h3>
        <button type="button" @click="switchMonth(1)">
          <img src="@/assets/svg/dropdown-caret-down.svg">
        </button>
      </div>
      <div class="body">
        <div class="days-name">
          <p v-for="(day, index) in daysShort" :key="index">
            {{ day }}
          </p>
        </div>
        <div class="days-digits">
          <button
            v-for="date in daysInMonth"
            :key="`${date.day}/${date.month}`"
            type="button"
            :class="{
              selected: selected === `${date.day}/${date.month}`,
              opacity: currentMonth.getMonth() + 1 !== date.month,
            }"
            :disabled="date.disabled"
            @click.stop="select(date)">
            {{ date.day }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import { toDMY } from '@/filters';

const oneDay = 24 * 60 * 60 * 1000;
const daysShort = [
  'Lun',
  'Mar',
  'Mer',
  'Jeu',
  'Ven',
  'Sam',
  'Dim',
];
const months = [
  'Janvier',
  'Février',
  'Mars',
  'Avril',
  'Mai',
  'Juin',
  'Juillet',
  'Août',
  'Septembre',
  'Octobre',
  'Novembre',
  'Décembre',
];

interface DayInMonth {
  year: number;
  month: number;
  day: number;
  disabled: boolean;
}

export default defineComponent({
  name: 'Datepicker',
  props: {
    modelValue: {
      type: Date as new () => Date,
      default: null,
    },
    min: {
      type: Date as new () => Date,
      default: null,
    },
    max: {
      type: Date as new () => Date,
      default: null,
    },
    keepOpen: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const opened = ref(false);
    function open() {
      if (props.disabled) return;

      opened.value = true;
    }

    const currentMonth = ref(props.modelValue || new Date());
    const currentMonthDisplay = computed(() => {
      const month = months[currentMonth.value.getMonth()];
      return `${month}, ${currentMonth.value.getFullYear()}`;
    });

    const selected = computed(() => (props.modelValue ? `${props.modelValue.getDate()}/${props.modelValue.getMonth() + 1}` : null));

    const firstDayOfMonth = computed(() => (new Date(
      currentMonth.value.getFullYear(),
      currentMonth.value.getMonth(),
      1,
    ).getDay() + 7 - 1) % 7);
    const previousMonthLastDay = computed(() => new Date(
      currentMonth.value.getFullYear(),
      currentMonth.value.getMonth(),
      0,
    ).getDate());

    const daysInMonth = computed<DayInMonth[]>(() => {
      const count = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 0)
        .getDate();
      const isAfterMax = (day: number) => {
        if (props.max) {
          const t = +new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), day);
          return t > +props.max;
        }
        return false;
      };
      const isBeforeMin = (day: number) => {
        if (props.min) {
          const t = +new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), day);
          return t < +props.min;
        }
        return false;
      };

      return [
        // Prepends calendar with previous month's days
        ...[...Array(firstDayOfMonth.value)].map((_, index) => ({
          day: previousMonthLastDay.value - index,
          month: ((currentMonth.value.getMonth() + 12 - 1) % 12) + 1,
          year: currentMonth.value.getMonth() === 0
            ? currentMonth.value.getFullYear() - 1
            : currentMonth.value.getFullYear(),
          disabled: isAfterMax(index + 1) || isBeforeMin(index + 1),
        })).reverse(),
        ...[...Array(count)].map((_, index) => ({
          day: index + 1,
          month: currentMonth.value.getMonth() + 1,
          year: currentMonth.value.getFullYear(),
          disabled: isAfterMax(index + 1) || isBeforeMin(index + 1),
        })),
      ];
    });

    function switchMonth(direction: number) {
      const to = Math.max(-1, Math.min(1, direction));
      currentMonth.value = new Date(+currentMonth.value + to * oneDay * 31);
    }

    function select(date: DayInMonth) {
      emit('update:modelValue', new Date(date.year, date.month - 1, date.day));
      if (!props.keepOpen) {
        opened.value = false;
      }
    }

    return {
      open,
      toDMY,
      daysShort,
      currentMonth,
      selected,
      select,
      daysInMonth,
      firstDayOfMonth,
      previousMonthLastDay,
      currentMonthDisplay,
      opened,
      switchMonth,
    };
  },
});
</script>

<style lang="scss" scoped>
.datepicker {
  @apply relative h-9;

  &:not(.disabled) {
    @apply cursor-pointer;

    .display:after {
      content: '\25BC';
      position: absolute;
      right: 8px;
      top: 55%;
      transform: translateY(-50%);
      font-size: 10px;
      color: #1A1A1A;
    }
  }

  .display {
    @apply relative rounded border border-gray-border;
    @apply text-xs;
    @apply flex items-center py-1 pr-6 pl-2 h-full;
  }

  &.disabled .display {
    @apply bg-disabled;
  }

  .picker {
    @apply absolute bg-white border border-gray-border rounded;
    @apply p-4 mt-2 mb-5 shadow-popup;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    width: 350px;
  }

  .head {
    @apply flex justify-between items-center mb-2;

    h3 {
      @apply text-black font-medium;
    }

    button {
      @apply p-2;
    }

    button:first-child img {
      @apply transform rotate-90;
    }
    button:last-child img {
      @apply transform -rotate-90;
    }
  }

  .body {
    .days-name,
    .days-digits {
      @apply grid grid-cols-7;
      justify-items: center;

      p, button {
        @apply flex justify-center items-center w-10 h-10 m-0;
      }
    }

    .days-name {
      @apply text-gray-400;
    }

    .days-digits {
      @apply text-black;

      button {
        @apply duration-300 rounded relative;

        &.opacity,
        &:disabled {
          color: rgba(#656565, .75);
        }

        &:not(:disabled):hover, &.selected {
          @apply bg-indigo text-white;
        }

        &.selected:after {
          content: '';
          position: absolute;
          bottom: 5px;
          left: 50%;
          transform: translateX(-50%);
          width: 3px;
          height: 3px;
          border-radius: 100%;
          background-color: #fff;
        }
      }
    }
  }
}
</style>
