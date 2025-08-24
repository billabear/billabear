import { ref, reactive } from 'vue'

export function useForm(initialData = {}) {
  const formData = reactive({ ...initialData })
  const errors = ref({})
  const isSubmitting = ref(false)
  const success = ref(false)
  const failed = ref(false)

  const resetForm = () => {
    Object.keys(formData).forEach(key => {
      if (initialData[key] !== undefined) {
        formData[key] = initialData[key]
      } else {
        delete formData[key]
      }
    })
    errors.value = {}
    success.value = false
    failed.value = false
  }

  const setErrors = (newErrors) => {
    errors.value = newErrors || {}
  }

  const clearErrors = () => {
    errors.value = {}
  }

  const setSubmitting = (submitting) => {
    isSubmitting.value = submitting
  }

  const setSuccess = (successState) => {
    success.value = successState
  }

  const setFailed = (failedState) => {
    failed.value = failedState
  }

  const handleSubmissionStart = () => {
    setSubmitting(true)
    setSuccess(false)
    setFailed(false)
    clearErrors()
  }

  const handleSubmissionSuccess = () => {
    setSubmitting(false)
    setSuccess(true)
    setFailed(false)
  }

  const handleSubmissionError = (error) => {
    setSubmitting(false)
    setSuccess(false)
    setFailed(true)
    
    if (error.response && error.response.data && error.response.data.errors) {
      setErrors(error.response.data.errors)
    }
  }

  return {
    formData,
    errors,
    isSubmitting,
    success,
    failed,
    resetForm,
    setErrors,
    clearErrors,
    setSubmitting,
    setSuccess,
    setFailed,
    handleSubmissionStart,
    handleSubmissionSuccess,
    handleSubmissionError
  }
}