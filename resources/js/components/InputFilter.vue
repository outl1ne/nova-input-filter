<template>
  <div>
    <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
      {{ filter.name }}
    </h3>

    <div class="p-1">
      <input
        class="block w-full form-control-sm form-input border-60"
        type="text"
        @input="debounceInput"
        :placeholder="filter.placeholder || filter.name"
        :value="value"
      />
    </div>
  </div>
</template>

<script>
export default {
  props: ['resourceName', 'filterKey'],

  data: () => ({
    search: null,
    debounce: null,
  }),

  methods: {
    debounceInput(event) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.handleChange(event);
      }, 600);
    },

    handleChange(event) {
      this.$store.commit(`${this.resourceName}/updateFilterState`, {
        filterClass: this.filterKey,
        value: event.target.value,
      });

      this.$emit('change');
    },
  },

  computed: {
    filter() {
      return this.$store.getters[`${this.resourceName}/getFilter`](this.filterKey);
    },

    value() {
      return this.filter.currentValue;
    },
  },
};
</script>
