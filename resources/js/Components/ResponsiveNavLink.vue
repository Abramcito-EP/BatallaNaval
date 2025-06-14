<template>
  <div>
    <button
      v-if="as === 'button'"
      :class="classes"
      @click="$emit('click', $event)"
    >
      <slot />
    </button>

    <Link
      v-else
      :href="href"
      :class="classes"
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
    active: {
      type: Boolean,
      default: false,
    },
    href: {
      type: String,
      default: '#',
    },
    as: {
      type: String,
      default: 'inertia',
    },
  },

  computed: {
    classes() {
      return this.active
        ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-blue-400 text-left text-base font-medium text-blue-700 bg-blue-50 focus:outline-none focus:text-blue-800 focus:bg-blue-100 focus:border-blue-700 transition duration-150 ease-in-out'
        : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
    },
  },
};
</script>