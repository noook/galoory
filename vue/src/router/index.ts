import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import LoggedLayout from '@/layouts/Logged.vue';
import UnloggedLayout from '@/layouts/Unlogged.vue';
import Home from '@/views/Home.vue';

import { isAuthenticated, revokeToken } from '@/composition/auth';

const routes: RouteRecordRaw[] = [
  {
    component: LoggedLayout,
    path: '/',
    redirect: { name: 'home' },
    beforeEnter(to, from, next) {
      if (isAuthenticated.value === false) {
        revokeToken();

        return next({ name: 'login' });
      }
      return next();
    },
    children: [
      { path: 'home', name: 'home', component: Home },
      { path: '/:catchAll(.*)', redirect: { name: 'home' } },
    ],
  },
  {
    component: UnloggedLayout,
    path: '/',
    beforeEnter(to, from, next) {
      if (isAuthenticated.value === true) {
        return next({ name: 'home' });
        return next();
      }
      return next();
    },
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import(/* webpackChunkName: "login" */ '@/views/Login.vue'),
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

export default router;
