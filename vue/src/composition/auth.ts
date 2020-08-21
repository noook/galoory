import { ref, computed, Ref } from 'vue';

const token = ref<null | string>(null);
export const localStorageKey = 'galoory_access_token';

interface JWT {
  iat: number;
  exp: number;
  roles: string[];
  username: string;
  firstname: string;
  lastname: string;
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

export const tokenInfo = computed<JWT>(() => {
  if (isTokenValid(token) && !hasTokenExpired(token)) {
    return readJWT(token);
  }

  throw new Error('Token not valid');
});

export const isAuthenticated = computed<boolean>(() => {
  return isTokenValid(token) && !hasTokenExpired(token);
});

export default {
  token,
  setToken,
  tokenInfo,
  getExpiracy,
  isTokenValid,
  revokeToken,
  isAuthenticated,
};
