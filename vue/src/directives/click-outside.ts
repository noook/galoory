/* eslint-disable no-underscore-dangle, no-param-reassign */
/**
 * https://jsfiddle.net/Linusborg/Lx49LaL8/
 */

import { App } from 'vue';

function clickOutside(app: App): App {
  return app.directive('click-outside', {
    mounted(el: Element & { __vueClickOutside__: Function | null }, binding) {
      // Provided expression must evaluate to a function.
      if (typeof binding.value !== 'function') {
        const compName = binding.instance?.$.type.name;
        // eslint-disable-next-line max-len
        let warn = `[v-click-outside:] provided expression '${binding.value}' is not a function, but has to be`;
        if (compName) { warn += `Found in component '${compName}'`; }
        console.warn(warn);
      }
      // Define Handler and cache it on the element
      const { bubble } = binding.modifiers;
      const handler = (e: Event) => {
        if (bubble || (!el.contains(e.target as Node) && el !== e.target)) {
          binding.value(e);
        }
      };
      el.__vueClickOutside__ = handler;
      document.addEventListener('click', handler);
    },
    beforeUnmount(el) {
      document.removeEventListener('click', el.__vueClickOutside__);
      el.__vueClickOutside__ = null;
    },
  });
}

export default [
  clickOutside,
];
