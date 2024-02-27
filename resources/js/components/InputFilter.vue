<template>
  <div class="o1-pt-2 o1-pb-1">
    <h3 class="px-3 text-xs uppercase font-bold tracking-wide">
      {{ filter.name }}
    </h3>

    <div class="p-2">
      <input
        class="w-full form-control form-input form-control-bordered"
        :type="filter.input_type"
        :pattern="filter.input_integers ? '\\d*' : false"
        :min="filter.input_min"
        :max="filter.input_max"
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
