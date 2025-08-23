import {CHECKOUT_TRANSLATIONS} from "./portal/checkout";
import {COUNTRY_TRANSLATIONS} from "../../../billabear/translations/en/country";
import {PORTAL_CUSTOMER_TRANSLATIONS} from "./portal/customer";

export const PORTUGUESE_TRANSLATIONS = {
    portal: {
        customer: PORTAL_CUSTOMER_TRANSLATIONS,
        checkout: CHECKOUT_TRANSLATIONS,
        invoice: {
            pay: {
                title: "Pagar",
                general: {
                    invoice_number: "Número da fatura",
                    issued_at: "Emitido em",
                },
                payment: {
                    already_paid: "Esta fatura já foi paga com sucesso!",
                    amount: "O saldo em dívida é: {amount} {currency}",
                    pay_button: "Pagar agora!"
                },
                biller_details: {
                    title: "Detalhes do vendedor"
                },
                payee_details: {
                    title: "Detalhes do cliente"
                },
                lines: {
                    description: "Descrição",
                    tax_total: "Imposto Total",
                    total: "Total",
                    tax_rate: "Taxa de imposto",
                },
                payment_details: {
                    title: "Detalhes do pagamento"
                },
                totals: {
                    total: "Total",
                    amount_due: "Montante devido",
                },
                loading: "Carregamento"
            },
        },
        quote: {
            pay: {
                title: "Informações sobre cotações",
                general: {
                    invoice_number: "Número da fatura",
                    issued_at: "Emitido em",
                },
                payment: {
                    already_paid: "Esta fatura já foi paga com sucesso!",
                    amount: "O saldo em dívida é: {amount} {currency}",
                    pay_button: "Pagar agora!"
                },
                biller_details: {
                    title: "Detalhes do vendedor"
                },
                payee_details: {
                    title: "Detalhes do cliente"
                },
                lines: {
                    description: "Descrição",
                    tax_total: "Imposto Total",
                    total: "Total",
                    tax_rate: "Taxa de imposto",
                },
                payment_details: {
                    title: "Detalhes do pagamento"
                },
                totals: {
                    total: "Total",
                    amount_due: "Montante devido",
                },
                loading: "Carregamento",
                not_found: "Não foi encontrada tal citação",
                general_error: "Aconteceu algo inesperado. Tente novamente mais tarde.",
                already_paid: "Este orçamento foi aceite e pago",
                has_expired: "Este orçamento já expirou. Contacte o seu representante de vendas para obter um novo orçamento",
                expires_at: "Este orçamento expira em {date}"
            },
        },
        loading: {
            message: "Carregamento"
        }

    },
    global: {
        "loading": "Carregamento",
        country: COUNTRY_TRANSLATIONS,
        select_country: "Selecionar País",
    }
};