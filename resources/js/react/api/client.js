import axios from 'axios'

// Same-origin API — Laravel serves both the API and this React app, so no
// CORS/base URL configuration is needed beyond the '/api' prefix.
const apiClient = axios.create({
  baseURL: '/api',
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    Accept: 'application/json',
  },
})

const TOKEN_STORAGE_KEY = 'erp_core_token'

export function getStoredToken() {
  return localStorage.getItem(TOKEN_STORAGE_KEY)
}

export function setStoredToken(token) {
  if (token) {
    localStorage.setItem(TOKEN_STORAGE_KEY, token)
  } else {
    localStorage.removeItem(TOKEN_STORAGE_KEY)
  }
}

export async function ensureCsrfCookie() {
  await axios.get('/sanctum/csrf-cookie', {
    withCredentials: true,
    withXSRFToken: true,
  })
}

// Attach the current token to every outgoing request.
apiClient.interceptors.request.use((config) => {
  const token = getStoredToken()
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default apiClient
