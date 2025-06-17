<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-red-200">
                Eliminar Cuenta
            </h2>

            <p class="mt-1 text-sm text-gray-300">
                Una vez que elimine su cuenta, todos sus recursos y datos se eliminarán permanentemente. 
                Antes de eliminar su cuenta, descargue cualquier dato o información que desee conservar.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion" class="game-button danger">Eliminar Cuenta</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6 bg-gray-900 text-gray-200">
                <h2 class="text-lg font-medium text-red-200">
                    ¿Está seguro de que desea eliminar su cuenta?
                </h2>

                <p class="mt-1 text-sm text-gray-300">
                    Una vez que elimine su cuenta, todos sus recursos y datos se eliminarán permanentemente.
                    Por favor, ingrese su contraseña para confirmar que desea eliminar permanentemente su cuenta.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Contraseña"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Contraseña"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal" class="game-button">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3 game-button danger"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Eliminar Cuenta
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>

<script>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

export default {
    name: 'DeleteUserForm',
    components: {
        DangerButton,
        InputError,
        InputLabel,
        Modal,
        SecondaryButton,
        TextInput,
    },
    data() {
        return {
            confirmingUserDeletion: false,
            form: useForm({
                password: '',
            }),
        };
    },
    mounted() {
        this.passwordInput = this.$refs.passwordInput;
    },
    methods: {
        confirmUserDeletion() {
            this.confirmingUserDeletion = true;
            this.$nextTick(() => {
                this.$refs.passwordInput.focus();
            });
        },
        deleteUser() {
            this.form.delete(route('profile.destroy'), {
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
                onError: () => this.$refs.passwordInput.focus(),
                onFinish: () => this.form.reset(),
            });
        },
        closeModal() {
            this.confirmingUserDeletion = false;
            this.form.clearErrors();
            this.form.reset();
        },
    },
};
</script>