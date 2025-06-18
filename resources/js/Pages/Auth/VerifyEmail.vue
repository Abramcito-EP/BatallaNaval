<template>
    <AppLayout>
        <template #header>
            <h2 class="text-xl font-bold text-blue-300 glow-text">VERIFICACIÓN DE CORREO</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="game-panel">
                    <div class="panel-header">
                        <h2 class="text-xl font-bold text-center">VERIFICACIÓN REQUERIDA</h2>
                    </div>
                    
                    <div class="panel-content">
                        <div class="mb-4 text-blue-300">
                            ¡Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que acabamos de enviarte? Si no has recibido el correo, podemos enviarte otro.
                        </div>

                        <div
                            class="mb-4 text-sm font-medium text-green-400"
                            v-if="verificationLinkSent"
                        >
                            Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.
                        </div>

                        <form @submit.prevent="submit">
                            <div class="flex items-center justify-between mt-4">
                                <button
                                    type="submit"
                                    class="game-button"
                                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                    :disabled="form.processing"
                                >
                                    REENVIAR CORREO DE VERIFICACIÓN
                                </button>

                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="game-button secondary xsmall"
                                >
                                    CERRAR SESIÓN
                                </Link>
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
    name: 'VerifyEmail',
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
            form: useForm({}),
        };
    },
    computed: {
        verificationLinkSent() {
            return this.status === 'verification-link-sent';
        },
    },
    methods: {
        submit() {
            this.form.post(route('verification.send'));
        },
    },
};
</script>