<template>
  <v-dialog v-model="dialog" max-width="400" persistent>
    <v-card>
      <v-toolbar color="success" dark>
        <v-btn icon @click="closeDialog">
          <v-icon>mdi-check-circle</v-icon>
        </v-btn>
        <v-toolbar-title>Success</v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        {{ text }}
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="closeDialog">OK</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    text: {
      type: String,
      default: "Success",
    },
  },
  data() {
    return {
      dialog: false,
    };
  },
  methods: {
    closeDialog() {
      this.dialog = false;
      this.$emit("ok-clicked"); // Emit an event when the "OK" button is clicked
    },
  },
};
</script>
