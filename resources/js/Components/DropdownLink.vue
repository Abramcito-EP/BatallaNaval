<template>
  <div>
    <button
      v-if="as === 'button'"
      type="submit"
      class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
      @click="$emit('click', $event)"
    >
      <slot />
    </button>

    <Link
      v-else-if="as === 'a'"
      :href="href"
      class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
      <slot />
    </Link>

    <Link
      v-else
      :href="href"
      :method="method"
      :as="as"
      :headers="headers"
      class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
      <slot />
    </Link>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  components: {
    Link,
  },

  props: {
    href: {
      type: String,
      default: '#',
    },
    method: {
      type: String,
      default: 'get',
    },
    as: {
      type: String,
      default: 'inertia',
    },
    headers: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>