<template>
  <v-app>
    <v-container class="fill-height">
      <LoginSheet />
      <infoSnack ref="snackbar" />
    </v-container>
  </v-app>
</template>

<script>
import LoginSheet from "../components/Auth/LoginSheet.vue";
import axios from "axios";

export default {
  components: {
    LoginSheet,
  },
  mounted() {
    this.verify(); // Call the verify function when the component is mounted
  },
  methods: {
    async verify() {
      // Check if the route contains a verification code
      const verificationCode = this.$route.params.verificationCode;

      if (verificationCode) {
        try {
          // Make an Axios request with the verification code
          const response = await axios.post("auth/verify/" + verificationCode);

          // Handle the response status and show the appropriate snackbar message
          if (response.status === 200) {
            this.$refs.snackbar.openSnackbar(response.data, "success");
          } else {
            this.$refs.snackbar.openSnackbar(response.data, "error");
          }
        } catch (error) {
          // Handle errors
          console.error(error);
          this.$refs.snackbar.openSnackbar("response.data", "error");
        }
      }
    },
  },
};
</script>
