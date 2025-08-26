import { describe, it, expect, vi, beforeEach } from 'vitest'
import { createShallowWrapper } from '@/test-utils'
import MetricList from '../MetricList.vue'

describe('MetricList.vue', () => {
  const defaultMocks = {
    $route: {
      path: '/metrics',
      params: {},
      query: {},
      hash: '',
      name: 'app.metric.list',
    },
    $router: {
      push: vi.fn(),
      replace: vi.fn(),
    },
  }

  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('has correct component name', () => {
    const wrapper = createShallowWrapper(MetricList, {
      global: {
        mocks: defaultMocks,
        stubs: {
          LoadingScreen: true,
          FiltersSection: true,
          RoleOnlyView: true,
          InternalApp: true,
          Dropdown: true,
          ListGroup: true,
          ListGroupItem: true,
        },
      },
    })

    expect(wrapper.vm.$options.name).toBe('MetricList')
  })

  it('has correct initial data state', () => {
    // Mock the loadMetrics method to prevent API calls
    const loadMetricsSpy = vi
      .spyOn(MetricList.methods, 'loadMetrics')
      .mockImplementation(() => {})

    const wrapper = createShallowWrapper(MetricList, {
      global: {
        mocks: defaultMocks,
        stubs: {
          LoadingScreen: true,
          FiltersSection: true,
          RoleOnlyView: true,
          InternalApp: true,
          Dropdown: true,
          ListGroup: true,
          ListGroupItem: true,
        },
      },
    })

    expect(wrapper.vm.ready).toBe(false)
    expect(wrapper.vm.loaded).toBe(false)
    expect(wrapper.vm.has_error).toBe(false)
    expect(wrapper.vm.metrics).toEqual([])
    expect(wrapper.vm.per_page).toBe('10')

    loadMetricsSpy.mockRestore()
  })

  it('has correct filters configuration', () => {
    // Mock the loadMetrics method to prevent API calls
    const loadMetricsSpy = vi
      .spyOn(MetricList.methods, 'loadMetrics')
      .mockImplementation(() => {})

    const wrapper = createShallowWrapper(MetricList, {
      global: {
        mocks: defaultMocks,
        stubs: {
          LoadingScreen: true,
          FiltersSection: true,
          RoleOnlyView: true,
          InternalApp: true,
          Dropdown: true,
          ListGroup: true,
          ListGroupItem: true,
        },
      },
    })

    expect(wrapper.vm.filters).toHaveProperty('name')
    expect(wrapper.vm.filters.name.label).toBe('app.metric.list.filter.name')
    expect(wrapper.vm.filters.name.type).toBe('text')

    loadMetricsSpy.mockRestore()
  })

  it('has correct pagination properties', () => {
    // Mock the loadMetrics method to prevent API calls
    const loadMetricsSpy = vi
      .spyOn(MetricList.methods, 'loadMetrics')
      .mockImplementation(() => {})

    const wrapper = createShallowWrapper(MetricList, {
      global: {
        mocks: defaultMocks,
        stubs: {
          LoadingScreen: true,
          FiltersSection: true,
          RoleOnlyView: true,
          InternalApp: true,
          Dropdown: true,
          ListGroup: true,
          ListGroupItem: true,
        },
      },
    })

    expect(wrapper.vm.has_more).toBe(false)
    expect(wrapper.vm.show_back).toBe(false)
    expect(wrapper.vm.next_page_in_progress).toBe(false)

    loadMetricsSpy.mockRestore()
  })
})
