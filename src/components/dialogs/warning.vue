<template>
  <v-dialog v-model="dialog" max-width="400" persistent>
    <v-card>
      <v-toolbar color="error" dark>
        <v-btn icon @click="dialog = false">
          <v-icon>mdi-alert-circle</v-icon>
        </v-btn>
        <v-toolbar-title>Warning</v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        {{ text }}
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="dialog = false">OK</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    text: {
      type: String,
      default: "Make sure all requirements are met before proceeding.",
    },
  },
  data() {
    return {
      dialog: false,
    };
  },
};
</script>
