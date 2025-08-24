import type { AxiosResponse, AxiosError } from 'axios'

export const handleResponse = (response: AxiosResponse | AxiosError): AxiosResponse => {
  const origResponse = response as AxiosResponse
  let responseToCheck = response as AxiosResponse

  // Handle AxiosError case
  if ('name' in response && response.name === 'AxiosError') {
    const axiosError = response as AxiosError
    responseToCheck = axiosError.response as AxiosResponse
  }

  if (responseToCheck?.status < 200 || responseToCheck?.status > 299) {
    const data = responseToCheck.data
    const error = (data && data.message) || data.error || data.errors || responseToCheck.statusText
    return Promise.reject(error)
  }

  return origResponse
}