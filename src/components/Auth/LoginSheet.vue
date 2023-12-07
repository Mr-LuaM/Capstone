<template>
  <v-container class="align-self-center">
    <v-card
      class="mx-auto align-self-center pa-10"
      min-width="500"
      max-width="500"
      border
      rounded
    >
      <v-progress-linear
        :active="loading"
        :indeterminate="loading"
        absolute
        bottom
        color="secondary"
      ></v-progress-linear>
      <div class="text-center mb-6">
        <span
          class="text-primary font-weight-meduim d-flex align-center justify-center"
        >
          <v-img
            src="../../assets/img/logo/pnp-logo.png"
            alt="Logo"
            :max-width="40"
            :max-height="52"
            aspect-ratio="1/1"
            cover
            class="ml-0 pa-0"
          />
          <span class="ml-3 text-h6 font-weight-bold">PnpEduLink</span>
        </span>
      </div>

      <v-window v-model="step">
        <v-window-item :value="1">
          <div class="text-center">
            <h2 class="mb-4 font-weight-regular">Sign in</h2>
            <span class="d-inline-block mb-8">Use your PnpEduLink Account</span>
          </div>
          <v-form ref="checkEmail" @submit.prevent="checkEmail">
            <EmailInput
              v-model="email"
              divLabel=""
              customDensity="default"
              customVariant="outlined"
              customLabel="Email"
              :showTooltip="false"
              customPlaceholder=""
              :error-messages="emailError"
            />
          </v-form>

          <a
            href="#forgot-email-section"
            class="text-body-2 text-decoration-none text-blue-accent-4 font-weight-medium"
            >Forgot Email?</a
          >

          <div class="text-body-2 text--secondary mt-8 text-grey-lighten-1">
            Not your computer? Use a Private Window to sign in.
          </div>
          <a
            href="#"
            class="d-inline-block text-blue-accent-4 text-decoration-none font-weight-medium text-body-2"
            >Learn More</a
          >
        </v-window-item>

        <v-window-item :value="2">
          <div class="text-center">
            <h2 class="mb-2 font-weight-regular">Welcome</h2>
            <v-chip class="mb-9" variant="outlined">
              <v-avatar left>
                <v-icon> mdi-account-circle </v-icon>
              </v-avatar>
              {{ email }}
              <v-avatar right>
                <v-icon> mdi-chevron-down </v-icon>
              </v-avatar>
            </v-chip>
          </div>
          <v-form ref="form" @submit.prevent="submit">
            <PasswordInput
              divLabel=""
              v-model="password"
              customDensity="default"
              customVariant="outlined"
              customLabel="Password"
              :showTooltip="false"
              customPlaceholder=""
              customHint=""
              :includeUppercase="false"
              :includeLowercase="false"
              :includeDigit="false"
              :includeSpecialChar="false"
              :error-messages="passwordError"
            />
          </v-form>

          <a
            href="#forgot-email-section"
            class="text-body-2 text-decoration-none text-blue-accent-4 font-weight-medium"
            >Forgot Password?</a
          >
        </v-window-item>
      </v-window>

      <v-card-actions class="mt-8 mb-0 pa-0">
        <router-link
          v-if="step === 1"
          to="/createAccount"
          class="d-inline-block text-blue-accent-4 text-decoration-none font-weight-medium text-body-2"
          >Create An Account</router-link
        >

        <v-btn v-if="step > 1" variant="text" @click="step--"> Back </v-btn>
        <v-spacer></v-spacer>
        <v-btn
          v-if="step === 1"
          color="primary"
          variant="flat"
          @click="checkEmail"
        >
          Next
        </v-btn>
        <v-btn v-if="step === 2" color="primary" variant="flat" @click="submit">
          Login
        </v-btn>
      </v-card-actions>
    </v-card>
    <v-row align="center" justify="center">
      <div class="d-flex justify-space-between mt-6">
        <v-btn class="text-none letter-spacing-0" variant="text">
          English (United States)
        </v-btn>
        <div style="margin-right: -12px" class="text-center">
          <v-btn class="text-none letter-spacing-0" variant="text">
            Help
          </v-btn>
          <v-btn class="text-none letter-spacing-0" variant="text">
            Privacy
          </v-btn>
          <v-btn class="text-none letter-spacing-0" variant="text">
            Terms
          </v-btn>
        </div>
      </div>
    </v-row>
  </v-container>
</template>
<script>
import { jwtDecode as jwt_decode } from "jwt-decode";
import axios from "axios";
export default {
  data: () => ({
    step: 1,

    email: "",
    password: "",
    emailError: null,
    passwordError: "",
    loading: false,
  }),
  methods: {
    async checkEmail() {
      // Set loading to true before making the request

      const { valid } = await this.$refs.checkEmail.validate();

      if (valid) {
        try {
          const formData = new FormData();
          formData.append("Email", this.email);

          // Make the Axios POST request
          const response = await axios.post("checkEmail", formData);

          if (response.data.success === true) {
            this.loading = true;
            // After receiving a successful response, wait for 3 seconds
            setTimeout(() => {
              // Update the loading state to false and increment the step

              this.emailError = null;
              this.step++;
              this.loading = false;
            }, 2000);
          } else {
            this.emailError = response.data.error || "Email not found";

            // If the response is not successful, wait for a shorter time before setting loading to false
            setTimeout(() => {
              this.loading = false;
            }, 100);
          }
        } catch (error) {
          console.error(error);
        } finally {
          // Set loading to false after the request is completed
          setTimeout(() => {
            this.loading = false;
          }, 2000);
        }
      }
    },

    handleUserRole(role) {
      const router = this.$router; // Access router directly

      if (router) {
        console.log("Role received:", role);

        const roleRoutes = {
          1: "/admin/applicants",
          6: "/applicant-dashboard",
          2: "/admin/applicants",
          3: "/station/dashboard",
          5: "/admin/applicants",
          4: "/teacher-dashboard",
        };

        const defaultRoute = "/hi";
        const targetRoute = roleRoutes[role] || defaultRoute;

        console.log(`Redirecting to ${targetRoute}`);
        router.push(targetRoute);
      }
    },

    async submit() {
      const { valid } = await this.$refs.form.validate();

      if (valid) {
        try {
          this.loading = true;

          const formData = new FormData();
          formData.append("Email", this.email);
          formData.append("Password", this.password);

          const response = await axios.post("login", formData);

          if (response.data.token) {
            const token = response.data.token;

            localStorage.setItem("jwt_token", token);

            // Use jwt_decode alias to decode the token
            const decodedToken = jwt_decode(token);

            // Extract user ID, email, and role from the decoded token
            const userId = decodedToken.user_id; // Check that 'user_id' matches the backend
            const userEmail = decodedToken.email;
            const userRole = decodedToken.role; // Add this line to extract the role

            // Store user ID, email, and role in Vuex
            this.$store.dispatch("login", { userId, userEmail, userRole });

            // Set the user role in the store
            this.$store.dispatch("setRole", userRole);

            this.$store.dispatch("fetchUserDetails");

            // If you have a specific reason for delaying execution, keep it; otherwise, remove this setTimeout
            this.handleUserRole(userRole);
          } else {
            // Display the error message returned from the server
            this.passwordError = response.data.error || "Account not active";
            console.log(response.data.error);
          }
        } catch (error) {
          this.passwordError = "Account not active";
        } finally {
          this.loading = false;
        }
      }
    },
  },
};
</script>
