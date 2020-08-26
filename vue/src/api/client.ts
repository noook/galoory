/* eslint-disable no-param-reassign */
import axios from 'axios';
import { getToken } from '@/composition/auth';
import { Router } from 'vue-router';

let router: Router;

const instance = axios.create({
  baseURL: process.env.VUE_APP_API_HOST,
});

export function injectRouter(routerToInject: Router) {
  router = routerToInject;
}

instance.interceptors.request.use((config) => {
  config.headers.Authorization = `Bearer ${getToken()}`;
  return config;
}, (error) => Promise.reject(error));

instance.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response.status === 401 && error.response.data.message === 'Expired JWT Token') {
      router.replace({ name: 'login' });
    }
    return Promise.reject(error);
  },
);

export const api = instance;
