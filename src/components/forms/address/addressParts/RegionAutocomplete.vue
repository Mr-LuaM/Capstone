<template>
  <div :class="labelClass">
    {{ divLabel }}
  </div>
  <v-autocomplete
    v-model="selectedRegion"
    :items="formattedRegions"
    :rules="selectRules"
    :label="customLabel || divLabel"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    @blur="updateValue"
  >
    <template #item="{ item }">
      {{ item.name }}
      <span v-if="false">{{ item.id }}</span>
    </template>
  </v-autocomplete>
</template>

<script>
import regionsData from "@../../../public/philippine-addresses-main/region";

export default {
  props: {
    modelValue: {
      type: Object,
      default: () => ({ id: null, name: null }), // Default value with id and name properties
    },
    divLabel: {
      type: String,
      default: "Region",
    },
    customLabel: {
      type: String,
      default: "",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    customPlaceholder: {
      type: String,
      default: "Select a region",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    labelClass: {
      type: String,
      default: "text-subtitle-1 text-medium-emphasis",
    },
  },
  data() {
    return {
      selectedRegion: this.modelValue,
      selectRules: [(v) => (v && v.id ? true : `${this.divLabel} is required`)],
      regions: [],
    };
  },
  async created() {
    try {
      this.regions = regionsData.map((region) => ({
        id: region.psgc_code,
        name: region.region_name,
      }));
    } catch (error) {
      console.error("Failed to load regions:", error);
    }
  },
  computed: {
    formattedRegions() {
      return this.regions; // Return the entire objects
    },
  },
  watch: {
    modelValue(newVal) {
      this.selectedRegion = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.selectedRegion);
    },
  },
};
</script>
