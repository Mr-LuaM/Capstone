<template>
  <v-btn icon @click="toggleTheme" class="dark-mode-button" variant="text">
    <v-icon>
      {{ darkMode ? "mdi-weather-sunny" : "mdi-weather-night" }}
    </v-icon>
  </v-btn>
</template>

<script>
import { useTheme } from "vuetify";
import { ref } from "vue";

export default {
  name: "ThemeSwitcher",
  setup() {
    const theme = useTheme();
    const darkMode = ref(true);

    function toggleTheme() {
      theme.global.name.value = theme.global.current.value.dark
        ? "light"
        : "dark";
      darkMode.value = !darkMode.value;
    }

    return {
      toggleTheme,
      darkMode,
    };
  },
};
</script>

<style>
.dark-mode-button {
  transition: background-color 0.5s;
}
</style>
