import { createApp } from 'vue';
import Spinner from './components/Spinner.vue';
import { setupDirectives } from './directives';
import App from './App.vue';
import router from './router';

const app = createApp(App);
app.component('Spinner', Spinner);
setupDirectives(app);

app.use(router).mount('#app');
