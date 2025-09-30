import axios, { type AxiosInstance } from 'axios'


const baseURL = import.meta.env.VITE_API_URL

const api: AxiosInstance = axios.create({
  baseURL: baseURL,
})

export default api
