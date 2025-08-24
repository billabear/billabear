import { mount, shallowMount, VueWrapper } from '@vue/test-utils'
import { ComponentMountingOptions } from '@vue/test-utils'
import { Component } from 'vue'
import { vi } from 'vitest'

/**
 * Test utilities for Vue components
 */

/**
 * Default global mocks for Vue components
 */
export const defaultGlobalMocks = {
  $t: (key: string, params?: Record<string, any>) => {
    if (params) {
      let result = key
      Object.keys(params).forEach(param => {
        result = result.replace(`{${param}}`, params[param])
      })
      return result
    }
    return key
  },
  $tc: (key: string, count: number = 1) => `${key} (${count})`,
  $te: (key: string) => true,
  $router: {
    push: vi.fn(),
    replace: vi.fn(),
    go: vi.fn(),
    back: vi.fn(),
    forward: vi.fn(),
  },
  $route: {
    path: '/',
    params: {},
    query: {},
    hash: '',
    name: 'test-route',
  },
}

/**
 * Default global stubs for common components
 */
export const defaultGlobalStubs = {
  'router-link': true,
  'router-view': true,
}

/**
 * Create a wrapper for a Vue component with common test setup
 */
export function createWrapper<T extends Component>(
  component: T,
  options: ComponentMountingOptions<T> = {}
): VueWrapper<any> {
  const defaultOptions: ComponentMountingOptions<T> = {
    global: {
      mocks: {
        ...defaultGlobalMocks,
        ...(options.global?.mocks || {}),
      },
      stubs: {
        ...defaultGlobalStubs,
        ...(options.global?.stubs || {}),
      },
      ...(options.global || {}),
    },
    ...options,
  }

  return mount(component, defaultOptions)
}

/**
 * Create a shallow wrapper for a Vue component with common test setup
 */
export function createShallowWrapper<T extends Component>(
  component: T,
  options: ComponentMountingOptions<T> = {}
): VueWrapper<any> {
  const defaultOptions: ComponentMountingOptions<T> = {
    global: {
      mocks: {
        ...defaultGlobalMocks,
        ...(options.global?.mocks || {}),
      },
      stubs: {
        ...defaultGlobalStubs,
        ...(options.global?.stubs || {}),
      },
      ...(options.global || {}),
    },
    ...options,
  }

  return shallowMount(component, defaultOptions)
}

/**
 * Mock axios for API testing
 */
export function createAxiosMock() {
  const mockAxios = {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    patch: vi.fn(),
    delete: vi.fn(),
    request: vi.fn(),
    defaults: {
      validateStatus: vi.fn(() => true),
    },
    history: {
      get: [],
      post: [],
      put: [],
      patch: [],
      delete: [],
    },
  }

  // Reset history after each call
  const originalGet = mockAxios.get
  const originalPost = mockAxios.post
  const originalPut = mockAxios.put
  const originalPatch = mockAxios.patch
  const originalDelete = mockAxios.delete

  mockAxios.get = vi.fn((...args) => {
    mockAxios.history.get.push({ url: args[0], config: args[1] })
    return originalGet(...args)
  })

  mockAxios.post = vi.fn((...args) => {
    mockAxios.history.post.push({ url: args[0], data: args[1], config: args[2] })
    return originalPost(...args)
  })

  mockAxios.put = vi.fn((...args) => {
    mockAxios.history.put.push({ url: args[0], data: args[1], config: args[2] })
    return originalPut(...args)
  })

  mockAxios.patch = vi.fn((...args) => {
    mockAxios.history.patch.push({ url: args[0], data: args[1], config: args[2] })
    return originalPatch(...args)
  })

  mockAxios.delete = vi.fn((...args) => {
    mockAxios.history.delete.push({ url: args[0], config: args[1] })
    return originalDelete(...args)
  })

  return mockAxios
}

/**
 * Wait for Vue's next tick and any pending promises
 */
export async function flushPromises(): Promise<void> {
  return new Promise(resolve => {
    setTimeout(resolve, 0)
  })
}

/**
 * Create mock props for components
 */
export function createMockProps<T extends Record<string, any>>(
  overrides: Partial<T> = {}
): T {
  return {
    ...overrides,
  } as T
}

/**
 * Helper to trigger events and wait for updates
 */
export async function triggerEvent(
  wrapper: VueWrapper<any>,
  selector: string,
  event: string,
  eventData?: any
): Promise<void> {
  const element = wrapper.find(selector)
  await element.trigger(event, eventData)
  await wrapper.vm.$nextTick()
  await flushPromises()
}

/**
 * Helper to set input values and trigger change events
 */
export async function setInputValue(
  wrapper: VueWrapper<any>,
  selector: string,
  value: string
): Promise<void> {
  const input = wrapper.find(selector)
  await input.setValue(value)
  await wrapper.vm.$nextTick()
  await flushPromises()
}