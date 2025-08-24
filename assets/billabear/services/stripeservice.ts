// TypeScript interfaces for Stripe functionality
interface StripeInstance {
  elements(options: { clientSecret: string }): StripeElements
  redirectToCheckout(options: { sessionId: string }): Promise<{ error?: StripeError }>
  createToken(card: StripeCard): Promise<StripeTokenResponse>
}

interface StripeElements {
  create(type: 'card', options: StripeCardOptions): StripeCard
}

interface StripeCard {
  mount(selector: string): void
  on(event: string, callback: (event: StripeCardEvent) => void): void
}

interface StripeCardOptions {
  iconStyle?: string
  style?: {
    base?: {
      color?: string
      fontWeight?: number
      fontFamily?: string
      fontSize?: string
      fontSmoothing?: string
      ':-webkit-autofill'?: { color?: string }
      '::placeholder'?: { color?: string }
    }
    invalid?: {
      iconColor?: string
      color?: string
    }
  }
}

interface StripeCardEvent {
  empty: boolean
  error?: { message: string }
}

interface StripeTokenResponse {
  token: {
    id: string
  }
  error?: StripeError
}

interface StripeError {
  message: string
  type: string
}

declare global {
  interface Window {
    Stripe: (apiKey: string) => StripeInstance
  }
}

function redirectToCheckout(apiKey: string, sessionId: string): void {
  addJs()
  setTimeout(() => {
    const stripe = window.Stripe(apiKey)
    return stripe.redirectToCheckout({ sessionId: sessionId })
  }, 500)
}

function getCardToken(stripe: StripeInstance, client_secret: string): StripeCard {
  const elements = stripe.elements({
    clientSecret: client_secret,
  })

  const card = elements.create('card', {
    iconStyle: 'solid',
    style: {
      base: {
        color: 'black',
        fontWeight: 500,
        fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
        fontSize: '16px',
        fontSmoothing: 'antialiased',

        ':-webkit-autofill': {
          color: '#fce883',
        },
        '::placeholder': {
          color: 'black',
        },
      },
      invalid: {
        iconColor: 'red',
        color: 'red',
      },
    },
  })

  card.mount('#cardInput')

  card.on('change', (event: StripeCardEvent) => {
    // Disable the Pay button if there are no card details in the Element
    const payButton = document.querySelector('.btn--main') as HTMLButtonElement
    if (payButton) {
      payButton.disabled = event.empty
    }
    
    const errorElement = document.querySelector('#cardError') as HTMLElement
    if (errorElement) {
      errorElement.textContent = event.error ? event.error.message : ''
    }
  })

  return card
}

function sendCard(stripe: StripeInstance, card: StripeCard): Promise<StripeTokenResponse> {
  return stripe.createToken(card)
}

function addJs(): void {
  // Implementation for adding Stripe JS if needed
}

export const stripeservice = {
  redirectToCheckout,
  getCardToken,
  sendCard,
}