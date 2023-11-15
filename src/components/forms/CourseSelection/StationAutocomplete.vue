<template>
  <v-autocomplete
    v-model="selectedStation"
    :items="filteredStations"
    label="Select a Station"
    @input="onStationSelected"
  ></v-autocomplete>
</template>

<script>
export default {
  props: {
    stations: Array, // Array of available stations
    selectedCourse: String, // The selected course
  },
  data() {
    return {
      selectedStation: null,
    };
  },
  computed: {
    // Compute filtered stations based on the selected course
    filteredStations() {
      if (!this.selectedCourse) {
        return this.stations; // Return all stations if no course is selected
      }
      return this.stations.filter((station) =>
        station.courses.includes(this.selectedCourse)
      );
    },
  },
  methods: {
    onStationSelected() {
      this.$emit("station-selected", this.selectedStation);
    },
  },
};
</script>
