<template>
  <v-snackbar
    v-model="snack.show"
    :timeout="snack.timeout"
    :color="snack.color"
    elevation="24"
    :location="snack.location"
    border="start"
    variant="tonal"
    max-height="200px"
    class="ma-2"
    closable
  >
    {{ snack.message }}
    <template v-slot:actions>
      <v-btn :color="snack.color" variant="text" @click="show = false">
        <v-icon>mdi-close-circle</v-icon>
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script>
export default {
  data() {
    return {
      snack: {
        show: false,
        message: "",
        timeout: 2000,
        color: "error",
        location: "bottom right",
      },
    };
  },
  methods: {
    openSnackbar(message, color) {
      this.snack.show = true;
      this.snack.message = message;
      this.snack.color = color;
    },
    closeSnackbar() {
      this.snack.show = false;
    },
  },
};
</script>
