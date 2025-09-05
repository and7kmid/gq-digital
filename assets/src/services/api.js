import axios from 'axios';

const api = axios.create({
  baseURL: window.companyHubConfig?.apiUrl || '/wp-json/company-hub/v1',
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': window.companyHubConfig?.nonce || ''
  }
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response?.status === 401) {
      // Redirect to login if unauthorized
      window.location.href = '/company-hub/login';
    }
    return Promise.reject(error);
  }
);

export default api;