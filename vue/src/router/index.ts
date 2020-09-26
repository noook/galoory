import {
  createRouter, createWebHistory, RouteRecordRaw, NavigationGuardNext,
} from 'vue-router';
import LoggedLayout from '@/layouts/Logged.vue';
import UnloggedLayout from '@/layouts/Unlogged.vue';
import PicturesIndex from '@/views/PicturesIndex.vue';

import { isAuthenticated, revokeToken, isAdmin } from '@/composition/auth';

function adminRequired(required: boolean, redirect: string, next: NavigationGuardNext): void {
  if (isAdmin.value === required) {
    return next({ name: redirect });
  }
  return next();
}

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
      {
        path: '/pictures',
        name: 'home',
        component: PicturesIndex,
        beforeEnter(to, from, next) {
          return adminRequired(true, 'accounts', next);
        },
      },
      {
        path: '/selection',
        name: 'selection',
        component: () => import(/* webpackChunkName: 'selection' */ '@/views/Selection.vue'),
        beforeEnter(to, from, next) {
          return adminRequired(true, 'accounts', next);
        },
      },
      {
        path: 'accounts',
        name: 'accounts',
        component: () => import(/* webpackChunkName: 'accounts' */ '@/views/Accounts.vue'),
        beforeEnter(to, from, next) {
          return adminRequired(false, 'home', next);
        },
      },
      {
        path: 'photoshoots/new',
        name: 'photoshoot-new',
        component: () => import(/* webpackChunkName: 'account-detail' */ '@/views/AccountDetail.vue'),
        beforeEnter(to, from, next) {
          return adminRequired(false, 'home', next);
        },
      },
      {
        path: 'photoshoots/:photoshootId',
        name: 'photoshoot-detail',
        component: () => import(/* webpackChunkName: 'account-detail' */ '@/views/AccountDetail.vue'),
        beforeEnter(to, from, next) {
          return adminRequired(false, 'home', next);
        },
      },
      {
        path: '/pictures/:pictureName',
        name: 'picture-detail',
        component: () => import(/* webpackChunkName: 'picture-detail' */ '@/views/PictureDetail.vue'),
        beforeEnter(to, from, next) {
          return adminRequired(true, 'accounts', next);
        },
      },
      { path: '/:catchAll(.*)', redirect: { name: 'home' } },
    ],
  },
  {
    component: UnloggedLayout,
    path: '/',
    beforeEnter(to, from, next) {
      if (isAuthenticated.value === true) {
        return next({ name: 'home' });
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
