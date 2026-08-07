<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Check, X } from '@lucide/vue';
import { store } from '@/routes/stores';
import { computed, ref, watch } from 'vue';

defineOptions({
    layout: {
        title: 'Create your store',
        description: 'Choose a name for your store. This will be used as your subdomain.',
    },
});

const name = ref('');
const isAvailable = ref<boolean | null>(null);
const isChecking = ref(false);
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const domain = computed(() => {
    const host = window.location.hostname;
    const parts = host.split('.');
    return parts.length > 1 ? parts.slice(1).join('.') : 'localhost';
});

const slug = computed(() => {
    return name.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
});

const checkName = (value: string) => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    if (!value || value.length < 2) {
        isAvailable.value = null;
        return;
    }

    isChecking.value = true;

    debounceTimer = setTimeout(async () => {
        try {
            const response = await fetch(`/api/store/check?name=${encodeURIComponent(value)}`);
            const data = await response.json();
            isAvailable.value = data.available;
        } catch {
            isAvailable.value = null;
        } finally {
            isChecking.value = false;
        }
    }, 500);
};

watch(name, (value) => {
    checkName(value);
});
</script>

<template>

    <Head title="Create your store" />

    <Form v-bind="store.form()" :only="['errors']" v-slot="{ errors, processing }" class="flex flex-col gap-6"
        @finish="isChecking = false">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="name">Store name</Label>
                <div class="relative">
                    <Input id="name" v-model="name" type="text" name="name" required autofocus :tabindex="1"
                        autocomplete="organization" placeholder="My Store"
                        :class="{ 'border-green-500 focus-visible:ring-green-500': isAvailable === true, 'border-red-500 focus-visible:ring-red-500': isAvailable === false }" />
                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                        <Spinner v-if="isChecking" class="h-4 w-4 text-muted-foreground" />
                        <Check v-else-if="isAvailable === true" class="h-4 w-4 text-green-500" />
                        <X v-else-if="isAvailable === false" class="h-4 w-4 text-red-500" />
                    </div>
                </div>
                <p v-if="name && isAvailable === true" class="text-sm text-green-600">
                    <span class="font-mono">{{ slug }}.{{ domain }}</span>
                </p>
                <p v-if="isAvailable === false" class="text-sm text-red-600">
                    This store name is already taken.
                </p>
                <InputError :message="errors.name" />
            </div>

            <Button type="submit" class="mt-4 w-full" :tabindex="2" :disabled="processing || !isAvailable"
                data-test="create-store-button">
                <Spinner v-if="processing" />
                Create store
            </Button>
        </div>
    </Form>
</template>
