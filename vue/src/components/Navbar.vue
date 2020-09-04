<template>
  <nav class="navbar">
    <div class="mobile">
      <img
        class="h-12"
        src="@/assets/img/louisethb-logo.svg"
        alt="Louisethb logo">
      <button @click="menuOpen = true">
        <img class="w-8" src="@/assets/svg/hamburger.svg" alt="Hamburger menu">
      </button>
      <transition name="fade" mode="out-in">
        <div v-if="menuOpen" class="menu-full">
          <div class="head">
            <img
              class="h-12"
              src="@/assets/img/louisethb-logo.svg"
              alt="Louisethb logo">
            <button role="close" class="text-5xl text-white font-bold" @click="menuOpen = false">
              &times;
            </button>
          </div>
          <div class="body">
            <ul @click="menuOpen = false">
              <li>
                <router-link :to="{ name: 'home' }">
                  Galerie
                </router-link>
              </li>
              <li>
                <router-link :to="{ name: 'selection' }">
                  Sélection
                </router-link>
              </li>
            </ul>
            <button class="text-white underline" @click="logout">
              Déconnexion
            </button>
          </div>
        </div>
      </transition>
    </div>
    <div class="desktop">
      <div class="flex items-center h-full links body">
        <router-link :to="{ name: 'home' }" class="flex h-full">
          <img
            class="h-full"
            src="@/assets/img/louisethb-logo.svg"
            alt="Louisethb logo">
        </router-link>
        <template v-if="!isAdmin">
          <router-link :to="{ name: 'home' }">
            Galerie
          </router-link>
          <router-link :to="{ name: 'selection' }">
            Sélection
          </router-link>
        </template>
      </div>
      <div>
        <ul v-if="isAuthenticated" class="flex items-center">
          <li>
            <div
              v-click-outside="() => dropdownVisible = false"
              class="dropdown relative flex cursor-pointer"
              @click="dropdownVisible = true">
              <div class="avatar">
                {{ (userInfos.firstname || '').toUpperCase()[0] || '' }}
              </div>
              <span class="mr-4">{{ userInfos.firstname }}</span>
              <button>
                <img src="@/assets/svg/dropdown-caret-down-white.svg" alt="Caret down">
              </button>
              <div
                v-if="dropdownVisible"
                class="dropdown-content">
                <button @click="logout">
                  Se déconnecter
                </button>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import {
  revokeToken, isAuthenticated, tokenInfo, isAdmin,
} from '@/composition/auth';
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'Navbar',
  setup() {
    const router = useRouter();
    const menuOpen = ref<boolean>(false);
    const dropdownVisible = ref<boolean>(false);

    const userInfos = computed(() => tokenInfo.value!);

    function logout() {
      revokeToken();
      router.push({ name: 'login' });
    }

    return {
      menuOpen,
      dropdownVisible,

      logout,

      isAuthenticated,
      isAdmin,
      userInfos,
    };
  },
});
</script>

<style scoped lang="scss">
.navbar {
  @apply flex z-20;

  .body .router-link-active {
    @apply relative font-bold;

    &:after {
      content: '';
      @apply absolute bg-white;
      width: 15%;
      height: 2px;
      bottom: -8px;
      left: 50%;
      transform: translateX(-50%);
    }
  }

  .mobile, .desktop {
    @apply w-full;
  }

  .mobile {
    @apply flex md:hidden;
    @apply justify-between items-center;
    @apply px-5 py-2;

    .menu-full {
      @apply fixed inset-0 px-5;
      @apply flex flex-col bg-black;

      .head {
        @apply h-16;
        @apply flex justify-between items-center;
      }
      .body {
        @apply flex flex-col justify-between items-center;
        @apply flex-grow my-6;

        ul {
          @apply flex-grow flex flex-col items-center justify-center;
          @apply pb-8;

          li {
            @apply text-center text-white text-3xl;
            @apply mb-4;
          }
        }
      }
    }
  }

  .desktop {
    @apply hidden md:flex justify-between items-center;
    @apply px-16 py-1;

    .links a {
      @apply mr-8 text-white;
    }
  }
}

ul li {
  @apply mr-4;
}

.avatar {
  @apply bg-red-500 h-6 w-6 mr-2;
  @apply flex justify-center items-center rounded-full text-sm;
}

.dropdown {
  @apply text-white;

  .dropdown-content {
   @apply absolute bg-white border border-gray-border text-black;
   @apply mt-2 right-0 shadow-sm rounded;
   @apply flex flex-col whitespace-no-wrap;
   top: 100%;

   button {
     @apply px-4 py-2 duration-150;

     &:hover {
       @apply bg-gray-border;
     }
   }
  }
}

</style>
