import axios from 'axios';

/**
 * ResourceHttp
 *
 * Instance of axios.
 *
 * @return axios
 */
export const ResourceHttp = axios.create({
    baseURL: "/"
});