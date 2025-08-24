import { ref, reactive } from 'vue'

export function useModal() {
  const isOpen = ref(false)
  
  const defaultOptions = {
    teleportTo: 'body',
    modelValue: false,
    displayDirective: 'if',
    hideOverlay: false,
    overlayTransition: 'vfm-fade',
    contentTransition: 'vfm-fade',
    clickToClose: true,
    escToClose: true,
    background: 'non-interactive',
    lockScroll: true,
    swipeToClose: 'none',
  }

  const options = reactive({ ...defaultOptions })

  const openModal = () => {
    isOpen.value = true
    options.modelValue = true
  }

  const closeModal = () => {
    isOpen.value = false
    options.modelValue = false
  }

  const toggleModal = () => {
    if (isOpen.value) {
      closeModal()
    } else {
      openModal()
    }
  }

  return {
    isOpen,
    options,
    openModal,
    closeModal,
    toggleModal
  }
}