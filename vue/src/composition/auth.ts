import { ref, computed, Ref } from 'vue';

const token = ref<null | string>(null);
export const localStorageKey = 'galoory_access_token';

interface JWT {
  iat: number;
  exp: number;
  roles: string[];
  username: string;
  firstname: string;
}

export function getToken(): string | null {
  return localStorage.getItem(localStorageKey);
}

const storedValue = getToken();
if (storedValue !== null) {
  token.value = storedValue;
}

export function setToken(jwt: string) {
  token.value = jwt;
  localStorage.setItem(localStorageKey, token.value);
}

export function revokeToken() {
  localStorage.removeItem(localStorageKey);
  token.value = null;
}

function readJWT(checkToken: Ref<string>): JWT {
  return JSON.parse(atob(checkToken.value.split('.')[1]));
}

export function isTokenValid(checkToken: Ref<string | null>): checkToken is Ref<string> {
  if (checkToken.value === null) {
    return false;
  }

  try {
    JSON.parse(atob(checkToken.value.split('.')[1]));
    return true;
  } catch {
    return false;
  }
}

export function getExpiracy(checkToken: Ref<string>): number {
  const readToken = readJWT(checkToken);
  return +new Date(readToken.exp * 1000);
}

function hasTokenExpired(checkToken: Ref<string>) {
  return getExpiracy(checkToken) < Date.now();
}

export const tokenInfo = computed<JWT | null>(() => {
  if (isTokenValid(token) && !hasTokenExpired(token)) {
    return readJWT(token);
  }
  return null;
});

export const isAuthenticated = computed<boolean>(() => {
  return isTokenValid(token) && !hasTokenExpired(token);
});

export const isAdmin = computed<boolean>(() => {
  return isAuthenticated.value === true
   && tokenInfo.value!.roles.includes('ROLE_ADMIN');
});

export default {
  token,
  tokenInfo,
  getExpiracy,
  isTokenValid,

  setToken,
  revokeToken,

  isAuthenticated,
  isAdmin,
};
