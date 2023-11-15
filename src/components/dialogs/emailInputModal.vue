<template>
  <v-dialog v-model="dialog" max-width="400">
    <v-card>
      <v-card-title class="headline">Email Credentials</v-card-title>
      <v-card-text>
        <!-- Add form fields for email credentials -->
        <v-text-field v-model="email" label="Email" required></v-text-field>
        <v-text-field
          v-model="password"
          label="Password"
          required
        ></v-text-field>
        
      </v-card-text>
      <v-card-actions>
        <v-btn color="blue darken-1" text @click="sendEmail">Send Email</v-btn>
        <v-btn text @click="closeDialog">Cancel</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data() {
    return {
      dialog: false,
      email: "",
      password: "",
    };
  },
  methods: {
    sendEmail() {
      // Add your logic to send the email using the email and password
      // Close the dialog after sending the email
      this.closeDialog();
    },
    closeDialog() {
      // Reset the email and password fields and close the dialog
      this.email = "";
      this.password = "";
      this.dialog = false;
    },
  },
};
</script>
