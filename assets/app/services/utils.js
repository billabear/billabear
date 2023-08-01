
export const handleResponse = function (response) {
    const origResponse = response;
    if (response.name === 'AxiosError') {
        response = response.response;
    }
    if (response.status < 200 || response.status > 299) {
        const data = response.data;
        const error = (data && data.message) || data.error || data.errors || response.statusText;
        return Promise.reject(error);
    }

    return origResponse;
}