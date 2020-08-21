<template>
  <div class="login">
    <div class="with-bg md:w-3/5">
      <img
        class="md:w-82"
        src="@/assets/img/louisethb-logo.svg"
        alt="louisethb logo">
      <form class="md:hidden" @submit.prevent="login">
        <input
          v-model="credentials.username"
          placeholder="Nom d'utilisateur"
          type="text">
        <input
          v-model="credentials.password"
          placeholder="Mot de passe"
          type="password">
        <div class="flex flex-col items-center">
          <button
            :disabled="!canSubmit"
            type="submit"
            class="btn bg-white text-indigo my-2">
            Se connecter
          </button>
          <div v-if="error !== null" class="error bg-white rounded">
            <span v-if="error.code === 401">
              Identifiants invalides
            </span>
          </div>
        </div>
      </form>
    </div>
    <div class="hidden w-2/5 md:flex justify-center items-center">
      <form class="flex flex-col" @submit.prevent="login">
        <h1 class="text-3xl font-semibold mb-6">
          Se connecter
        </h1>
        <div class="form-row">
          <label for="username">Email</label>
          <input id="username" v-model="credentials.username" type="text">
        </div>
        <div class="form-row">
          <label for="password">Mot de passe</label>
          <input id="password" v-model="credentials.password" type="password">
        </div>
        <div v-if="error !== null" class="error">
          <span v-if="error.code === 401">
            Identifiants invalides
          </span>
        </div>
        <div class="flex justify-center mt-2">
          <button :disabled="!canSubmit" type="submit" class="btn primary">
            Connexion
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { setToken } from '@/composition/auth';

export default defineComponent({
  name: 'Login',
  setup() {
    const router = useRouter();
    const error = ref(null);

    const credentials = ref({
      username: '',
      password: '',
    });

    const canSubmit = computed(() => {
      return Object.values(credentials.value)
        .every(value => !!value.length);
    });

    function login() {
      error.value = null;

      return axios
        .post<{ token: string }>('http://api.local.nook.sh/login', credentials.value)
        .then(({ data }) => {
          setToken(data.token);
          router.push({ name: 'home' });
        })
        .catch(({ response }) => {
          error.value = response.data;
        });
    }

    return {
      credentials,
      error,

      canSubmit,

      login,
    };
  },
});
</script>

<style lang="scss" scoped>
.login {
  @apply h-screen flex;
}

.with-bg {
  @apply h-full;
  background-image: url('~@/assets/img/login-background.jpg');
  background-position: 33% center;
  @apply flex flex-col items-center justify-center;
  @apply p-12;
}

input {
  @apply w-full rounded;
  @apply my-2 h-8 px-2;
}

.error {
  @apply my-2 px-2 h-8 text-sm;
  @apply flex items-center;
}

.form-row {
  @apply flex flex-col mt-2 w-64;
}
</style>
