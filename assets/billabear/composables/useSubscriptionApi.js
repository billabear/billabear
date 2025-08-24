import { ref, reactive } from 'vue'
import axios from 'axios'

export function useSubscriptionApi() {
  const loading = ref(false)
  const error = ref(null)
  const subscription = ref({})
  const customer = ref({})
  const product = ref({})
  const paymentDetails = ref(null)
  const payments = ref([])
  const refunds = ref([])
  const usageEstimate = ref(null)
  const subscriptionEvents = ref([])
  const paymentMethods = ref([])

  const fetchSubscription = async (subscriptionId) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get(`/app/subscription/${subscriptionId}`)
      subscription.value = response.data.subscription
      customer.value = response.data.customer
      product.value = response.data.product
      paymentDetails.value = response.data.payment_details
      payments.value = response.data.payments
      refunds.value = response.data.refunds
      usageEstimate.value = response.data.usage_estimate
      subscriptionEvents.value = response.data.subscription_events
    } catch (err) {
      error.value = err.response?.data?.message || 'An error occurred while fetching subscription data'
      console.error('Error fetching subscription:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchPaymentMethods = async (customerId) => {
    try {
      const response = await axios.get(`/app/customer/${customerId}/payment-methods`)
      paymentMethods.value = response.data.data || []
    } catch (err) {
      console.error('Error fetching payment methods:', err)
    }
  }

  const cancelSubscription = async (subscriptionId, cancelData) => {
    loading.value = true
    error.value = null

    try {
      await axios.post(`/app/subscription/${subscriptionId}/cancel`, cancelData)
      // Refresh subscription data after cancellation
      await fetchSubscription(subscriptionId)
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to cancel subscription'
      console.error('Error cancelling subscription:', err)
      return false
    } finally {
      loading.value = false
    }
  }

  const changePlan = async (subscriptionId, planData) => {
    loading.value = true
    error.value = null

    try {
      await axios.post(`/app/subscription/${subscriptionId}/plan`, planData)
      await fetchSubscription(subscriptionId)
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to change plan'
      console.error('Error changing plan:', err)
      return false
    } finally {
      loading.value = false
    }
  }

  const changePrice = async (subscriptionId, priceData) => {
    loading.value = true
    error.value = null

    try {
      await axios.post(`/app/subscription/${subscriptionId}/price`, priceData)
      await fetchSubscription(subscriptionId)
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to change price'
      console.error('Error changing price:', err)
      return false
    } finally {
      loading.value = false
    }
  }

  const changeSeats = async (subscriptionId, seatData) => {
    loading.value = true
    error.value = null

    try {
      await axios.post(`/app/subscription/${subscriptionId}/seats`, seatData)
      await fetchSubscription(subscriptionId)
      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to change seats'
      console.error('Error changing seats:', err)
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    // State
    loading,
    error,
    subscription,
    customer,
    product,
    paymentDetails,
    payments,
    refunds,
    usageEstimate,
    subscriptionEvents,
    paymentMethods,
    
    // Methods
    fetchSubscription,
    fetchPaymentMethods,
    cancelSubscription,
    changePlan,
    changePrice,
    changeSeats
  }
}