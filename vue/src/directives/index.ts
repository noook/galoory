import { App } from 'vue';
import clickDirectives from './click-outside';

const directives = [
  ...clickDirectives,
];

export function setupDirectives(app: App) {
  directives.forEach((setupDirective) => {
    setupDirective(app);
  });
}
