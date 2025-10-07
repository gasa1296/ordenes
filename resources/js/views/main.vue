<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
        <div class="w-full max-w-lg bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-bold mb-6 text-center">{{ phase === 1 ? 'Registrar usuario' : 'Seleccionar productos' }}</h1>
            <form v-if="phase === 1" @submit.prevent="registerInvoiceId" class="space-y-6">
                <div class="flex flex-col">
                    <label class="mb-1 font-medium text-gray-700">Nombre:</label>
                    <input v-model="name" required class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>
                <div class="flex flex-col">
                    <label class="mb-1 font-medium text-gray-700">Correo:</label>
                    <input v-model="email" type="email" required class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" />
                </div>
                <button type="submit" :disabled="disabledSubmit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Registrar</button>
            </form>
            <form v-else @submit.prevent="submitInvoice" class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold mb-2">Productos</h2>
                    <div v-if="loading" class="text-blue-500">Cargando productos...</div>
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="product in products" :key="product.id" class="flex items-center bg-gray-100 rounded p-2">
                            <label class="flex items-center w-full cursor-pointer">
                                <input
                                    type="checkbox"
                                    :value="product.id"
                                    @change="saveSelectedProducts($event, product.id)"
                                    :checked="selectedProducts.findIndex(p => p.id === product.id) !== -1"
                                    class="mr-2 accent-blue-500"
                                />
                                <span class="flex-1">{{ product.name }}: {{ product.price }}$</span>
                                <input v-if="selectedProducts.findIndex(p => p.id === product.id) !== -1"
                                    type="number"
                                    min="1"
                                    value="1"
                                    @input="saveSelectedProducts($event, product.id, Number(($event.target as HTMLInputElement).value))"
                                    class="ml-2 w-20 border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    placeholder="Qty"
                                />
                            </label>
                        </div>
                    </div>
                </div>
                <button type="submit" :disabled="disabledSubmit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Enviar factura</button>
            </form>
            <transition name="fade-slow">
                <div v-if="message" class="fixed inset-0 z-50 flex items-center justify-center">
                    <div
                        :class="[
                            'w-full max-w-md bg-white rounded-lg shadow-lg p-6 text-center relative',
                            messageType === 'success' ? 'border-green-400' : 'border-red-400',
                            'border-2'
                        ]"
                        role="dialog"
                        aria-modal="true"
                    >
                        <button @click="closeAlert" class="absolute top-2 right-2 text-2xl font-bold text-gray-500 hover:text-gray-700">&times;</button>
                        <div :class="messageType === 'success' ? 'text-green-700' : 'text-red-700'" class="mb-4 text-lg font-semibold">
                            {{ message }}
                            <ul v-if="errors && Object.entries(errors).length && messageType === 'error'" class="mt-2 text-left list-disc list-inside">
                                <li v-for="(error, field) in errors" :key="field">
                                    {{ field }}:
                                    <p v-for="(msg, index) in error" :key="index">
                                        {{ msg }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                        <div v-if="invoiceResponse && messageType === 'success'" class="text-left mt-2">
                            <div class="mb-2"><span class="font-bold">Invoice ID:</span> {{ invoiceResponse.id }}</div>
                            <div class="mb-2"><span class="font-bold">Name:</span> {{ invoiceResponse.name }}</div>
                            <div class="mb-2"><span class="font-bold">Email:</span> {{ invoiceResponse.email }}</div>
                            <div class="mb-2"><span class="font-bold">Products:</span>
                                <ul class="list-disc ml-6">
                                    <li v-for="prod in invoiceResponse.products" :key="prod.id">
                                        {{ prod.name }}: {{ prod.quantity }}<span v-if="prod.quantity > 1">unidades</span><span v-else>unidad</span>  * {{ prod.price }} = {{ prod.quantity * prod.price }}$
                                    </li>
                                </ul>
                            </div>
                            <div class="mb-2"><span class="font-bold">Total:</span> {{ invoiceResponse.total }}$ </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>
<style>
.fade-slow-enter-active, .fade-slow-leave-active {
    transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-slow-enter-from, .fade-slow-leave-to {
    opacity: 0;
}
.fade-slow-enter-to, .fade-slow-leave-from {
    opacity: 1;
}
</style>
<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '../services/api';
import { type AxiosResponse, type AxiosError } from 'axios';

interface Product {
    id: number;
    name: string;
    price: number;
}
interface SelectedProduct {
    id: number;
    quantity: number;
}
interface InvoiceProduct {
    id: number;
    name: string;
    quantity: number;
    price: number;
}
interface InvoiceResponse {
    id: number;
    name: string;
    email: string;
    products: InvoiceProduct[];
    total?: number;
}
const messageType = ref('success');

const closeAlert = () => {
    message.value = '';
};

const invoiceId = ref<number|null>(Number(localStorage.getItem('invoiceId')) || null);
const phase = ref(invoiceId.value ? 2 : 1);
const invoiceResponse = ref<InvoiceResponse|null>(null);
const products = ref<Product[]>([]);
const selectedProducts = ref<SelectedProduct[]>([]);
const name = ref('');
const email = ref('');
const loading = ref(false);
const disabledSubmit = ref(false);
const message = ref('');
const errors = ref<{ [key: string]: string[] }>({});

const fetchProducts = async () => {
    loading.value = true;
    try {
        const res = await api.get('/products');
        products.value = res.data.data;
    } catch (e) {
        message.value = 'Failed to load products.';
    } finally {
        loading.value = false;
    }
};

const saveSelectedProducts = (event: any ,id: number, quantity = 1) => {
    const index = selectedProducts.value.findIndex(p => p.id === id);
    const { type, checked, value } = event.target;
    if (type === 'number') {
        if (index !== -1) {
            selectedProducts.value[index] = { id: id, quantity: Number(value) || 1 };
        } else {
            selectedProducts.value.push({ id: id, quantity: Number(value) || 1 });
        }
        localStorage.setItem('selectedProducts', JSON.stringify(selectedProducts.value));
    } else if (type === 'checkbox') {
        if (checked) {
            if (index === -1) {
                selectedProducts.value.push({ id: id, quantity: 1 });
            }
        } else {
            selectedProducts.value = selectedProducts.value.filter(p => p.id !== id);
        }
        localStorage.setItem('selectedProducts', JSON.stringify(selectedProducts.value));
    }
};

const submitInvoice = async () => {
    if (!selectedProducts.value.length) {
        message.value = 'Please select at least one product.';
        messageType.value = 'error';
        return;
    }
    disabledSubmit.value = true;
    try {
        const res: AxiosResponse = await api.put(`/invoices/${invoiceId.value}`, {
            products: selectedProducts.value
        });
        if (res.status === 200 || res.status === 201) {
            message.value = res.data?.message || 'Invoice sent successfully!';
            invoiceResponse.value = res.data.data;
            messageType.value = 'success';

            localStorage.removeItem('invoiceId');
            localStorage.removeItem('selectedProducts');

            selectedProducts.value = [];
            name.value = '';
            email.value = '';
            invoiceId.value = null;
            phase.value = 1;
        } else {
            message.value = res.data?.message || 'Failed to send invoice.';
            errors.value = res.data?.errors || {};
            messageType.value = 'error';
        }
    } catch (e: any) {
        message.value = e?.response?.data?.message || 'Error sending invoice.';
        errors.value = e?.response?.data?.errors || {};
        messageType.value = 'error';
    }
    disabledSubmit.value = false;
};
const registerInvoiceId = async () => {
    disabledSubmit.value = true;
    try {
        const res: AxiosResponse = await api.post('/invoices', {
            name: name.value,
            email: email.value,
        });
        if (res.status === 200 || res.status === 201) {
            message.value = res.data?.message || 'Invoice started successfully!';
            messageType.value = 'success';

            name.value = res.data.data.name;
            email.value = res.data.data.email;

            invoiceId.value = res.data.data.id;
            localStorage.setItem('invoiceId', invoiceId.value?.toString() || '');
            phase.value = 2;
        } else {
            message.value = res.data?.message || 'Failed to send invoice.';
            errors.value = res.data?.errors || {};
            messageType.value = 'error';
        }
    } catch (e: any) {
        message.value = e?.response?.data?.message || 'Error sending invoice.';
        errors.value = e?.response?.data?.errors || {};
        messageType.value = 'error';
    }
    disabledSubmit.value = false;
};
const fetchInvoice = async (id: number) => {
    try {
        const res: AxiosResponse = await api.get(`/invoices/${id}`);
        if (res.status === 200) {
            name.value = res.data.data.name;
            email.value = res.data.data.email;
        }
    } catch (e: any) {
        message.value = e?.response?.data?.message || 'Error sending invoice.';
        errors.value = e?.response?.data?.errors || {};
        messageType.value = 'error';
    }
};

onMounted(() => {
    const storedProducts = localStorage.getItem('selectedProducts') || '';
    selectedProducts.value = storedProducts ? JSON.parse(storedProducts) : [];

    const storedInvoiceId = localStorage.getItem('invoiceId');
    invoiceId.value = storedInvoiceId !== null ? Number(storedInvoiceId) : null;

    if (invoiceId.value) {
        phase.value = 2;
        fetchInvoice(invoiceId.value);
    }

    fetchProducts();
});
</script>
