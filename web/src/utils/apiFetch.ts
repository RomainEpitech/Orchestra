import axios, { AxiosInstance, AxiosResponse, InternalAxiosRequestConfig } from 'axios';

interface ApiResponse<T = any> {
    message?: string;
    data?: T;
    [key: string]: any;
}

interface ApiError {
    status?: number;
    message: string;
    errors?: Record<string, string[]>;
    originalError?: any;
}

const api: AxiosInstance = axios.create({
    baseURL: 'http://localhost:8080/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    timeout: 30000
});

api.interceptors.request.use((config: InternalAxiosRequestConfig): InternalAxiosRequestConfig => {
    const token = localStorage.getItem('token');
    if (token && config.headers) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

api.interceptors.response.use(
    (response: AxiosResponse): any => response.data,
    (error: any): Promise<never> => {
        if (error.response && error.response.status === 401) {
            if (window.location.pathname !== '/login') {
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        }
        
        return Promise.reject({
            status: error.response?.status,
            message: error.response?.data?.message || 'Une erreur est survenue',
            errors: error.response?.data?.errors || {},
            originalError: error
        } as ApiError);
    }
);

const apiFetch = {
    /**
     * Effectue une requête GET
     * @param url - URL de la requête (sans le baseURL)
     * @param params - Paramètres de la requête (query params)
     * @param config - Configuration supplémentaire pour axios
     */
    get: <T = any>(url: string, params = {}, config = {}): Promise<T> => {
        return api.get(url, { params, ...config });
    },

    /**
     * Effectue une requête POST
     * @param url - URL de la requête (sans le baseURL)
     * @param data - Données à envoyer dans le corps de la requête
     * @param config - Configuration supplémentaire pour axios
     */
    post: <T = any>(url: string, data = {}, config = {}): Promise<T> => {
        return api.post(url, data, config);
    },

    /**
     * Effectue une requête PUT
     * @param url - URL de la requête (sans le baseURL)
     * @param data - Données à envoyer dans le corps de la requête
     * @param config - Configuration supplémentaire pour axios
     */
    put: <T = any>(url: string, data = {}, config = {}): Promise<T> => {
        return api.put(url, data, config);
    },

    /**
     * Effectue une requête PATCH
     * @param url - URL de la requête (sans le baseURL)
     * @param data - Données à envoyer dans le corps de la requête
     * @param config - Configuration supplémentaire pour axios
     */
    patch: <T = any>(url: string, data = {}, config = {}): Promise<T> => {
        return api.patch(url, data, config);
    },

    /**
     * Effectue une requête DELETE
     * @param url - URL de la requête (sans le baseURL)
     * @param config - Configuration supplémentaire pour axios
     */
    delete: <T = any>(url: string, config = {}): Promise<T> => {
        return api.delete(url, config);
    },

    /**
     * Effectue une requête avec upload de fichier
     * @param url - URL de la requête (sans le baseURL)
     * @param formData - FormData contenant les fichiers et données
     * @param config - Configuration supplémentaire pour axios
     */
    upload: <T = any>(url: string, formData: FormData, config = {}): Promise<T> => {
        return api.post(url, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            ...config
        });
    },

    instance: api
};

export default apiFetch;