import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createWrapper, flushPromises } from '@/test-utils'
import CancelSubscription from '../CancelSubscription.vue'

// Mock axios
vi.mock('axios', () => ({
  default: {
    post: vi.fn(),
  },
}))

import axios from 'axios'
const mockAxiosPost = vi.mocked(axios.post)

describe('CancelSubscription.vue', () => {
  const mockSubscription = {
    id: 'sub_123',
    plan: {
      name: 'Premium Plan',
    },
  }

  const mockToken = 'test-token-123'

  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renders correctly with props', () => {
    const wrapper = createWrapper(CancelSubscription, {
      props: {
        subscription: mockSubscription,
        token: mockToken,
      },
      global: {
        stubs: {
          SubmitButton: true,
        },
      },
    })

    expect(wrapper.exists()).toBe(true)
    expect(wrapper.find('h3').text()).toBe('portal.customer.manage.modal.cancel.title')
    expect(wrapper.find('.fa-circle-exclamation').exists()).toBe(true)
  })

  it('displays subscription plan name in warning message', () => {
    const wrapper = createWrapper(CancelSubscription, {
      props: {
        subscription: mockSubscription,
        token: mockToken,
      },
      global: {
        stubs: {
          SubmitButton: true,
        },
        mocks: {
          $t: (key: string, params?: Record<string, any>) => {
            if (key === 'portal.customer.manage.modal.cancel.warning_message' && params) {
              return `Warning message for ${params.plan_name}`
            }
            return key
          },
        },
      },
    })

    const warningText = wrapper.find('p').text()
    expect(warningText).toContain('Premium Plan')
  })

  it('has correct initial data state', () => {
    const wrapper = createWrapper(CancelSubscription, {
      props: {
        subscription: mockSubscription,
        token: mockToken,
      },
      global: {
        stubs: {
          SubmitButton: true,
        },
      },
    })

    expect(wrapper.vm.sending).toBe(false)
    expect(wrapper.vm.error).toBe(false)
  })

  it('has sendCancel method', () => {
    const wrapper = createWrapper(CancelSubscription, {
      props: {
        subscription: mockSubscription,
        token: mockToken,
      },
      global: {
        stubs: {
          SubmitButton: true,
        },
      },
    })

    expect(typeof wrapper.vm.sendCancel).toBe('function')
  })

  it('makes API call with correct parameters when sendCancel is called', async () => {
    mockAxiosPost.mockResolvedValue({ data: { success: true } })

    const wrapper = createWrapper(CancelSubscription, {
      props: {
        subscription: mockSubscription,
        token: mockToken,
      },
      global: {
        stubs: {
          SubmitButton: true,
        },
      },
    })

    await wrapper.vm.sendCancel()

    expect(mockAxiosPost).toHaveBeenCalledWith(
      `/public/subscription/${mockToken}/${mockSubscription.id}/cancel`
    )
  })

  it('updates sending state correctly', async () => {
    const wrapper = createWrapper(CancelSubscription, {
      props: {
        subscription: mockSubscription,
        token: mockToken,
      },
      global: {
        stubs: {
          SubmitButton: true,
        },
      },
    })

    // Initially sending should be false
    expect(wrapper.vm.sending).toBe(false)
    
    // We can test that the data property exists and can be modified
    await wrapper.setData({ sending: true })
    expect(wrapper.vm.sending).toBe(true)
  })

  it('emits close-modal event on successful API call', async () => {
    mockAxiosPost.mockResolvedValue({ data: { success: true } })

    const wrapper = createWrapper(CancelSubscription, {
      props: {
        subscription: mockSubscription,
        token: mockToken,
      },
      global: {
        stubs: {
          SubmitButton: true,
        },
      },
    })

    await wrapper.vm.sendCancel()
    await flushPromises()

    expect(wrapper.emitted('close-modal')).toBeTruthy()
    expect(wrapper.emitted('close-modal')).toHaveLength(1)
  })

  it('handles API error correctly', async () => {
    mockAxiosPost.mockRejectedValue(new Error('API Error'))

    const wrapper = createWrapper(CancelSubscription, {
      props: {
        subscription: mockSubscription,
        token: mockToken,
      },
      global: {
        stubs: {
          SubmitButton: true,
        },
      },
    })

    await wrapper.vm.sendCancel()
    await flushPromises()

    expect(wrapper.vm.sending).toBe(false)
    expect(wrapper.vm.error).toBe(true)
    expect(wrapper.emitted('close-modal')).toBeFalsy()
  })

  it('renders SubmitButton component', () => {
    const wrapper = createWrapper(CancelSubscription, {
      props: {
        subscription: mockSubscription,
        token: mockToken,
      },
      global: {
        stubs: {
          SubmitButton: true,
        },
      },
    })

    const submitButton = wrapper.findComponent({ name: 'SubmitButton' })
    expect(submitButton.exists()).toBe(true)
  })
})