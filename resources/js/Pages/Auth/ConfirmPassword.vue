<template>
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-bold text-blue-300 glow-text">CONFIRMAR ACCESO</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="game-panel max-w-md mx-auto">
                    <div class="panel-header">
                        <h2 class="text-xl font-bold text-center">ZONA RESTRINGIDA</h2>
                    </div>
                    
                    <div class="panel-content">
                        <div class="mb-4 text-blue-300">
                            Esta es un área segura de la aplicación. Por favor, confirma tu código de acceso antes de continuar.
                        </div>

                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label for="password" class="game-label">CÓDIGO DE ACCESO</label>
                                <input
                                    id="password"
                                    type="password"
                                    class="game-input"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    autofocus
                                    placeholder="••••••••"
                                />
                                <div v-if="form.errors.password" class="game-error">{{ form.errors.password }}</div>
                            </div>

                            <div class="flex justify-end mt-4">
                                <button
                                    type="submit"
                                    class="game-button"
                                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                    :disabled="form.processing"
                                >
                                    CONFIRMAR
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
    name: 'ConfirmPassword',
    components: {
        AppLayout,
        Head,
    },
    data() {
        return {
            form: useForm({
                password: '',
            }),
        };
    },
    methods: {
        submit() {
            this.form.post(route('password.confirm'), {
                onFinish: () => this.form.reset(),
            });
        },
    },
};
</script>