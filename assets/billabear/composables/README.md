# Vue Composition API Migration Guide

This document outlines the migration patterns and composables for converting Vue components from Options API to Composition API with `<script setup>` syntax.

## Migration Patterns

### 1. Simple Components (No Logic)
For components with only template and no reactive data or methods:

**Before (Options API):**
```vue
<script>
export default {
  name: "ComponentName"
}
</script>
```

**After (Composition API):**
```vue
<script setup>
// Component migrated from Options API to Composition API
// This component requires no reactive data or methods, so script setup is minimal
</script>
```

### 2. Form Components (Typical Pattern)
For components with form handling, API calls, and state management:

**Before (Options API):**
```vue
<script>
import axios from "axios";

export default {
  name: "FormComponent",
  data() {
    return {
      formData: { name: null },
      sendingInProgress: false,
      success: false,
      errors: {}
    }
  },
  methods: {
    send() {
      // Manual API call and state management
    }
  }
}
</script>
```

**After (Composition API):**
```vue
<script setup>
import { useForm } from '../composables/useForm'

const initialData = { name: null }

const {
  formData,
  isSubmitting: sendingInProgress,
  success,
  errors,
  submitForm
} = useForm(initialData)

const send = async () => {
  try {
    await submitForm('/api/endpoint')
  } catch (error) {
    // Error handling managed by composable
  }
}
</script>
```

## Available Composables

### useApi
Handles HTTP requests with consistent loading states and error management.

```javascript
import { useApi } from './useApi'

const { get, post, put, delete: del, loading, error, errors } = useApi()

// Usage
await post('/api/endpoint', data)
```

### useForm
Comprehensive form state management with validation and submission.

```javascript
import { useForm } from './useForm'

const {
  formData,
  isSubmitting,
  success,
  failed,
  errors,
  submitForm,
  updateForm,
  setField,
  getField,
  hasError,
  getError,
  resetForm
} = useForm(initialData)
```

## Migration Checklist

When migrating a component:

1. **Replace `<script>` with `<script setup>`**
2. **Remove `export default` and `name` property**
3. **Convert `data()` to reactive declarations**
   - Simple data: `const value = ref(initialValue)`
   - Complex data: `const data = reactive(initialObject)`
   - Forms: Use `useForm(initialData)`
4. **Convert `methods` to regular functions**
5. **Replace lifecycle hooks:**
   - `mounted()` → `onMounted()`
   - `created()` → Code runs immediately in setup
   - `beforeUnmount()` → `onBeforeUnmount()`
6. **Update Vuex usage:**
   - `mapActions` → `const store = useStore(); store.dispatch()`
   - `mapState` → `computed(() => store.state.value)`
7. **Props and Emits:**
   - `props` → `defineProps()`
   - `$emit` → `defineEmits()`
8. **Extract reusable logic into composables**

## Benefits of Migration

- **Reduced boilerplate**: Less code for common patterns
- **Better reusability**: Logic can be shared via composables  
- **Improved TypeScript support**: Better type inference
- **Tree-shaking**: Unused code is eliminated
- **Performance**: Smaller bundle sizes
- **Modern patterns**: Aligns with Vue 3 best practices

## Testing Migrated Components

After migration:
1. Verify all functionality works as expected
2. Check that form validation still works
3. Ensure API calls are made correctly
4. Test loading and error states
5. Verify component props and events work
6. Update unit tests if needed

## Common Patterns

### Nested Form Data
```javascript
const initialData = {
  user: {
    name: null,
    address: {
      street: null,
      city: null
    }
  }
}

const { formData, setField, getField } = useForm(initialData)

// Access nested fields
setField('user.address.street', 'Main St')
const street = getField('user.address.street')
```

### Dynamic Arrays (Metadata)
```javascript
import { ref } from 'vue'

const metadata = ref([])

const addMetadata = () => {
  metadata.value.push({ key: '', value: '' })
}

const removeMetadata = (index) => {
  metadata.value.splice(index, 1)
}
```

### API Data Loading
```javascript
import { ref, onMounted } from 'vue'
import { useApi } from './composables/useApi'

const { get } = useApi()
const data = ref([])

onMounted(async () => {
  try {
    const response = await get('/api/data')
    data.value = response.data
  } catch (error) {
    console.error('Failed to load data')
  }
})
```