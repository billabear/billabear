import { describe, it, expect } from 'vitest'
import { createShallowWrapper } from '@/test-utils'
import App from '../App.vue'

describe('App.vue', () => {
  it('renders correctly', () => {
    const wrapper = createShallowWrapper(App)
    
    expect(wrapper.exists()).toBe(true)
    expect(wrapper.find('.w-screen.h-screen').exists()).toBe(true)
  })

  it('contains router-view', () => {
    const wrapper = createShallowWrapper(App)
    
    expect(wrapper.findComponent({ name: 'router-view' }).exists()).toBe(true)
  })

  it('contains ModalsContainer', () => {
    const wrapper = createShallowWrapper(App, {
      global: {
        stubs: {
          ModalsContainer: true,
        },
      },
    })
    
    expect(wrapper.findComponent({ name: 'ModalsContainer' }).exists()).toBe(true)
  })

  it('has correct component name', () => {
    const wrapper = createShallowWrapper(App)
    
    expect(wrapper.vm.$options.name).toBe('App')
  })

  it('applies correct CSS classes', () => {
    const wrapper = createShallowWrapper(App)
    const rootDiv = wrapper.find('div')
    
    expect(rootDiv.classes()).toContain('w-screen')
    expect(rootDiv.classes()).toContain('h-screen')
  })
})