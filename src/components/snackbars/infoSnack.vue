<template>
  <v-snackbar
    v-model="snack.show"
    :timeout="snack.timeout"
    :color="snack.color"
    elevation="24"
    :location="snack.location"
  >
    {{ snack.message }}
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
        location: "top right",
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
