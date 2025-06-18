<template>
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-bold text-blue-300 glow-text">RECUPERAR CÓDIGO DE ACCESO</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="game-panel max-w-md mx-auto">
                    <div class="panel-header">
                        <h2 class="text-xl font-bold text-center">RECUPERACIÓN DE ACCESO</h2>
                    </div>
                    
                    <div class="panel-content">
                        <div class="mb-4 text-blue-300">
                            ¿Olvidaste tu código de acceso? No hay problema. Sólo indícanos tu dirección de correo electrónico y te enviaremos un enlace para que puedas elegir uno nuevo.
                        </div>

                        <div
                            v-if="status"
                            class="game-alert success mb-4"
                        >
                            {{ status }}
                        </div>

                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label for="email" class="game-label">IDENTIFICADOR</label>
                                <input
                                    id="email"
                                    type="email"
                                    class="game-input"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="correo@ejemplo.com"
                                />
                                <div v-if="form.errors.email" class="game-error">{{ form.errors.email }}</div>
                            </div>

                            <div class="flex items-center justify-between mt-6">
                                <Link
                                    :href="route('login')"
                                    class="game-button secondary xsmall"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                                    </svg>
                                    VOLVER
                                </Link>
                                
                                <button
                                    type="submit"
                                    class="game-button"
                                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                    :disabled="form.processing"
                                >
                                    ENVIAR ENLACE
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

export default {
    name: 'ForgotPassword',
    components: {
        AppLayout,
        Head,
        Link,
    },
    props: {
        status: {
            type: String,
        },
    },
    data() {
        return {
            form: useForm({
                email: '',
            }),
        };
    },
    methods: {
        submit() {
            this.form.post(route('password.email'));
        },
    },
};
</script>