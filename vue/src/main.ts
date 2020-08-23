import { createApp } from 'vue';
import { setupDirectives } from './directives';
import App from './App.vue';
import router from './router';

const app = createApp(App);
setupDirectives(app);

app.use(router).mount('#app');
