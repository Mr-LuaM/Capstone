<template>
  <v-container class="fill-height">
    <LoginSheet />
  </v-container>
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
console.log(verificationCode)
      if (verificationCode) {
        try {
          // Make an Axios request with the verification code
          const response = await axios.post("auth/verify/"
            + verificationCode);

          // Handle the response as needed
          console.log(response);
        } catch (error) {
          // Handle errors
          console.error(error);
        }
      }
    },
  },
};
</script>
