<template>
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-bold text-blue-300 glow-text">RESTABLECER CÓDIGO DE ACCESO</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="game-panel max-w-md mx-auto">
                    <div class="panel-header">
                        <h2 class="text-xl font-bold text-center">NUEVO CÓDIGO DE ACCESO</h2>
                    </div>
                    
                    <div class="panel-content">
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

                            <div class="mb-4">
                                <label for="password" class="game-label">NUEVO CÓDIGO DE ACCESO</label>
                                <input
                                    id="password"
                                    type="password"
                                    class="game-input"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="••••••••"
                                />
                                <div v-if="form.errors.password" class="game-error">{{ form.errors.password }}</div>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="game-label">CONFIRMAR CÓDIGO</label>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    class="game-input"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="••••••••"
                                />
                                <div v-if="form.errors.password_confirmation" class="game-error">
                                    {{ form.errors.password_confirmation }}
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <button
                                    type="submit"
                                    class="game-button"
                                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                    :disabled="form.processing"
                                >
                                    RESTABLECER CÓDIGO
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
import { Head, useForm } from '@inertiajs/vue3';

export default {
    name: 'ResetPassword',
    components: {
        AppLayout,
        Head,
    },
    props: {
        email: {
            type: String,
            required: true,
        },
        token: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            form: useForm({
                token: this.token,
                email: this.email,
                password: '',
                password_confirmation: '',
            }),
        };
    },
    methods: {
        submit() {
            this.form.post(route('password.store'), {
                onFinish: () => this.form.reset('password', 'password_confirmation'),
            });
        },
    },
};
</script>