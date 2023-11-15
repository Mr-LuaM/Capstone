<template>
  <div :class="labelClass">
    {{ divLabel }}
  </div>
  <!-- <v-row> -->
  <!-- <v-col sm="6"> -->
  <v-autocomplete
    v-model="selectedReligion"
    :items="religions"
    :item-text="(item) => item"
    :rules="selectRules"
    :label="customLabel"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    @blur="updateValue"
  ></v-autocomplete>
  <!-- </v-col> -->
  <!-- <v-col sm="6">
      <v-text-field
        v-model="selectedReligion"
        :variant="customVariant"
        :density="customDensity"
        placeholder="Other, Please specify"
        disabled
      >
      </v-text-field>
    </v-col> -->
  <!-- </v-row> -->
</template>

<script>
export default {
  props: {
    modelValue: {
      type: String,
      default: "", // You can set a default value if needed
    },
    divLabel: {
      type: String,
      default: "",
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
      default: "Select a religion",
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
      selectedReligion: this.modelValue,
      selectRules: [(v) => !!v || `Religion is required`],
      religions: [
        "Roman Catholicism",
        "Islam",
        "Iglesia ni Cristo",
        "Protestantism",
        "Buddhism",
        "Hinduism",
        "Sikhism",
        "Judaism",
        "Bahá'í Faith",
        "None",
        // You can add more specific denominations or indigenous belief systems here
      ],
    };
  },
  watch: {
    modelValue(newVal) {
      this.selectedReligion = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.selectedReligion);
    },
  },
};
</script>
