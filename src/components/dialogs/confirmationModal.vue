<template>
  <v-dialog v-model="dialog" max-width="500" persistent>
    <v-card class="text-center">
      <v-card-title>
        <v-icon x-large color="warning">mdi-alert</v-icon>
      </v-card-title>
      <v-card-text class="headline">{{ message }}</v-card-text>
      <v-card-actions class="justify-center">
        <v-btn color="error" @click="cancelAction">{{ cancelText }}</v-btn>
        <v-btn color="primary" @click="confirmAction">{{ confirmText }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    message: {
      type: String,
      default: "Are you sure?",
    },
    confirmText: {
      type: String,
      default: "OK",
    },
    cancelText: {
      type: String,
      default: "Cancel",
    },
  },
  data() {
    return {
      dialog: false,
    };
  },
  methods: {
    confirmAction() {
      this.$emit("confirm");
    },
    cancelAction() {
      this.$emit("cancel");
    },
  },
};
</script>

<style scoped>
.v-card-title {
  margin-top: 16px; /* Adjust as needed */
}

.v-card-actions {
  padding: 16px;
}

.v-btn {
  margin: 0 8px;
}
</style>
